@extends('layouts.admin')
@section('page-title')
    {{__('Manage Form Builder')}}
@endsection
@push('script-page')
    <script>
        $(document).ready(function () {
            $('.cp_link').on('click', function () {
                var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                show_toastr('success', '{{__('Link Copy on Clipboard')}}')
            });
        });

        $(document).ready(function () {
            $('.iframe_link').on('click', function () {
                var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                show_toastr('success', '{{__('Link Copy on Clipboard')}}')
            });
        });
    </script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Form Builder')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        <a href="#" data-size="md" data-url="{{ route('form_builder.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create New Form')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
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
                                <th>{{__('Response')}}</th>
                                @if (\Auth::user()->type == 'company' || (\Auth::user()->can('manage form field') || \Auth::user()->can('view form response') || \Auth::user()->can('edit form builder') || \Auth::user()->can('delete form builder')))
                                    <th class="text-end" width="200px">{{__('Action')}}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($forms as $form)
                                <tr>
                                    <td>{{ $form->name }}</td>
                                    <td>
                                        {{ $form->response->count() }}
                                    </td>
                                        <td class="text-end">


                                            <div class="action-btn me-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center cp_link bg-warning-subtle" data-link="<iframe src='{{url('/form/'.$form->code)}}' title='{{ $form->name }}'></iframe>" data-bs-toggle="tooltip" title="{{__('Click to copy iframe link')}}"><i class="ti ti-frame text-white"></i></a>
                                            </div>

                                            @if(\Auth::user()->type=='company' || \Auth::user()->type=='accou   ntant')
                                            <div class="action-btn me-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center bg-light-blue-subtitle" data-url="{{ route('form.field.bind',$form->id) }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="{{__('Convert into Lead Setting')}}" data-title="{{__('Convert into Lead Setting')}}">
                                                    <i class="ti ti-exchange text-white"></i>
                                                </a>
                                            </div>
                                            @endif

                                            <div class="action-btn me-2">
                                                <a href="#" class="mx-3 btn btn-sm align-items-center cp_link bg-primary" data-link="{{url('/form/'.$form->code)}}" data-bs-toggle="tooltip" title="{{__('Click to copy link')}}"><i class="ti ti-copy text-white"></i></a>
                                            </div>

                                            @can('manage form field')
                                                <div class="action-btn me-2">
                                                    <a href="{{route('form_builder.show',$form->id)}}" class="mx-3 btn btn-sm align-items-center bg-secondary" data-bs-toggle="tooltip" title="{{__('Form field')}}"><i class="ti ti-table text-white"></i></a>
                                                </div>
                                            @endcan

                                            @can('view form response')
                                                <div class="action-btn me-2">
                                                    <a href="{{route('form.response',$form->id)}}" class="mx-3 btn btn-sm align-items-center bg-warning" data-bs-toggle="tooltip" title="{{__('View Response')}}"><i class="ti ti-eye text-white"></i></a>
                                                </div>
                                            @endcan
                                            @can('edit form builder')
                                                <div class="action-btn me-2">
                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bg-info" data-url="{{ route('form_builder.edit',$form->id) }}" data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Form Builder Edit')}}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete form builder')
                                                <div class="action-btn ">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['form_builder.destroy', $form->id],'id'=>'delete-form-'.$form->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}"><i class="ti ti-trash text-white"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
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
@endsection
