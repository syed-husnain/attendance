@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Holiday</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Holiday</h4>
        <form id="userForm">
          @csrf
          @method('POST')
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="due_date" class="form-label">Date</label>
              <div class="input-group date datepicker" id="datePicker">
                <input type="text" name="due_date" class="form-control">
                <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" name="status" id="status">
                <option value="Holiday">Holiday</option>
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
                        url: "{{ route('holiday.store') }}",
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
                                    title: 'Record Created Successfully',
                                    confirmButtonText: 'Ok',
                                    }).then((result) => {
                                    /* Read more about isConfirmed, isDenied below */
                                    if (result.isConfirmed) {
                                      window.location.href = "{{route('holiday.index')}}";
    
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
        status: {
          required: true,
        },
      },
      messages: {
        status: {
          required: "Status field is required."
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
    $('#datePicker').datepicker('setDate', today);
  }


  });


  $('#startTimePicker, #endTimePicker').datetimepicker({
    format: 'HH:mm'
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