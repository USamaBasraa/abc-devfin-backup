@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
@include('components.toast-notifications')
<style>
        .accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
  background-color: #ccc;
}

.panel {
  padding: 0 18px;
  display: none;
  background-color: white;
  overflow: hidden;
}
div{
    border: 0px solid black;
}


.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}
.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {background-color: #ddd;}

.dropdown:hover .dropdown-content {display: block;}

.dropdown:hover .dropbtn {background-color: #3e8e41;}



</style>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<!--Arsalan working on this -->
<div class="card card-border-primary">
    <div class="card-header">
        <h5>Eportal Category Request Details</h5>
    </div>
    <div class="card-block">
        <div class="container">
            <form id="myForm">
                @csrf
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                        <label for="category">Select Category Request</label>
                        <select class="form-control select2-show-search" id="category_id" name="category_id">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }} <small class="text-muted">({{ $category->dept_name }})</small>
                            </option>
                        @endforeach
                    </select>

                    </div>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                        <label for="select_option">Select an Option</label>
                        <select class="form-control select2-show-search" id="select_option" name="select_option">
                            <option value="">-- Select an Option --</option>
                            @foreach($options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Instruction Text Area -->
                <div class="form-group" id="user_instruction" style="display: none;">
                    <label for="select_option">Instructions</label>
                    <input type="text" class="form-control" id="userInstruction" name="userInstruction">
                </div>
                  <!-- User Dropdown (Searchable) -->
                  <div class="form-group" id="user_dropdown" style="display: none;">
                <label for="user_id">Select User</label>
                <select style="width: 100%;" class="form-control  select2-show-search" id="user_id" name="user_id">
                    <option value="">-- Select User --</option>
                    <option value="0~0">HOD</option>
                    <option value="1~1">Dean</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}~{{ $user->emp_dept_id }}">{{ $user->full_name }}</option>
                    @endforeach
                </select>
                <!-- Hidden input to store the selected user ID -->
            </div>
                            <div class="form-group" id="resolving_users" style="display: none;">
                    <label for="user">Select Resolving User</label>
                    <select  style="width: 100%;" class="form-control  select2-show-search"     id="resolving_user" name="resolving_user">
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{$user->id}}~{{$user->emp_dept_id}}">{{$user->full_name}}</option>
                        @endforeach
                    </select>
                    <!-- Hidden input to store the selected user ID -->
                </div>
                <div class="form-group" id="viewing_users" style="display: none;">
                    <label for="user">Select Viewer User</label>
                    <select style="width: 100%;" class="form-control select2-show-search" id="viewing_user" name="viewing_user">
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{$user->id}}~{{$user->emp_dept_id}}">{{$user->full_name}}</option>
                        @endforeach
                    </select>
                    <!-- Hidden input to store the selected user ID -->
                </div>
                <!-- Completion Time Dropdowns (Initially Hidden) -->
                <div class="form-group" id="completion_time" style="display: none;">
                    <label for="completion_days">Completion Time</label>
                    <div class="row">
                        <div class="col">
                            <select class="form-control select2-show-search" id="completion_days" name="completion_days">
                                <option value="">-- Select days --</option>
                                <option value="1">1 day</option>
                                <option value="2">2 days</option>
                                <option value="3">3 days</option>
                                <option value="4">4 days</option>
                                <option value="5">5 days</option>
                                <option value="6">6 days</option>
                                <option value="7">7 days</option>
                                <option value="8">8 days</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-control select2-show-search" id="completion_hours" name="completion_hours">
                                <option value="">-- Select hours  --</option>
                                @for ($i = 0; $i <= 24; $i++)
                                    <option value="{{ $i }}">{{ $i }} hour{{ $i !== 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:30px;text-align:center;margin-bottom:10px">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12" >
                        <!-- Submit form button -->
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Permission
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div>
<?php
$ecrd_id = '';
foreach($resEpcd as $row){
if ($ecrd_id != $row->ec_id) {
    $ecrd_id = $row->ec_id;
?>
<div class="card-block">
<button class="accordion">
    {{ $row->ec_title }} 
    <small class="text-muted">({{ $row->dept_name }})</small>
</button>

    <div class="panel">
        <?php foreach($resEpcdetails as $res){
        if($res->ec_id == $row->ec_id){
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="p-4" style="background-color:#f0f0f0;color:black !important">
                    @if($res->ecd_approval_user && $res->ecd_approval_user != '0' && $res->ecd_approval_user != '1') 
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <b>Approval:</b> {{$res->full_name_app}}
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="text-align:right">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{$res->id}}','{{$res->ecd_approval_user}}','{{$res->ec_id}}')" title="Delete this permission">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endif 
                    @if($res->ecd_approval_user =='0') 
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <b>Approval:</b> Relevant HOD 
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="text-align:right">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{$res->id}}','{{$res->ecd_approval_user}}','{{$res->ec_id}}')" title="Delete this permission">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @elseif($res->ecd_approval_user =='1' && $res->ecd_dep_id =='1') 
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <b>Approval:</b> Relevant Dean 
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="text-align:right">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{$res->id}}','{{$res->ecd_approval_user}}','{{$res->ec_id}}')" title="Delete this permission">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endif 

                    
                    @if($res->ecd_resolving_user)
                        <?php $resolvingUser = $res->ecd_resolving_user;
                           
                        ?>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <b>Resolving:</b> {{$res->full_name_resolving}}
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="text-align:right">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{$res->id}}','{{$res->ecd_resolving_user}}','{{$res->ec_id}}')" title="Delete this permission">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endif
                    @if($res->ecd_viewer_user)
                        <?php $viewerUser = $res->ecd_viewer_user;
                           
                        ?>
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <b>Viewer:</b> {{$res->full_name_viewer}}
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="text-align:right">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{$res->id}}','{{$res->ecd_viewer_user}}','{{$res->ec_id}}')" title="Delete this permission">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endif
              
                    @if($res->ecd_completion_days)
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <b>Time:</b> {{$res->ecd_completion_days}} days
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="text-align:right">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{$res->id}}','{{$res->ecd_completion_days}}','{{$res->ec_id}}')" title="Delete this permission">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endif
                    @if($res->ecd_fee)
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <b>Fee:</b> {{ $res->ecd_fee == 1 ? 'Required' : 'N/A' }}
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="text-align:right">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{$res->id}}','{{$res->ecd_fee}}','{{$res->ec_id}}')" title="Delete this permission">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endif
                    @if($res->ecd_instruction_categories)
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <b>Instruction:</b> {{$res->ecd_instruction_categories}}
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="text-align:right">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{$res->id}}','{{$res->ecd_instruction_categories}}','{{$res->ec_id}}')" title="Delete this permission">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endif
                    @if($res->ecd_upload_type)
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <b>Upload File:</b> {{ $res->ecd_upload_type == 1 ? 'Required' : 'N/A' }}
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6" style="text-align:right">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow('{{$res->id}}','{{$res->ecd_upload_type}}','{{$res->ec_id}}')" title="Delete this permission">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <?php }} ?>
    </div>
