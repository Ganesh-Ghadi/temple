<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\DevtasController;
use App\Http\Controllers\Api\GurujisController;
use App\Http\Controllers\Api\ProfilesController;
use App\Http\Controllers\Api\ReceiptsController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\PoojaDatesController;
use App\Http\Controllers\Api\PoojaTypesController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\ReceiptHeadsController;
use App\Http\Controllers\Api\ReceiptTypesController;
use App\Http\Controllers\Api\DenominationsController;



Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware'=>['auth:sanctum', 'permission']], function(){
  
   Route::resource('departments', DepartmentController::class);  
   Route::resource('profiles', ProfilesController::class);  
   Route::resource('denominations', DenominationsController::class);  
   Route::resource('receipts', ReceiptsController::class);  
   Route::resource('devtas', DevtasController::class);
   Route::resource('gurujis', GurujisController::class);
   Route::resource('receipt_types', ReceiptTypesController::class);
   Route::resource('pooja_types', PoojaTypesController::class);    
   Route::resource('pooja_dates', PoojaDatesController::class);    
   Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');
   Route::get('/roles', [RolesController::class, 'index'])->name("roles.index");
   Route::get('/all_devtas', [DevtasController::class, 'allDevtas'])->name("devtas.all");
   Route::get('/all_pooja_types', [PoojaTypesController::class, 'allPoojaTypes'])->name("pooja_types.all");
   Route::get('/generate_denomination/{id}', [DenominationsController::class, 'generateDenomination'])->name("denominations.print");
   Route::get('/all_receipt_heads', [ReceiptHeadsController::class, 'allReceiptHeads'])->name("receipt_heads.all");
   Route::get('/all_receipt_types', [ReceiptTypesController::class, 'allReceiptTypes'])->name("receipt_types.all");
   Route::get('/generate_receipt/{id}', [ReceiptsController::class, 'generateReceipt'])->name("receipts.print");
   Route::get('/cancel_receipt/{id}', [ReceiptsController::class, 'cancelReceipt'])->name("receipts.cancle");
   Route::get('/permissions', [PermissionsController::class, 'index'])->name("permissions.index");
   Route::get('/generate_permissions', [PermissionsController::class, 'generatePermissions'])->name("permissions.generate");
   Route::get('/roles/{id}', [RolesController::class, 'show'])->name("roles.show");
   Route::get('/rol', [RolesController::class, 'shfdfow'])->name("rolfdefes.sfdefhow");

});