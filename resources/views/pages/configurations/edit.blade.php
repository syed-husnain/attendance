@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

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
          <div class="row">
          <div class="col-md-6 mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <div class="input-group date timepicker" id="startTimePicker" data-target-input="nearest">
              <input type="text" name="start_time" value="{{$config->start_time ?? ''}}" class="form-control datetimepicker-input" data-target="#startTimePicker"/>
              <span class="input-group-text" data-target="#startTimePicker" data-toggle="datetimepicker"><i data-feather="clock"></i></span>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <div class="input-group date timepicker" id="endTimePicker" data-target-input="nearest">
              <input type="text" name="end_time" value="{{$config->end_time ?? ''}}" class="form-control datetimepicker-input" data-target="#endTimePicker"/>
              <span class="input-group-text" data-target="#endTimePicker" data-toggle="datetimepicker"><i data-feather="clock"></i></span>
            </div>
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
                        url: "{{ route('configuration.store') }}",
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
                                    title: 'Record updated Successfully',
                                    confirmButtonText: 'Ok',
                                    }).then((result) => {
                                    /* Read more about isConfirmed, isDenied below */
                                    if (result.isConfirmed) {
                                       
    
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
        start_time: {
          required: true,
        },
        end_time: {
          required: true,
        },
      },
      messages: {
        start_time: {
          required: "Start time field is required."
        },
        end_time: {
          required: "End time field is required."
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