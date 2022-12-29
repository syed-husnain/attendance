@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Change Password</a></li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-6 grid-margin stretch-card" style="margin-left: 25%;">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Change Password</h4>
        <form id="changePasswordForm">
          @csrf
          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="current_password" class="form-label">Current Password</label>
              <input id="current_password" class="form-control" name="current_password" type="password">
            </div>
            <div class="col-md-12 mb-3">
              <label for="new_password" class="form-label">New Password</label>
              <input id="new_password" class="form-control" name="new_password" type="password">
            </div>
            <div class="col-md-12 mb-3">
              <label for="confirm_password" class="form-label">Confirm Password</label>
              <input id="confirm_password" class="form-control" name="confirm_password" type="password">
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
                    let formData = new FormData(document.getElementById("changePasswordForm"));
                  
                    $( "#submit" ).prop( "disabled", true );
                    
                    $.ajax({
                        url: "{{ route('updatePassword') }}",
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
                            else{

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
    $("#changePasswordForm").validate({
      rules: {
        current_password: {
          required: true,
          minlength: 8
        },
        new_password: {
          required: true,
          minlength: 8
        },
        confirm_password: {
          required: true,
          minlength: 8
        },
      },
      messages: {
        current_password: {
          required: "Current Password field is required.",
          minlength: "Name must consist of at least 8 characters"
        },
        new_password: {
          required: "New Password field is required.",
          minlength: "Name must consist of at least 8 characters"
        },
        confirm_password: {
          required: "Confirm Password field is required.",
          minlength: "Name must consist of at least 8 characters"
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



});
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