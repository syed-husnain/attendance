@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Attendance</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create Attendance</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Attendance</h4>
        <form id="userForm">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="user" class="form-label">User</label>
              <select class="form-select" name="user_id" id="user_id">
                <option value="">Select Option</option>
                @foreach ( $users as $user )
                  <option {{($attendance->user_id == $user->id)? 'selected' : '' }} value="{{$user->id}}">{{ $user->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="due_date" class="form-label">Date</label>
              <div class="input-group date datepicker" id="datePicker">
                <input type="text" name="due_date" value="{{ old('due_date', date('d/m/Y', strtotime($attendance->due_date ?? date('Y-m-d'))) ?? '') }}" class="form-control">
                <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="check_in" class="form-label">Sign In</label>
              <div class="input-group date timepicker" id="startTimePicker" data-target-input="nearest">
                <input type="text" name="check_in" value="{{$attendance->check_in ?? ''}}" class="form-control datetimepicker-input" data-target="#startTimePicker"/>
                <span class="input-group-text" data-target="#startTimePicker" data-toggle="datetimepicker"><i data-feather="clock"></i></span>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="check_out" class="form-label">Sign Out</label>
              <div class="input-group date timepicker" id="endTimePicker" data-target-input="nearest">
                <input type="text" name="check_out" value="{{$attendance->check_out ?? ''}}" class="form-control datetimepicker-input" data-target="#endTimePicker"/>
                <span class="input-group-text" data-target="#endTimePicker" data-toggle="datetimepicker"><i data-feather="clock"></i></span>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" name="status" id="status">
                <option value="">Select Option</option>
                <option {{($attendance->status == 'Start') ? 'selected' : '' }} value="Start">Start</option>
                <option {{($attendance->status == 'Full') ? 'selected' : '' }} value="Full">Full</option>
                <option {{($attendance->status == 'Reduced') ? 'selected' : '' }} value="Reduced">Reduced</option>
                <option {{($attendance->status == 'Absent') ? 'selected' : '' }} value="Absent">Absent</option>
                <option {{($attendance->status == 'Leave') ? 'selected' : '' }} value="Leave">Leave</option>
                <option {{($attendance->status == 'Holiday') ? 'selected' : '' }} value="Holiday">Holiday</option>
              </select>
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
  <script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.js') }}"></script>

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
                        url: "{{ route('attendance.update',$attendance->id) }}",
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
                                    title: 'Record Updated Successfully',
                                    confirmButtonText: 'Ok',
                                    }).then((result) => {
                                    /* Read more about isConfirmed, isDenied below */
                                    if (result.isConfirmed) {
                                      window.location.href = "{{route('attendance.index')}}";
    
                                    } else if (result.isDenied) {
                                        Swal.fire('Changes are not saved', '', 'info')
                                    }
                                })
  
                            }else{
                              Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonText: 'Ok',
                                    }).then((result) => {
                                    /* Read more about isConfirmed, isDenied below */
                                    if (result.isConfirmed) {
                                      $( "#submit" ).prop( "disabled", false );
                                        // window.location="{{route('user.index')}}";
    
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
        check_in: {
          required: true,
        },
        check_out: {
          required: true,
        },
        status: {
          required: true,
        },
      },
      messages: {
        check_in: {
          required: "Sign in field is required."
        },
        check_out: {
          required: "Sign out field is required."
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

    if($('#datePicker').length) {
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    $('#datePicker').datepicker({
      format: "dd/mm/yyyy",
      todayHighlight: true,
      autoclose: true
    });
   
  }


  });


  $('#startTimePicker, #endTimePicker').datetimepicker({
    format: 'HH:mm'
  });

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
  </script>



@endpush