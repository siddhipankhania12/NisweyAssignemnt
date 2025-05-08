<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;


Route::get('/', [ContactController::class, 'index']);
Route::resource('contacts', ContactController::class);
// Route::post('contacts/import', [ContactController::class, 'importXML'])->name('contacts.import');
Route::post('/contacts/import', [ContactController::class, 'import'])->name('contacts.import');
