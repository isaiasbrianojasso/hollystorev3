<?php

use App\Http\Controllers\ControllerBase;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Servicio;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard',[
            'Servicio' => Servicio::all()]);
    })->name('dashboard');

    Route::get('/Admin', function () {
        return view('Admin.show',[
            'Servicio' => Servicio::all()]);
    })->name('admin');

    Route::get('/Services/show', function () {
        return view('Services.show',[
            'Servicio' => Servicio::all()]);
    })->name('admin');

    Route::get('/Services/Apple', function () {
        return view('Services.Apple',[
            'Servicio' => Servicio::all()]);
    })->name('admin');

    Route::get('/Services/Call', function () {
        return view('Services.Call',[
            'Servicio' => Servicio::all()]);
    })->name('admin');

    Route::get('/Services/Email', function () {
        return view('Services.Email',[
            'Servicio' => Servicio::all()]);
    })->name('admin');

    Route::get('/Services/SMS', function () {
        return view('Services.SMS',[
            'Servicio' => Servicio::all()]);
    })->name('admin');

    Route::get('/Services/Xiaomi', function () {
        return view('Services.Xiaomi',[
            'Servicio' => Servicio::all()]);
    })->name('admin');

    Route::match(['get', 'post'],'/agregar_usuario', [ControllerBase::class, 'agregar_usuario'])->name('agregar_usuario');
    Route::match(['get', 'post'],'/editar_usuario', [ControllerBase::class, 'editar_usuario'])->name('editar_usuario');
    Route::match(['get', 'post'],'/agregar_creditos', [ControllerBase::class, 'agregar_creditos'])->name('agregar_creditos');
    Route::match(['get', 'post'],'/eliminar_usuario/{id}', [ControllerBase::class, 'eliminar_usuario'])->name('eliminar_usuario');
    Route::match(['get', 'post'],'/agregar_servicio', [ControllerBase::class, 'agregar_servicio'])->name('agregar_servicio');

    Route::get('/admin/creditos/show/{id}', [ControllerBase::class, 'show_creditos']);
    Route::get('/recuerda_pago/{id}', [ControllerBase::class, 'recuerda_pago']);
    Route::get('/reciclar_creditos/{idcreditos}', [ControllerBase::class, 'reciclar_creditos']);
    Route::get('/agregar_tiempo', [ControllerBase::class, 'agregar_tiempo']);



});

