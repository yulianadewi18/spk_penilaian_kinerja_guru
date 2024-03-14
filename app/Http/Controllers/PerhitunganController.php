<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Alternatif;
use App\Models\Penilaian;
use Illuminate\Support\Facades\Auth;

class PerhitunganController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();

        // Mendapatkan data dari database
        $alternatif = Alternatif::with(['penilaian' => function ($query) use ($user_id) {
            $query->where('id_admin', $user_id);
        }])->orderBy('kode_alternatif', 'ASC')->get();
        $kriteria = Kriteria::get();
        $penilaian = Penilaian::with('subKriteria')->where('id_admin', $user_id)->get();

        // Calculate min and max values for each criteria
        $minMax = [];
        foreach ($kriteria as $vkriteria) {
            foreach ($penilaian as $vpenilaian) {
                if ($vkriteria->id == $vpenilaian->id_kriteria) {
                    if ($vkriteria->sifat == "benefit") {
                        $minMax[$vkriteria->id][] = $vpenilaian->subKriteria['bobot'];
                    } elseif ($vkriteria->sifat == "cost") {
                        $minMax[$vkriteria->id][] = $vpenilaian->subKriteria['bobot'];
                    }
                }
            }
        }

        // Perform normalization
        $normalisasi = [];
        foreach ($penilaian as $vpenilaian) {
            foreach ($kriteria as $vkriteria) {
                if ($vkriteria->id == $vpenilaian->id_kriteria) {
                    if ($vkriteria->sifat == "benefit") {
                        $normalisasi[$vpenilaian->alternatif->guru['nama_guru']][$vkriteria->id] = $vpenilaian->subKriteria['bobot'] / max($minMax[$vkriteria->id]);
                    } elseif ($vkriteria->sifat == "cost") {
                        $normalisasi[$vpenilaian->alternatif->guru['nama_guru']][$vkriteria->id] = min($minMax[$vkriteria->id]) / $vpenilaian->subKriteria['bobot'];
                    }
                }
            }
        }

        // Perform ranking
        $rank = [];
        foreach ($normalisasi as $key => $vnormalisasi) {
            
            foreach ($kriteria as $vkriteria) {
                if (isset($vnormalisasi[$vkriteria->id])) {
                    $rank[$key][] = $vnormalisasi[$vkriteria->id] * $vkriteria->bobot_kriteria;
                } else {
                    $rank[$key][] = 0; // Assign a default value
                }
            }
        }

        // Calculate total ranking for each alternative
        foreach ($normalisasi as $key => $value) {
            $rank[$key][] = array_sum($rank[$key]);
        }
        // dd($kriteria);
        // Sort the ranking
        arsort($rank);

        // Pass data to the view
        return view('pages.proses_penilaian.index', compact('kriteria', 'alternatif', 'penilaian', 'minMax', 'normalisasi', 'rank'));
    }
}
