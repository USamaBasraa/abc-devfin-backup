@extends('layouts.master')
@section('title')
    BNU CMS
@stop
@section('mainBody')

    <table>
        <tr>
            <td align="left" class="course-title">
                CS101 - Introduction to Computer
            </td>
        </tr>
    </table>

    <table class="course-table" cellspacing="0" cellpadding="2" border="0">
        <tbody>
        <tr>
            <td width="90px" valign="middle" align="center">
                <img title="Course Website" src="{{asset('images/btn_courses.png')}}" onclick="openModalCourseWeb()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Quizzes" src="{{asset('images/btn_quizzes_1.png')}}" onclick="openModalQuiz()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Assignments" src="{{asset('images/btn_assignments_1.png')}}" onclick="openModalAssignment()">
            </td>
            <td width="90px" valign="middle" align="center">
                <img title="Class Participation" src="{{asset('images/btn_F2F_1.png')}}" onclick="openModalClass()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Midterm Exams" src="{{asset('images/btn_GDB_1.png')}}" onclick="openModalMid()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Finalterm Exam" src="{{asset('images/btn_GDB_1.png')}}" onclick="openModalFinal()">
            </td>
        </tr>
        <tr style="height: 30px;">
            <td width="95px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Course Website</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Quizzes</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Assignments</span>
            </td>
            <td width="90px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Attendance</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Midterm</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Final Term</span>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <table>
        <tr>
            <td align="left" class="course-title">
                MGT201 - Financial Accounting
            </td>
        </tr>
    </table>

    <table class="course-table" cellspacing="0" cellpadding="2" border="0">
        <tbody>
        <tr>
            <td width="90px" valign="middle" align="center">
                <img title="Course Website" src="{{asset('images/btn_courses.png')}}" onclick="openModalCourseWeb()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Quizzes" src="{{asset('images/btn_quizzes_1.png')}}" onclick="openModalQuiz()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Assignments" src="{{asset('images/btn_assignments_1.png')}}" onclick="openModalAssignment()">
            </td>
            <td width="90px" valign="middle" align="center">
                <img title="Class Participation" src="{{asset('images/btn_F2F_1.png')}}" onclick="openModalClass()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Midterm Exams" src="{{asset('images/btn_GDB_1.png')}}" onclick="openModalMid()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Finalterm Exam" src="{{asset('images/btn_GDB_1.png')}}" onclick="openModalFinal()">
            </td>
        </tr>
        <tr style="height: 30px;">
            <td width="95px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Course Website</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Quizzes</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Assignments</span>
            </td>
            <td width="90px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Attendance</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Midterm</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Final Term</span>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <table>
        <tr>
            <td align="left" class="course-title">
                CS708 - Theory of Automata
            </td>
        </tr>
    </table>

    <table class="course-table" cellspacing="0" cellpadding="2" border="0">
        <tbody>
        <tr>
            <td width="90px" valign="middle" align="center">
                <img title="Course Website" src="{{asset('images/btn_courses.png')}}" onclick="openModalCourseWeb()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Quizzes" src="{{asset('images/btn_quizzes_1.png')}}" onclick="openModalQuiz()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Assignments" src="{{asset('images/btn_assignments_1.png')}}" onclick="openModalAssignment()">
            </td>
            <td width="90px" valign="middle" align="center">
                <img title="Class Participation" src="{{asset('images/btn_F2F_1.png')}}" onclick="openModalClass()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Midterm Exams" src="{{asset('images/btn_GDB_1.png')}}" onclick="openModalMid()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Finalterm Exam" src="{{asset('images/btn_GDB_1.png')}}" onclick="openModalFinal()">
            </td>
        </tr>
        <tr style="height: 30px;">
            <td width="95px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Course Website</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Quizzes</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Assignments</span>
            </td>
            <td width="90px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Attendance</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Midterm</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Final Term</span>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <table>
        <tr>
            <td align="left" class="course-title">
                CS707 - Network Security
            </td>
        </tr>
    </table>

    <table class="course-table" cellspacing="0" cellpadding="2" border="0">
        <tbody>
        <tr>
            <td width="90px" valign="middle" align="center">
                <img title="Course Website" src="{{asset('images/btn_courses.png')}}" onclick="openModalCourseWeb()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Quizzes" src="{{asset('images/btn_quizzes_1.png')}}" onclick="openModalQuiz()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Assignments" src="{{asset('images/btn_assignments_1.png')}}" onclick="openModalAssignment()">
            </td>
            <td width="90px" valign="middle" align="center">
                <img title="Class Participation" src="{{asset('images/btn_F2F_1.png')}}" onclick="openModalClass()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Midterm Exams" src="{{asset('images/btn_GDB_1.png')}}" onclick="openModalMid()">
            </td>
            <td width="85px" valign="middle" align="center">
                <img title="Finalterm Exam" src="{{asset('images/btn_GDB_1.png')}}" onclick="openModalFinal()">
            </td>
        </tr>
        <tr style="height: 30px;">
            <td width="95px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Course Website</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Quizzes</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Assignments</span>
            </td>
            <td width="90px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Attendance</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Midterm</span>
            </td>
            <td width="85px" valign="top" nowrap="nowrap" align="center">
                <span class="txtBlue">Final Term</span>
            </td>
        </tr>
        </tbody>
    </table>


    <div class="modal fade" id="assignmentModal" role="dialog">
        <div class="modal-dialog bigModal">
            <div class="modal-content">
                <div class="modal-body">
                    @include('student/assignment')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="quizModal" role="dialog">
        <div class="modal-dialog bigModal">
            <div class="modal-content">
                <div class="modal-body">
                    @include('student/quiz')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="midModal" role="dialog">
        <div class="modal-dialog bigModal">
            <div class="modal-content">
                <div class="modal-body">
                    @include('student/midterm')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    function openModalAssignment(){
        $('#assignmentModal').modal('show')
    }
    function openModalQuiz(){
        $('#quizModal').modal('show')
    }
    function openModalMid(){
        $('#midModal').modal('show')
    }
</script>
@stop