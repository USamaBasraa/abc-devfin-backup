<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Grader\Alumni\AlumniController;




Route::post('alumni/get_programs',[AlumniController::class,'getPrograms']) ;
Route::post('alumni/get_alumnis',[AlumniController::class,'getAlumnis']) ;

Route::post('alumni/get_dept',[AlumniController::class,'getSelectDepartments']) ;



?>
