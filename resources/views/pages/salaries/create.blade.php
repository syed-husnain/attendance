@extends('layout.master')

@push('plugin-styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.0.0/daterangepicker.css">
<link href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Salary</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create Salary</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Basic Information</h4>
        <form id="userForm">
          @csrf
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="user" class="form-label">User</label>
              <select class="form-select" name="user_id" id="user_id">
                <option value="">Select Option</option>
                @foreach ( $users as $user )
                  <option value="{{$user->id}}">{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3" id="custom_date_range" name="custom_date_range">
              <label for="custom_date_range" class="form-label">Date Range:</label>
              <div class="input-group date datepicker">
                <input type="text" class="form-control" id="custom_date_range_input" name="custom_date_range_input" value="{{date('m')}}/01/{{date('Y')}} - {{date('m')}}/28/{{date('Y')}}" >
                <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
              </div>
          </div>
            <div class="col-md-6 mb-3">
              <label for="basic_salary" class="form-label">Basic Salary</label>
              <input id="basic_salary" onkeypress="return isNumber(event)" class="form-control" name="basic_salary" type="text">
            </div>
            <div class="col-md-6 mb-3">
              <label for="travel_allowance" class="form-label">Travel</label>
              <input id="travel_allowance" onkeypress="return isNumber(event)" class="form-control" name="travel_allowance" type="text">
            </div>
            <div class="col-md-6 mb-3">
              <label for="medical_allowance" class="form-label">Medical</label>
              <input id="medical_allowance" onkeypress="return isNumber(event)" class="form-control" name="medical_allowance" type="text">
            </div>
            <div class="col-md-6 mb-3">
              <label for="bonus" class="form-label">Bonus</label>
              <input id="bonus" onkeypress="return isNumber(event)" class="form-control" name="bonus" type="text">
            </div>
        </div>
        <div class="row">
          <h4 class="card-title text-center">Review Salary</h4>
          <div class="col-md-4">
            <label for="working_days" class="form-label">Working Days</label>
            <input id="working_days" onkeypress="return isNumber(event)" class="form-control" name="working_days" type="text">
          </div>
          <div class="col-md-4">
            <label for="working_hours" class="form-label">Working Hours</label>
            <input id="working_hours" onkeypress="return isNumber(event)" class="form-control" name="working_hours" type="text">
          </div>
          <div class="col-md-4">
            <label for="working_hours" class="form-label">Total Late(current month)</label>
            <input id="working_hours" onkeypress="return isNumber(event)" class="form-control" name="working_hours" type="text">
          </div>
        </div>
        <div class="row" style="margin-top: 12px">
          <div class="col-md-4">
            <input class="btn btn-primary" id="submit" type="submit" value="Submit">
          </div>
        </div>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker@3.0.0/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker@3.0.0/moment.js"></script>


  <script>
    $(function() {
  'use strict';

  $.validator.setDefaults({
    submitHandler: function(form,event) {
      event.preventDefault();
                    let formData = new FormData(document.getElementById("userForm"));
                  
                    $( "#submit" ).prop( "disabled", true );
                    
                    $.ajax({
                        url: "{{ route('user.store') }}",
                        type:"POST",
                        data: formData,
                        processData: false,
                                contentType: false,
                                cache: false,
                        success:function(response){
                           
                            // $('#successMsg').show();
                            if(response.success)
                            {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Created Successfully',
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
        user_id: {
          required: true,
        },
        basic_salary: {
          required: true,
        },
        travel_allowance: {
          required: true,
        },
        medical_allowance: {
          required: true,
        },
        bonus: {
          required: true,
        },
      },
      messages: {
        user_id: {
          required: "User field is required.",
        },
        basic_salary: {
          required: "Basic salary field is required.",
         
        },
        travel_allowance: {
          required: 'Travel allowance field is required',
          
        },
        medical_allowance: {
          required: 'Medical allowance field is required.',
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
    $('#datePickerMember').datepicker({
      format: "mm/dd/yyyy",
      todayHighlight: true,
      autoclose: true
    });
    $('#datePickerMember').datepicker('setDate', today);
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


    $(function() {
          $('input[name="custom_date_range_input"]').daterangepicker({
            opens: 'left'
          }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
          });
        });

  

  </script>



@endpush