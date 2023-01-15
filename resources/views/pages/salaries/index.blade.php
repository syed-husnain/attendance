@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">User</a></li>
    <li class="breadcrumb-item active" aria-current="page">List Users</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="row" style="margin-bottom: 12px;">
          <div class="col-md-4">
            <label for="user" class="form-label">User</label>
            <select class="form-select" name="user" id="user">
              <option value="">Select Option</option>
              @foreach ( $users as $user )
                <option value="{{$user->id}}">{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
       <div class="table-responsive">
          <table id="user_list" class="table">
            <thead>
              <tr>
                <th>Id</th>
                <th>Name</th>
                 <th>Month</th>
                <th>Basic Salary</th>
               <th>Current Salary</th>
                 <th>Travel Allowance</th>
                <th>Medical Allowance</th>
                <th>Bonus</th>
                <th>Working Hours</th>
                <th>Late</th>
                <th>Absent</th>
                {{-- <th>Action</th> --}}

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
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
  <script>
    $(document).ready( function () {

            var table;
            table  = $('#user_list').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('salary.index') }}",
                    data: function (d){
                      d.search = $('input[type="search"]').val(),
                      d.user = $('#user').val()
                    }
                },
                order:[[0,"desc"]],
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'basic_salary', name: 'basic_salary'},
                    {data: 'salary', name: 'salary'},
                    {data: 'travel_allowance', name: 'travel_allowance'},
                    {data: 'medical_allowance', name: 'medical_allowance'},
                    {data: 'bonus', name: 'bonus'},
                    {data: 'working_hours', name: 'working_hours'},
                    {data: 'late', name: 'late'},
                    {data: 'absent', name: 'absent'},
                    // {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                drawCallback: function (response) {
                    /*$('#countTotal').empty();
                    $('#countTotal').append(response['json'].recordsTotal);*/
                }
            });
            $('input[type="search"],#user').change(function () {
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
        function changeStatus(id) {
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
                        url: '{{url('user/status')}}',
                        data: {
                            _token: '{{ csrf_token() }}',
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
  </script>
@endpush