</div>
<?php } } ?>
@stop
@section('javascript')
 <!-- Include Select2 and JavaScript for toggling completion time -->
<script>
// Using toast notification component

// Toast notifications are working properly
function deleteRow(id,categ,ec_id){
    // Show confirmation dialog
    if(confirm('⚠️ Are you sure you want to delete this permission?\n\nThis action cannot be undone and may affect existing requests.')){
        // Show loading state
        const deleteBtn = event.target;
        const originalText = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Deleting...';
        deleteBtn.disabled = true;
        
        var data = {
            ecd_id: id,
            categ:categ,
            categ_id:ec_id,
            _token: '{{ csrf_token() }}'
        };
        $.ajax({
            url: "{{route('delete-ecdr')}}",
            method: 'POST',
            data: data,
            success: function(response) {
                if(response.data){
                    // Show success message
                    showToast('success', 'Permission deleted successfully!');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showToast('error', 'Failed to delete permission. Please try again.');
                    deleteBtn.innerHTML = originalText;
                    deleteBtn.disabled = false;
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                showToast('error', 'An error occurred while deleting. Please try again.');
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
            }
        });
    }
}
$('#myForm').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission
    
    // Form validation
    const categoryId = $('#category_id').val();
    const selectOption = $('#select_option').val();
    
    if (!categoryId) {
        showToast('error', 'Please select a category first!');
        $('#category_id').focus();
        return;
    }
    
    if (!selectOption) {
        showToast('error', 'Please select an option!');
        $('#select_option').focus();
        return;
    }
    
    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Adding Permission...');
    submitBtn.prop('disabled', true);
    
    // Serialize the form data
    var formData = $(this).serialize();
    $.ajax({
        url: "{{ route('add_permistion') }}",
        method: 'POST',
        data: formData,
        success: function(response) {
            if(response.data){
                showToast('success', 'Permission added successfully!');
                // Reset form
                $('#myForm')[0].reset();
                $('#user_dropdown, #resolving_users, #viewing_users, #completion_time, #user_instruction').hide();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast('error', 'Failed to add permission. Please try again.');
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            showToast('error', 'An error occurred. Please try again.');
            submitBtn.html(originalText);
            submitBtn.prop('disabled', false);
        }
    });
});
var acc = document.getElementsByClassName("accordion");
var i;
for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}
$(document).ready(function() {
    // Initialize Select2 for all dropdowns
    $('.select2-show-search').select2({
        placeholder: function() {
            return $(this).find('option:first').text();
        },
        allowClear: true
    });
    // Handle change event of the "Select an Option" dropdown
    $('#select_option').on('change', function() {
        var selectedOption = $(this).val();
        var completionTime = $('#completion_time');
        var user_instruction = $('#user_instruction');
        var userDropdown = $('#user_dropdown');
        var ressolvingDropdown = $('#resolving_users');
        var viewingDropdown = $('#viewing_users');
        
        // Hide all dropdowns first
        userDropdown.hide();
        ressolvingDropdown.hide();
        viewingDropdown.hide();
        completionTime.hide();
        user_instruction.hide();
        
        // Clear all values
        $('#user_id').val('');
        $('#resolving_user').val('');
        $('#viewing_user').val('');
        $('#userInstruction').val('');
        $('#completion_days').val('');
        $('#completion_hours').val('');
        
        // Show relevant dropdown based on selection
        if (selectedOption === 'Approval') {
            userDropdown.show();
            // Reinitialize Select2 for the user dropdown
            $('#user_id').select2({
                placeholder: '-- Select User --',
                allowClear: true
            });
        } else if (selectedOption === 'Resolving Authority') {
            ressolvingDropdown.show();
            // Reinitialize Select2 for the resolving user dropdown
            $('#resolving_user').select2({
                placeholder: '-- Select User --',
                allowClear: true
            });
        } else if (selectedOption === 'Viewer Authority') {
            viewingDropdown.show();
            // Reinitialize Select2 for the viewing user dropdown
            $('#viewing_user').select2({
                placeholder: '-- Select User --',
                allowClear: true
            });
        } else if (selectedOption === 'Time') {
            completionTime.show();
            // Reinitialize Select2 for the completion time dropdowns
            $('#completion_days').select2({
                placeholder: '-- Select days --',
                allowClear: true
            });
            $('#completion_hours').select2({
                placeholder: '-- Select hours --',
                allowClear: true
            });
        } else if (selectedOption === 'Instruction') {
            user_instruction.show();
        }
    });
});
// update EMPLOYEE_ID () for adding caegory_id in category
// function updateUserId() {
//     var userInput = document.getElementById("user");
//     var userIdInput = document.getElementById("user_id");
//     // Find the selected option based on the visible input's value
//     var selectedOption = document.querySelector('#user-options option[value="' + userInput.value + '"]');
//     // Update the hidden input with the selected user's ID
//     if (selectedOption) {
//         userIdInput.value = selectedOption.getAttribute('data-id');
//     }else {
//         userIdInput.value = ""; // No option selected
//     }
// }
</script>


@stop
