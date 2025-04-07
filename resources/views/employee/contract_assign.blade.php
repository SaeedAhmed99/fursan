@extends('layouts.admin')
@section('page-title')
    {{__('Manage Contracts Assigned')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Contracts Assigned')}}</li>
@endsection


@section('action-btn')
  
@endsection

@section('content')

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
                                @foreach ($assignedFiles as $contract)
                                    <tr>
                                        <td>{{ $contract->name }}</td>
                                        <td>{{ $contract->description }}</td>
                                        <td>
                                            <span><a href="{{ asset('storage/uploads/employee_contract').'/'.$contract->file_name }}" target="_blank">{{ $contract->file_name }}</a></span>
                                        </td>
                                        <td style="display: inline-flex">
                                            {{-- @dd($contract->signedFiles->where('employee_id', \Auth::user()->employee->id)->first()) --}}
                                            @if ($contract->signedFiles->where('employee_id', \Auth::user()->employee->id)->first())
                                                <button type="button" class="btn btn-sm btn-success me-1" style="margin-right: 10px;" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                    <a href="{{ asset('storage/uploads/employee_contract/assigned').'/'.$contract->signedFiles->where('employee_id', \Auth::user()->employee->id)->first()->file_name }}" target="_blank"><i class="ti ti-eye text-white"></i></a>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-primary me-1" style="margin-right: 10px;" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                    <i class="ti ti-plus"></i>
                                                </button>
                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Upload File</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST" action="{{ route('employee.contracts.assign.upload') }}" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="number" name="contract_id" value="{{ $contract->id }}" hidden>
                                                            <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="nationality" class="form-label">File Name<x-required></x-required></label>
                                                                                <input type="file" class="form-control " name="file_name" required >
                                                                                <span class="text-xs text-muted">{{ __('Please upload a pdf file.')}}</span>
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
