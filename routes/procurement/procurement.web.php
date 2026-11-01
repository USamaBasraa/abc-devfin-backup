<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreProcurement\VendorController;
use App\Http\Controllers\StoreProcurement\VendorCategoryController;
use App\Http\Controllers\StoreProcurement\PurchaseOrderController;
use App\Http\Controllers\StoreProcurement\QuotationRequestController;
use App\Http\Controllers\StoreProcurement\VendorQuotationController;
use App\Http\Controllers\StoreProcurement\HeadApprovalController;
use App\Http\Controllers\StoreProcurement\ParentDeptApprovController;
use App\Http\Controllers\StoreProcurement\PurchaseOrderApprovalController;
use App\Http\Controllers\StoreProcurement\BudgetController;
use App\Http\Controllers\StoreProcurement\ReappropriationController;
use App\Http\Controllers\StoreProcurement\GatePassController;
use App\Http\Controllers\StoreProcurement\QuotationController;
use App\Http\Controllers\StoreProcurement\AssetController;
use App\Http\Controllers\StoreProcurement\AssetInspectionController;
use App\Http\Controllers\StoreProcurement\AssetAllocationController;
use App\Http\Controllers\StoreProcurement\SpApprovalMasterController;
use App\Http\Controllers\StoreProcurement\SpPermissionController;
use App\Http\Controllers\StoreProcurement\ComparativeReportController;
use App\Http\Controllers\StoreProcurement\BudgetItemDetailController;
use App\Http\Controllers\StoreProcurement\ItemController;
use App\Http\Controllers\StoreProcurement\BudgetImportController;
use App\Http\Controllers\StoreProcurement\CostCenterMappingController;
use App\Http\Controllers\StoreProcurement\StoreItemsBudgetController;
use App\Http\Controllers\StoreProcurement\StoreItemsQuotationController;
use App\Http\Controllers\StoreProcurement\StoreItemsQuotationProcessController;
use App\Http\Controllers\StoreProcurement\StoreItemHandoverController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


//-------------------- Store Procurement ------------------------//

//  Route::get('/vendor-profiling', [VendorController::class, 'index'])->name('vendor-profiling.index');
// Route::post('/assets/items/{id}/inspection', [AssetInspectionController::class, 'updateInspection'])
//  ->name('assets.inspection.update');


// Store Procurement Routes with Procurement Middleware
// Route::middleware(['procurement'])->group(function () {

//     Route::prefix('store-procurement')->group(function () {
//         Route::get('/permissions', [SpPermissionController::class, 'index'])->name('permissions.index');
//         Route::get('/permissions/list', [SpPermissionController::class, 'list'])->name('permissions.list');
//         Route::get('/permissions/create', [SpPermissionController::class, 'create'])->name('permissions.create');
//         Route::post('/permissions', [SpPermissionController::class, 'store'])->name('permissions.store');
//         Route::post('/permissions/{id}', [SpPermissionController::class, 'update'])->name('permissions.update');
//         Route::delete('/permissions/{id}', [SpPermissionController::class, 'destroy'])->name('permissions.destroy');
//     });

//   });


