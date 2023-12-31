<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penilaian;
use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\SubKriteria;

class PenilaianAlternatifController extends Controller
{
    function index() {
        $penilaian = Penilaian::with(['guru','kriteria','subKriteria'])->get();
        return view('pages.penilaian.index',compact('penilaian'));
    }

    function create() {
        $alternatif     = Alternatif::with('alternatif')->get();
        $kriteria       = Kriteria::get();
        $subKriteria    = SubKriteria::get();
        return view('pages.penilaian.form', compact(['alternatif','kriteria','subKriteria']));
    }

    function store(Request $request) {
        $kriteria      = $request->id_kriteria;
        $subKriteria   = $request->id_sub;

        for ($i=0; $i < count($kriteria); $i++) { 
            $penilaian = new Penilaian;
            $penilaian->periode       = $request->periode;
            $penilaian->id_alternatif = $request->id_alternatif;
            $penilaian->id_kriteria   = $kriteria[$i];
            $penilaian->id_sub        = $subKriteria[$i];
            $penilaian->save();
        }
    }
}
