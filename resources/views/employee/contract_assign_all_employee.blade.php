@extends('layouts.admin')
@section('page-title')
    {{__('Manage Contracts Assigned')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Contracts Assigned')}}</li>
@endsection


@section('action-btn')
    
    <div class="float-end">
            <!-- Button trigger modal -->
        <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="ti ti-search"></i>
        </button>
    </div>
@endsection

@section('content')
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Data search</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="GET" action="{{ route('employee.contracts.all.assign') }}">
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="input-group ">
                            <label class="input-group-text bg-primary text-white" for="contract">Select Contract</label>
                            <select class="form-select" name="contract" id="contract">
                                <option value="">Select Contract</option>
                                @foreach ($contracts as $contract)
                                    <option value="{{ $contract->id }}" {{ request('contract') == $contract->id ? 'selected' : '' }}>{{ $contract->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="input-group ">
                            <label class="input-group-text bg-primary text-white" for="employee">Select Employee</label>
                            <select class="form-select" name="employee" id="employee">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
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
                                    <th>{{__('Employee Name')}}</th>
                                    <th>{{__('Contract Name')}}</th>
                                    <th>{{__('Description')}}</th>
                                    <th>{{__('File Name')}}</th>
                                    <th width="200px">{{__('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assignedFiles as $contract)
                                    <tr>
                                        <td>{{ $contract->employee->name }}</td>
                                        <td>{{ $contract->file ? $contract->file->name : '' }}</td>
                                        <td>{{ $contract->file ? $contract->file->description : '' }}</td>
                                        <td>
                                            <span><a href="{{ asset('storage/uploads/employee_contract').'/'.$contract->file_name }}" target="_blank">{{ $contract->file_name }}</a></span>
                                        </td>
                                        <td style="display: inline-flex">
                                            {{-- @dd($contract->signedFiles->where('employee_id', \Auth::user()->employee->id)->first()) --}}
                                            @if ($contract->file_name)
                                                <button type="button" class="btn btn-sm btn-success me-1" style="margin-right: 10px;" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                    <a href="{{ asset('storage/uploads/employee_contract/assigned').'/'.$contract->file_name }}" target="_blank"><i class="ti ti-eye text-white"></i></a>
                                                </button>
                                            @else
                                                <button class="btn btn-primary">not upload yet</button>
                                            @endif
                                            
                                            
                                            {{-- {!! Form::open(['method' => 'DELETE', 'route' => ['employee.contracts.delete', $contract->id],'id'=>'delete-form-'.$contract->id]) !!}
                                                <a href="#"  class="btn btn-sm align-items-center bs-pass-para bg-danger"  data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$contract->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                                            {!! Form::close() !!} --}}
                                        </td>
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
