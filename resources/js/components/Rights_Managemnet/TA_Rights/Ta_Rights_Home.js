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
        http.get("/get_employee_data?co_id=" + co_id+"&approval_status='Approved'", pass_token)
            .then(res => {
                setEmployees_Data(res.data.data);
                setCourse_Name(res.data.data[0].course_name);
            })
    }, []);


       useEffect(() => {
        const style = document.createElement('style');

        style.innerHTML = `
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');


.e_panel.card.container {
    width: 100% !important;
    max-width: none !important;
    min-height: 100% !important;
    margin: 0 !important;
    padding: 40px 32px !important;
    border: 0 !important;
    border-radius: 10px !important;
    box-shadow: none !important;
    box-sizing: border-box !important;
    font-family: 'Montserrat', sans-serif !important;
}
  .app-content .side-app {
    padding-left: 0 !important;
    padding-right: 0 !important;
    padding-bottom: 0 !important;

    min-height: 100vh !important;
}

/* Header: title + course name */
.e_panel > .card_header {
    display: flex !important;
    flex-direction: row !important;
    align-items: flex-start !important;
    gap: 6px !important;
    width: 100% !important;
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
}

.e_panel > .card_header > .row {
   width: calc(50% - 8px) !important;
max-width: calc(50% - 8px) !important;
flex: 0 0 calc(50% - 8px) !important;
    margin: 0 !important;
    padding: 0 !important;
}

.e_panel > .card_header h4 {
    margin: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 22px !important;
    font-weight: 700 !important;
    line-height: 1.25 !important;
    color: #1e293b !important;
}

.e_panel > .card_header h5,
.e_panel > .card_header h5.text-right {
    margin: 0 !important;
    text-align: right !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
    color: #17675f !important;
}

/* Select employee card */
.e_panel > .row.mb-5 {
    display: block !important;
    width: 100% !important;
    margin: 0 0 24px !important;
    padding: 24px !important;
    background: #ffffff !important;
    border: 0 !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 6px rgba(15, 23, 42, 0.03) !important;
    box-sizing: border-box !important;
}

.e_panel > .row.mb-5 > .col-5 {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

.e_panel > .row.mb-5 .mt-4.mb-4 {
    margin: 0 !important;
}

.e_panel > .row.mb-5 .col-12 {
    width: 100% !important;
    padding: 0 !important;
}

.e_panel > .row.mb-5 h6 {
    margin: 0 0 8px !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
    color: #64748b !important;
    text-transform: uppercase !important;
}

/* React Select - works with generated react-select DOM without changing component props */
.e_panel > .row.mb-5 .col-12 > div[class*="css-"] {
    font-family: 'Montserrat', sans-serif !important;
}

.e_panel > .row.mb-5 .col-12 > div[class*="css-"] > div[class*="-control"] {
    min-height: 44px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    box-shadow: none !important;
    background: #ffffff !important;
}

.e_panel > .row.mb-5 .col-12 > div[class*="css-"] > div[class*="-control"]:hover {
    border-color: #cbd5e1 !important;
}

.e_panel > .row.mb-5 [class*="-singleValue"] {
    color: #1e293b !important;
    font-size: 14px !important;
    font-weight: 500 !important;
}

.e_panel > .row.mb-5 [class*="-placeholder"] {
    color: #94a3b8 !important;
    font-size: 14px !important;
}

/* Main rights area: preserve original DOM, change visual flow to Figma */
.e_panel > .row:not(.mb-5) {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;

    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;

    gap: 16px !important;
}

.e_panel > .row:not(.mb-5) > .col-5 {
    width: calc(50% - 8px) !important;
    max-width: calc(50% - 8px) !important;
    flex: 0 0 calc(50% - 8px) !important;

    margin: 0 !important;
    padding: 32px !important;

    background: #ffffff !important;
    border: 0 !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 8px rgba(15, 23, 42, 0.03) !important;

    box-sizing: border-box !important;
}

/* Original divider stays in JSX but is visually removed in Figma layout */
.e_panel > .row:not(.mb-5) > .col-1 {
    display: none !important;
}

/* Section headings - Bootstrap Alert visually converted to Figma heading */
.e_panel > .row:not(.mb-5) > .col-5 > .alert.alert-primary {
    width: 100% !important;
    margin: 0 0 24px !important;
    padding: 0 !important;
    background: transparent !important;
    color: #1e293b !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

.e_panel > .row:not(.mb-5) > .col-5 > .alert.alert-primary p {
    margin: 0 !important;
    padding: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
    color: #1e293b !important;
}

/* Available rights grid */
.e_panel > .row:not(.mb-5) > .col-5:first-child > .row {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 16px !important;
    margin: 0 !important;
}

.e_panel > .row:not(.mb-5) > .col-5:first-child .card_body {
    position: relative !important;
    width: 100% !important;
    max-width: 100% !important;
    min-height: 132px !important;
    margin: 0 !important;
    padding: 0 !important;
    background: rgba(15, 118, 110, 0.05) !important;
    border: 0 !important;
    border-radius: 14px !important;
    overflow: hidden !important;
    transition: background-color .18s ease, transform .18s ease, box-shadow .18s ease !important;
}

.e_panel > .row:not(.mb-5) > .col-5:first-child .card_body > .make_curser {
    position: relative !important;
    width: 100% !important;
    min-height: 132px !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 12px !important;
    padding: 20px !important;
    box-sizing: border-box !important;
}

.e_panel > .row:not(.mb-5) > .col-5:first-child .card_body:hover {
    background: rgba(15, 118, 110, 0.09) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 16px rgba(15, 118, 110, 0.08) !important;
}

.e_panel > .row:not(.mb-5) > .col-5:first-child .card_body center {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 44px !important;
    height: 44px !important;
    margin: 0 !important;
    background: #ffffff !important;
    border-radius: 8px !important;
}

.e_panel > .row:not(.mb-5) > .col-5:first-child .card_body img {
    display: block !important;
    width: 36px !important;
    height: 36px !important;
    max-width: 36px !important;
    max-height: 36px !important;
    object-fit: contain !important;
}

.e_panel > .row:not(.mb-5) > .col-5:first-child .card_body p {
    width: 100% !important;
    margin: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    line-height: 1.35 !important;
    color: #1e293b !important;
    text-align: center !important;
}

/* Figma add action trigger - visual only, original click target remains unchanged */
.e_panel > .row:not(.mb-5) > .col-5:first-child .card_body > .make_curser::after {
    content: "+";
    position: absolute !important;
    top: 12px !important;
    right: 12px !important;
    width: 22px !important;
    height: 22px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: #17675f !important;
    color: #ffffff !important;
    border-radius: 50% !important;
    font-family: Arial, sans-serif !important;
    font-size: 18px !important;
    font-weight: 400 !important;
    line-height: 1 !important;
    pointer-events: none !important;
}

/* Selected rights panel */
.e_panel > .row:not(.mb-5) > .col-5:last-child > .row {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 16px !important;
    margin: 0 !important;
}

.e_panel > .row:not(.mb-5) > .col-5:last-child .card_body {
    width: 100% !important;
    max-width: 100% !important;
    min-height: 72px !important;
    margin: 0 !important;
    padding: 0 !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    transition: border-color .18s ease, box-shadow .18s ease !important;
}

.e_panel > .row:not(.mb-5) > .col-5:last-child .card_body > .make_curser {
    position: relative !important;
    min-height: 72px !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 14px !important;
    padding: 16px 46px 16px 16px !important;
    box-sizing: border-box !important;
}

.e_panel > .row:not(.mb-5) > .col-5:last-child .card_body:hover {
    border-color: rgba(23, 103, 95, 0.35) !important;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
}

.e_panel > .row:not(.mb-5) > .col-5:last-child .card_body center {
    width: 38px !important;
    height: 38px !important;
    flex: 0 0 38px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    background: rgba(15, 118, 110, 0.07) !important;
    border-radius: 8px !important;
}

.e_panel > .row:not(.mb-5) > .col-5:last-child .card_body img {
    display: block !important;
    width: 30px !important;
    height: 30px !important;
    max-width: 30px !important;
    max-height: 30px !important;
    object-fit: contain !important;
}

.e_panel > .row:not(.mb-5) > .col-5:last-child .card_body p {
    flex: 1 !important;
    margin: 0 !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
    color: #1e293b !important;
    text-align: left !important;
}

/* Figma remove action - visual only */
.e_panel > .row:not(.mb-5) > .col-5:last-child .card_body > .make_curser::after {
    content: "−";
    position: absolute !important;
    right: 16px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 24px !important;
    height: 24px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #fb7185 !important;
    font-family: Arial, sans-serif !important;
    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;
    pointer-events: none !important;
}

/* Existing danger alert */
.e_panel .alert-danger {
    margin-top: 10px !important;
    border: 1px solid #fecaca !important;
    border-radius: 8px !important;
    background: #fef2f2 !important;
    color: #b91c1c !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
}

.e_panel .make_curser {
    cursor: pointer !important;
}

@media (max-width: 1200px) {
    .e_panel > .row:not(.mb-5) > .col-5:first-child > .row {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 991px) {
    .e_panel.card.container {
        padding: 28px 22px !important;
    }

    .e_panel > .row:not(.mb-5) > .col-5:first-child > .row {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    .e_panel > .row:not(.mb-5) > .col-5:last-child > .row {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 767px) {
    .e_panel.card.container {
        padding: 22px 16px !important;
    }

    .e_panel > .card_header h4 {
        font-size: 19px !important;
    }

    .e_panel > .row.mb-5,
    .e_panel > .row:not(.mb-5) > .col-5 {
        padding: 20px !important;
    }

    .e_panel > .row:not(.mb-5) > .col-5:first-child > .row {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 480px) {
    .e_panel.card.container {
        padding: 18px 12px !important;
    }

    .e_panel > .row:not(.mb-5) > .col-5:first-child > .row {
        grid-template-columns: 1fr !important;
    }
}
    @media (max-width: 991px) {
    .e_panel > .row:not(.mb-5) {
        flex-direction: column !important;
    }

    .e_panel > .row:not(.mb-5) > .col-5 {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
}
    /* LEFT GRID - dynamic items */
.e_panel > .row:not(.mb-5) > .col-5:first-child > .row {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 16px !important;

    width: 100% !important;
    height: auto !important;

    margin: 0 !important;
    align-content: start !important;
}

/* RIGHT GRID - dynamic items */
.e_panel > .row:not(.mb-5) > .col-5:last-child > .row {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 16px !important;

    width: 100% !important;
    height: auto !important;

    margin: 0 !important;
    align-content: start !important;
}

/* Both sections auto grow */
.e_panel > .row:not(.mb-5) > .col-5 {
    height: auto !important;
    min-height: 0 !important;
    overflow: visible !important;
}

/* Parent also auto grow */
.e_panel > .row:not(.mb-5) {
    height: auto !important;
    align-items: stretch !important;
    overflow: visible !important;
}
    .e_panel > .card_header > .row:last-child {
    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;
}


/* LEFT + RIGHT GRID - keep 3 items per row */
.e_panel > .row:not(.mb-5) > .col-5:first-child > .row,
.e_panel > .row:not(.mb-5) > .col-5:last-child > .row {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 16px !important;
    margin: 0 !important;
    width: 100% !important;
    height: auto !important;
    align-content: start !important;
}


/* BOTH LEFT + RIGHT ITEM CARDS */
.e_panel > .row:not(.mb-5) > .col-5 .card_body {
    position: relative !important;

    width: 100% !important;
    max-width: 100% !important;

    height: 132px !important;
    min-height: 132px !important;

    margin: 0 !important;
    padding: 0 !important;

    background: rgba(15, 118, 110, 0.05) !important;

    border: none !important;
    border-radius: 14px !important;

    overflow: hidden !important;
    box-sizing: border-box !important;
}


/* BOTH LEFT + RIGHT CARD CONTENT */
.e_panel > .row:not(.mb-5) > .col-5 .card_body > .make_curser {
    position: relative !important;

    width: 100% !important;
    height: 132px !important;
    min-height: 132px !important;

    display: flex !important;
    flex-direction: column !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 10px !important;

    padding: 18px !important;

    box-sizing: border-box !important;
}


/* ICON WRAPPER - BOTH SIDES */
.e_panel > .row:not(.mb-5) > .col-5 .card_body center {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    width: 44px !important;
    height: 44px !important;

    flex: 0 0 44px !important;

    margin: 0 !important;
    padding: 0 !important;

    background: #ffffff !important;
    border-radius: 8px !important;
}


/* ICON IMAGE */
.e_panel > .row:not(.mb-5) > .col-5 .card_body center img {
    display: block !important;

    width: 32px !important;
    height: 32px !important;

    max-width: 32px !important;
    max-height: 32px !important;

    object-fit: contain !important;
}


/* ITEM TITLE - BOTH SIDES */
.e_panel > .row:not(.mb-5) > .col-5 .card_body p {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 1.35 !important;

    color: #1e293b !important;

    text-align: center !important;

    white-space: normal !important;
    word-break: normal !important;
}


/* LEFT SIDE + BUTTON */
.e_panel > .row:not(.mb-5)
> .col-5:first-child
.card_body > .make_curser::after {

    content: "+" !important;

    position: absolute !important;
    top: 10px !important;
    right: 10px !important;

    width: 20px !important;
    height: 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #0f766e !important;
    color: #ffffff !important;

    border-radius: 50% !important;

    font-family: Arial, sans-serif !important;
    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1 !important;

    pointer-events: none !important;
}


/* RIGHT SIDE - REMOVE OLD CHIP STYLE */
.e_panel > .row:not(.mb-5)
> .col-5:last-child
.card_body {

    background: rgba(15, 118, 110, 0.05) !important;

    border: none !important;
    border-radius: 14px !important;

    height: 132px !important;
    min-height: 132px !important;
}


/* RIGHT SIDE CONTENT ALSO CENTERED LIKE LEFT */
.e_panel > .row:not(.mb-5)
> .col-5:last-child
.card_body > .make_curser {

    height: 132px !important;
    min-height: 132px !important;

    display: flex !important;
    flex-direction: column !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 10px !important;

    padding: 18px !important;
}


/* RIGHT ICON SAME AS LEFT */
.e_panel > .row:not(.mb-5)
> .col-5:last-child
.card_body center {

    width: 44px !important;
    height: 44px !important;

    flex: 0 0 44px !important;

    background: #ffffff !important;

    border-radius: 8px !important;
}


/* RIGHT TITLE CENTER */
.e_panel > .row:not(.mb-5)
> .col-5:last-child
.card_body p {

    flex: none !important;

    width: 100% !important;

    text-align: center !important;

    font-size: 12px !important;
    font-weight: 600 !important;
}


/* RIGHT REMOVE BUTTON - RED CIRCLE TOP RIGHT */
.e_panel > .row:not(.mb-5)
> .col-5:last-child
.card_body > .make_curser::after {

    content: "−" !important;

    position: absolute !important;
    top: 10px !important;
    right: 10px !important;

    transform: none !important;

    width: 20px !important;
    height: 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #fb4361 !important;
    color: #ffffff !important;

    border-radius: 50% !important;

    font-family: Arial, sans-serif !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    line-height: 1 !important;

    pointer-events: none !important;
}


/* HOVER - BOTH SIDES */
.e_panel > .row:not(.mb-5) > .col-5 .card_body:hover {
    background: rgba(15, 118, 110, 0.09) !important;

    transform: translateY(-1px) !important;

    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
}



/* =========================================================
   LEFT ITEMS = EXACTLY SAME STYLE AS RIGHT ITEMS
   Only + button remains green
========================================================= */

/* LEFT CARD */
.e_panel > .row:not(.mb-5)
> .col-5:first-child
.card_body {
    position: relative !important;

    width: 100% !important;
    max-width: 100% !important;

    height: 132px !important;
    min-height: 132px !important;

    margin: 0 !important;
    padding: 0 !important;

    background: rgba(15, 118, 110, 0.05) !important;

    border: none !important;
    border-radius: 14px !important;

    overflow: hidden !important;
    box-sizing: border-box !important;
}


/* LEFT CONTENT - SAME VERTICAL STYLE AS RIGHT */
.e_panel > .row:not(.mb-5)
> .col-5:first-child
.card_body > .make_curser {

    position: relative !important;

    width: 100% !important;
    height: 132px !important;
    min-height: 132px !important;

    display: flex !important;
    flex-direction: column !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 10px !important;

    padding: 18px !important;

    box-sizing: border-box !important;
}


/* LEFT ICON WRAPPER */
.e_panel > .row:not(.mb-5)
> .col-5:first-child
.card_body center {

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    width: 44px !important;
    height: 44px !important;
    flex: 0 0 44px !important;

    margin: 0 !important;
    padding: 0 !important;

    background: #ffffff !important;
    border-radius: 8px !important;
}


/* LEFT ICON */
.e_panel > .row:not(.mb-5)
> .col-5:first-child
.card_body center img {

    display: block !important;

    width: 32px !important;
    height: 32px !important;

    max-width: 32px !important;
    max-height: 32px !important;

    object-fit: contain !important;
}


/* LEFT TITLE - SAME AS RIGHT */
.e_panel > .row:not(.mb-5)
> .col-5:first-child
.card_body p {

    flex: none !important;

    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 1.35 !important;

    color: #1e293b !important;

    text-align: center !important;

    white-space: normal !important;
    word-break: normal !important;
}


/* LEFT + BUTTON */
.e_panel > .row:not(.mb-5)
> .col-5:first-child
.card_body > .make_curser::after {

    content: "+" !important;

    position: absolute !important;
    top: 10px !important;
    right: 10px !important;

    width: 20px !important;
    height: 20px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #0f766e !important;
    color: #ffffff !important;

    border-radius: 50% !important;

    font-family: Arial, sans-serif !important;
    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1 !important;

    pointer-events: none !important;
}


/* LEFT HOVER SAME AS RIGHT */
.e_panel > .row:not(.mb-5)
> .col-5:first-child
.card_body:hover {

    background: rgba(15, 118, 110, 0.09) !important;

    transform: translateY(-1px) !important;

    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
}
.app-content .side-app {
    min-height: 100vh !important;

    padding-left: 0 !important;
    padding-right: 0 !important;
    padding-bottom: 0 !important;
}

.e_panel.card.container {
}
    @media (min-width: 768px) {
    .app-content {
        margin-left: 250px;
        padding-bottom: 0 !important;
    }
}

.app-content .side-app {
    min-height: 100vh !important;

    padding-left: 0 !important;
    padding-right: 0 !important;
    padding-bottom: 0 !important;
}

.e_panel.card.container {
    min-height: auto !important;
    height: auto !important;
    background: transparent !important;
}

        `;

        document.head.appendChild(style);

        return () => {
            document.head.removeChild(style);
        };
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
          
            <Toaster position="bottom-left" reverseOrder={false} />
            <div className='card  e_panel container' >
                <div className='card_header'>
                    <div className='row  col-6 col-sm-12 col-md-6 col-xl-6' >
                        <h4>Teacher Assistant Rights Management </h4>
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