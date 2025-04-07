@extends('layouts.admin')

@section('page-title')
    {{ __('Payslip') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('payslip file') }}</li>
@endsection

@section('content')
    

    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center justify-content-start mt-2">
                            <h5>{{ __('Payslip File') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="pc-dt-render-column-cells">
                        <thead>
                            <tr>
                                <th>{{ __('Employee Name') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allFiles as $item)
                                <tr>
                                    <td>{{ $item->employee->name }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        <span><a href="{{ (!empty($item->pdf_file)?asset('storage/uploads/payslip').'/'.$item->pdf_file:'') }}" target="_blank">{{ (!empty($item->pdf_file)?$item->pdf_file:'') }}</a></span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

