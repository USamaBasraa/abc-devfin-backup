<?php
/**
 * Created by PhpStorm.
 * User: bilal.saleem
 * Date: 24/08/2018
 * Time: 4:03 PM
 */

namespace App\Http\Controllers\Grader;


use App\Models\SchoolAssessments;
use App\Models\Assessments;
use App\Models\Schools;
use App\Models\Departments;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSchoolAssessment;

class SchoolCriteriaController extends Controller
{
       public function __construct(SchoolAssessments $schoolassessment)
   {
       $this->schoolassessment = $schoolassessment;
   }
    public function retrieveSchoolCriterias()
    {

        //$sessions = \App::call('App\Http\Controllers\AcademicSessionController@retrieve');
        $assessments = new Assessments();
        $schools     = new Schools();
        $departments = new Departments();

        $schoolassessment =  $this->schoolassessment->fetchSchoolAssessment();

       // return $schoolassessment;exit();
        return view('grader.common.schoolcriteria', ['schools'=>$schools->fetchSchools(),'schoolassessment'=>$this->schoolassessment->fetchSchoolAssessment(),'assessments'=>$assessments->fetchAssessmentss(),'departments'=>$departments->fetchDepartments()]);
    }
    public function retrievemaxOrder()
    {
        return  $this->schoolassessment->maxOrder();
        // return view('common.assessment', compact('maxorder'));
    }
    public function create(StoreSchoolAssessment $request)
    {
        $data = $request->all();
        return $this->schoolassessment->insertSchoolAssessment( $data);
    }



}