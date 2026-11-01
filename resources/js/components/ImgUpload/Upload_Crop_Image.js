import React from "react";
import { Button, Slider } from "@material-ui/core";
import { useState, useEffect } from "react";
import Cropper from "react-easy-crop";
import getCroppedImg from "./utils/cropImage";
import { generateDownload, UploadCroppedImg } from "./utils/cropImage";
import { dataURLtoFile } from "./utils/dataURLtoFile";
import toast, { Toaster } from "react-hot-toast";
import { useSearchParams } from "react-router-dom";
import { useNavigate } from "react-router-dom";
import CameraAltOutlinedIcon from '@mui/icons-material/CameraAltOutlined';
import UploadOutlinedIcon from '@mui/icons-material/UploadOutlined';
import DownloadForOfflineOutlinedIcon from '@mui/icons-material/DownloadForOfflineOutlined';
import http from "../http";
function Upload_Crop_Image() {
    const inputRef = React.useRef();
    let [searchParams, setSearchParams] = useSearchParams();
    const navigate = useNavigate();
    const student_reg_no = searchParams.get("student_reg_no").toUpperCase();
    const BASE_URL = window.location.origin;
    const http_img = `${BASE_URL}/students_pics/${student_reg_no}.jpg`;
    const auth_username = [
        "haider.raza",
        "muhammad.hamid",
        "muhammad.nabeel",
        "zain.ali",
        "bilal.saleem",
        "zonia.akbar",
    ];
    const triggerFileSelectPopup = () => inputRef.current.click();
    const [image, setImage] = React.useState(null);
    const [croppedArea, setCroppedArea] = React.useState(null);
    const [crop, setCrop] = React.useState({ x: 0, y: 0 });
    const [zoom, setZoom] = React.useState(1);

    const onCropComplete = (croppedAreaPercentage, croppedAreaPixels) => {
        setCroppedArea(croppedAreaPixels);
    };

    useEffect(() => {
        document.title = "Upload Student Photo";
        http.get("/checkusertoken?token=" + searchParams.get("token")).then((res) => {
            if (!auth_username.includes(res.data[0].username)) {
                navigate("/noaccess");
            }
        });
    }, []);

    const onSelectFile = (event) => {
        if (event.target.files && event.target.files.length > 0) {
            const reader = new FileReader();
            reader.readAsDataURL(event.target.files[0]);
            reader.addEventListener("load", () => {
                setImage(reader.result);
            });
        }
    };

    const onDownload = async () => {
        generateDownload(image, croppedArea);
    };
    const onUpload = () => {
        UploadCroppedImg(image, croppedArea, student_reg_no);
    };
    return (
        <div className="container-fluid p-4">
            <Toaster position="top-center" reverseOrder={false} />
            <div className="row justify-content-center">
                <div className="col-12 col-lg-10">
                    <div className="card shadow-sm">
                        {/* Header Section - Buttons Only */}
                        <div className="card-header bg-white border-0 p-3">
                            <div className="d-flex justify-content-center align-items-center flex-wrap gap-2">
                                <input
                                    type="file"
                                    ref={inputRef}
                                    onChange={onSelectFile}
                                    style={{ display: "none" }}
                                    accept=".jpg,.jpeg"
                                />
                                
                                <Button
                                    variant="contained"
                                    onClick={triggerFileSelectPopup}
                                    startIcon={<CameraAltOutlinedIcon/>}
                                >
                                    Choose Image
                                </Button>
                                
                                {image && (
                                    <>
                                        <Button
                                            variant="contained"
                                            onClick={onDownload}
                                            startIcon={<DownloadForOfflineOutlinedIcon/>}
                                        >
                                            Download
                                        </Button>
                                        <Button
                                            variant="contained"
                                            onClick={onUpload}
                                            startIcon={<UploadOutlinedIcon/>}
                                        >
                                            Upload
                                        </Button>
                                    </>
                                )}
                            
                                <a className="btn btn-dark" href={searchParams.get("url")}>
                                    Go Back
                                </a>
                            </div>

                            {/* Zoom Control */}
                            {image && (
                                <div className="mt-3 d-flex align-items-center justify-content-center gap-2 p-2 bg-light rounded">
                                    <span>Zoom:</span>
                                    <Slider
                                        value={zoom}
                                        min={1}
                                        max={3}
                                        step={0.1}
                                        onChange={(e, newValue) => setZoom(newValue)}
                                        style={{ width: '200px', color: '#4555C3' }}
                                    />
                                    <span className="fw-bold">{zoom.toFixed(1)}x</span>
                                </div>
                            )}
                        </div>

                        {/* Body Section - Cropper */}
                        <div className="card-body p-3">
                            <div style={{
                                position: 'relative',
                                width: '100%',
                                height: '500px',
                                background: '#f5f5f5',
                                borderRadius: '8px',
                                overflow: 'hidden'
                            }}>
                                {image ? (
                                    <div className="cropper" style={{
                                        position: 'relative',
                                        width: '100%',
                                        height: '100%'
                                    }}>
                                        <Cropper
                                            image={image}
                                            crop={crop}
                                            zoom={zoom}
                                            aspect={5 / 5}
                                            onCropChange={setCrop}
                                            onZoomChange={setZoom}
                                            onCropComplete={onCropComplete}
                                        />
                                    </div>
                                ) : (
                                    <div className="cropper" style={{
                                        position: 'relative',
                                        width: '100%',
                                        height: '100%'
                                    }}>
                                        <Cropper
                                            image={http_img}
                                            crop={crop}
                                            zoom={zoom}
                                            aspect={1}
                                            onCropChange={setCrop}
                                            onZoomChange={setZoom}
                                            onCropComplete={onCropComplete}
                                        />
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Upload_Crop_Image;
