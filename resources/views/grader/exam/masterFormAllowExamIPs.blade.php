@extends('layouts.app')
@section('content')
<style>
    .disclaimer{
        color:#4c62ff;
    }
</style>
<div class="container">
    <div class="card" style="padding: 10px 40px;">
        <form id="submitForm">
            @csrf
            <div class="row"><h3>Allow Exams:</h3></div>
            <input type="hidden"  name="main_id" id="main_id">
            <div class="row  justify-content-center">
                <div class="form-group col-md-6">
                    <label for="examTitle">Select Exam:</label>
                    <select name="examID" id="examTitle" class="form-control">
                        <option value="" selected disabled>--- Select Exam ---</option>
                        @foreach ($examPapers as $paper)
                            <option value="{{ $paper->id }}">{{ $paper->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row  justify-content-center">
                <div class="form-group col-md-6">
                    <label for="vlan">Allowed VLANs:</label>
                    <small class="disclaimer"> (example: 172.20.250|172.20.250|172.20.250)</small>
                    {{-- <input type="text" name="vlans" id="vlans" class="form-control" placeholder="Enter VlANs" style="width: 100%;"> --}}
                    <textarea onkeypress="return /[0-9.|]/i.test(event.key)" name="vlans" id="vlans" class="form-control" placeholder="172.20.250|172.20.250" style="width: 100%;" rows="3"></textarea>

                </div>
            </div>
            <div class="row  justify-content-center">
                <div class="form-group col-md-6">
                    <label for="ip">Allowed IPs:</label>
                    <small class="disclaimer"> (example: 172.20.250.36|172.20.250.43|172.20.250.243)</small>
                    {{-- <input type="text" name="ips" id="ips" class="form-control" placeholder="Enter IPs comma separated" style="width: 100%;"> --}}
                    <textarea onkeypress="return /[0-9.|]/i.test(event.key)" type="text" name="ips" id="ips" class="form-control" placeholder="172.20.250.43|172.20.250.243" style="width: 100%;"rows="3"></textarea>

                </div>
            </div>
            <div class="row  justify-content-center">
                <div class="form-group col-sm-6">
                    <label for="type">Allowed University Premises</label>
                    <div class="form-check form-check-inline" style="margin-left: 40px;">
                        <input class="form-check-input" type="radio" name="premises" id="premises" value="inside">
                        <label class="form-check-label" for="premises">Inside</label>
                    </div>
                    <div class="form-check form-check-inline" style="margin-left: 40px;">
                        <input class="form-check-input" type="radio" name="premises" id="premises" value="outside">
                        <label class="form-check-label" for="premises">Outside</label>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <button type="button" onclick='submitForm()' class="btn btn-primary">Submit</button>
            </div>
            <div id="error_message" class="alert alert-danger" style="display: none;"></div>
            <div id="success_message" class="alert alert-success" style="display: none;"></div>


        </form>


        <div id="dataDiv"   style="margin-top:20px;">

        </div>



    </div>








</div>



@stop
@section('javascript')
<script>

function submitForm(){
        let form = document.getElementById('submitForm');
        let data = new FormData(form);

        fetch(`/exam/update-allow-exam-ips`, {
            method: 'POST', // Using POST method
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
            // refreshFields();
            // $("#processing_message").hide();
            $("#error_message").text('').hide();
            $('#success_message').html(data.message);
            $('#success_message').fadeIn('slow');
            $('#success_message').fadeOut(5000);
            // LoadFee();
        } else {
            console.log(data);
            var message = "";
            if ('message' in data) {
                message = data.message;
            } else if ('errors' in data) {
                var errors = data.errors;
                $.each(errors, function (i, obj) {
                    message += "<p>" + obj[0] + "</p>";
                });
            }
            // $("#processing_message").hide();
            $('#success_message').text('').hide();
            $('#error_message').html(message);
            $('#error_message').fadeIn('slow');
            $('#error_message').fadeOut(5000);
        }
        })
        .catch((error) => {
            // displayValidationErrors(error);
            console.error('Error:', error);
            // Handle fetch error here
        });
}



</script>
@stop
