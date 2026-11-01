import React from "react";
import { useEffect, useState, useContext } from "react";
import { useSearchParams } from 'react-router-dom';
import { useNavigate } from 'react-router-dom';
import http from "../http";
import Select from 'react-select';
import dateFormat from 'dateformat';
import Table from 'react-bootstrap/Table';
import { Form } from "react-bootstrap";
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import PropagateLoader from "react-spinners/PropagateLoader";
import toast,{ Toaster } from 'react-hot-toast';

const Students_Excemption_Mark=()=>{
    const navigate = useNavigate();
    //for getting the id from the url
    let [searchParams, setSearchParams] = useSearchParams();
    // Enable CA Status to E from Pending
    const [En_ca_status, setEn_ca_status] = useState(false);
    // fetching the attendance slots
    const [Slotlist, setSlotlist] = useState([]);
    // declare variable and store the value of the attendance slot id and append options to html select tag
    const optionSlots = Slotlist.map(slot => ({ value: slot.tt_id, label: dateFormat(slot.tt_class_date, "dddd, mmmm ,dS, yyyy") + "   " + slot.tt_subject_time_from + " TO " + slot.tt_subject_time_to, slot_label: slot.tt_class_date }));
    // state for model handling of attendance excempted students
    const handleClose = () => setShow(false);
    const [show, setShow] = useState(false);
    // fetching Student list regarding the attendance slot
    const [StudentList, setStudentList] = useState([]);
    const [SessionList, setSessionList] = useState([]);
    var optionSession = SessionList.map(session => ({ value: session.sess_id, label: session.sess_name }))
    const [session_id, setSessionId] = useState("");
    const [Spi_name, setSpi_name] = useState("");
    const [Student_Enrollment_Number, setStudent_Enrollment_Number] = useState('');
    // show loader while fetching data
    let [loading, setLoading] = useState(true);
    // set he label and value of the attendance slot to the state of the select tag
    const [Handle_ca_not_attendance_slots_label, setHandle_ca_not_attendance_slots_label] = useState('');
    const [Handle_ca_not_attendance_slots_id, setHandle_ca_not_attendance_slots_id] = useState('');
    const [Exempted, setExempted] = useState('');
    const [show_model_for_not_attendance_slots, set_show_model_for_not_attendance_slots] = useState(false);
    const handleClose_show_model_for_not_attendance_slots = () => set_show_model_for_not_attendance_slots(false);
    // set the id of student for mark excempted student
    const [handle_ca_id_of_exempted_id, setHandle_ca_id_of_exempted_id] = useState('');
    // set label of student for mark all present students in the attendance slot
    const [slot_label, setSlot_label] = useState('');
    const [SelectedSlots, setSelectedSlots] = useState({
        value: window.localStorage.getItem('selected_slot_id'),
        label: window.localStorage.getItem('selected_slot_label')
    });
    //var emp_login_email = window.localStorage.getItem('emp_email').trim();
	// var auth_check = "Ivan.duran@bnu.edu.pk";
   //var auth_check = "haider.raza@bnu.edu.pk";
   //var user_id = window.localStorage.getItem('user_id').trim();
    //  fetching the attendance slots from the database
	
    useEffect(() => {
        setLoading(false);
        document.title = "Excemption Handling"
        http.get("/getsessions")
            .then(res => {
                setSessionList(res.data.data);
            })
        // if (SelectedSlots.value) {
        //     setLoading(true);
        //     http.get("/get_students_from_time_slots/" + SelectedSlots.value + "/" + searchParams.get('id'))
        //         .then(res => {
        //             setStudentList(res.data.data);
        //             setLoading(false);
        //         })
        // }
    }, []);
    // Call function HandleSlots on  select slot
    const HandleSlots = (e) => {
        window.localStorage.setItem('selected_slot_id', e.value),
            window.localStorage.setItem('selected_slot_label', e.label)
        setLoading(true);
        http.get("/get_students_from_time_slots/" + e.value + "/" + searchParams.get('id'))
            .then(res => {
                setStudentList([]);
                setLoading(false);
                if (res.data.attendance_status == 0) {
                    Handle_slot_not_present();
                    setHandle_ca_not_attendance_slots_label(e.label);
                    setSlot_label(e.slot_label);
                    setHandle_ca_not_attendance_slots_id(e.value);
                } else {
                    setStudentList(res.data.data);
                }
            })
    }
    // show or close model for attendance Mark excempted Button
    const handleShow = (e) => {
        setHandle_ca_id_of_exempted_id(e.target.id);
        if (setShow == true) {
            setShow(false);
        }
        else {
            setShow(true);
        }
    }
    const Handle_slot_not_present = (e) => {
        if (set_show_model_for_not_attendance_slots == true) {
            set_show_model_for_not_attendance_slots(false);
        }
        else {
            set_show_model_for_not_attendance_slots(true);
        }
    }
    // Function to marks the attendance of the student with Exempted status
    const handleAttendance = (e) => {
        const value = e.target.value;
        const name = e.target.name;
        const id = e.target.id;
        const _filteredStudentsList = StudentList.map(item => item.ca_id == id ?
            {
                ...item,
                ca_attendance: (e.target.value === 'EX_Remove') ? 'A' : e.target.value,
                ca_hod_attendance: (e.target.value === 'EX_Remove') ? 'A' : e.target.value,
                presents:
                    (e.target.value === 'EX_Remove') ? item.presents :

                        (e.target.value === 'P') ?

                            parseInt(item.presents) + 1 :
                            // parseInt(item.presents)<=0?parseInt(item.presents)===0:
                            parseInt(item.presents) - 1,

                absents: (e.target.value === 'EX_Remove') ?
                    parseInt(item.absents) + 1. :
                    (e.target.value === 'A' ?
                        (e.target.value === 'A') ? parseInt(item.absents) + 1 :
                            parseInt(item.absents === 0) ? 0 : parseInt(item.absents - 1)
                        :
                        parseInt(item.absents - 1)
                    ),
                absents_ex:
                    (e.target.value === 'EX_Remove')
                        ?
                        parseInt(item.absents_ex) - 1
                        :
                        item.absents_ex
            } : item)
        setStudentList(_filteredStudentsList);
        const data = {
            attendance_status: value,
            exemption_remarks: Exempted,
        }
        http.post("/update_course_attendance/" + e.target.id, data)
            .then(res => {
                e.target.value === 'EX_Remove' ? toast.success(`Excempted Removed Successfully !`)
                    : e.target.value === 'P' ? toast.success(`Marked Present Successfully !`)
                        : e.target.value === 'A' ? toast.success(`Marked Absent Successfully !`) : ''

            })
    }
    //This function get input value of excemted student and set it to the state of the component
    const handleExempted = (e) => {
        setExempted(e.target.value);
    }
    const handlesave = (e) => {
        const value = e.target.value;
        const id = e.target.id;
        const _filteredStudentsList = StudentList.map(item => item.ca_id == id ? {
            ...item, ca_hod_attendance: e.target.value,
            absents_ex: (e.target.value === 'EX') ? parseInt(item.absents_ex) + 1 : item.absents_ex,
            absents: parseInt(item.absents) - 1,
            ca_attendance: 'Exempted'
        } : item)
        setStudentList(_filteredStudentsList);
        setShow(false);
        const data = {
            attendance_status: value,
            exemption_remarks: Exempted,
        }
        http.post("/update_course_attendance/" + e.target.id, data)
            .then(res => {
                toast.success(`Excempted Successfully!`);
            })
    }
    // This function mark all stunents as present in the attendance slot if the attendance slot is empty
    const create_attendance = (e) => {
        //console.log(Handle_ca_not_attendance_slots_id);
        //console.log(slot_label);
        //console.log(searchParams.get('id'));
        http.get("/create_attendance/?co_id=" + searchParams.get('id') + "&slot_id=" + Handle_ca_not_attendance_slots_id + "&slot_date=" + slot_label + '&user_id='+user_id)
            .then(res => {
                console.log(res.data);
                set_show_model_for_not_attendance_slots(false);
                setLoading(true);
                http.get("/get_students_from_time_slots/" + Handle_ca_not_attendance_slots_id + "/" + searchParams.get('id'))
                    .then(res => {
                        setStudentList([]);
                        setTimeout(() => {
                            setLoading(false);
                        }, 0);
                        setStudentList(res.data.data);
                        setEn_ca_status(true)
                        setShow(false);
                        setExempted('');
                        setHandle_ca_not_attendance_slots_id('');
                        setHandle_ca_not_attendance_slots_label('');
                        setSlot_label('');
                    })
            })

 
    }
    const handleSession = (e) => {

        setSessionId(e.value);
    }

    const Enable_ca_status=()=>{
        //alert('ok')
        http.get("/enable_ca_status/?co_id=" + searchParams.get('id') + "&slot_id=" + window.localStorage.getItem('selected_slot_id'))
        .then(res => {setEn_ca_status
            toast.success(res.data.success);
            //console.log(res.data.success);
            setEn_ca_status(false)

    });}

    const goback = () => {
        localStorage.removeItem("selected_slot_id");
        localStorage.removeItem("selected_slot_label");
        navigate(-1)
    }

    const Search_Student=(e)=>{
        //alert(session_id + " " + Student_Enrollment_Number )
        if (session_id && Student_Enrollment_Number){

            setLoading(true);
            // http.get("/get_students_from_time_slots/" + 123 + "/" + 112)
            http.get("/get_student_attendance_details?session_id=" + session_id + "&en_no=" + Student_Enrollment_Number)
            .then(res => {
                setStudentList([]);
                setLoading(false);
                setStudentList(res.data.data);
                setSpi_name(res.data.data[0].spi_name); 
            })
        }
        else{
            toast.error('Please Select Session or Enter Student Enrollment Number');
        }

    }








    return(<>
    
    <Toaster position="bottom-left" reverseOrder={false} />
     {/* ---- Select Slots option start from here */}
     <div className="col-md-12 col-lg-12 mt-4 pb-5">
                <div className="card overflow-hidden">
                    <div className="card-header bg-primary ">
                        <h3 className="card-title text-white">Excemption Handling</h3>
                    </div>
                    <div className="card-body pb-4">
                    <div className={"row offset-2 mb-1"}>
                    <div className={"col-sm-4"}>
                      <label htmlFor="School" className={"mt-2 "}>Select Session</label>
                      <Select
                        //defaultValue={SelectedSession}
                        options={optionSession}
                        onChange={handleSession}
                         />
                    </div>
                    <div className={"col-sm-4"}>
                      <label htmlFor="School" className={"mt-2 "}>Enter Student Enrollment Nuumber</label>
                      <input value={Student_Enrollment_Number}  className='form-control'  name="Student_Enrollment_Number" onChange={e => setStudent_Enrollment_Number(e.target.value)} />
                     
                      <Button variant="primary float-right mt-2 d-flex align-items-center "  size="md" onClick={Search_Student} >Search</Button>
                    
                    </div>
                    
                  </div>

                    </div>
                    <div className="card-footer pt-5">

                        <div className='row'>
                        <div className="col-11 mt-2 mb-2">
                               {
                                En_ca_status?
                                <Button variant="info float-right" size="md" onClick={Enable_ca_status} active >Mark Present</Button>
                                    :
                                    ''
                               }
                               {
                                <h3 className="text-secondary text-uppercase font-weight-bold" > {Spi_name}</h3>
                               }
                            </div>
                            
                        </div>


                        <Table bordered hover>
                            <thead>
                                <tr >
                                    <th>Course Name</th>
                                    <th>Date</th>
                                    <th>Total Presents</th>
                                    <th>Total Absents</th>
                                    <th>Excempted</th>
                                    <th>Attendance Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {
                                    // if loaing true show loading else show table
                                    loading ?
                                        <PropagateLoader loading={loading} height={20} width={5} margin={2} size={10} cssOverride={{
                                            position: "absolute",
                                            top: "50%",
                                            left: "50%",
                                            marginTop: "50px",
                                            transform: "translate(-50%, -50%)"
                                        }} />
                                        :
                                        StudentList.map((item, index) => {
                                            const is_p_checked = item.ca_attendance == "P" ? "checked" : "";
                                            const is_a_checked = item.ca_attendance == "A" ? "checked" : item.ca_attendance == "EX_Remove" ? "checked" : "";

                                            return (
                                                <tr key={index} classNames="text-left ">
                                                    <td>{item.course_cd_name}</td>

                                                    <td>
                                                    {dateFormat(item.ca_attendance_date, "dddd, mmmm ,dS, yyyy")}
                                                        
                                                        </td>
                                                    <td>{item.presents}</td>
                                                    <td>{item.absents}</td>
                                                    <td>{item.absents_ex}</td>
                                                    <td>{item.ca_attendance}</td>
                                                    <td>
                                                        {

                                                            item.ca_hod_attendance == "EX" ?
                                                                <>
                                                                    <Button variant="primary ml-3" size="sm" id={item.ca_id} value="EX_Remove" onClick={handleAttendance}>Remove Exempted</Button>
                                                                </>
                                                                :
                                                                item.ca_attendance == "A" ?
                                                                    <>
                                                                        Present <input type="radio" classNames="mr-2" checked={is_p_checked} name={index + "check_attendance"} value="P" onChange={handleAttendance} id={item.ca_id} />
                                                                        Absent  <input type="radio" classNames="mr-2" checked={is_a_checked} name={index + "check_attendance"} value="A" onChange={handleAttendance} id={item.ca_id} />
                                                                        <Button variant="outline-dark ml-3" size="sm" id={item.ca_id} onClick={handleShow}>Mark Exempted</Button>
                                                                    </>
                                                                    :
                                                                    <>
                                                                        Present <input type="radio" classNames="mr-2" checked={is_p_checked} name={index + "check_attendance"} value="P" onChange={handleAttendance} id={item.ca_id} />
                                                                        Absent  <input type="radio" classNames="mr-2" checked={is_a_checked} name={index + "check_attendance"} value="A" onChange={handleAttendance} id={item.ca_id} />

                                                                    </>
                                                        }
                                                    </td>
                                                </tr>
                                            )
                                        })
                                }
                            </tbody>
                        </Table>

                    </div>
                </div>
            </div>
            {/* -------------- Model Start----------------------------------- */}
            <Modal show={show}
                backdrop="static"
                keyboard={false}
                onHide={handleClose}>
                <Modal.Header closeButton>
                    <Modal.Title>Exempted</Modal.Title>
                </Modal.Header>
                <Modal.Body> Explain Reason...!
                    <Form.Control type="text" placeholder="Enter Reason" name="ca_attendance" onChange={handleExempted} />
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleClose}>
                        Close
                    </Button>
                    <Button variant="primary" id={handle_ca_id_of_exempted_id} value={"EX"} onClick={handlesave} >
                        Save
                    </Button>
                </Modal.Footer>
            </Modal>

            {/* -------------  Model Start  for Not Attendance   ---------------------------- */}

            <Modal show={show_model_for_not_attendance_slots}
                backdrop="static"
                keyboard={false}
                onHide={handleClose_show_model_for_not_attendance_slots}>
                <Modal.Header closeButton>
                    <Modal.Title>Note....!</Modal.Title>
                </Modal.Header>
                <Modal.Body> Attendance for {Handle_ca_not_attendance_slots_label} slot is not marked.All Students will be  marked as present in this slot on click of Mark Present button.
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleClose_show_model_for_not_attendance_slots}>
                        Close
                    </Button>
                    <Button variant="primary" onClick={create_attendance} >
                        Mark Present
                    </Button>
                </Modal.Footer>
            </Modal>
            {/* --------------  Model End for Not Attendance ----------------------- */}





    </>)
}

export default Students_Excemption_Mark;