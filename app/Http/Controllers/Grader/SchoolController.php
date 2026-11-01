<?php

namespace App\Http\Controllers\Grader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grader\Schools;


class SchoolController extends Controller
{
    private $sortBy = 'id';
    private $sortDirection = 'ASC';

    private $school;

    public function __construct(Schools $schools)
    {
        $this->school = $schools;
    }

    public function index()
    {
        $schools = $this->school->fetch();
        return view('grader.common.schools', compact('schools'));
    }

    public function retrieve()
    {
        return $schools = $this->school->fetch();
    }
}