Route::middleware(['auth'])->group(function () {






//working pinnicale Api 
Route::get('/budget-import', [BudgetImportController::class, 'index'])->name('budget.index');
Route::post('/budget/import', [BudgetImportController::class, 'import'])->name('budget.import');
Route::get('/budget/importItem', [BudgetImportController::class, 'importItems'])->name('budget.importItem');
Route::get('/budget/importVendors', [BudgetImportController::class, 'importVendors'])->name('budget.importItem');
Route::get('/budget/importCostCenter', [BudgetImportController::class, 'importCostCenter'])->name('budget.importCostCenter');
//end working pinnicale Api 

        Route::get('/reappropriation/approvals', [ReappropriationController::class, 'index'])
            ->name('reappropriation.approvals');

        Route::post('/reappropriation/approve', [ReappropriationController::class, 'approve'])
            ->name('reappropriation.approve');
        Route::post('/reappropriation/decline', [ReappropriationController::class, 'decline'])->name('reappropriation.decline');


    // Budget routes
    Route::controller(BudgetController::class)->group(function () {
        Route::post('/budget/store', 'store')->name('budget.store');
        Route::post('/budget/filter', 'filter')->name('budgets.filter');
        Route::get('/budget/{id}/edit', 'edit')->name('budget.edit');
        // Route::put('/budget/{id}', 'update')->name('budget.update');
        // Route::delete('/budget/{id}', 'destroy')->name('budget.destroy');
    });

   Route::post('/edit-bh-item/{id}', [BudgetController::class, 'update'])->name('edit-bh-item.edit');
   Route::get('/delete-bh-item/{id}', [BudgetController::class, 'destroy'])->name('delete-bh-item.destroy');

    Route::get('/assets/non-allocated', [AssetAllocationController::class, 'index'])->name('assets.non_allocated');
    Route::post('/assets/allocate', [AssetAllocationController::class, 'allocate'])->name('assets.allocate');
    Route::post('/assets/reallocate', [AssetAllocationController::class, 'reallocate'])->name('assets.reallocate');
     Route::post('/assets/deallocate', [AssetAllocationController::class, 'deallocate'])->name('assets.deallocate');



    Route::post('/assets/items/inspection-bulk', [AssetInspectionController::class, 'updateInspectionBulk'])->name('assets.inspection.bulk');
    Route::post('/assets/items/{id}/inspection', [AssetInspectionController::class, 'updateInspection'])->name('assets.inspection.update');
    Route::get('/sp_assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/employees/search', [AssetController::class, 'searchEmployees'])->name('assets.employees.search');
    Route::get('/assets/{id}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('gatepass', [GatePassController::class, 'index'])->name('gatepass.index');
    Route::post('update-serial', [GatePassController::class, 'UpdateSerial'])->name('update-serial');
    // new route for listing already created gatepasses
    Route::get('gatepass/list', [GatePassController::class, 'list'])->name('gatepass.list');
    //    Route::get('gatepass/show', [GatePassController::class, 'list'])->name('gatepass.list');

    Route::get('/vendor-show/{id}', [VendorController::class, 'show']);
    Route::post('/vendor/delete/{id}', [VendorController::class, 'delete'])->name('vendor.delete');
    Route::get('/vendor-profiling', [VendorController::class, 'index'])->name('vendor-profiling.index');
    Route::post('/vendor-profiling/store', [VendorController::class, 'store'])->name('vendor-profiling.store');

    Route::get('/vendor-create', [VendorController::class, 'create'])->name('vendor.create');
    Route::get('get-po-details/{po_id}', [GatePassController::class, 'getPODetails'])->name('gatepass.po.details');
    Route::get('/gatepass/{gp_id}', [GatePassController::class, 'show'])->name('gatepass.show');

    Route::get('quotation-requests', [HeadApprovalController::class, 'index'])->name('quotation.index');
    Route::get('parent-dept-review', [ParentDeptApprovController::class, 'index'])->name('parent-dept-review.index');
    Route::get('item-recieved-acknowledgment', [ParentDeptApprovController::class, 'itemRecieved'])->name('item-recieved-acknowledgment');
    Route::post('resource-acknow', [ParentDeptApprovController::class, 'resourceAcknow'])->name('resource-acknow');
    Route::post('/quotation-requests/remarks/{id}', [ParentDeptApprovController::class, 'saveRemarks'])
    ->name('quotation.remarks');
    Route::get('/quotation-request-create', [QuotationRequestController::class, 'create'])->name('quotation.request.create');
    Route::get('/quotation-request-purchase', [QuotationRequestController::class, 'purchase'])->name('quotation.request.purchase');
    Route::get('/quotation/{id}/vendor-quotations', [QuotationRequestController::class, 'viewVendorQuotations'])
    ->name('quotation.related.view');
    Route::get('/store-items/quotation-request-create', [StoreItemsQuotationProcessController::class, 'create'])
    ->name('store-items.quotation.request.create');
    Route::get('/store-items/quotation/{id}/vendor-quotations', [StoreItemsQuotationProcessController::class, 'viewVendorQuotations'])
    ->name('store-items.quotation.related.view');


    Route::get('/select_quotation/{id}/vendor-quotations', [QuotationRequestController::class, 'viewQuotationsSelect'])
    ->name('select.quotation.related.view');
 

    Route::get('/vendor-category-create', [VendorCategoryController::class, 'index'])->name('vendor-category.create');

    // DataTable endpoint - MUST be before other POST routes
    Route::post('/vendor-category/datatable', [VendorCategoryController::class, 'getCategoryData'])->name('vendor-category.data');

    Route::post('/vendor-category/store', [VendorCategoryController::class, 'store'])->name('vendor-category.store');
    Route::get('/vendor-category/{id}/edit', [VendorCategoryController::class, 'edit'])->name('vendor-category.edit');
    Route::post('/vendor-category/{id}/update', [VendorCategoryController::class, 'update'])->name('vendor-category.update');
    Route::post('/vendor-category/{id}/quick-update', [VendorCategoryController::class, 'quickUpdate'])->name('vendor-category.quick-update');
    Route::post('/vendor-category/{id}/delete', [VendorCategoryController::class, 'destroy'])->name('vendor-category.destroy');



    Route::get('/quotation-requests/{id}/comparative-report', [ComparativeReportController::class, 'show'])
    ->name('quotation.comparative-report');

    


    Route::get('/quotation-requests/{id}/rfq-pdf', [VendorQuotationController::class, 'downloadRfqPdf'])
    ->name('quotation-request.rfq-pdf');


    Route::get('/purchase-order-create', [PurchaseOrderController::class, 'create'])->name('purchase-order.create');
    Route::get('/purchase-orders/{id}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::get('/purchase-orders/{id}/new-pdf', [PurchaseOrderController::class, 'newPdf'])->name('purchase-orders.new-pdf');

    

//   Route::get('/purchase-orders-api', [PurchaseOrderController::class, 'poList'])->name('purchase-orders.poList');
   


    Route::get('/vendor/quotations', [VendorQuotationController::class, 'vendorQuotations'])->name('vendor.quotations');
    Route::get('/sp/quotations', [VendorQuotationController::class, 'spQuotations'])->name('sp.quotations');
    Route::get('/sp/po-list', [VendorQuotationController::class, 'po_list'])->name('sp.po_list');
    Route::post('/sp/quotations/chat', [VendorQuotationController::class, 'storeChat'])->name('sp.quotations.chat.store');
    Route::post('/sp/quotations/chat/read', [VendorQuotationController::class, 'markChatRead'])->name('sp.quotations.chat.read');


    Route::get('/quotation/view/{id}', [QuotationController::class, 'viewQuotations'])
    ->name('quotation.viewQuotations');

    // Route::get('purchase-orders/approval', [PurchaseOrderApprovalController::class, 'index'])->name('purchase-order.approval.index');
    Route::get('purchase-order-approval', [PurchaseOrderApprovalController::class, 'index'])
    ->name('purchase-order.approval.index');

    Route::prefix('budget')->group(function () {
    Route::get('/', [BudgetController::class, 'index'])->name('budget.index'); // show all
    // Route::get('/create', [BudgetController::class, 'create'])->name('budget.create'); // form
    // Route::post('/store', [BudgetController::class, 'store'])->name('budget.store'); // save
       });

  // Edit quotation request - Create revision
  Route::post('/budgets/{bh_id}/edit-quotation/{qr_id}', [BudgetController::class, 'editQuotation'])
  ->name('budget.editQuotation');


  Route::get('/budgets/quotation-history/{qr_id}', [BudgetController::class, 'getQuotationHistory'])
  ->name('budget.quotationHistory');


  Route::post('/budgets/reappropriate/modal', [BudgetController::class, 'loadReappropriateModal'])->name('budgets.reappropriate.modal');



    Route::get('/reappropriation-history/{itemId}', [BudgetController::class, 'getReappropriationHistory']);

    // routes/web.php
    Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index');   // page + ajax refresh
    Route::post('/budget', [BudgetController::class, 'store'])->name('budget.store'); // ajax submit
    Route::post('/budget/sync', [BudgetController::class, 'syncFromApi'])->name('budget.sync');
        Route::get('/store-items-budget', [StoreItemsBudgetController::class, 'index'])->name('store-items-budget.index');
    Route::post('/store-items-budget', [StoreItemsBudgetController::class, 'store'])->name('store-items-budget.store');
    Route::get('/store-items-budget/items/search', [StoreItemsBudgetController::class, 'search'])->name('store-items-budget.items.search');
    Route::post('/store-items-budget/{id}/request-quotation', [StoreItemsBudgetController::class, 'requestQuotation'])->name('store-items-budget.requestQuotation');
    Route::get('/store-items/quotations', [StoreItemsQuotationController::class, 'spQuotations'])->name('store-items.quotations');// Show list of budgets
    Route::get('/store-items/quotation-requests/{id}/rfq-pdf', [StoreItemsQuotationController::class, 'downloadRfqPdf'])
        ->name('store-items.rfq-pdf');
    Route::get('/store-items/inventory', [StoreItemHandoverController::class, 'index'])->name('store-items.inventory');
    Route::get('/store-items/requests', [StoreItemHandoverController::class, 'requestsList'])->name('store-items.requests');
    Route::get('/store-items/issued', [StoreItemHandoverController::class, 'issuedList'])->name('store-items.issued');
    Route::post('/store-items/inventory/{storeItemId}/warning-qty', [StoreItemHandoverController::class, 'updateWarningQty'])->name('store-items.warning-qty.update');
    Route::get('/store-items/request/{storeItemId?}', [StoreItemHandoverController::class, 'requestForm'])->name('store-items.request.form');
    Route::post('/store-items/request', [StoreItemHandoverController::class, 'storeRequest'])->name('store-items.request.store');
    Route::post('/store-items/requests/{id}/issue', [StoreItemHandoverController::class, 'issueRequest'])->name('store-items.request.issue');
    Route::post('/store-items/requests/{id}/reject', [StoreItemHandoverController::class, 'rejectRequest'])->name('store-items.request.reject');
    Route::post('/store-items/requests/{id}/acknowledge', [StoreItemHandoverController::class, 'acknowledgeRequest'])->name('store-items.request.acknowledge');
    Route::get('/store-items/gate-pass-list', [GatePassController::class, 'storeItemsGatePassList'])->name('store-items.gate-pass.list');
    Route::get('/budgets/list', [BudgetController::class, 'list'])
        ->name('budget.list')
        ->middleware('autologin.budgets')
        ->withoutMiddleware([\App\Http\Middleware\Authenticate::class]);
    Route::get('/budgets/list', [BudgetController::class, 'list'])
    ->name('budget.list')
    ->withoutMiddleware([\App\Http\Middleware\Authenticate::class]);


    Route::post('/reappropriation/send-approval', [BudgetController::class, 'sendForApproval'])
    ->name('reappropriation.sendForApproval');

    // Request quotation against a budget head
    Route::post('/budgets/{id}/request-quotation', [BudgetController::class, 'requestQuotation'])
        ->name('budget.requestQuotation');
    Route::post('/budgets/filter', [BudgetController::class, 'filterByAcademicYear'])
            ->name('budgets.filter');

    Route::post('/budgets/reappropriate/cross', [BudgetController::class, 'reappropriateAcross']);

    // DataTables endpoints for budget items
    Route::post('/budget/items/data', [BudgetController::class, 'getBudgetItemsData'])->name('budget.items.data');
    Route::post('/budget/items/total', [BudgetController::class, 'getBudgetItemsTotal'])->name('budget.items.total');
    Route::post('/budget/items/grouped', [BudgetController::class, 'getBudgetItemsGrouped'])->name('budget.items.grouped');

    // Get items by budget head (for reappropriation modal)
    Route::get('/get-items-by-budget-head/{bhId}', [BudgetController::class, 'getItemsByBudgetHead'])->name('budget.items.by_head');
    Route::get('/get-items-by-budget-head-title', [BudgetController::class, 'getItemsByBudgetHeadTitle'])->name('budget.items.by_head_title');
    Route::get('/get-items-by-category/{categoryId}', [BudgetController::class, 'getItemsByCategory'])->name('budget.items.by_category');

    // Budget Cards Data
    Route::post('/budget/cards/data', [BudgetController::class, 'getBudgetCardsData'])->name('budget.cards.data');

    // Get item status - vendor emails and quotations
    Route::get('/budget/item-status', [VendorQuotationController::class, 'getItemStatus'])->name('budget.item.status');

    Route::get('/recommended-quotations', [PurchaseOrderController::class, 'recommendedQuotations'])
    ->name('recommended.quotations');

    Route::POST('/quotations/approve', [PurchaseOrderController::class, 'approvePo'])
        ->name('quotation.approve');

    Route::POST('/quotations/reject', [PurchaseOrderController::class, 'rejectPo'])
        ->name('quotation.reject');

        Route::get('/quotation/summary/data', [QuotationRequestController::class, 'getSummaryData'])
        ->name('quotation.summary.data');




        Route::get('/quotation/summary/vendors', [QuotationRequestController::class, 'getSummaryVendors'])
        ->name('quotation.summary.vendors');

    Route::post('/quotation/approval/update-remarks', [QuotationRequestController::class, 'updateApprovalRemarks'])
        ->name('quotation.approval.update-remarks');
    Route::get('/budget-item-details', [BudgetItemDetailController::class, 'index'])->name('budget-item-details.index');
    Route::get('/budget-item-details/show', [BudgetItemDetailController::class, 'show'])->name('budget-item-details.show');
    Route::get('/approval-master/create', [SpApprovalMasterController::class, 'create'])->name('approval-master.create');
    Route::post('/approval-master/store', [SpApprovalMasterController::class, 'store'])->name('approval-master.store');
    Route::get('/sp-approval-master/list', [SpApprovalMasterController::class, 'list'])->name('sp-approval-master.list');
    Route::post('/approval-master/delete', [SpApprovalMasterController::class, 'delete'])->name('approval-master.delete');



    // Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items/store', [ItemController::class, 'store'])->name('items.store');

    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::post('/items/data', [ItemController::class, 'data'])->name('items.data');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sp_items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');

    Route::get('/cost-center-mapping', [CostCenterMappingController::class, 'index'])->name('cost-center-mapping.index');
    Route::get('/cost-center-mapping/data', [CostCenterMappingController::class, 'data'])->name('cost-center-mapping.data');
    Route::post('/cost-center-mapping/store', [CostCenterMappingController::class, 'store'])->name('cost-center-mapping.store');


    Route::get('/sp_assets/create', [AssetController::class, 'create']);
    Route::get('/sp_assets/manual-list', [AssetController::class, 'manualList'])->name('assets.manual.list');
    Route::post('/assets/store', [AssetController::class, 'store'])->name('assets.store');






    });
// });
