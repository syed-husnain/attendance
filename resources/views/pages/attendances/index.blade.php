@extends('layout.master')

@push('plugin-styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.0.0/daterangepicker.css">
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Attendance</a></li>
    <li class="breadcrumb-item active" aria-current="page">List Attendance</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label for="user" class="form-label">User</label>
                <select class="form-select" name="user" id="user">
                <option value="">Select Option</option>
                @foreach ( $users as $user )
                    <option value="{{$user->id}}">{{ $user->name }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="user" class="form-label">Date Filter</label>
                <select class="form-select" name="date_type" id="date_type">
                <option value="">Select Date</option>
                <option value="1">Today</option>
                <option value="2">Yesterday</option>
                <option value="3">Last 7 Days</option>
                <option value="4">Last 30 Days</option>
                <option value="5">Custom Date</option>
                <option value="6">Custom Date Range</option>
                </select>
            </div>
            <div class="col-md-4 d-none" id="custom_date" name="custom_date">
                <label for="custom_date" class="form-label">Custom Date:</label>
                <div class="input-group date datepicker">
                <input type="text" class="form-control" id="custom_date_input" name="custom_date_input" value="{{date('m')}}/01/{{date('Y')}}" disabled>
                <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
                </div>
            </div>
            <div class="col-md-4 d-none" id="custom_date_range" name="custom_date_range">
            <label for="custom_date_range" class="form-label">Custom Date Range:</label>
            <div class="input-group date datepicker">
                <input type="text" class="form-control" id="custom_date_range_input" name="custom_date_range_input" value="{{date('m')}}/01/{{date('Y')}} - {{date('m')}}/20/{{date('Y')}}" disabled >
                <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
            </div>
            </div>
        </div>
       <div class="table-responsive">
          <table id="user_list" class="table">
            <thead>
              <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Date</th>
                <th>Sign In</th>
                <th>Sign Out</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker.js') }}"></script>
  <script src="{{ asset('assets/js/timepicker.js') }}"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker@3.0.0/daterangepicker.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker@3.0.0/moment.js"></script>
 <script>
    $(document).ready( function () {

            var table;
            table  = $('#user_list').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('attendance.index') }}",
                    data: function (d){
                      d.search = $('input[type="search"]').val(),
                      d.user = $('#user').val(),
                      d.date_type = $('#date_type').val(),
                      d.date_range = $('#custom_date_range_input').val(),
                      d.custom_date = $('#custom_date_input').val()
                    }
                },
                order:[[0,"desc"]],
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'due_date', name: 'due_date'},
                    {data: 'check_in', name: 'check_in'},
                    {data: 'check_out', name: 'check_out'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                drawCallback: function (response) {
                    /*$('#countTotal').empty();
                    $('#countTotal').append(response['json'].recordsTotal);*/
                }
            });
            $('input[type="search"],#user,#date_type,#custom_date_range_input,#custom_date_input').change(function () {
                table.draw();
            });
        });
        function deleteUser(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to Delete",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "DELETE",
                        url: '{{url('user')}}'+'/'+id,
                        data: {
                            _token: '{{ csrf_token() }}',
                            'id': id
                        },
                        success: function (response) {
                            if(response.success)
                            {
                              Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted Successfully',
                                    confirmButtonText: 'Ok',
                                    }).then((result) => {
                                    /* Read more about isConfirmed, isDenied below */
                                    if (result.isConfirmed) {
                                      $('#user_list').DataTable().ajax.reload();

                                    }
                                })

                            }
                        }
                    });
                }
            })
        }
        function changeStatus(status,id) {

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to change Status",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "POST",
                        url: '{{url('attendance/status')}}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            'status': status,
                            'id': id
                        },
                        success: function (response) {
                            if(response.success)
                            {
                              Swal.fire({
                                    icon: 'success',
                                    title: 'Status Change Successfully',
                                    confirmButtonText: 'Ok',
                                    }).then((result) => {
                                    /* Read more about isConfirmed, isDenied below */
                                    if (result.isConfirmed) {
                                      $('#user_list').DataTable().ajax.reload();

                                    }
                                })

                            }
                        }
                    });
                }
            })
        }

        $("#date_type").change(function (e) {
        e.preventDefault();
        var option = $(this).val();

        if (option == 5) {
            $("#custom_date_range").addClass('d-none');
            $("#custom_date_range_input").attr('disabled', true);
            $("#custom_date").removeClass('d-none');
            $("#custom_date_input").attr('disabled', false);
        }else if(option == 6){
            $("#custom_date_range").removeClass('d-none');
            $("#custom_date_range_input").attr('disabled', false);
            $("#custom_date").addClass('d-none');
            $("#custom_date_input").attr('disabled', true);
        }else {
            $("#custom_date").addClass('d-none');
            $("#custom_date_input").attr('disabled', true);
            $("#custom_date_range").addClass('d-none');
            $("#custom_date_range_input").attr('disabled', true);
        }
    });
        if($('#custom_date_input').length) {
          var date = new Date();
          var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
          $('#custom_date_input').datepicker({
            format: "mm/dd/yyyy",
            todayHighlight: true,
            autoclose: true
          });
          $('#custom_date_input').datepicker('setDate', today);
        }

        $(function() {
          $('input[name="custom_date_range_input"]').daterangepicker({
            opens: 'left'
          }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
          });
        });

  </script>
@endpush
