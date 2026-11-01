import React, { useState, useEffect } from 'react'
import 'bootstrap/dist/css/bootstrap.min.css';
import Form from 'react-bootstrap/Form';
import http from '../../http';
import { Link, useSearchParams } from 'react-router-dom';
import Alert from 'react-bootstrap/Alert';
import Select from 'react-select';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal'
import toast, { Toaster } from 'react-hot-toast';
import PropagateLoader from "react-spinners/PropagateLoader";
import { received } from 'laravel-mix/src/Log';
import NavMain from '../../NavBar/NavMain';
const Ta_Rights = () => {
    let [loading, setLoading] = useState(false);
    const [showModel, setShowModel] = useState(false);
    const handleClose = () => setShowModel(false);
    const handleShow = () => setShowModel(true);
    const [CMS_Category_Data, setCMS_Category_Data] = useState([]);
    const [emp_id, setEmp_id] = useState();
    let [searchParams, setSearchParams] = useSearchParams();
    let co_id = searchParams.get('co_id');
    const [Employees_Data, setEmployees_Data] = useState([]);
    const [Course_Name, setCourse_Name] = useState('');
    const [ShowMsg, setShowMsg] = useState('');
    const [Show, setShow] = useState(false);
    var optionemployee = Employees_Data.map(item => ({ value: item.employee_id, label: item.employee_name + ' ( ' + item.emp_ad_username + ' )', emp_username: item.emp_ad_username }))
    let pass_token = ({ headers: { "Authorization": `Bearer ${searchParams.get('token')}` } })
    useEffect(() => {
        document.title = "TA Rights Management"
        http.get('/get_employee_data?co_id=' + co_id, pass_token)
            .then(res => {
                setEmployees_Data(res.data.data);
                setCourse_Name(res.data.data[0].course_name);
            })
    }, []);
    const get_cms_categories = () => {
        try {
            http.get('/ta_rights?emp_id=' + emp_id + '&co_id=' + co_id, pass_token)
                .then(res => {

                    setCMS_Category_Data(res.data.data);
                })
        }
        catch (err) {
            toast.error(err);
        }
    }
    const handle_ta_rights = (e) => {
        const selected_status = e.currentTarget.getAttribute("selected_status");
        const category_title = e.currentTarget.getAttribute("category_title");
        setLoading(true)
        http.get('/ta_rights?co_id=' + co_id + '&emp_id=' + emp_id + '&status=' + selected_status + '&categ_id=' + e.currentTarget.id, pass_token)
            .then(res => {
                get_cms_categories(selected_status);
                setLoading(false)
                toast.success(selected_status === '0' ? `${category_title} is assigned Successfully` : `${category_title}  is removed `);

            }
            )
    }
    const handle_Select_emp = (e) => {
        setEmp_id(e.value)
        setShowMsg('')
        setLoading(true)
        http.get('/ta_rights?emp_id=' + e.value + '&co_id=' + co_id, pass_token)
            .then(res => {
                setCMS_Category_Data(res.data.data);
                setLoading(false);
            })
        setShow(true);
    };
    return (
        <>
            <NavMain />
            <Toaster position="bottom-left" reverseOrder={false} />
            <div className='card  e_panel container' >
                <div className='card_header'>
                    <div className='row  col-6 col-sm-12 col-md-6 col-xl-6' >
                        <h4>Teacher Assistant Rights Management5 </h4>
                    </div>
                    <div className='row  col-6 col-sm-12  col-md-6 col-xl-6' >
                        <h5 className='text-right'>{Course_Name}</h5>
                    </div>
                </div>
                <div className='row mb-5'>
                    <div className='col-5  offset-3'>
                        <div className='mt-4 mb-4 content-align-center'>
                            <div className='col-12'>
                                <h6> Select Employee for TA</h6>
                                <Select options={optionemployee} onChange={handle_Select_emp} />
                                {ShowMsg != '' ?
                                    <Alert key={'danger'} className='mt-2' variant={'danger'}>
                                        {ShowMsg}
                                    </Alert>
                                    :
                                    <Alert hidden key={'danger'} variant={'danger'}>
                                        {ShowMsg}
                                    </Alert>
                                }
                            </div>
                        </div>
                    </div>
                </div>
                {      // if loaing true show loading else show table
                    loading ?
                        <PropagateLoader loading={loading} height={30} width={10} margin={2} size={20} cssOverride={{
                            position: "absolute",
                            top: "50%",
                            left: "50%",
                            marginTop: "50px",
                            transform: "translate(-50%, -50%)"
                        }} /> :
                        Show ?
                            <div className='row' >
                                <div className='col-5 '>
                                    <Alert variant="primary ">
                                        <p>
                                            Please click on the category that you want to assign to TA
                                        </p>
                                    </Alert>
                                    <div className='row'>
                                        {CMS_Category_Data.map((item, index) =>
                                        (item.selected_status == '0' ?
                                            <div className="card_body col-3 mb-5" key={index} >
                                                <div onClick={handle_ta_rights} id={item.id} category_title={item.category_title} selected_status={item.selected_status} className='pe-auto make_curser' >
                                                    <center  ><img src={item.category_icon} /></center>
                                                    <p className='text-center text-decoration-none'>{item.category_title}</p>
                                                </div>
                                                {/* </Link> */}
                                            </div>
                                            : '')
                                        )
                                        }
                                    </div>
                                </div>
                                <div className='col-1'>

                                    <div className="vr" style={{ height: '350px' }}></div>
                                </div>
                                <div className='col-5'>
                                    <Alert variant="primary ">
                                        <p>Selected Rights assigned to TA</p>
                                    </Alert>
                                    <div className='row'>
                                        {CMS_Category_Data.map((item, index) =>
                                        (item.selected_status == '1' ?
                                            <div className="card_body col-3 mb-5" key={index} >
                                                <div onClick={handle_ta_rights} id={item.id} category_title={item.category_title} selected_status={item.selected_status} className='pe-auto make_curser' >
                                                    <center  ><img src={item.category_icon} /></center>
                                                    <p className='text-center text-decoration-none'>{item.category_title}</p>
                                                </div>
                                                {/* </Link> */}
                                            </div> : '')
                                        )
                                        }
                                    </div>
                                </div>
                            </div>
                            :
                            ''
                }

            </div>
        </>
    )
}
export default Ta_Rights;