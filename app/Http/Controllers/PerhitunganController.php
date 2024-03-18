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
        $alternatif = Alternatif::with('penilaian.kriteria')->orderBy('kode_alternatif', 'ASC')->get();
        $kriteria = Kriteria::get();
        $penilaian = Penilaian::with('subKriteria')->get();
        // return response()->json($alternatif);

        // mencari min max
        foreach ($kriteria as $key => $vkriteria) {
            foreach ($penilaian as $key_1 => $vpenilaian) {
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
            foreach ($kriteria as $key_1 => $vkriteria) { // hasil normalisasi x bobot_kriteria
                // Check if the key exists in the $vnormalisasi array
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
        // asort($rank); //sortir $rank

        // Pass data to the view
        return view('pages.proses_penilaian.index', compact('kriteria', 'alternatif', 'penilaian', 'minMax', 'normalisasi', 'rank'));
    }
}
