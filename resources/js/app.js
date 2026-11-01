require('./bootstrap');
import React from 'react';
import ReactDOM from 'react-dom';
import '../css/app.css';
import Index from './index';
import { BrowserRouter } from "react-router-dom";
import { Routes, Route, HashRouter, Link, useParams } from "react-router-dom";
import '../css/ta_rights.css';
import SideBar from "./Layouts/SideBar";
import ImgUpload from './components/ImgUpload/ImgUpload';
import Upload_Crop_Image from './components/ImgUpload/Upload_Crop_Image';
import NotFound from "./components/NotFound";
const exampleElement = document.getElementById('example');

if (exampleElement) {
    ReactDOM.render(
        <BrowserRouter>
            {window.location.pathname === '/uploadimg' ? <Upload_Crop_Image/> : <SideBar/>}
        </BrowserRouter>,
        exampleElement
    );
}



var hours = 10; // to clear the localStorage after 1 hour
               // (if someone want to clear after 8hrs simply change hours=8)
var now = new Date().getTime();
var setupTime = localStorage.getItem('setupTime');
if (setupTime == null) {
    localStorage.setItem('setupTime', now)
} else {
    if(now-setupTime > hours*60*60*1000) {
        var loginRemember = {
            rememberUser: localStorage.getItem('rememberUser'),
            rememberPass: localStorage.getItem('rememberPass'),
            rememberMe: localStorage.getItem('rememberMe')
        };
        localStorage.clear();
        if (loginRemember.rememberUser !== null) {
            localStorage.setItem('rememberUser', loginRemember.rememberUser);
        }
        if (loginRemember.rememberPass !== null) {
            localStorage.setItem('rememberPass', loginRemember.rememberPass);
        }
        if (loginRemember.rememberMe !== null) {
            localStorage.setItem('rememberMe', loginRemember.rememberMe);
        }
        localStorage.setItem('setupTime', now);
    }
}