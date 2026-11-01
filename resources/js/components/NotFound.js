import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";

const NotFound = () => {
    useEffect(() => {
        const hideSidebar = document.querySelector(".app-sidebar.app-sidebar3");

        if (hideSidebar) {
          hideSidebar.style.display = "none";
        }

        return () => {
          if (hideSidebar) {
            hideSidebar.style.display = ""; // Reset the display property when the component unmounts
          }
        };
      }, []);


  return (
    <div className="container text-center">
      <h1 className="display-4 mt-4">404 Not Found</h1>
      <p className="lead">The page you are looking for does not exist.</p>
      <p>Please check the URL or navigate back to the homepage.</p>
    
      <a href="/" className="btn btn-primary">
      Go to Homepage
      </a>
    </div>
  );
};

export default NotFound;
