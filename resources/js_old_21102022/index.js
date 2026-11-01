import React from "react";
import { Routes,Route,} from "react-router-dom";
import Fetch_Attendance_Slots from "./components/Attendance/Fetch_Attendance_Slots";
import Fetch_Courses_From_Slots from "./components/Home/Fetch_Courses_From_Slots";
import AccessDenied from "./components/AccessDenied";
import Ta_Rights_Home from "./components/Rights_Managemnet/TA_Rights/Ta_Rights_Home"
import TA_Rights_Approval from "./components/Rights_Managemnet/TA_Rights/TA_Rights_Approval";
import NavMain from "./components/NavBar/NavMain";
const Index = () => {
    return(

        <>

            <Routes>
                <Route path="markattendance" element={<Fetch_Courses_From_Slots/>} />
                <Route path="attendanceslots" element={<Fetch_Attendance_Slots/>} />
                <Route path="noaccess" element={<AccessDenied/>} />
                <Route path="tarightsmanagement" element={<Ta_Rights_Home/>} />
                <Route path="taapproval" element={<TA_Rights_Approval/>} />
            </Routes>
          
        </>
    )
}
export default Index;