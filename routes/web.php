<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    LoginController,
};
use App\Http\Controllers\{
    DashboardController,
    DataGuruController,
    DataAlternatifController,
    DataKriteriaController,
    DataSubKriteriaController,
    HasilPerhitunganController,
    PenilaianAlternatifController,
    PerhitunganController,
    UsersController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->middleware('guest')->name('login');
Route::post('/process', [LoginController::class, 'authenticate'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin & Penguji role
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::middleware('admin')->group(function () {
        Route::prefix('data-kriteria')->group(function () {
            Route::get('/', [DataKriteriaController::class, 'index'])->name('data_kriteria');
            Route::get('/tambah', [DataKriteriaController::class, 'create'])->name('create_kriteria');
            Route::post('/store', [DataKriteriaController::class, 'store'])->name('store_kriteria');
            Route::get('/edit{id}', [DataKriteriaController::class, 'edit'])->name('edit_kriteria');
            Route::post('/update{id}', [DataKriteriaController::class, 'update'])->name('update_kriteria');
            Route::delete('/hapus/{id}', [DataKriteriaController::class, 'destroy'])->name('destroy_kriteria');
        });

        Route::prefix('data-sub-kriteria')->group(function () {
            Route::get('/', [DataSubKriteriaController::class, 'index'])->name('data_sub_kriteria');
            Route::get('/tambah', [DataSubKriteriaController::class, 'create'])->name('create_sub_kriteria');
            Route::post('/store', [DataSubKriteriaController::class, 'store'])->name('store_sub_kriteria');
            Route::get('/edit{id}', [DataSubKriteriaController::class, 'edit'])->name('edit_sub_kriteria');
            Route::post('/update{id}', [DataSubKriteriaController::class, 'update'])->name('update_sub_kriteria');
            Route::delete('/hapus/{id}', [DataSubKriteriaController::class, 'destroy'])->name('destroy_sub_kriteria');
        });
        Route::prefix('data-pengguna')->group(function () {
            Route::get('/', [UsersController::class, 'index'])->name('data_pengguna');
            Route::get('/tambah', [UsersController::class, 'create'])->name('create_pengguna');
            Route::post('/store', [UsersController::class, 'store'])->name('store_pengguna');
            Route::delete('/hapus/{id}', [UsersController::class, 'destroy'])->name('destroy_pengguna');
        });
    });
    Route::middleware('penguji')->group(function () {
        Route::prefix('data-guru')->group(function () {
            Route::get('/', [DataGuruController::class, 'index'])->name('data_guru');
            Route::get('/tambah', [DataGuruController::class, 'create'])->name('create_guru');
            Route::post('/store', [DataGuruController::class, 'store'])->name('store_guru');
            Route::get('/edit{id}', [DataGuruController::class, 'edit'])->name('edit_guru');
            Route::get('/detail{id}', [DataGuruController::class, 'detail'])->name('detail_guru');
            Route::post('/update{id}', [DataGuruController::class, 'update'])->name('update_guru');
            Route::delete('/hapus/{id}', [DataGuruController::class, 'destroy'])->name('destroy_guru');
        });


        Route::prefix('data-alternatif')->group(function () {
            Route::get('/', [DataAlternatifController::class, 'index'])->name('data_alternatif');
            Route::get('/tambah', [DataAlternatifController::class, 'create'])->name('create_alternatif');
            Route::post('/store', [DataAlternatifController::class, 'store'])->name('store_alternatif');
            Route::get('/edit{id}', [DataAlternatifController::class, 'edit'])->name('edit_alternatif');
            Route::post('/update{id}', [DataAlternatifController::class, 'update'])->name('update_alternatif');
            Route::delete('/hapus/{id}', [DataAlternatifController::class, 'destroy'])->name('destroy_alternatif');
        });


        Route::prefix('penilaian-alternatif')->group(function () {
            Route::get('/', [PenilaianAlternatifController::class, 'index'])->name('penilaian_alternatif');
            Route::get('/tambah', [PenilaianAlternatifController::class, 'create'])->name('create_penilaian');
            Route::post('/store', [PenilaianAlternatifController::class, 'store'])->name('store_penilaian');
            Route::delete('/delete/{kode_alternatif}/{periode}', [PenilaianAlternatifController::class, 'delete'])->name('delete_penilaian');
            Route::get('/edit/{kode_alternatif}/{periode}', [PenilaianAlternatifController::class, 'edit'])->name('edit_penilaian');
            Route::post('/update/{kode_alternatif}/{periode}', [PenilaianAlternatifController::class, 'update'])
                ->name('update_penilaian');
        });

        Route::prefix('proses-saw')->group(function () {
            Route::get('/', [PerhitunganController::class, 'index'])->name('proses_saw');
        });

        Route::prefix('hasil-perhitungan')->group(function () {
            Route::get('/', [HasilPerhitunganController::class, 'index'])->name('laporan_hasil');
        });
    });
});
