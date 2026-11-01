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
import { Globel_Variable } from "../AttendanceHome";
import NavMain from "../NavBar/NavMain";



const AttendanceSlots = () => {
    const navigate = useNavigate();
    //for getting the id from the url
    let [searchParams, setSearchParams] = useSearchParams();
    // fetching the attendance slots
    const [Slotlist, setSlotlist] = useState([]);
    // declare variable and store the value of the attendance slot id and append options to html select tag
    const optionSlots = Slotlist.map(slot => ({ value: slot.tt_id, label: dateFormat(slot.tt_class_date, "dddd, mmmm ,dS, yyyy") + "   " + slot.tt_subject_time_from + " TO " + slot.tt_subject_time_to, slot_label: slot.tt_class_date }));
    // state for model handling of attendance excempted students
    const handleClose = () => setShow(false);
    const [show, setShow] = useState(false);
    // fetching Student list regarding the attendance slot
    const [StudentList, setStudentList] = useState([]);
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

    var emp_login_email = window.localStorage.getItem('emp_email').trim();
    var auth_check = "haider.raza@bnu.edu.pk";

    //  fetching the attendance slots from the database


    useEffect(() => {

        setLoading(false);
        document.title = "TA Approval"

        // console.log(res.data.data + '--');
        // if (emp_login_email != auth_check) {
        //     navigate("/noaccess")
        // }

        // else {


        http.get("/get_time_slots/" + searchParams.get('id'))
            .then(res => {
                //   console.log(res.data.data);
                setSlotlist(res.data.data);
            })
        if (SelectedSlots.value) {
            setLoading(true);
            http.get("/get_students_from_time_slots/" + SelectedSlots.value + "/" + searchParams.get('id'))
                .then(res => {
                    setStudentList(res.data.data);
                    setLoading(false);
                })

            // }

        }

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
                    // console.log(res.data.attendance_status+"__"+e.label);
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
        //console.log(e)
        const value = e.target.value;
        // alert(value)
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

                            parseInt(item.presents) + 1 : parseInt(item.presents) - 1,

                absents: (e.target.value === 'EX_Remove') ? parseInt(item.absents) :
                    (e.target.value === 'A' ?
                        (e.target.value === 'A') ? parseInt(item.absents) + 1 :
                            parseInt(item.absents === 0) ? 0 : parseInt(item.absents - 1)
                        :
                        parseInt(item.absents - 1)
                    ),
                absents_ex:
                    (e.target.value === 'EX_Remove') ?
                        parseInt(item.absents_ex) - 1 :
                        (e.target.value === 'P') ?
                            (e.target.value === 'A') ? parseInt(item.absents) + 1 :
                                parseInt(item.absents_ex === 0) ? 0 : parseInt(item.absents_ex - 1)
                            :
                            item.absents_ex
            } : item)
        setStudentList(_filteredStudentsList);
        console.log(_filteredStudentsList)
        console.log(_filteredStudentsList, { id })
        const data = {
            attendance_status: value,
            exemption_remarks: Exempted,
        }
        http.post("/update_course_attendance/" + e.target.id, data)
            .then(res => {
                console.log(res.data);
            })
    }
    //This function get input value of excemted student and set it to the state of the component
    const handleExempted = (e) => {
        // console.log(e.target.value);
        setExempted(e.target.value);
    }
    const handlesave = (e) => {
        const value = e.target.value;
        const id = e.target.id;
        console.log(e.target.id);
        const _filteredStudentsList = StudentList.map(item => item.ca_id == id ? {
            ...item, ca_hod_attendance: e.target.value,
            absents_ex: (e.target.value === 'EX') ?
                parseInt(item.absents_ex) + 1 : item.absents_ex
        } : item)
        setStudentList(_filteredStudentsList);
        setShow(false);
        const data = {
            attendance_status: value,
            exemption_remarks: Exempted,
        }
        // console.log(e.target.id,data);
        http.post("/update_course_attendance/" + e.target.id, data)
            .then(res => {
                console.log(res.data);
            })
    }
    // This function mark all stunents as present in the attendance slot if the attendance slot is empty
    const create_attendance = (e) => {
        console.log(Handle_ca_not_attendance_slots_id);
        console.log(slot_label);
        console.log(searchParams.get('id'));
        http.get("/attendance/create_attendance/" + searchParams.get('id') + "/" + Handle_ca_not_attendance_slots_id + "/" + slot_label)
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
                        setShow(false);
                        setExempted('');
                        setHandle_ca_not_attendance_slots_id('');
                        setHandle_ca_not_attendance_slots_label('');
                        setSlot_label('');
                    })
            })
    }
    const goback = () => {
        localStorage.removeItem("selected_slot_id");
        localStorage.removeItem("selected_slot_label");
        navigate(-1)
    }
    return (
        <>
            <NavMain />
            {/* ---- Select Slots option start from here */}
            <div className="container">
                <h1 className="text-center">AttendanceSlots Page  </h1>
                <div className="col-6 offset-3">
                    <div className="col-12">
                        <label htmlFor="Attendance_Slots">Attendance Slots</label>
                        <Select
                            defaultValue={SelectedSlots}
                            onChange={HandleSlots}
                            options={optionSlots} />
                        <hr />
                        {/* Table ---------------------------------- */}
                    </div>
                </div>
                <div className="col-3 mb-5">
                    <button className="btn btn-secondary" onClick={goback}>Go Back</button>
                </div>
            </div>
            {/* ---- Students List start from here in Table format */}
            <div className="container-fluid">
                <div className="row">
                    <Table >
                        <thead>
                            <tr className="text-left ">
                                <th>Enroll No.</th>
                                <th>Student Name</th>
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
                                            <tr key={index} className="text-left ">
                                                <td>{item.sai_enrollment_no}</td>
                                                <td>{item.spi_name}</td>
                                                <td>{item.presents}</td>
                                                <td>{item.absents}</td>
                                                <td>{item.absents_ex}</td>
                                                {/* <td>{item.attendance_marked}</td> */}
                                                <td>{item.ca_attendance}</td>
                                                <td>
                                                    Present <input type="radio" className="mr-2" checked={is_p_checked} name={index + "check_attendance"} value="P" onChange={handleAttendance} id={item.ca_id} />
                                                    Absent  <input type="radio" className="mr-2" checked={is_a_checked} name={index + "check_attendance"} value="A" onChange={handleAttendance} id={item.ca_id} />
                                                    {item.ca_attendance == "A" ?
                                                        item.ca_hod_attendance == "EX" ?
                                                            <>
                                                                <Button variant="primary ml-3" size="sm" id={item.ca_id} value="EX_Remove" onClick={handleAttendance}>Exempted</Button>
                                                            </>
                                                            :
                                                            <>
                                                                <Button variant="outline-dark ml-3" size="sm" id={item.ca_id} onClick={handleShow}>Mark Exempted</Button>
                                                            </>
                                                        :
                                                        null}
                                                </td>
                                            </tr>
                                        )
                                    })
                            }
                        </tbody>
                    </Table>
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
        </>
    )
}
export default AttendanceSlots;






