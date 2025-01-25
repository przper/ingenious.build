<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Presentation\Http\CreateInvoiceController;
use Modules\Invoices\Presentation\Http\GetInvoiceController;
use Modules\Invoices\Presentation\Http\SendInvoiceController;

Route::get('/invoice/{id}', GetInvoiceController::class);
Route::post('/invoice', CreateInvoiceController::class);
Route::post('/invoice/{id}/send', SendInvoiceController::class);
