import React from "react";
import axios from "axios";
import { useEffect ,useState} from "react";
import 'bootstrap/dist/css/bootstrap.min.css';
import http from "./http";
import Form from 'react-bootstrap/Form'
import Table from 'react-bootstrap/Table';
import { Link } from "react-router-dom";
import Select from 'react-select';
import PropagateLoader from "react-spinners/PropagateLoader";
// import AttendanceSlots from "./Attendance/AttendanceSlots";
const Home = () => {
        const [SessionList , setSessionList] = useState([]);
        const [SchoolsList , setSchoolsList] = useState([]);
        const [DepartmentList , setDepartmentList] = useState([]);
        const [session_id , setSessionId] = useState("");
        const [Session_label , setSession_label] = useState("");
        const [school_id , setSchoolId] = useState("");
        const [data, setData] = useState([]);
        let [loading, setLoading] = useState(true);
        const [SelectedSession, setSelectedSession] = useState({
          value:window.localStorage.getItem('session_id'),
          label:window.localStorage.getItem('Session_label')
        });
        const [SelectedSchool, setSelectedSchool] = useState({
          value:window.localStorage.getItem('School_id'),
          label:window.localStorage.getItem('School_label')
        });
        const optionSession=SessionList.map(session =>({
          
          // value:(window.localStorage.getItem('session_id'))?window.localStorage.getItem('session_id'):session.sess_id,
          // label:(window.localStorage.getItem('Session_label'))?window.localStorage.getItem('Session_label'):session.sess_name
          // value:Temp_Value?Temp_Value:session.sess_id,
          // label:Temp_Label?Temp_Label:session.sess_name
          value:session.sess_id,label:session.sess_name
        }))
        var optionSchool=SchoolsList.map(schools =>({value:schools.school_id,label:schools.school_name}))
        const [Setlect_from_Local_Storatge,setSetlect_from_Local_Storatge]=useState({
        })
        useEffect(() => {
                      setLoading(false);
                        //  Getting Session List
                        http.get('/getsessions')
                        .then(res => {setSessionList(res.data.data);})
                        // // get schools list
                        http.get('/getschools')
                          .then(res => {setSchoolsList(res.data.data);})
                          if(SelectedSession.value && SelectedSchool.value){
                            http.get('/courses_by_school_id/'+SelectedSession.value +'/'+ SelectedSchool.value)
                        .then(res => {
                          setData(res.data.data);
                        })
                          }
                          else{
                            http.get('/coursesdetails/'+SelectedSession.value)
                            .then(res => {
                              setData(res.data.data);
                            })
                          }
                    },[]);
          
        // get data from database using session id 
const handleSession = (e) => {
                        // alert(session_id);
                        window.localStorage.setItem("session_id",e.value)
                        window.localStorage.setItem("Session_label",e.label)
                        setLoading(true);
                        http.get('/coursesdetails/'+e.value)
                        .then(res => {setData(res.data.data);
                          setTimeout(() => {
                            setLoading(false);      
                          }, 1000);
                        })
                      }
                      // get data from database using session id and school id
                      const HandleSchools = (e) => {
                        setSchoolId(e.value);
                        window.localStorage.setItem("School_id",e.value)
                        window.localStorage.setItem("School_label",e.label)
                        setLoading(true);
                        http.get('/courses_by_school_id/'+ (SelectedSession.value?SelectedSession.value:session_id) +'/'+ e.value)
                          .then(res => {
                            setData(res.data.data);
                            setTimeout(() => {
                              setLoading(false);      
                      }, 0);
                        
                          })
                  }
    return(
        <>
<div className="container">
                  <h1 className="text-center">Attendance Home Page</h1>
                  <div className="col-6 offset-3">
                  <div className="col-12">
                        <Select 
                        defaultValue={SelectedSession}
                        options={optionSession} onChange={handleSession}/>
                            {/*  or Schools */}
                          <label  htmlFor="Session"></label>
                        <Select id="HandleSchools"
                        defaultValue={SelectedSchool}
                        options={optionSchool}   onChange={HandleSchools}/>
            </div>
</div>
{/* Table ---------------------------------- */}
<Table striped bordered hover size="sm">
      <thead>
                    <tr>
                      <th>Course Name</th>
                      <th>Teacher Name</th>
                      <th>Action</th>
                    </tr>
      </thead>
      <tbody>
        {loading ? 
                    <PropagateLoader loading={loading}  height={20}  width={5} margin={2} size={10}   cssOverride={{
                      position: "absolute",
                      top: "50%",
                      left: "50%",
                      marginTop: "50px",
                      transform: "translate(-50%, -50%)"
                  }}  />
           :
                      data.map((item ,index)=>
                      <tr key={item.co_id}>
                        <td>{item.course_name}</td>
                        <td>{item.teacher_name}</td>
                        <td>
                          <Link  className="btn btn-primary"   to={'/attendanceslots/?id='+item.co_id}> Mark Attendance</Link>
                        </td>
                      </tr>
         )}
      </tbody>
    </Table>
</div>
  </>)
}

export default Home;
