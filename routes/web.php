<?php

use App\Models\Guard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('admin');
});

/* Route::get('/pdf', function () {
    $guards = Guard::where('id', 1)->get();
    return view('working-time-pdf', compact('guards'));
}); */
/* Route::get('/pdf', function () {
    $guards = Guard::where('site_id', 1)->get();
    return view('guards-pdf', compact('guards'));
}); */