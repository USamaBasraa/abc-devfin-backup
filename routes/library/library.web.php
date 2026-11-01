<?php
use App\Http\Controllers\Grader\Library\BookController;  
use App\Http\Controllers\Grader\Library\BookCirculationController;  
use App\Http\Controllers\Grader\Library\LibraryRoomsController;



Route::get('/add-book-item',[App\Http\Controllers\Grader\Library\BookController::class, 'addBookItem'])->name('add-book-item');

// Route::get('/ahmed', function () {
//    echo "Ahmed";
// });
Route::get('/cataloging-reports-excel',[App\Http\Controllers\Grader\Library\BookController::class, 'accessionRegister_excel']);
Route::get('/librarycirculation',[App\Http\Controllers\Grader\Library\BookController::class, 'allocation_new']);
Route::get('/pro_add_book',[App\Http\Controllers\Grader\Library\BookController::class, 'pro_add_book']);
Route::get('/add-book',[App\Http\Controllers\Grader\Library\BookController::class, 'addBook']);
Route::get('/view-book-item',[App\Http\Controllers\Grader\Library\BookController::class, 'viewBookItem']);
Route::get('/spine-barcode',[App\Http\Controllers\Grader\Library\BookController::class, 'spineBarcode']);
Route::get('/daily-library-visitor',[App\Http\Controllers\Grader\Library\BookController::class, 'dailyLibraryVisitor']);
Route::post('/store_daily_library_vistitor',[App\Http\Controllers\Grader\Library\BookController::class, 'storeDailyLibraryVistitor']);
Route::get('/download',[App\Http\Controllers\Grader\Library\BookController::class, 'download']);
Route::post('/insert-library-items',[App\Http\Controllers\Grader\Library\BookController::class, 'InsertBookItem']);
Route::post('/update-library-items/{id?}',[App\Http\Controllers\Grader\Library\BookController::class, 'UpdateBookItem']);
Route::get('/insert-book-status/{bookstatus?}/{id?}',[App\Http\Controllers\Grader\Library\BookController::class, 'UpdateBookStatus']);
Route::get('/search-by-title/{keyword?}/{title?}/{author?}/{check_status?}' ,  [App\Http\Controllers\Grader\Library\BookController::class, 'viewBookSearchItem']);
Route::post('/insert-library-items-catlog',[App\Http\Controllers\Grader\Library\BookController::class, 'viewBookSearchItemCatlog'])->name('insert-library-items-catlog');
Route::post('/search-from-allocation/{accession?}/{username?}',[App\Http\Controllers\Grader\Library\BookController::class, 'SearchFromAllocation'])->name('search-from-allocation');
Route::get('/catalog',[App\Http\Controllers\Grader\Library\BookController::class, 'accessionRegister']);
Route::get('/circulation',[App\Http\Controllers\Grader\Library\BookController::class, 'libraryCirculation']);
// Route::get('/book-allocation-status/{id?}/{access_no?}' ,  [App\Http\Controllers\Grader\Library\BookController::class, 'libraryAllocation']);
Route::get('/book-allocation-status' ,[App\Http\Controllers\Grader\Library\BookController::class, 'libraryAllocation']);
Route::get('/report_cataloging',[App\Http\Controllers\Grader\Library\BookController::class, 'report_cataloging']);
Route::get('/book-allocation-status2' ,[App\Http\Controllers\Grader\Library\BookController::class, 'libraryAllocation2']);
Route::get('/get-allocation/{roll?}/{library_book_id?}',[App\Http\Controllers\Grader\Library\BookController::class, 'getAllocation']);
Route::get('/allocation-load-details/{roll_no?}/{id?}/{access_no?}',[App\Http\Controllers\Grader\Library\BookController::class, 'loadDetails']);
Route::get('/allocation-load-details2/{roll_no?}/{id?}/{access_no?}',[App\Http\Controllers\Grader\Library\BookController::class, 'loadDetails2']);
Route::post('/issue-book/{issue_date?}/{access_no?}/{book_id?}',[App\Http\Controllers\Grader\Library\BookController::class, 'issueBook'])->name('issue-book');
Route::get('/library-criteria' ,[App\Http\Controllers\Grader\Library\BookController::class, 'librarycriteria']); 
Route::post('/insert-criteria' ,[App\Http\Controllers\Grader\Library\BookController::class, 'insertCriteria'])->name('insert-criteria'); 
Route::post('/update-criteria/{id?}' ,[App\Http\Controllers\Grader\Library\BookController::class, 'updateCriteria'])->name('update-criteria/{id?}'); 
Route::get('/show-library-criteria' ,[App\Http\Controllers\Grader\Library\BookController::class, 'showlibrarycriteria']); 
Route::get('/get-library-criteria/{id?}' ,[App\Http\Controllers\Grader\Library\BookController::class, 'getlibrarycriteria']); 
Route::post('/insert-accession-no/{id?}', [BookController::class, 'updateAccessionNo']);



// //////zain to do work

Route::get('/return-book' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'view_book']); 
Route::get('/circulation-book-history' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'circulation_book_history']); 
Route::get('/overdue-book' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'overdue_book']); 
Route::get('/update-library-status/{status?}/{id?}' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'updateStatus'])->name('update-library-status');
Route::get('/author_books_details' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'viewauthordetail'])->name('author_books_details');

Route::get('/library_fine_slip/{id?}' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'library_fine_slip']); 
Route::post('/user-return-book' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'return_book']); 
Route::post('/user-fine-update' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'fineBookUpdate']); 
Route::get('/get-user/{id?}' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'getResult']); 
Route::get('/get-user-id/{id?}' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'getResultuser']); 
Route::get('/inbox' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'inboxlog'])->name('inbox'); 
Route::get('/datacorrection' ,[App\Http\Controllers\Grader\Library\BookController::class, 'datacorrection']); 
Route::get('/insert-bookcorrection-status/{bookstatus?}/{id?}' ,[App\Http\Controllers\Grader\Library\BookController::class, 'BookCorrectionStatus']); 
Route::post('/user-reminder' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'SendMail']); 


// // Ahmed Work

Route::get('/fine/print/pdf' ,[App\Http\Controllers\Grader\Library\BookCirculationController::class, 'printFinePDF']); 
Route::get('/get-user-detail-pdf' ,[App\Http\Controllers\Grader\Library\CirculationDashBoardController::class, 'getUserDetailPDF']); 

Route::get('/room-allocation' ,[LibraryRoomsController::class, 'index']);