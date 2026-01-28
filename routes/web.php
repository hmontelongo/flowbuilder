<?php

use App\Livewire\FlowBuilder\Canvas;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/flow-builder', Canvas::class)->name('flow-builder');
