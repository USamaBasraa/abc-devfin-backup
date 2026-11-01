import React, { useState, useEffect, Fragment } from 'react'
import 'bootstrap/dist/css/bootstrap.min.css';
import Form from 'react-bootstrap/Form';
import http from '../../http';
import { Link, useSearchParams } from 'react-router-dom';
import Alert from 'react-bootstrap/Alert';
import Select from 'react-select';
import EditOutlinedIcon from '@material-ui/icons/EditOutlined';
import PropagateLoader from "react-spinners/PropagateLoader";
import LocalPostOfficeIcon from '@material-ui/icons/LocalPostOffice';
import 'react-toastify/dist/ReactToastify.css';
import { useNavigate } from 'react-router-dom';
import toast,{ Toaster } from 'react-hot-toast';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import Badge from 'react-bootstrap/Badge';
import Table from 'react-bootstrap/Table';
 const TA_RIGHTS_INTERNAL_CSS = `
.ta-rights-approval-page {
    --ta-primary: #17675f;
    --ta-primary-strong: #0f766e;
    --ta-page-bg: rgba(15, 118, 110, 0.05);
    --ta-heading: #1e293b;
    --ta-body: #334155;
    --ta-muted: #64748b;
    --ta-subtle: #94a3b8;
    --ta-border: #e2e8f0;
    --ta-table-border: #e2e5ea;
    --ta-white: #ffffff;

    width: 100%;
    min-height: 689px;
    padding: 40px 32px;
    background: var(--ta-page-bg);
    border-radius: 10px;
    font-family: "Montserrat", "Segoe UI", Arial, sans-serif;
}


/* =========================
   OUTER WRAPPER
========================= */

.ta-rights-approval-page > .ta-rights-shell {
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}

.ta-rights-approval-page .ta-rights-column {
    padding: 0 !important;
}


/* =========================
   MAIN CARD
========================= */

.ta-rights-card {
    overflow: visible !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}


/* =========================
   PAGE HEADING
========================= */

.ta-rights-card > .ta-rights-card-header {
    min-height: 27px;
    margin: 0;
    padding: 0 0 22px !important;
    border: 0 !important;
    background: transparent !important;
}

.ta-rights-card-title {
    margin: 0 !important;
    color: var(--ta-heading) !important;
    font-size: 22px !important;
    font-weight: 700 !important;
    line-height: normal !important;
    letter-spacing: 0;
}


/* =========================
   SELECT EMPLOYEE CARD
========================= */

.ta-rights-card > .ta-rights-card-body {
    position: relative;
    margin: 0 0 22px;
    padding: 24px !important;
    border: 0;
    border-radius: 12px;
    background: var(--ta-white);
}

.ta-rights-card > .ta-rights-card-body::before {
    content: "SELECT EMPLOYEE FOR TA";
    display: block;
    width: 100%;
    margin: 0 0 8px;

    color: var(--ta-muted);
    font-size: 11px;
    font-weight: 600;
    line-height: normal;
    text-transform: uppercase;
}

.ta-rights-select-wrap {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
    padding: 0 !important;
}

.ta-rights-select {
    width: 100%;
    color: var(--ta-heading);
    font-size: 14px;
}


/* =========================
   REACT SELECT
========================= */

.ta-rights-select__control {
    min-height: 44px !important;

    border: 1px solid var(--ta-border) !important;
    border-radius: 8px !important;

    background: var(--ta-white) !important;

    box-shadow: none !important;

    transition:
        border-color 0.15s ease,
     
}

.ta-rights-select__control:hover,
.ta-rights-select__control--is-focused {
    border-color: var(--ta-primary) !important;

}

.ta-rights-select__value-container {
    min-height: 42px;

    padding: 0 16px !important;
}

.ta-rights-select__single-value,
.ta-rights-select__input-container,
.ta-rights-select__placeholder {
    margin: 0 !important;

    color: var(--ta-heading) !important;

    font-family:
        "Montserrat",
        "Segoe UI",
        Arial,
        sans-serif;

    font-size: 14px !important;
    font-weight: 500;
}

.ta-rights-select__input-container {
    padding: 0 !important;
}

.ta-rights-select__indicator-separator {
    display: none;
}

.ta-rights-select__dropdown-indicator {
    padding: 0 12px !important;

    color: #94a3b8 !important;
}

.ta-rights-select__dropdown-indicator svg {
    width: 16px;
    height: 16px;
}

.ta-rights-select__menu {
    z-index: 20 !important;

    overflow: hidden;

    margin-top: 6px !important;

    border: 1px solid var(--ta-border);
    border-radius: 8px !important;

    background: var(--ta-white);

}

.ta-rights-select__option {
    padding: 10px 16px !important;

    color: var(--ta-body) !important;

    font-size: 13px !important;
    font-weight: 500;
}

.ta-rights-select__option--is-focused {
    background:
        rgba(15, 118, 110, 0.06) !important;
}

.ta-rights-select__option--is-selected {
    color: var(--ta-white) !important;

    background: var(--ta-primary) !important;
}


/* =========================
   TABLE CONTAINER
========================= */

.ta-rights-card > .ta-rights-card-footer {
    position: relative;

    overflow-x: auto;

    padding: 0 !important;

    border: 1px solid var(--ta-table-border) !important;
    border-radius: 10px !important;

    background: var(--ta-white) !important;
}

.ta-rights-table {
    min-width: 760px;

    margin: 0 !important;

    border: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;

    background: var(--ta-white);
}


/* =========================
   TABLE HEADER
========================= */

.ta-rights-table > thead > tr > th {
    padding: 12px 16px !important;

    border: 0 !important;

    background: var(--ta-primary) !important;

    color: var(--ta-white) !important;

    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: normal !important;

    text-transform: none;

    vertical-align: middle;

    white-space: nowrap;
}

.ta-rights-table > thead > tr > th:first-child {
    border-top-left-radius: 9px;
}

.ta-rights-table > thead > tr > th:last-child {
    border-top-right-radius: 9px;
}


/* =========================
   TABLE BODY
========================= */

.ta-rights-table > tbody > tr > td {
    padding: 14px 16px !important;

    border-right: 0 !important;
    border-left: 0 !important;
    border-top: 0 !important;

    border-bottom:
        1px solid #eef2f6 !important;

    background: var(--ta-white);

    color: var(--ta-body);

    font-size: 13px;
    font-weight: 500;
    line-height: 1.45;

    vertical-align: middle;
}

.ta-rights-table > tbody > tr:last-child > td {
    border-bottom: 0 !important;
}

.ta-rights-table.table-hover > tbody > tr:hover > * {
    --bs-table-accent-bg:
        rgba(15, 118, 110, 0.035);

    color: var(--ta-body);
}


/* =========================
   EMPLOYEE LINK
========================= */

.ta-rights-employee-link {
    color: var(--ta-primary) !important;

    font-weight: 600;

    text-decoration: none !important;

    transition: color 0.15s ease;
}

.ta-rights-employee-link:hover,
.ta-rights-employee-link:focus {
    color: var(--ta-primary-strong) !important;

    text-decoration: underline !important;
}


/* =========================
   STATUS BADGE
========================= */

.ta-rights-status-badge {
    display: inline-flex !important;

    align-items: center;

    min-height: 26px;

    padding: 5px 10px !important;

    border:
        1px solid rgba(245, 158, 11, 0.18);

    border-radius: 999px !important;

    background:
        rgba(245, 158, 11, 0.10) !important;

    color: #a16207 !important;

    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
}


/* =========================
   APPROVE BUTTON
========================= */

.ta-rights-approve-btn {
    min-height: 34px;

    padding: 8px 14px !important;

    border:
        1px solid var(--ta-primary) !important;

    border-radius: 8px !important;

    background:
        var(--ta-primary) !important;

    color:
        var(--ta-white) !important;

    box-shadow: none !important;

    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1.2;

    letter-spacing: 0.02em;
}

.ta-rights-approve-btn:hover,
.ta-rights-approve-btn:focus,
.ta-rights-approve-btn:active {
    border-color:
        var(--ta-primary-strong) !important;

    background:
        var(--ta-primary-strong) !important;

}


/* =========================
   MODAL
========================= */

.ta-rights-modal .modal-content {
    overflow: hidden;

    border: 1px solid #e2e8f0;

    border-radius: 16px;


    font-family:
        "Montserrat",
        "Segoe UI",
        Arial,
        sans-serif;
}

.ta-rights-modal-header {
    padding: 24px 28px !important;

    border-bottom:
        1px solid #eef2f6 !important;

    background: #ffffff;
}

.ta-rights-modal-title-name {
    margin: 0 0 8px !important;

    color: #1e293b !important;

    font-size: 20px !important;
    font-weight: 700 !important;
}

.ta-rights-modal-email {
    display: flex;

    align-items: center;

    gap: 6px;

    margin: 0 !important;

    color: #64748b !important;

    font-size: 13px;
    font-weight: 500;
}

.ta-rights-modal-email svg {
    color: #17675f;
}

.ta-rights-modal-body {
    padding:
        10px 28px 22px !important;
}


/* =========================
   MODAL DETAILS TABLE
========================= */

.ta-rights-details-table {
    margin: 0 !important;
}

.ta-rights-details-table td {
    width: 33.333%;

    padding:
        18px 14px !important;

    border-color:
        #eef2f6 !important;

    vertical-align: top;
}

.ta-rights-details-table h6 {
    margin:
        0 0 7px !important;

    padding: 0 !important;

    color: #64748b;

    font-size: 10px;
    font-weight: 700;
    line-height: 1.3;

    letter-spacing: 0.02em;

    text-transform: uppercase;
}

.ta-rights-details-table p {
    margin: 0 !important;

    color: #1e293b !important;

    font-size: 13px;
    font-weight: 600;
    line-height: 1.45;
}

.ta-rights-details-table
.ta-rights-status-badge {
    margin-top: 1px;
}


/* =========================
   MODAL FOOTER
========================= */

.ta-rights-modal-footer {
    padding:
        16px 28px 22px !important;

    border-top: 0 !important;
}

.ta-rights-close-btn {
    min-width: 92px;

    padding:
        9px 18px !important;

    border:
        1px solid #17675f !important;

    border-radius: 8px !important;

    background:
        #17675f !important;

    color:
        #ffffff !important;

    font-size: 12px !important;
    font-weight: 700 !important;

    box-shadow: none !important;
}

.ta-rights-close-btn:hover,
.ta-rights-close-btn:focus,
.ta-rights-close-btn:active {
    border-color:#0f766e !important;

    background:#0f766e !important;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 991.98px) {

    .ta-rights-approval-page {
        padding: 28px 20px;
    }

}


@media (max-width: 767.98px) {

    .ta-rights-approval-page {
        min-height: auto;

        padding: 24px 16px;

        border-radius: 8px;
    }

    .ta-rights-card-title {
        font-size: 19px !important;
    }

    .ta-rights-card
    > .ta-rights-card-body {
        padding: 18px !important;
    }

    .ta-rights-details-table,
    .ta-rights-details-table tbody,
    .ta-rights-details-table tr,
    .ta-rights-details-table td {
        display: block;

        width: 100%;
    }

    .ta-rights-details-table td {
        padding:
            14px 0 !important;
    }

}
`;
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
    label: Select_emp_type === '1' ? 'Staff' : Select_emp_type === '2' ? 'Faculty' : Select_emp_type === '3' ? 'Visiting Faculty' : Select_emp_type === '4' ? 'Dean' : Select_emp_type === '5' ? 'HOD' : Select_emp_type === '6' ? 'TA' : ''};
  const Selected_emp_status = {
    value: Select_approval_status,
    label: Select_approval_status === 'E' ? 'Enable' : Select_approval_status === 'D' ? 'Disable' : ''};
  var em_token=window.localStorage.getItem('em_token').trim();
  let pass_token = ({ headers: { "Authorization": `Bearer ${em_token}`}})
  // var pass_token=window.localStorage.getItem('em_token').replace(/\\n/g, '');
  const get_employee = (url) => {
    setLoading(true);
    http.get(url,pass_token)
    .then(res => {
      setLoading(false)
      setEmployees_Data(res.data.data);
      setEmployees_Data_table(res.data.data);
    })

  }
  var emp_login_email = window.localStorage.getItem('emp_email').trim();
  // var auth_check = "Ivan.duran@bnu.edu.pk";
  var auth_check = "haider.raza@bnu.edu.pk";

  useEffect(() => {

    document.title = "TA Approval"
    if (emp_login_email != auth_check && 1 != 1) {
      navigate("/noaccess")
  }
  else {
    get_employee("/get_employee_data?approval_status='Pending'")
  }
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
  }
  const UpdateTA = (e) => {
    const emp_name=e.currentTarget.getAttribute("emp_name")
    setLoading(true);
    http.get('/updateapproval?fc_id='+e.currentTarget.getAttribute("fc_id")+'&approval_status='+e.target.value,pass_token)
    .then(res => {
      setLoading(false);
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
<style>{TA_RIGHTS_INTERNAL_CSS}</style>
    <Toaster  position="bottom-left"reverseOrder={false}/>

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
                    <td> <h6 className='pt-3 pb-3'>Current Status</h6>
                    {<h5><Badge pill  bg="secondary text-white"> {Select_approval_status}</Badge></h5>}
                     </td>
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
      <div className='container-fluid ta-approval-component'>
        <style>{`
  .custom-row {
    margin-top: 50px !important;
    border: none !important;
    padding: 0 !important;
    box-shadow: none !important;
  }
.ta-approval-component {
    min-height: calc(100vh - 104px);
    padding-top: 1px;
    background-color: #f2f6f9 !important;
}.ta-approval-component .custom-row {
    background-color: transparent !important;
}.ta-approval-component .card {
    background-color: #ffffff !important;
}.side-app {
background-color: #f2f6f9 !important;
}`}</style>
        <div className='row mt-2 mb-2 border shadow-sm p-2  bg-white rounded custom-row' style={{
    marginTop: '50px !important',
    border: 'none !important',
    padding: '0 !important',
}}>
        <div className="col-md-12 col-lg-12" style={{
    paddingLeft: 0,
    paddingRight: 0,
    boxShadow: 'none'
}}>
								<div className="card overflow-hidden" style={{marginBottom:0,boxShadow:'none'}}>
                    <style>{`
  .headBg {
      background-color: #17675F !important;
  }
`}</style>
									<div className="card-header bg-primary headBg">
										<h3 className="card-title  text-white">Select Employee for TA Approval</h3>
										{/* <div className="card-options ">
											<a href="#" className="card-options-collapse mr-2" data-toggle="card-collapse"><i className="fe fe-chevron-up text-white"></i></a>
											<a href="#" className="card-options-remove" data-toggle="card-remove"><i className="fe fe-x text-white"></i></a>
										</div> */}
									</div>
									<div className="card-body">
                  <div className='col-6 pb-5 '>
                <Select options={optionemployee} onChange={handle_Select_emp} />
              </div>
                  </div>
									<div className="card-footer">
                  {/* Table start here */}
                  <Table  bordered hover>
                  <thead >
              <tr scope='row'>
                <th scope="col">Employee Name</th>
                <th scope="col">Course Name</th>
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
                  Employees_Data_table.map((item,index) => {
                    return (<>
                      <tr key={index}>
                        <td> <a className='text-dark style-none' href='#' school_code={item.school_code} course_name={item.course_name} emp_email={item.emp_email} dept_name={item.dept_name} teacher_name={item.teacher_name} value={item.fc_id} emp_name={item.employee_name} emp_type={item.emp_type} approval_status={item.fc_approval_status} onClick={handle_Employee_Details}>{item.employee_name}</a></td>
                        <td>{item.course_name}</td>
                        {/* <td>{item.dept_name}</td> */}
                        <td>
                        <Badge pill  bg="secondary text-white"> {item.fc_approval_status}</Badge>
                          </td>
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
                  </>
                    )
                  })}
            </tbody>
                    </Table>
    {/* Table end  here */}
									</div>
								</div>
							</div>

        </div>

      </div>

    </>
  );
}
export default TA_Rights_Approval;
