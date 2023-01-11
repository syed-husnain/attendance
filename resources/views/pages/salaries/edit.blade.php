@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create User</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Basic Information</h4>
        <form id="userForm">
          @csrf
          @method('PUT')
          <div class="row">
          <div class="col-md-6 mb-3">
            <label for="name" class="form-label">Name</label>
            <input id="name" class="form-control" name="name" value="{{$user->name}}" type="text">
          </div>
          <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" class="form-control" name="email" value="{{$user->email}}" type="email">
          </div>
          <div class="col-md-6 mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input id="phone" class="form-control" name="phone" value="{{$user->phone}}" type="text" onkeypress="return isNumber(event)" placeholder="03XXXXXXXXX">
          </div>
          <div class="col-md-6 mb-3">
            <label for="cnic" class="form-label">CNIC</label>
            <input id="cnic" class="form-control" name="cnic" value="{{$user->cnic}}" type="text">
          </div>
          <div class="col-md-6 mb-3">
            <label for="dob" class="form-label">Date of Birth</label>
            <div class="input-group date datepicker" id="datePickerdob">
              <input type="text" name="dob" value="{{ old('start_date', date('d/m/Y', strtotime($user->dob ?? date('Y-m-d'))) ?? '') }}" class="form-control">
              <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="designation" class="form-label">Designation</label>
            <input id="designation" class="form-control" name="designation" value="{{$user->designation}}" type="text">
          </div>
          <div class="col-md-6 mb-3">
            <label for="member_since" class="form-label">Member Since</label>
            <div class="input-group date datepicker" id="datePickerMember">
              <input type="text" name="member_since" value="{{ old('start_date', date('d/m/Y', strtotime($user->member_since ?? date('Y-m-d'))) ?? '') }}" class="form-control">
              <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="basic_salary" class="form-label">Basic Salary</label>
            <input id="basic_salary" class="form-control" name="basic_salary" value="{{$user->basic_salary}}" type="text">
          </div>
        </div>
          <input class="btn btn-primary" id="submit" type="submit" value="Submit">
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/bootstrap-maxlength.js') }}"></script>
<script src="{{ asset('assets/js/datepicker.js') }}"></script>
<script src="{{ asset('assets/js/timepicker.js') }}"></script>

  <script>
    $(function() {
  'use strict';

  $.validator.setDefaults({
    submitHandler: function(form,event) {
      event.preventDefault();
                    let formData = new FormData(document.getElementById("userForm"));
                    
                    $( "#submit" ).prop( "disabled", true );
                    
                    $.ajax({
                        url: "{{ route('user.update',$id) }}",
                        type:"POST",
                        data:formData,
                        processData: false,
                                contentType: false,
                                cache: false,
                        success:function(response){
                           
                            // $('#successMsg').show();
                            if(response.success)
                            {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated Successfully',
                                    confirmButtonText: 'Ok',
                                    }).then((result) => {
                                    /* Read more about isConfirmed, isDenied below */
                                    if (result.isConfirmed) {
                                        window.location="{{route('user.index')}}";
    
                                    } else if (result.isDenied) {
                                        Swal.fire('Changes are not saved', '', 'info')
                                    }
                                })
  
                            }
                        },
                        error: function(response) {
                            $("#submit").prop("disabled", false);
                   
                            errorsGet(response.responseJSON.errors);
                
            
                        },
                    });
            


    }
  });
  $(function() {
    // validate signup form on keyup and submit
    $("#userForm").validate({
      rules: {
        name: {
          required: true,
          minlength: 3
        },
        email: {
          required: true,
          email: true
        },
        phone: {
          required: true,
          maxlength: 11,
        },
        designation: {
          required: true,
          maxlength: 199,
        },
        basic_salary: {
          required: true,
        },
      },
      messages: {
        name: {
          required: "Name field is required.",
          minlength: "Name must consist of at least 3 characters"
        },
        email: "Please enter a valid email address",
        phone: {
          required: "Phone field is required.",
          minlength: "Phone must consist of at 11 characters"
        },
        designation: {
          required: 'Designation field is required',
          maxlength: 'Designation not be greater then 199 characters',
        },
        basic_salary: {
          required: 'Basic Salary field is required.',
        },
      },
      errorPlacement: function(error, element) {
        error.addClass( "invalid-feedback" );

        if (element.parent('.input-group').length) {
          error.insertAfter(element.parent());
        }
        else {
          error.insertAfter(element);
        }
      },
      highlight: function(element, errorClass) {
        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') != 'radio') {
          $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
        }
      },
      unhighlight: function(element, errorClass) {
        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') != 'radio') {
          $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
        }
      }
    });
  });


  if($('#datePickerMember').length) {
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    $('.datepicker').datepicker({
      format: "dd/mm/yyyy",
      todayHighlight: true,
      autoclose: true
    });
    // $('#datePickerMember').datepicker('setDate', today);
  }

});
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
function errorsGet(errors) {
    $('span.invalid-feedback').remove();
    for (x in errors) {

        var formGroup = $('.errors[data-id="' + x + '"],input[name="' + x + '"],select[name="' + x + '"],textarea[name="' + x + '"]').parent();
      
        for (item in errors[x]) {
            console.log(item);
            formGroup.append(' <span class="invalid-feedback d-block" role="alert"><strong>' + errors[x][item] + '</strong></span>');
        }
    }
}
$(document).on('keypress','#phone',function(e){
    if($(e.target).prop('value').length>=11){
      if(e.keyCode!=32)
        {return false} 
    }});
    $(document).on('keypress','#cnic',function(e){
    if($(e.target).prop('value').length>=13){
      if(e.keyCode!=32)
        {return false} 
    }});
  </script>



@endpush