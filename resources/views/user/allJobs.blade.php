@extends('layouts.admin')
@php
    $profile=\App\Models\Utility::get_file('uploads/avatar/');
@endphp
@section('page-title')
    {{__('Profile Account')}}
@endsection
@push('script-page')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })
        $(".list-group-item").click(function(){
            $('.list-group-item').filter(function(){
                return this.href == id;
            }).parent().removeClass('text-primary');
        });
    </script>

    <script>
        document.getElementById('avatar').onchange = function () {
            var src = URL.createObjectURL(this.files[0])
            document.getElementById('image').src = src
        }
        </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Jobs')}}</li>
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
                            <th>{{__('Branch')}}</th>
                            <th>{{__('Title')}}</th>
                            <th>{{__('Start Date')}}</th>
                            <th>{{__('End Date')}}</th>
                            <th>{{__('Status')}}</th>
                            <th>{{__('Created At')}}</th>
                            <th width="200px">{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody class="font-style">
                        @foreach ($jobs as $job)
                            <tr>
                                <td>{{ !empty($job->branches)?$job->branches->name:__('All') }}</td>
                                <td>{{$job->title}}</td>
                                <td>{{\Auth::user()->dateFormat($job->start_date)}}</td>
                                <td>{{\Auth::user()->dateFormat($job->end_date)}}</td>
                                <td>
                                    @if($job->status=='active')
                                        <span class="status_badge badge bg-primary p-2 px-3 rounded">{{App\Models\Job::$status[$job->status]}}</span>
                                    @else
                                        <span class="status_badge badge bg-danger p-2 px-3 rounded">{{App\Models\Job::$status[$job->status]}}</span>
                                    @endif
                                </td>
                                <td>{{ \Auth::user()->dateFormat($job->created_at) }}</td>
                                <td>
                                    @if($job->status != 'in_active')
                                        <div class="action-btn me-2">
                                            <a href="#" id="{{ route('job.requirement',[$job->code,!empty($job)?$job->createdBy->lang:'en']) }}" class="mx-3 btn btn-sm align-items-center bg-secondary"  onclick="copyToClipboard(this)" data-bs-toggle="tooltip" title="{{__('Copy')}}" data-original-title="{{__('Click to copy')}}"><i class="ti ti-link text-white"></i></a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    
        

        <script>
            var stillWorking = document.getElementById("still-working").checked;
            console.log(stillWorking);
            
            function toggleEndDate() {
                var stillWorking = document.getElementById("still-working").checked;
                var endDateField = document.getElementById("end_date_box");
                
                if (stillWorking) {
                    endDateField.style.display = 'none'; // Hide the end date field
                } else {
                    endDateField.style.display = 'block'; // Show the end date field
                }
            }
        </script>
@endsection

@push('script-page')


    <script>
        function copyToClipboard(element) {

            var copyText = element.id;
            navigator.clipboard.writeText(copyText);
            // document.addEventListener('copy', function (e) {
            //     e.clipboardData.setData('text/plain', copyText);
            //     e.preventDefault();
            // }, true);
            //
            // document.execCommand('copy');
            show_toastr('success', 'Url copied to clipboard', 'success');
        }
    </script>


@endpush