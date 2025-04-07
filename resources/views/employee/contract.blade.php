@extends('layouts.admin')
@section('page-title')
    {{__('Manage Contracts')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Contracts')}}</li>
@endsection


@section('action-btn')
    <div class="float-end d-flex">
        <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="ti ti-plus"></i>
        </button>
    </div>
@endsection

@section('content')
 <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Create Contract</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('employee.contracts.create') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                            
                            <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="form-label">Name<x-required></x-required></label>
                                    <input class="form-control" placeholder="Enter User Name" value="" name="name" type="text" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nationality" class="form-label">Description</label>
                                    <input class="form-control" placeholder="Enter Description" value="" name="description" type="text">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nationality" class="form-label">File Name<x-required></x-required></label>
                                    <input type="file" class="form-control " name="file_name" required >
                                    <span class="text-xs text-muted">{{ __('Please upload a pdf file.')}}</span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="assign_all" name="assign_to_all" checked>
                                    <label class="form-check-label" for="assign_all">Assignment to all employees</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="assign_specific" name="assign_specific">
                                    <label class="form-check-label" for="assign_specific">Assignment to specific employees</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3 mt-2 d-none" id="employees_select">
                                    <label for="employees" class="form-label">Select employees:</label>
                                    <select class="form-select" name="employees[]" id="employees" multiple>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
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
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Description')}}</th>
                                    <th>{{__('File Name')}}</th>
                                    <th width="200px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contracts as $contract)
                                    <tr>
                                        <td>{{ $contract->name }}</td>
                                        <td>{{ $contract->description }}</td>
                                        <td>
                                            <span><a href="{{ asset('storage/uploads/employee_contract').'/'.$contract->file_name }}" target="_blank">{{ $contract->file_name }}</a></span>
                                        <td style="display: inline-flex">
                                            {{-- <button type="button" class="btn btn-sm btn-primary me-1" style="margin-right: 10px;" data-bs-toggle="modal" data-bs-target="#exampleModal02">
                                                <i class="ti ti-edit"></i>
                                            </button> --}}
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['employee.contracts.delete', $contract->id],'id'=>'delete-form-'.$contract->id]) !!}
                                                <a href="#"  class="btn btn-sm align-items-center bs-pass-para bg-danger"  data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$contract->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                     <!-- Modal -->
                                     {{-- <div class="modal fade" id="exampleModal02" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Create Contract</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('employee.contracts.update') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                            
                                                            <div class="row">
                                
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="name" class="form-label">Name<x-required></x-required></label>
                                                                    <input class="form-control" placeholder="Enter User Name" value="{{ $contract->name }}" name="name" type="text" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="nationality" class="form-label">Description</label>
                                                                    <input class="form-control" placeholder="Enter Description" value="{{ $contract->description }}" name="description" type="text">
                                                                </div>
                                                            </div>
                                
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="nationality" class="form-label">File Name</label>
                                                                    <input type="file" class="form-control " name="file_name" >
                                                                    <span class="text-xs text-muted">{{ __('Please upload a pdf file.')}}</span>
                                                                    <span><a href="{{ asset('storage/uploads/employee_contract').'/'.$contract->file_name }}" target="_blank">{{ $contract->file_name }}</a></span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-md-12">
                                                                <div class="form-check">

                                                                    
                                                                    @if($contract->assign_to_all == true)
                                                                        <input class="form-check-input" type="checkbox" id="assign_all" name="assign_to_all" checked>
                                                                    @else
                                                                        <input class="form-check-input" type="checkbox" id="assign_all" name="assign_to_all">
                                                                    @endif
                                                                    <label class="form-check-label" for="assign_all">Assignment to all employees</label>

                                                                </div>
                                                            </div>
                                
                                                            <div class="col-md-12">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="assign_specific" name="assign_specific" >
                                                                    <label class="form-check-label" for="assign_specific">Assignment to specific employees</label>
                                                                </div>
                                                            </div>
                                
                                                            <div class="col-md-12">
                                                                <div class="mb-3 mt-2 d-none" id="employees_select">
                                                                    <label for="employees" class="form-label">Select employees:</label>
                                                                    <select class="form-select" name="employees[]" id="employees" multiple>
                                                                        @foreach($employees as $employee)
                                                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                                        @endforeach
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
                                    </div> --}}
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


<script>
    document.addEventListener("DOMContentLoaded", function () {
        let assignAll = document.getElementById("assign_all");
        let assignSpecific = document.getElementById("assign_specific");
        let employeesSelect = document.getElementById("employees_select");

        function toggleEmployeeSelect() {
            if (assignSpecific.checked) {
                employeesSelect.classList.remove("d-none");
                assignAll.checked = false;
            } else {
                employeesSelect.classList.add("d-none");
                assignAll.checked = true;
            }
        }

        assignAll.addEventListener("change", function () {
            if (assignAll.checked) {
                assignSpecific.checked = false;
                employeesSelect.classList.add("d-none");
            }
        });

        assignSpecific.addEventListener("change", toggleEmployeeSelect);
    });
</script>
@endsection
