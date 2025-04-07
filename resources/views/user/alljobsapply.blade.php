@extends('layouts.admin')
@php
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{__('Profile Account')}}
@endsection
@push('script-page')
   
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Jobs Applied')}}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
        <div class="card-body table-border-style">
                <div class="table-responsive">
                <table class="table datatable">
                        <thead>
                        <tr>
                            <th>{{__('Title Job')}}</th>
                            <th>{{__('Stage')}}</th>
                            <th>{{__('Applied At')}}</th>
                        </tr>
                        </thead>
                        <tbody class="font-style">
                            @foreach ($jobsApplay as $item)
                                <td>{{ $item->jobs->title }}</td>
                                <td>
                                    @php
                                        $stage = DB::table('job_stages')->find($item->stage);
                                    @endphp
                                    <p class="btn btn-sm btn-primary btn-icon count">{{ $stage->title }}</p></td>
                                <td>{{ $item->created_at }}</td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    
        

@endsection

@push('script-page')


@endpush