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
// import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useNavigate } from 'react-router-dom';
import toast,{ Toaster } from 'react-hot-toast';
import { ControlCameraOutlined } from '@material-ui/icons';
import Button from 'react-bootstrap/Button';
import Modal from 'react-bootstrap/Modal';
import Badge from 'react-bootstrap/Badge';