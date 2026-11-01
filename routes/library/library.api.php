<?php


use App\Http\Controllers\Grader\Library\CirculationDashBoardController;
use App\Http\Controllers\Grader\Library\BookController;
use App\Http\Controllers\Grader\Library\LibraryRoomsController;


 Route::post('search-by-filter', [CirculationDashBoardController::class, 'searchByFilter']);
 Route::get('user-issued-book-history', [CirculationDashBoardController::class, 'userIssuedBookHistory']);
 Route::post('post-issue-books', [BookController::class, 'issueBook2']);
 Route::post('update-library-status', [CirculationDashBoardController::class, 'updateStatus']);
 Route::get('get-user-personal-info', [CirculationDashBoardController::class, 'getUserPersonalInfo']);
 Route::post('delete-newarrival-rows/{id?}', [BookController::class, 'deleterows']);
 Route::get('search-user-for-suggestion', [CirculationDashBoardController::class, 'userSearchSuggestion']);
 Route::get('search-user-for-suggestion_library', [CirculationDashBoardController::class, 'userSearchSuggestionLibrary']);
 Route::get('search-campusID-for-suggestion', [CirculationDashBoardController::class, 'campusSearchSuggestion']);
 Route::get('search-eportal', [CirculationDashBoardController::class, 'eportalSearch']);
 Route::get('/catalog',[App\Http\Controllers\Grader\Library\BookController::class, 'getLibraryBooks']);
 Route::get('/get-book/{id?}',[App\Http\Controllers\Grader\Library\BookController::class, 'getBook']);
 
 Route::post('/update-room-allocation' ,[LibraryRoomsController::class, 'updateStatus']);
 Route::post('/room-allocation-list' ,[LibraryRoomsController::class, 'getRoomAllocationRequests']);
 Route::post('/allocation-details' ,[LibraryRoomsController::class, 'show']);
 Route::post('/room-availability' ,[LibraryRoomsController::class, 'getRoomAvailability']);
