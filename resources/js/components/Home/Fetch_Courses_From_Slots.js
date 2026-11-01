import React from "react";
import axios from "axios";
import { useEffect, useState } from "react";
import 'bootstrap/dist/css/bootstrap.min.css';
import http from "../http";
import Form from 'react-bootstrap/Form'
import Table from 'react-bootstrap/Table';
import { Link, useNavigate } from "react-router-dom";
import Select from 'react-select';
import PropagateLoader from "react-spinners/PropagateLoader";
import Courses_Api from "../Courses_Api";
import { ToastContainer, toast } from 'react-toastify';
var em_token = (window.localStorage.getItem('em_token') || '').trim();
let pass_token = ({ headers: { "Authorization": `Bearer ${em_token}` } })

const Fetch_Courses_From_Slots = () => {
  const [SessionList, setSessionList] = useState([]);
  const [SchoolsList, setSchoolsList] = useState([]);
  const [DepartmentList, setDepartmentList] = useState([]);
  const [TeachersList, setTeachersList] = useState([]);
  const [CoursesList, setCoursesList] = useState([]);
  const [Offereddata, setData] = useState([]);
  const navigate = useNavigate();
  let [loading, setLoading] = useState(false);
  const [session_id, setSessionId] = useState("");
  const [SchoolId, setSchoolId] = useState("");
  const [DepartmentId, setDepartmentId] = useState("");
  const [Teacher_Id, setTeacher_Id] = useState("");
  var optionSession = SessionList.map(session => ({ value: session.sess_id, label: session.sess_name }))
  const optionSchool = SchoolsList.map(schools => ({ value: schools.school_id, label: schools.school_name }))
  var optionDepartment = DepartmentList.map(department => ({ value: department.dept_id, label: department.dept_name }))
  var optionTeachers = TeachersList.map(teacher => ({ value: teacher.employee_id, label: teacher.faculty_name }))
  var optionCourses = Offereddata.map(item => ({ value: item.co_id, label: item.course_name }))
  const SelectedSession = {
    value: window.localStorage.getItem('session_id'),
    label: window.localStorage.getItem('Session_label')
  };
  const SelectedSchool = {
    value: window.localStorage.getItem('School_id'),
    label: window.localStorage.getItem('School_label')
  }
  const SelectedDepartment = {
    value: window.localStorage.getItem('Department_id'),
    label: window.localStorage.getItem('Department_label')
  }
  const SelectedTeachers = {
    value: window.localStorage.getItem('Teacher_id'),
    label: window.localStorage.getItem('Teacher_label')
  }
  var emp_login_email = (window.localStorage.getItem('emp_email') || '').trim();
  //var auth_check = "Ivan.duran@bnu.edu.pk";
  //  var auth_check ="muhammad.na@bnu.edu.pk";
  var auth_check = "haider.raza@bnu.edu.pk";
  useEffect(() => {
    //  Getting Session List
 //   if (emp_login_email != auth_check) {
  //    navigate("/noaccess")
 //   }
 //   else {
      http.get('/getsessions', pass_token)
        .then(res => { setSessionList(res.data.data); })
      // ------- Handle using Local Storage
      if (SelectedSession.value && SelectedSchool.value && SelectedDepartment.value) {
        http.get('/getschools', pass_token)
          .then(res => { setSchoolsList(res.data.data); })
        http.get('/getdepartments/' + SelectedSchool.value, pass_token)
          .then(res => { setDepartmentList(res.data.data); })
        get_Courses('/get_offered_courses?session_id=' + SelectedSession.value + '&school_id=' + SelectedSchool.value + '&department_id=' + SelectedDepartment.value, pass_token)
        faculty_by_offered_course('/get_teachers?session_id=' + SelectedSession.value + '&school_id=' + SelectedSchool.value + SelectedDepartment.value), pass_token;
      }
      else if (SelectedSession.value && SelectedSchool.value) {
        get_Courses('/get_offered_courses?session_id=' + SelectedSession.value + '&school_id=' + SelectedSchool.value, pass_token)
        http.get('/getschools', pass_token)
          .then(res => { setSchoolsList(res.data.data); })
        http.get('/getdepartments/' + SelectedSchool.value, pass_token)
          .then(res => { setDepartmentList(res.data.data); })
        faculty_by_offered_course('/get_teachers?session_id=' + SelectedSession.value + '&school_id=' + SelectedSchool.value);
      }
      else if (SelectedSession.value) {
        http.get('/getschools', pass_token)
          .then(res => { setSchoolsList(res.data.data); })
        // get offered courses against session id
        //get_Courses('/get_offered_courses?session_id=' + SelectedSession.value)
        // get offered courses against session id
        faculty_by_offered_course('/get_teachers?session_id=' + SelectedSession.value);
      }
//    }
  }, []);
  const faculty_by_offered_course = (get_url) => {
    http.get(get_url, pass_token)
      .then(res => {
        // setData(res.data.data);
        setTeachersList(res.data.data)
      })
  }
  const get_Courses = (get_url) => {
    setLoading(true);
    http.get(get_url, pass_token)
      .then(res => {
        setTimeout(() => {
          setLoading(false);
        }, 0);
        setData(res.data.data);
      })
  }
  const handleSession = (e) => {
    setSessionId(e.value);
    window.localStorage.setItem("session_id", e.value);
    window.localStorage.setItem("Session_label", e.label);
    // this api get schools against session
    http.get('/getschools', pass_token)
      .then(res => { setSchoolsList(res.data.data); })
    // get offered courses against session id
    //get_Courses('/get_offered_courses?session_id=' + e.value)
    // get offered courses against session id
    faculty_by_offered_course('/get_teachers?session_id=' + e.value);
    const local_storage = window.localStorage
    if (local_storage.session_id) {
      if (local_storage.School_id) {
        console.log(local_storage.School_id)
        console.log(local_storage.School_label)
        local_storage.removeItem("School_id")
        local_storage.removeItem("School_label")
        local_storage.removeItem("Department_id")
        local_storage.removeItem("Department_label")
        local_storage.removeItem("Teacher_id")
        local_storage.removeItem("Teacher_label")
        window.location.reload();
      }
    }
  }
  const HandleSchools = (e) => {
    setSchoolId(e.value);
    window.localStorage.setItem("School_id", e.value)
    window.localStorage.setItem("School_label", e.label)
    get_Courses('/get_offered_courses?session_id=' + (session_id ? session_id : SelectedSession.value) + '&school_id=' + e.value)
    http.get('/getdepartments/' + e.value, pass_token)
      .then(res => {
        setDepartmentList(res.data.data);
      })
    // get offered courses against session id
    faculty_by_offered_course('/get_teachers?session_id=' + (session_id ? session_id : SelectedSession.value) + '&school_id=' + e.value);
  }
  const handleDepartment = (e) => {
    // alert(e.value )
    setDepartmentId(e.value);
    window.localStorage.setItem("Department_id", e.value)
    window.localStorage.setItem("Department_label", e.label)
    get_Courses('/get_offered_courses?session_id=' + (session_id ? session_id : SelectedSession.value) + '&school_id=' + (SchoolId ? SchoolId : SelectedSchool.value) + '&department_id=' + e.value)
    // faculty_by_offered_course('/get_teachers?session_id='+session_id+'&school_id='+SchoolId+'&department_id='+e.value);
    faculty_by_offered_course('/get_teachers?session_id=' + (session_id ? session_id : SelectedSession.value) + '&school_id=' + (SchoolId ? SchoolId : SelectedSchool.value) + '&department_id=' + e.value);
  }
  const HandleTeacher = (e) => {
    setTeacher_Id(e.value)
    // alert(e.value)
    window.localStorage.setItem("Teacher_id", e.value)
    window.localStorage.setItem("Teacher_label", e.label)
    setLoading(true);
    // Courses_Api.get('?session_id='+(session_id?session_id:SelectedSession.value)+'&school_id='+(SchoolId?SchoolId:SelectedSchool.value)+'&department_id='+DepartmentId?DepartmentId:SelectedDepartment.value+'&faculty_employee_id='+e.value)
    Courses_Api.get('?session_id=' + (session_id ? session_id : SelectedSession.value) + '&faculty_employee_id=' + e.value, pass_token)
      .then(res => {
        console.log(res.data.data)
        setData(res.data.data);
        // // -------- get departments data
        setLoading(false);
      })
  }
  const handleCourse = (e) => {
    setLoading(true);
    Courses_Api.get('?session_id=' + (session_id ? session_id : SelectedSession.value) + '&co_id=' + e.value, pass_token)
      .then(res => {
        setData(res.data.data);
        // -------- get departments data
        setLoading(false);
      })
  }
  const handle_reset = (e) => {
    // window.localStorage.clear()
    localStorage.removeItem("Teacher_label");
    localStorage.removeItem("session_id");
    localStorage.removeItem("Session_label");
    localStorage.removeItem("Department_id");
    localStorage.removeItem("School_label");
    localStorage.removeItem("Teacher_id");
    localStorage.removeItem("Department_label");
    localStorage.removeItem("School_id");
    localStorage.removeItem("selected_slot_id");
    localStorage.removeItem("selected_slot_label");
    window.location.reload();
  }

  return (
    <>
      <ToastContainer />
      <div className=" mb-5 mt-4">
        <div className="col-md-12 col-lg-12">
          <div className="card overflow-hidden">
            <div className="card-header col-12 col-md-12 col-lg-12 bg-primary ">
              <h3 className="card-title col-8 text-white">Attendance Management</h3>
              <Link className=" col-2 offset-1 text-center btn btn-primary" to={'/handle_excemption'}> Mark Excemption</Link>
            </div>
            <div className="card-body">
              <div className={"row offset-2 mb-1"}>
                <div className={"col-sm-4"}>
                  <label htmlFor="School" className={"mt-2 "}>Select Session</label>
                  <Select
                    defaultValue={SelectedSession}
                    options={optionSession}
                    onChange={handleSession} />

                </div>
                <div className={"col-sm-4"}>
                  <label htmlFor="School" className={"mt-2 "}>Select School</label>
                  <Select
                    defaultValue={SelectedSchool}
                    options={optionSchool} onChange={HandleSchools} />
                </div>
              </div>

              {/* row 2 */}
              <div className={"row  offset-2 mb-1 "}>
                <div className={"col-sm-4"}>
                  <label htmlFor="School" className={"mt-2 "}>Select Department</label>
                  <Select
                    defaultValue={SelectedDepartment}
                    options={optionDepartment}
                    onChange={handleDepartment} />
                </div>
                <div className={"col-sm-4"}>
                  <label htmlFor="School" className={"mt-2 "}>Select Teacher</label>
                  <Select
                    defaultValue={SelectedTeachers}
                    options={optionTeachers}
                    onChange={HandleTeacher} />
                </div>
              </div>
              {/* row 3 */}

              <div className={"row  offset-3 mb-1"}>
                <div className={"col-sm-5"}>
                  <label htmlFor="School" className={"mt-2  "}>Select Course</label>
                  <Select
                    options={optionCourses}
                    onChange={handleCourse} />
                </div>
                <div className="col-sm-5 align-middle m-3" >
                  <button className="btn btn-outline-dark col-3 mt-4" onClick={handle_reset}>Reset</button>

                </div>

              </div>
              {/* Select Options End here */}

            </div>
            <div className="card-footer">
              {/* Table of Courses Start here */}


              <Table bordered hover>
                <thead>
                  <tr scope='row'>
                    <th scope="col">Course Name</th>
                    <th scope="col">Teacher Name</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>

                <tbody>
                  {

                    loading ?
                      <PropagateLoader loading={loading} height={20} width={5} margin={2} size={10} cssOverride={{
                        position: "absolute",
                        top: "50%",
                        left: "50%",
                        marginTop: "50px",
                        transform: "translate(-50%, -50%)"
                      }} />
                      :
                      // print students data in table
                      Offereddata.map((item, index) =>
                        <tr key={index}>
                          {/* <td>{item.co_id}</td> */}
                          <td>{item.course_name} </td>
                          <td>{item.teacher_name}</td>
                          <td>
                            <Link className="btn btn-primary" to={'/attendanceslots/?id=' + item.co_id}> Mark Attendance</Link>
                          </td>
                        </tr>
                      )


                  }
                </tbody>


              </Table>


              {/* Table of Courses End here */}
            </div>
          </div>
        </div>
      </div>




    </>
  )
}

export default Fetch_Courses_From_Slots;