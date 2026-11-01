import React, { useState, useEffect } from 'react'
import 'bootstrap/dist/css/bootstrap.min.css';
import Form from 'react-bootstrap/Form';
import http from '../../http';
import { Link, useSearchParams } from 'react-router-dom';
import Alert from 'react-bootstrap/Alert';
import Select from 'react-select';
import EditOutlinedIcon from '@material-ui/icons/EditOutlined';
import PropagateLoader from "react-spinners/PropagateLoader";
import LocalPostOfficeIcon from '@material-ui/icons/LocalPostOffice';
// import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useNavigate } from 'react-router-dom';
import toast,{ Toaster } from 'react-hot-toast';
import { ControlCameraOutlined } from '@material-ui/icons';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import NavMain from '../../NavBar/NavMain';

const TA_Rights_Approval = () => {
  const [Show_Emp_Details, setShow_Emp_Details] = useState(false);
  const handleClose = () => setShow_Emp_Details(false);
  const handleShow = () => setShow_Emp_Details(true);
  const navigate = useNavigate();
  let [loading, setLoading] = useState(false);
  const [Employees_Data_table, setEmployees_Data_table] = useState([]);
  const [Employees_Data, setEmployees_Data] = useState([]);
  const optionemployee = Employees_Data.map(item => ({ value: item.employee_id, label: item.employee_name, emp_username: item.emp_ad_username }))
  const [optionType, setoptionType] = useState([{ value: 1, label: 'Staff' }, { value: 2, label: 'Faculty' }, { value: 3, label: 'Visiting Faculty' }, { value: 4, label: 'Dean' }, { value: 5, label: 'HOD' }, { value: 6, label: 'TA' }])
  // var optionType = [{ value: 1, label: 'Staff' }, { value: 2, label: 'Faculty' }, { value: 3, label: 'Visiting Faculty' }, { value: 4, label: 'Dean' }, { value: 5, label: 'HOD' }, { value: 6, label: 'TA' }];
  var optionStatus = [{ value: 'E', label: 'Enable' }, { value: 'D', label: 'Disable' }];
  const [employee_id, setemployee_id] = useState('');
  const [Fc_id, setFc_id] = useState('');
  const [selectType_emp, setselectType_emp] = useState('');
  const [selectStatus_emp, setselectStatus_emp] = useState('');
  const [Select_emp_name, setSelect_emp_name] = useState('');
  const [Show_ta_div, setShow_ta_div] = useState(false)
  const [Msg, setMsg] = useState(false);
  const [Select_emp_type, setSelect_emp_type] = useState('');
  const [Select_approval_status, setSelect_approval_status] = useState('');
  const [Select_school_code, setSelect_school_code] = useState('');
  const [Select_course_name, setSelect_course_name] = useState('');
  const [Select_emp_email, setSelect_emp_email] = useState('');
  const [Select_dept_name, setSelect_dept_name] = useState('');
  const [Select_teacher_name, setSelect_teacher_name] = useState('');
  const Selected_emp_type = {
    value: Select_emp_type,
    label: Select_emp_type === '1' ? 'Staff' : Select_emp_type === '2' ? 'Faculty' : Select_emp_type === '3' ? 'Visiting Faculty' : Select_emp_type === '4' ? 'Dean' : Select_emp_type === '5' ? 'HOD' : Select_emp_type === '6' ? 'TA' : ''

  };
  const Selected_emp_status = {
    value: Select_approval_status,
    label: Select_approval_status === 'E' ? 'Enable' : Select_approval_status === 'D' ? 'Disable' : ''
  };
  var em_token=window.localStorage.getItem('em_token').trim();
  let pass_token = ({ headers: { "Authorization": `Bearer ${em_token}`}})
  // var pass_token=window.localStorage.getItem('em_token').replace(/\\n/g, '');
  const get_employee = (url) => {	
  
    setLoading(true);
	
    http.get(url,pass_token)
	
    .then(res => {
	
      // console.log(res.data.data)
      setTimeout(() => {
        setLoading(false);
      }, 0);
      setEmployees_Data(res.data.data);
      setEmployees_Data_table(res.data.data);
      setLoading(false)
    })
  }
  useEffect(() => {
	//  alert("/get_employee_data?sess_id=156&approval_status='Pending'");
    get_employee("/get_employee_data?sess_id=156&approval_status='Pending'")
    document.title = "TA Approval"
  }, []);
  const handle_Select_emp = (e) => {
    http.get('/get_employee_data?emp_id=' + e.value,pass_token)
      .then(res => {
        // console.log(res.data.data)
        setEmployees_Data_table(res.data.data);
      })
  }

  const handle_Employee_Details = (e) => {
    
    setFc_id(e.target.value)
    setSelect_emp_name(e.currentTarget.getAttribute("emp_name"));
    setSelect_emp_type(e.currentTarget.getAttribute("emp_type"));
    setSelect_approval_status(e.currentTarget.getAttribute("approval_status"));
    setSelect_school_code(e.currentTarget.getAttribute("school_code"));
    setSelect_course_name(e.currentTarget.getAttribute("course_name"));
    setSelect_emp_email(e.currentTarget.getAttribute("emp_email"));
    setSelect_dept_name(e.currentTarget.getAttribute("dept_name"));
    setSelect_teacher_name(e.currentTarget.getAttribute("teacher_name"));
    setemployee_id(e.target.value)
    handleShow()
    // setShow_ta_div(true)
    // setShow_ta_div(true);
    // window.scrollTo({ top: 0, behavior: 'smooth' });
  }
  const UpdateTA = (e) => {
    const emp_name=e.currentTarget.getAttribute("emp_name")

    http.get('/updateapproval?fc_id='+e.currentTarget.getAttribute("fc_id")+'&approval_status='+e.target.value,pass_token)
    .then(res => {
      toast.success(` ${emp_name} is Approved for Teacher Assistant Successfully!`)
      });
      if (e.target.value==='Approved'){
        const __Employees_Data_table=Employees_Data_table.filter(item=>item.fc_id!=e.currentTarget.getAttribute("fc_id"));
        setEmployees_Data_table(__Employees_Data_table);
        setEmployees_Data(__Employees_Data_table);
      }
  }
  return (
    <>
    <NavMain/>
    <Toaster  position="bottom-left"reverseOrder={false}/>

    {/* Show_Emp_Details */}
{/* start employee details model here */}

<Modal
      size="lg"
      aria-labelledby="contained-modal-title-vcenter"
      centered
      show={Show_Emp_Details}
      onHide={() => handleClose(false)}
    >
      <Modal.Header closeButton>
        <Modal.Title id="contained-modal-title-vcenter">
        <h3 className=' text-primary'>{Select_emp_name}</h3>
      <p className='text-secondary'><LocalPostOfficeIcon style={{ fontSize: 30 }} className='pr-1' />  {Select_emp_email}</p>
        </Modal.Title>
      </Modal.Header>
      <Modal.Body>
      <table className="table">
                <thead>
                </thead>
                <tbody>
                  <tr className='mb-4'>
                    <td > <h6 className='pt-3 pb-3' >Department Name</h6> {<p className='text-secondary'>{Select_dept_name}</p>}</td>
                    <td> <h6 className='pt-3 pb-3' >Designation</h6> {<p className='text-secondary'>{Select_emp_type === '1' ? 'Staff' : Select_emp_type === '2' ? 'Faculty' : Select_emp_type === '3' ? 'Visiting Faculty' : Select_emp_type === '4' ? 'Dean' : Select_emp_type === '5' ? 'HOD' : Select_emp_type === '6' ? 'TA' : ''}</p>}</td>
                    <td> <h6 className='pt-3 pb-3'>Current Status</h6> {<p className='text-white bg-secondary rounded p-2  text-center'>{Select_approval_status}</p>}</td>
                    {/* <td> <h5 className='pt-3 pb-3'>Current Status</h5> {<h6>{Select_emp_status==='E'?'Enable':Select_emp_status==='D'?'Disable':''}</h6>}</td> */}
                  </tr>
                  <tr>
                    <td> <h6>Teacher Name</h6> {<p className='text-secondary'>{Select_teacher_name}</p>}</td>
                    <td> <h6>Course</h6> {<p className='text-secondary'>{Select_course_name}</p>}</td>
                    <td> <h6>School</h6> {<p className='text-secondary'>{Select_school_code}</p>}</td>
                  </tr>
                </tbody>
              </table>
      </Modal.Body>
      <Modal.Footer>
        <Button  variant="primary" onClick={handleClose}>Close</Button>
      </Modal.Footer>
    </Modal>






      
{/* end employee details model here */}




      <div className='container'>

      
        <div className='row mt-5 mb-2 border shadow-sm p-3  bg-white rounded'>
          <div className='col-12 rounded p-2 mb-2 bg-primary text-white text-uppercase'>

            <h6> Select Employee for TA Approval</h6>
          </div>
          <div className='col-6  '>
            <Select options={optionemployee} onChange={handle_Select_emp} />
          </div>
        </div>
        <div className='row'>
          <table className='table'>
            <thead >
              <tr scope='row'>
                <th scope="col">Employee Name</th>
                <th scope="col">Course Name</th>
                {/* <th scope="col">Department Name</th> */}
                <th scope="col">Status</th>
                <th scope="col">School</th>
                <th scope="col">Action  </th>
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


                  Employees_Data_table.map((item, index) => {
                    return (<>
                      <tr key={index}>
                        <td> <a className='text-dark style-none' href='#' school_code={item.school_code} course_name={item.course_name} emp_email={item.emp_email} dept_name={item.dept_name} teacher_name={item.teacher_name} value={item.fc_id} emp_name={item.employee_name} emp_type={item.emp_type} approval_status={item.fc_approval_status} onClick={handle_Employee_Details}>{item.employee_name}</a></td>
                        <td>{item.course_name}</td>
                        {/* <td>{item.dept_name}</td> */}
                        <td className='text-warn'>{item.fc_approval_status}</td>
                        <td>{item.school_code}</td>
                        <td>
                          {/* <button className="btn btn-primary text-uppercase" school_code={item.school_code} course_name={item.course_name} emp_email={item.emp_email} dept_name={item.dept_name} teacher_name={item.teacher_name} value={item.fc_id} emp_name={item.employee_name} emp_type={item.emp_type} approval_status={item.fc_approval_status} onClick={handle_Edit_Employee}>Update</button> */}
                          {
                            item.fc_approval_status==='Pending' ?
                            <Button variant="success text-uppercase " value={'Approved'}  emp_name={item.employee_name} fc_id={item.fc_id}  approval_status={item.fc_approval_status} onClick={UpdateTA}>Approve</Button>
                              :
                              ''
                          }
                          </td>
                      </tr>
                    </>)
                  })}
            </tbody>
          </table>
        </div>
      </div>

    </>
  );
}
export default TA_Rights_Approval;







