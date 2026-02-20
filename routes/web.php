<?php

use Darvis\FluxFilemanager\Livewire\EditorDemo;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/darvis/editor-demo', EditorDemo::class)
        ->name('flux-filemanager.editor-demo');

    Route::view('/darvis/filemanager-checklist', 'flux-filemanager::examples.filemanager-checklist')
        ->name('flux-filemanager.filemanager-checklist');
});
