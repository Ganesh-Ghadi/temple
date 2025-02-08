<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\DevtasController;
use App\Http\Controllers\Api\GurujisController;
use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\ProfilesController;
use App\Http\Controllers\Api\ReceiptsController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\PoojaDatesController;
use App\Http\Controllers\Api\PoojaTypesController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\ReceiptHeadsController;
use App\Http\Controllers\Api\ReceiptTypesController;
use App\Http\Controllers\Api\DenominationsController;



Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware'=>['auth:sanctum', 'permission']], function(){
  
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
   Route::put('/roles/{id}', [RolesController::class, 'update'])->name("roles.update");
   Route::post('/all_receipt_report', [ReportsController::class, 'allReceiptReport'])->name("report.all_receipt_report");
   Route::post('/summary_receipt_report', [ReportsController::class, 'ReceiptSummaryReport'])->name("report.summary_receipt_report");
   Route::post('/cheque_collection_report', [ReportsController::class, 'ChequeCollectionReport'])->name("report.cheque_collection_report");
   Route::post('/upi_collection_report', [ReportsController::class, 'upiCollectionReport'])->name("report.upiCollectionReport");
   Route::post('/khat_report', [ReportsController::class, 'khatReport'])->name("report.khatReport");
   Route::post('/naral_report', [ReportsController::class, 'naralReport'])->name("report.naralReport");
   Route::post('/cancelled_receipt_report', [ReportsController::class, 'cancelledReceiptReport'])->name("report.cancelled_receipt_report");
   Route::post('/receipt_report', [ReportsController::class, 'ReceiptReport'])->name("report.receipt_report");
   Route::get('/show_pooja_dates/{id}', [PoojaDatesController::class, 'showPoojaDates'])->name("pooja_dates.showPoojaDates");
   Route::get('/all_gurujis', [GurujisController::class, 'allGurujis'])->name("gurujis.all");
   Route::post('/gotravali_summary_report', [ReportsController::class, 'gotravaliSummaryReport'])->name("report.gotravali_summary_report");
   Route::get('/saree_date', [ReceiptsController::class, 'SareeDate'])->name("receipts.saree_date");
   Route::get('/uparane_date', [ReceiptsController::class, 'UparaneDate'])->name("receipts.uparane_date");
   Route::post('/gotravali_report', [ReportsController::class, 'gotravaliReport'])->name("report.gotravali_report");
   Route::get('/saree_date_evening', [ReceiptsController::class, 'SareeDateEvening'])->name("receipts.saree_date_evening");
   Route::get('/uparane_date_evening', [ReceiptsController::class, 'UparaneDateEvening'])->name("receipts.uparane_date_evening");
   Route::get('/dashboards', [DashboardController::class, 'index'])->name("dashboards.index");

});