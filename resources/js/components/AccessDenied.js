import React from "react";
import Alert from 'react-bootstrap/Alert';
import { useNavigate,Link} from 'react-router-dom';
const AccessDenied=()=>{
    const navigate = useNavigate();
    const goback=()=>{

        navigate(-1)
    }
    return (<>
   <div class="d-flex mt-5 align-items-center justify-content-center ">
            <div className="text-center">
                <h1 className="display-1 fw-bold">101</h1>
                <p className="fs-3"> <span class="text-danger">Opps!</span> Permission Denied.</p>
                <p className="lead">
                    You don't have permission to access this page
                  </p>
                <a href="/" className="btn btn-primary">Go Home</a>
            </div>
        </div>

    
    </>);
}

export default AccessDenied