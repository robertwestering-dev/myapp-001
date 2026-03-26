<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $trainings = \App\Models\Training::latest()->get();
    return view('home', compact('trainings'));
});

Route::post('/training', function () {
    session()->push('trainings', request('titel'));
    return redirect('/');
});

Route::delete('/training/{id}', function ($id) {
    \App\Models\Training::find($id)?->delete();
    return redirect('/');
});