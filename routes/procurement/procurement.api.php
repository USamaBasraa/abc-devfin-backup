<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StoreProcurement\VendorController;
use App\Http\Controllers\StoreProcurement\VendorCategoryController;
use App\Http\Controllers\StoreProcurement\PurchaseOrderController;
use App\Http\Controllers\StoreProcurement\QuotationRequestController;
use App\Http\Controllers\StoreProcurement\VendorQuotationController;
use App\Http\Controllers\StoreProcurement\HeadApprovalController;
use App\Http\Controllers\StoreProcurement\RecommendationController;
use App\Http\Controllers\StoreProcurement\PurchaseOrderApprovalController;
use App\Http\Controllers\StoreProcurement\BudgetController;
use App\Http\Controllers\StoreProcurement\GatePassController;
use App\Http\Controllers\StoreProcurement\AssetInspectionController;
use App\Http\Controllers\StoreProcurement\PinnacleController;
use App\Http\Controllers\StoreProcurement\CostCenterMappingController;

/*
|--------------------------------------------------------------------------
| Procurement API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for procurement module.
|
*/

// Add procurement API routes here when needed

           //-------------------- Store Procurement ------------------------//







  Route::controller(BudgetController::class)->group(function () {
        // Route::post('/budget/store', 'store')->name('budget.store');
        // Route::post('/budget/filter', 'filter')->name('budgets.filter');
        // Route::get('/budget/{id}/edit', 'edit')->name('budget.edit');
        // Route::put('/budget/{id}/update', 'update')->name('budget.update');
        // Route::get('/delete-bh-item/{id}', 'destroy')->name('delete-bh-item.destroy');
    });
