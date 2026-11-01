<?php

namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Grader\Departments;

class DepartmentController extends Controller
{
    private $sortBy = 'id';
    private $sortDirection = 'ASC';

    private $sessions;

    public function __construct(Departments $departments)
    {
        $this->department = $departments;
    }

    public function index()
    {
        $departments = $this->department->fetch();
        return view('grader.common.schools', compact('departments'));
    }

    public function retrieve($id=0)
    {
        if($id)
        {
            return $this->department->fetch($id);
        }
        else
        {
            return $this->department->fetch();
        }
    }
}
