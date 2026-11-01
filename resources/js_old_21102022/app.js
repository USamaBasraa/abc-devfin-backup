
require('./bootstrap');
import React from 'react';
import ReactDOM from 'react-dom';
import Index from './index';
import { BrowserRouter } from "react-router-dom";
import { Routes, Route, HashRouter, Link, useParams } from "react-router-dom";
import '../css/ta_rights.css';
if (document.getElementById('example')) {
    ReactDOM.render(

        <>
            <BrowserRouter>

                <Index />
            </BrowserRouter>
        </>
        , document.getElementById('example'));
}