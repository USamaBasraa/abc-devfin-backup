<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Grader\Alumni\AlumniController;





Route::get('alumni',[AlumniController::class,'index']) ;



?>
