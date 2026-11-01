import React, { useEffect, useState } from "react";
import http from "../http";
import toast, { Toaster } from "react-hot-toast";
import { useSearchParams } from "react-router-dom";
import { useNavigate } from "react-router-dom";

const isValidFileUploaded = (file) => {
    const validExtensions = ["jpeg", "jpg", "JPEG", "JPG"];
    const fileExtension = file.type.split("/")[1];
    return validExtensions.includes(fileExtension);
};

const ImgUpload = () => {
    let [searchParams, setSearchParams] = useSearchParams();
    const [selectedImage, setSelectedImage] = useState();
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
        'zonia.akbar'
    ];

    useEffect(() => {
        document.title = "Upload Student Photo";
        http.get("/checkusertoken?token=" + searchParams.get("token")).then((res) => {
            if (!auth_username.includes(res.data[0].username)) {
                navigate("/noaccess");
            }
        });
    }, []);
    const handlePicInput = () => {
        if (!selectedImage) return;
        
        if (isValidFileUploaded(selectedImage)) {
            const data = new FormData();
            data.append("image", selectedImage);
            http.post("/upload_img?student_reg_no=" + student_reg_no, data).then((res) => {
                if (res.data.success) {
                    toast.success(res.data.success);
                }
            });
        } else {
            toast.error("Please select JPEG or JPG type image only");
        }
    };

    return (
        <>
            <Toaster position="top-center" reverseOrder={false} />
            <div className="container-fluid p-4">
                <div className="row justify-content-center">
                    <div className="col-12 col-md-8">
                        <div className="card shadow-sm">
                            {/* Header Section - Buttons Only */}
                            <div className="card-header bg-white border-0 p-3">
                                <div className="d-flex justify-content-center align-items-center flex-wrap gap-2">
                                    <label className="btn btn-primary" style={{ cursor: 'pointer' }}>
                                        <input
                                            type="file"
                                            name="myImage"
                                            accept="image/jpeg,image/jpg"
                                            onChange={(e) => setSelectedImage(e.target.files[0])}
                                            style={{ display: 'none' }}
                                        />
                                        Choose Image
                                    </label>
                                    
                                    {selectedImage && (
                                        <button className="btn btn-primary" onClick={handlePicInput}>
                                            Upload Photo
                                        </button>
                                    )}
                                    
                                    <a className="btn btn-dark" href={searchParams.get("url")}>
                                        Go Back
                                    </a>
                                </div>
                            </div>

                            {/* Body Section - Image Preview */}
                            <div className="card-body text-center p-4">
                                {selectedImage ? (
                                    <img
                                        alt="Selected"
                                        src={URL.createObjectURL(selectedImage)}
                                        style={{
                                            width: '260px',
                                            height: '260px',
                                            objectFit: 'cover',
                                            borderRadius: '50%',
                                            border: '5px solid #ddd'
                                        }}
                                    />
                                ) : (
                                    <img
                                        alt="Current"
                                        src={http_img}
                                        onError={(e) => {
                                            e.target.src = 'https://via.placeholder.com/260?text=No+Image';
                                        }}
                                        style={{
                                            width: '260px',
                                            height: '260px',
                                            objectFit: 'cover',
                                            borderRadius: '50%',
                                            border: '5px solid #ddd'
                                        }}
                                    />
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ImgUpload;
