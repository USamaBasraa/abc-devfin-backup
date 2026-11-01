import React from "react";
import Alert from 'react-bootstrap/Alert';
import { useNavigate,Link} from 'react-router-dom';

const AccessDenied=()=>{
    const navigate = useNavigate();
    const goback=()=>{

        navigate(-1)
    }

    return (<>
    
    <h1 className="display-1">User Permission Denied</h1>
    {/* <button className="btn btn-secondary" onClick={goback}>Go Back</button> */}
    <a className="btn btn-primary" href='/'> Home</a>
    
    
    </>);
}

export default AccessDenied