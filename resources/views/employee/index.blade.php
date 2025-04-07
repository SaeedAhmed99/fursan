@extends('layouts.admin')
@section('page-title')
    {{__('Manage Employee')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Employee')}}</li>
@endsection


@section('action-btn')
    <div class="float-end d-flex">
        {{-- <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('employee.file.import') }}" data-ajax-popup="true" data-title="{{__('Import employee CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a> --}}
        @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'HR')
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="ti ti-search"></i>
            </button>
        @endif
        <a href="#" data-size="md"  data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('employee.file.import') }}" data-ajax-popup="true" data-title="{{__('Import employee CSV file')}}" class="btn btn-sm btn-primary me-2">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('employee.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary me-2">
            <i class="ti ti-file-export"></i>
        </a>
        <a href="{{ route('employee.create') }}"
            data-title="{{ __('Create New Employee') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Data sorting and search</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="GET" action="{{ route('employee.index') }}">
            @csrf
            <div class="modal-body">
                        
                        <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input class="form-control" placeholder="Enter User Email" value="{{ request('email') }}" name="email" type="email" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">Name</label>
                                <input class="form-control" placeholder="Enter User Name" value="{{ request('name') }}" name="name" type="text">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input class="form-control" placeholder="Enter User Nationality" value="{{ request('nationality') }}" name="nationality" type="text" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country_of_residence" class="form-label">Country Of Residence</label>
                                <input class="form-control" placeholder="Enter User country Of Residence" value="{{ request('country_of_residence') }}" name="country_of_residence" type="text" >
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('branch_id', __('Select Branch'), ['class' => 'form-label']) }}
                            <div class="form-icon-user">
                                {{ Form::select('branch_id', $branches, request('branch_id'), ['class' => 'form-control ', 'placeholder' => 'Select Branch']) }}
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('department_id', __('Select Department'), ['class' => 'form-label']) }}
                            <div class="form-icon-user">
                                {{ Form::select('department_id', $departments, request('department_id'), ['class' => 'form-control ', 'id' => 'department_id' , 'placeholder' => 'Select Department']) }}
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('designation_id', __('Select Designation'), ['class' => 'form-label']) }}

                            <div class="form-icon-user">
                                {{ Form::select('designation_id', $designations, request('designation_id'), ['class' => 'form-control ', 'id' => 'designation_id' , 'placeholder' => 'Select Designation']) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country_of_residence" class="form-label">Date Of Joining</label>
                                <input class="form-control" placeholder="Enter Date Of Joining" value="{{ request('company_doj') }}" name="company_doj" type="date" >
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="input-group ">
                                <label class="input-group-text bg-primary text-white" for="sort">sort by</label>
                                <select class="form-select" name="sort" id="sort">
                                    <option value="">Select Option</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>oldest to newest</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>newest to oldest</option>
                                    <option value="age_asc" {{ request('sort') == 'age_asc' ? 'selected' : '' }}>age ascending</option>
                                    <option value="age_desc" {{ request('sort') == 'age_desc' ? 'selected' : '' }}>age descending</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>name Alphabetically (A-Z)</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>name Alphabetically (Z-A)</option>
                                </select>
                            </div>
                    </div>
                    </div>        
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
  </div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
        <div class="card-body table-border-style">
                    <div class="table-responsive">
                    <table class="table datatable">
                            <thead>
                            <tr>
                                <th>{{__('Employee ID')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Email')}}</th>
                                <th>{{__('Branch') }}</th>
                                <th>{{__('Department') }}</th>
                                <th>{{__('Designation') }}</th>
                                <th>{{__('Date Of Joining') }}</th>
                                <th> {{__('Last Login')}}</th>
                                <th width="200px">{{__('Action')}}</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td class="Id">
                                        @can('show employee profile')
                                            <a href="{{route('employee.show',\Illuminate\Support\Facades\Crypt::encrypt($employee->id))}}" class="btn btn-outline-primary">{{ \Auth::user()->employeeIdFormat($employee->id) }}</a>
                                        @else
                                            <a href="#"  class="btn btn-outline-primary">{{ \Auth::user()->employeeIdFormat($employee->id) }}</a>
                                        @endcan
                                    </td>
                                    <td class="font-style">{{ $employee->name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    @if($employee->branch_id)
                                        <td class="font-style">{{$employee->branch  ? $employee->branch->name:''}}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                    @if($employee->department_id)
                                        <td class="font-style">{{$employee->department ? $employee->department->name:''}}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                    @if($employee->designation_id)
                                        <td class="font-style">{{$employee->designation ? $employee->designation->name:''}}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                    @if($employee->company_doj)
                                        <td class="font-style">{{ \Auth::user()->dateFormat($employee->company_doj )}}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                    <td>
                                        {{ (!empty($employee->user->last_login_at)) ? $employee->user->last_login_at : '-' }}
                                    </td>
                                    @if(Gate::check('edit employee') || Gate::check('delete employee'))
                                        <td>
                                            @if($employee->is_active==1)
                                                @can('edit employee')
                                                <div class="action-btn me-2">
                                                    <a href="{{route('employee.edit',\Illuminate\Support\Facades\Crypt::encrypt($employee->id))}}" class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" title="{{__('Edit')}}"
                                                     data-original-title="{{__('Edit')}}"><i class="ti ti-pencil text-white"></i></a>
                                                </div>

                                                    @endcan
                                                @can('delete employee')
                                                <div class="action-btn ">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['employee.destroy', $employee->id],'id'=>'delete-form-'.$employee->id]) !!}

                                                    <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$employee->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
                                                @endcan
                                            @else

                                                <i class="ti ti-lock"></i>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div >
                            <small class="p-2">To show the movement cursor, place the mouse below the text.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
    <script>
        $('input[type="file"]').change(function(e) {
            var file = e.target.files[0].name;
            var file_name = $(this).attr('data-filename');
            $('.' + file_name).append(file);
        });
    </script>
    <script>
        $(document).ready(function() {
            var d_id = $('.department_id').val();
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });

        function getDesignation(did) {

            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append('<option value="">Select any Designation</option>');
                    $.each(data, function (key, value) {
                        $('#designation_id').append('<option value="' + key + '"  >' + value + '</option>');
                    });
                }


            });
        }
    </script>
@endpush

