<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Response;

class ImageUploadController extends Controller
{
    /**
     * Upload image to local and remote server
     * Step 1: Validate the uploaded image
     * Step 2: Upload to local server
     * Step 3: Upload to remote server
     * Step 4: Return success or error
     */
    public function upload_img(Request $request)
    {
        // Step 1: Validate image file
        $request->validate([
            'image' => 'required|image|mimes:jpg|max:5048',
        ]);

        // Step 2: Check if image file exists in request
        if (!$request->has('image')) {
            return response()->json(['Failed' => 'No image file found']);
        }

        $image = $request->file('image');
        $extension = strtolower($image->getClientOriginalExtension());

        // Step 3: Check if file is JPG or JPEG
        if ($extension != 'jpg' && $extension != 'jpeg') {
            return response()->json(['Failed' => 'Extension Not Allowed! Only JPG/JPEG allowed']);
        }

        // Step 4: Create image filename from student registration number
        $image_name = strtoupper($request->input('student_reg_no')) . '.jpg';

        // Step 5: Upload to local server first
        $local_path = public_path() . '/students_pics';
        $source_file = $local_path . '/' . $image_name;

        // Step 6: Create directory if it doesn't exist
        if (!file_exists($local_path)) {
            @mkdir($local_path, 0777, true);
        }

        // Step 7: Move file to local server
        $upload_students_pics=$image->move($local_path, $image_name);

        // Step 8: Check if local upload was successful
        if (!$upload_students_pics) {
            return response()->json(['Failed' => 'Local upload failed. Image not uploaded.']);
        }
		
		
	 return response()->json(['success' => 'Success! Image uploaded successfully!']);


        // Step 9: Upload to remote server using cURL
        $remote_url = 'https://staff.bnu.edu.pk/upload-external-student-image.php';
        $file_path = str_replace('\\', '/', $source_file);

        // Step 10: Prepare data to send to remote server
        $post_data = [
            'image' => new \CURLFile($file_path, 'image/jpeg', $image_name),
            'student_reg_no' => $request->input('student_reg_no'),
            'filename' => $image_name
        ];

        // Step 11: Initialize cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Step 12: Execute cURL request and get response
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Step 13: Check if remote upload was successful
        $network_success = false;
        if ($http_code == 200) {
            $result = json_decode($response, true);
            if (isset($result['success']) && $result['success'] == true) {
                $network_success = true;
            }
        }
		
		
		dd($http_code,$network_success,$response,$curl_error);

        // Step 14: If remote upload failed, delete local file and return error
        if (!$network_success) {
            if (file_exists($source_file)) {
                @unlink($source_file);
            }
            $error_msg = 'Remote upload failed. HTTP Code: ' . $http_code;
            if ($curl_error) {
                $error_msg .= ', Error: ' . $curl_error;
            }
            return response()->json(['Failed' => $error_msg]);
        }

        // Step 15: Both uploads successful - return success message
        return response()->json(['success' => 'Success! Image uploaded successfully!']);
    }

    /**
     * Show image file
     */
    public function showimage(Request $request)
    {
        $fileName = $request->input('imagename');
        $path = public_path() . '/students_pics/' . $fileName;
        return response()->file($path);
    }

    /**
     * Check user token
     */
    public function check_user_token(Request $request)
    {
        $sql = "SELECT username FROM sisusers WHERE remember_token = '" . $request->input('token') . "'";
        $result = DB::select($sql);
        return $result;
    }
}
