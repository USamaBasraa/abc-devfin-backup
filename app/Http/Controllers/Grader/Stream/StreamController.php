<?php
namespace App\Http\Controllers\Grader\Stream;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\sessions;
use App\Models\User;

use App\Models\Grader\AcademicSessions;
use DB;


class StreamController extends Controller{

   

    public function index(){
        $sessions = $this->session->fetch();
        return view('grader.common.sessions', compact('sessions'));
    }

    function courseStreamView(){
        // 19045
        return view('grader.stream.course_stream');
    }

    
}