Route::put('/budget-item/{id}', [BudgetController::class, 'update'])->name('budget-item.update');



  Route::prefix('gatepass')->group(function () {
                // starting page
                Route::get('/create/{po_id}', [GatePassController::class, 'create'])->name('gatepass.create');
                Route::post('/store', [GatePassController::class, 'store'])->name('gatepass.store');
                Route::get('/show/{gp_id}', [GatePassController::class, 'show'])->name('gatepass.show');

            });

        Route::post('/vendor/store', [VendorController::class, 'store'])->name('vendor.store');
        Route::post('/quotation/request/store', [QuotationRequestController::class, 'store'])->name('quotation.request.store');
        Route::post('/vendor-quotations', [VendorQuotationController::class, 'store'])->name('vendor.quotations.store');

        Route::prefix('recommendation')->name('recommendation.')->group(function () {
            Route::get('/{id}', [RecommendationController::class, 'show'])->name('show');
            Route::post('/store', [RecommendationController::class, 'store'])->name('store');
        });

        // Combined route for Recommendation + Purchase Order
        Route::post('/quotation/approve-and-order', [QuotationRequestController::class, 'storeRecommendationAndPO'])->name('quotation.approve-and-order');


        Route::post('/quotation/request-vc-approval', [QuotationRequestController::class, 'requestVcApproval'])->name('quotation.request-vc-approval');




        // Get PO Approvals Details
        Route::get('/quotation/po-approvals', [QuotationRequestController::class, 'getPoApprovals'])->name('quotation.po-approvals');

        // Generate PO route
        Route::post('/quotation/generate-po', [QuotationRequestController::class, 'generatePo'])->name('quotation.generate-po');

        // Route::prefix('head')->name('head.')->group(function () {

        Route::post('head-approve', [HeadApprovalController::class, 'approve'])->name('head.quotation.approve');
        Route::post('head-reject', [HeadApprovalController::class, 'reject'])->name('head.quotation.reject');
        // });
        Route::get('/vendor-category', [VendorCategoryController::class, 'index'])->name('vendor-category.index');
        Route::get('/vendor-category/list', [VendorCategoryController::class, 'list'])->name('vendor-category.list');
        Route::post('/vendor-category/store', [VendorCategoryController::class, 'store'])->name('vendor-category.store');
        Route::get('/vendor-category/{id}/edit', [VendorCategoryController::class, 'edit'])->name('vendor-category.edit');
        Route::put('/vendor-category/{id}', [VendorCategoryController::class, 'update'])->name('vendor-category.update');
        Route::post('/vendor-category/sync-pinnacle-chunk', [VendorCategoryController::class, 'syncPinnacleChunk'])->name('vendor-category.sync-pinnacle-chunk');


        Route::post('/purchase-order/proceed', [PurchaseOrderController::class, 'proceedWithOrder'])
        ->name('purchase-order.proceed');


        Route::get('/purchase-orders-list', [PurchaseOrderController::class, 'poList'])->name('purchase-orders.poList');
   


        Route::get('/vendor/list', [VendorController::class, 'list'])->name('vendor.list');
        Route::get('/vendor/master-categories', [VendorController::class, 'masterCategories'])->name('vendor.master-categories');
        Route::post('/vendor/master-categories', [VendorController::class, 'storeMasterCategory'])->name('vendor.master-categories.store');
        Route::put('/vendor/master-categories/{id}', [VendorController::class, 'updateMasterCategory'])->name('vendor.master-categories.update');
        Route::post('/vendor/sync-pinnacle-chunk', [VendorController::class, 'syncPinnacleChunk'])
            ->name('vendor.sync-pinnacle-chunk');
        Route::get('/store-procurement/pinnacle/{endpointKey}', [PinnacleController::class, 'fetch'])
            ->name('store-procurement.pinnacle.fetch');

        Route::prefix('cost-center-mapping')->name('cost-center-mapping.')->group(function () {
            Route::get('/data', [CostCenterMappingController::class, 'data'])->name('data');
            Route::post('/store', [CostCenterMappingController::class, 'store'])->name('store');
        });
        Route::get('/quotation-requests/data', [VendorQuotationController::class, 'getLazyLoad'])->name('quotation.requests.data');
        Route::get('/quotation-requests/{id}/details', [VendorQuotationController::class, 'getDetails'])->name('quotation.requests.details');
        Route::get('/quotation-requests/search-items', [VendorQuotationController::class, 'searchBudgetItems'])->name('quotation.search-items');
        Route::get('/search-po', [VendorQuotationController::class, 'searchPo'])->name('search-po');
        Route::get('/get-po-list', [VendorQuotationController::class, 'get_po_list'])->name('get_po_list');

        Route::get('/vendor-quotations/check', [VendorQuotationController::class, 'check'])->name('vendor.quotations.check');
        Route::post('/vendor-quotations/send-email', [VendorQuotationController::class, 'sendMailToVendors'])->name('vendor.quotations.send-email');
        Route::get('/vendor-quotations/email-history', [VendorQuotationController::class, 'getEmailHistory'])->name('vendor.quotations.email-history');
        Route::get('/vendor-quotations/submitted', [VendorQuotationController::class, 'getSubmittedQuotations'])->name('vendor.quotations.submitted');

        Route::get('/quotation/recommendations/details', [QuotationRequestController::class, 'getRecommendationsDetails'])->name('quotation.recommendations.details');


        Route::post('purchase-orders/approve', [PurchaseOrderApprovalController::class, 'approve'])->name('purchase-order.approve');
        Route::post('purchase-orders/reject', [PurchaseOrderApprovalController::class, 'reject'])->name('purchase-order.reject');


        // routes/web.php
         Route::get('/sp-items/search', [BudgetController::class, 'search'])
          ->name('sp.items.search');

        //  Route::prefix('budget')->group(function () {

        //     Route::get('/create', [BudgetController::class, 'create'])->name('budget.create'); // form
        //     Route::post('/store', [BudgetController::class, 'store'])->name('budget.store'); // save
        // });
        Route::post('/budgets/filter', [BudgetController::class, 'filterByAcademicYear'])
            ->name('budgets.filter');






        Route::post('/purchase-order/store', [PurchaseOrderController::class, 'store'])->name('purchase-order.store');

Route::prefix('api')->group(function () {
    Route::get('/get-mentors', [PeerMentorshipController::class, 'getMentors']);
    Route::get('/get-mentor-details', [PeerMentorshipController::class, 'getMentorDetails']);


});


// Route::get('/vendor/list', [VendorController::class, 'list'])->name('vendor.list');
// Route::post('/vendor/store', [VendorController::class, 'store'])->name('vendor.store');

    Route::put('/vendor/update', [VendorController::class, 'update'])->name('vendor.update');
    // Route::delete('/vendor/delete/{id}', [VendorController::class, 'delete']);
    // Route::post('/vendor/delete/{id}', [VendorController::class, 'delete'])->name('vendor.delete');



   //-------------------- Store Procurement END------------------------//
