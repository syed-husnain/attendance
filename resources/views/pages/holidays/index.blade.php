@extends('layout.master')

@push('plugin-styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.0.0/daterangepicker.css">
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Holiday</a></li>
    <li class="breadcrumb-item active" aria-current="page">List Holiday</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
      
       <div class="table-responsive">
          <table id="user_list" class="table">
            <thead>
              <tr>
                <th>Id</th>
                <th>Date</th>
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
                    url: "{{ route('holiday.index') }}",
                    data: function (d){
                      d.search = $('input[type="search"]').val()
                    }
                },
                order:[[0,"desc"]],
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'due_date', name: 'due_date'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                drawCallback: function (response) {
                    /*$('#countTotal').empty();
                    $('#countTotal').append(response['json'].recordsTotal);*/
                }
            });
            $('input[type="search"]').change(function () {
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
                text: "You want to Remove",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        method: "POST",
                        url: '{{url('holiday/status')}}',
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
                                    title: 'Slot Active Successfully',
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