// {
//   Show_ta_div ?
//     <div className='row mt-5 mb-2 border shadow-sm p-3 mb-5 bg-white rounded'>
//       <div className='col-12 mb-3'> <h4 className='text-secondary'>{Select_emp_name}</h4></div>
//       <div className='col-12 mb-3'> <h4 className='text-secondary'>{Fc_id}</h4></div>

//       <div className='col-5 '>
//         {/* <input type="text" name="employee_id" value={employee_id ? employee_id : ''} id="employee_id"></input> */}
//         <h6> Select Type</h6>
//         <Select options={optionType} defaultValue={Selected_emp_type} onChange={selectType} />
//       </div>
//       <div className='col-5'>
//         <h6> Select Status</h6>
//         <Select options={optionStatus} defaultValue={Selected_emp_status} onChange={selectStatus} />
//       </div>

//       <div className='col-5 mt-3'>
//         <button className="btn btn-primary text-uppercase" onClick={saveta}> Submit</button>
//       </div>
//       <div className='mt-3'>
//         {Msg != '' ?
//           <Alert key={'danger'} className='mt-2' variant={'danger'} >
//             {Msg}
//           </Alert>
//           :
//           <Alert hidden key={'danger'} variant={'danger'}>
//             {Msg}
//           </Alert>}
//       </div>
//     </div>
//     : ""
// }