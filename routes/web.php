<?php

use App\Livewire\FlowBuilder\Canvas;
use App\Models\Flow;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Create new flow and redirect
Route::get('/flow-builder', function () {
    $flow = Flow::create();

    return redirect()->route('flow-builder.show', $flow);
})->name('flow-builder.create');

// Show specific flow
Route::get('/flow-builder/{flow}', Canvas::class)->name('flow-builder.show');
