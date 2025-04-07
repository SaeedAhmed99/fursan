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
       <style>
        .image-container {
            
        }

        .image {
            width: 200px;
            height: auto;
            cursor: zoom-in;
            transition: transform 0.3s ease-in-out;
        }

        .fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .fullscreen img {
            max-width: 90%;
            max-height: 90%;
            cursor: zoom-out;
            z-index: 2000;

        }
    </style>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Profile')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-3">
            <div class="card sticky-top" style="top:30px">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <a href="#personal_info" class="list-group-item list-group-item-action border-0">{{__('Personal Info')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    @if ($employee != null || $userDetail->type == 'user')
                    <a href="#languageUser" class="list-group-item list-group-item-action border-0">{{__('Language')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    <a href="#work_experience" class="list-group-item list-group-item-action border-0">{{__('Work Experience')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    <a href="#education" class="list-group-item list-group-item-action border-0">{{__('Education')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    <a href="#skills" class="list-group-item list-group-item-action border-0">{{__('Skills')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    <a href="#membership" class="list-group-item list-group-item-action border-0">{{__('Membership')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    <a href="#traning" class="list-group-item list-group-item-action border-0">{{__('Training')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    <a href="#certificateUser" class="list-group-item list-group-item-action border-0">{{__('Certificate')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    <a href="#cvUser" class="list-group-item list-group-item-action border-0">{{__('CV / Resume')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    @endif
                    <a href="#change_password" class="list-group-item list-group-item-action border-0">{{__('Change Password')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                </div>
            </div>
        </div>
        <div class="col-xl-9">

            <div id="personal_info" class="card">
                <div class="card-header">
                    <h5>{{__('Personal Info')}}<x-required></x-required></h5>
                </div>
                    <div class="card-body">
                        {{Form::model($userDetail,array('route' => array('update.account'), 'method' => 'post', 'enctype' => "multipart/form-data"))}}
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Name')}}</label>
                                    <input class="form-control" name="name" type="text" id="name" placeholder="{{ __('Enter Your Name') }}" value="{{ $userDetail->name ?? '' }}" required autocomplete="name">
                                </div>
                            </div>
                            {{-- @if ($employee != null || $userDetail->type == 'user') --}}
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Date Of Birth')}}</label>
                                    <input class="form-control" name="dob" type="date" id="dob" placeholder="{{ __('Enter Date of Birth') }}" value="{{ $userDetail->dob ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Country of Residence')}}</label>
                                    <input class="form-control" name="country_of_residence" type="text" id="country_of_residence" placeholder="{{ __('Enter Country of Residence') }}" value="{{ $userDetail->country_of_residence ?? '' }}" required >
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Nationality')}}</label>
                                    <input class="form-control" name="nationality" type="text" id="nationality" placeholder="{{ __('Enter Nationality') }}" value="{{ $userDetail->nationality ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Address')}}</label>
                                    <input class="form-control" name="address" type="text" id="address" placeholder="{{ __('Enter Address') }}" value="{{ $userDetail->address ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Select Gender')}}</label>
                                    {{-- <label>Select Gender:</label> --}}
                                    <div>
                                        <input type="radio" id="male" name="gender" value="male" {{ $userDetail->gender ?? ''  == 'male' ? 'checked' : '' }} required>
                                        <label for="male">Male</label>
                                    </div>
                                    <div>
                                        <input type="radio" id="female" name="gender" value="female" {{ $userDetail->gender ?? '' == 'female' ? 'checked' : '' }} required>
                                        <label for="female">Female</label>
                                    </div>
                                    {{-- <input class="form-control" name="gender" type="text" id="gender" placeholder="{{ __('Enter Gender') }}" value="{{ $userDetail->employee->gender }}" required> --}}
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Phone')}}</label>
                                    <input class="form-control" name="phone" type="text" id="phone" placeholder="{{ __('Enter Phone') }}" value="{{ $userDetail->phone ?? '' }}" required>
                                </div>
                            </div>
                            {{-- @endif --}}
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label for="email" class="col-form-label text-dark">{{__('Email')}}</label>
                                    <input class="form-control" name="email" type="email" id="email" placeholder="{{ __('Enter Your Email Address') }}" value="{{ $userDetail->email ?? '' }}" required autocomplete="email">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <div class="theme-avtar-logo mt-4">
                                        <img id="image" src="{{ ($userDetail->avatar) ? $profile  . $userDetail->avatar : $profile . 'avatar.png' }}"
                                             class="big-logo">
                                    </div>
                                    <div class="choose-files mt-3">
                                        <label for="avatar">
                                            <div class=" bg-primary profile_update"> <i class="ti ti-upload px-1"></i>{{__('Choose file here')}}</div>
                                            <input type="file" class="form-control file file-validate" name="profile" id="avatar" data-filename="profile_update">
                                            <p id="" class="file-error text-danger"></p>
                                        </label>
                                    </div>
                                    <span class="text-xs text-muted">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.')}}</span>
                                </div>

                            </div>
                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Save Changes')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                        </form>
                    </div>

            </div>

            
            @if ($employee != null || $userDetail->type == 'user')
            <div id="languageUser" class="card">
                <div class="card-header">
                    <h5>{{__('Language')}}<x-required></x-required></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($langUser as $item)
                        <div class="col-lg-10 col-sm-10 form-group">
                            <p>{{ $item->language_name ?? '' }}, {{ $item->level ?? '' }} </p>
                        </div>
                        <div class="col-lg-2 col-sm-2 form-group">
                            {!! Form::open(['method' => 'DELETE', 'route' => ['delete.lang.user', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                                <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                            {!! Form::close() !!}
                        </div>
                        @endforeach
                    </div>
                    <form method="post" action="{{ route('add.lang.user') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Language')}}</label>
                                    <input class="form-control" name="language_name" type="text" id="language_name" placeholder="{{ __('Language') }}" value="" required >
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 form-group">
                                <label for="language-level" class="col-form-label text-dark">{{ __('Level Language') }}:</label>
                                <select id="language-level" name="level" class="form-select form-control">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                            </div>

                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Add Language')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif

            @if ($employee != null || $userDetail->type == 'user')
            <div id="work_experience" class="card">
                <div class="card-header">
                    <h5>{{__('Work Experience')}}</h5>
                </div>
                <div class="card-body">

                    @foreach ($workExperiences as $item)
                        <div class="row">
                            <div class="col-lg-5 col-sm-5 form-group">
                                <p>{{ $item->job_title  ?? '' }} at {{ $item->company_name ?? '' }} ({{ \Carbon\Carbon::parse($item->start_date ?? '')->year }} - {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date ?? '')->year : 'Still working' }})</p>
                            </div>
                            <div class="col-lg-5 col-sm-5 form-group">
                                <p>{{ __('Job Detail') }}: {{$item->job_detail ?? '' }} </p>
                            </div>
                            <div class="col-lg-2 col-sm-2 form-group">
                                {!! Form::open(['method' => 'DELETE', 'route' => ['delete.work.experience', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                                    <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    @endforeach

                    <form method="post" action="{{route('add.work.experience')}}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Job Title')}}</label>
                                    <input class="form-control" name="job_title" type="text" id="job_title" placeholder="{{ __('Enter Job Title') }}" value="" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Company Name')}}</label>
                                    <input class="form-control" name="company_name" type="text" id="company_name" placeholder="{{ __('Enter Company Name') }}" value="" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Start Date')}}</label>
                                    <input class="form-control" name="start_date" type="date" id="start_date" placeholder="{{ __('Enter Start Date') }}" value="" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group" id="end_date_box">
                                    <label class="col-form-label text-dark">{{__('End Date')}}</label>
                                    <input class="form-control" name="end_date" style="display: block;" type="date" id="end_date" placeholder="{{ __('Enter End Date') }}" value="" >
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label for="still-working">Still working here:</label>
                                    <input type="checkbox" id="still-working" name="still_working" onchange="toggleEndDate()"><br><br> 
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Job Detail')}}</label>
                                    <textarea class="form-control" name="job_detail"  id="job_detail" cols="30" rows="10" placeholder="{{ __('Job Detail') }}" value="" required></textarea>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Add Work Experience')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif
            

            @if ($employee != null || $userDetail->type == 'user')
            <div id="education" class="card">
                <div class="card-header">
                    <h5>{{__('Education')}}<x-required></x-required></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($edus as $item)
                            <div class="col-lg-10 col-sm-10 form-group">
                                <p>{{ $item->educational_level ?? '' }}, {{ $item->academic_major ?? '' }}, at {{ $item->university ?? '' }} ({{ \Carbon\Carbon::parse($item->graduation_date ?? '')->year }})</p>
                            </div>
                            <div class="col-lg-2 col-sm-2 form-group">
                                {!! Form::open(['method' => 'DELETE', 'route' => ['delete.education', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                                    <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                                {!! Form::close() !!}
                            </div>
                        @endforeach
                    </div>
                    <form method="post" action="{{ route('add.education') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-sm-6 form-group">
                                <label for="education-level" class="col-form-label text-dark">Highest level of education:</label>
                                <select id="education-level" name="educational_level" class="form-select form-control" required>
                                    <option value="high-school">High School or Equivalent</option>
                                    <option value="Diploma Degree">Diploma</option>
                                    <option value="Bachelor's Degree">Bachelor's Degree</option>
                                    <option value="Higher Diploma">Higher Diploma</option>
                                    <option value="Master's Degree">Master's Degree</option>
                                    <option value="Doctorate">Doctorate</option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Country of Graduation')}}</label>
                                    <select id="country-select" name="country_of_graduation" class="form-select form-control" required>
                                        <option value="">{{ __('Select Country of Graduation') }}</option>
                                        @foreach ($availableCountries as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    {{-- <input class="form-control" name="country_of_graduation" type="text" id="country_of_graduation" placeholder="{{ __('Country of Graduation') }}" value="" required > --}}
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('University or Educational Institution')}}</label>
                                    <select id="university-select" name="university" class="form-select form-control" required>
                                        <option value="">{{ __('Select University or Educational Institution') }}</option>
                                    </select>
                                    {{-- <input class="form-control" name="university" type="text" id="university" placeholder="{{ __('University or Educational Institution') }}" value="" required > --}}
                                </div>
                            </div>

                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Academic Major')}}</label>
                                    <input class="form-control" name="academic_major" type="text" id="academic_major" placeholder="{{ __('Academic Major') }}" value="" required >
                                </div>
                            </div>


                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Graduation Date')}}</label>
                                    <input class="form-control" name="graduation_date" type="date" id="graduation_date" placeholder="{{ __('Graduation Date') }}" value="" required>
                                </div>
                            </div>

                          
                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Add Education')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif
            

            @if ($employee != null || $userDetail->type == 'user')
            <div id="skills" class="card">
                <div class="card-header">
                    <h5>{{__('Skills')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($skills as $item)
                        <div class="col-lg-10 col-sm-10 form-group">
                            <p>{{ $item->skill ?? '' }}, {{ $item->level ?? '' }} </p>
                        </div>
                        <div class="col-lg-2 col-sm-2 form-group">
                            {!! Form::open(['method' => 'DELETE', 'route' => ['delete.skill', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                                <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                            {!! Form::close() !!}
                        </div>
                        @endforeach
                    </div>
                    <form method="post" action="{{ route('add.skill') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Skill')}}</label>
                                    <input class="form-control" name="skill" type="text" id="skill" placeholder="{{ __('Skill') }}" value="" required >
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6 form-group">
                                <label for="skill-level" class="col-form-label text-dark">{{ __('Level Skill') }}:</label>
                                <select id="skill-level" name="skill_level" class="form-select form-control">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                            </div>

                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Add Skills')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif


            @if ($employee != null || $userDetail->type == 'user')
            <div id="membership" class="card">
                <div class="card-header">
                    <h5>{{__('Membership')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($membershipUser as $item)
                        <div class="col-lg-10 col-sm-10 form-group">
                            <p>{{ $item->organization_name ?? '' }}, {{ $item->role_in_organization ?? '' }} {{ __( 'since') }} {{ $item->member_since ?? '' }}</p>
                        </div>
                        <div class="col-lg-2 col-sm-2 form-group">
                            {!! Form::open(['method' => 'DELETE', 'route' => ['delete.membership.user', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                                <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                            {!! Form::close() !!}
                        </div>
                        @endforeach
                    </div>
                    <form method="post" action="{{ route('add.membership.user') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('organization_name')}}</label>
                                    <input class="form-control" name="organization_name" type="text" id="organization_name" placeholder="{{ __('organization_name') }}" value="" required >
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Role in organization')}}</label>
                                    <input class="form-control" name="role_in_organization" type="text" id="role_in_organization" placeholder="{{ __('Role in organization') }}" value="" required >
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Member Since')}}</label>
                                    <input class="form-control" name="member_since" type="date" id="member_since" placeholder="{{ __('Member Since') }}" value="" required>
                                </div>
                            </div>

                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Add Membership')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif

            @if ($employee != null || $userDetail->type == 'user')
            <div id="traning" class="card">
                <div class="card-header">
                    <h5>{{__('Training')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($traningUser as $item)
                        <div class="col-lg-6 col-sm-6 form-group">
                            <p>{{__('Training topic')}}: {{ $item->training_topic ?? '' }}</p>
                            <p>{{__('Institution')}}: {{ $item->institution ?? '' }}</p>
                            <p>{{__('Start date')}}: {{ $item->start_date ?? '' }}</p>
                            <p>{{__('Hours')}}: {{ $item->hours ?? '' }}</p>
                        </div>
                        {{-- <div class="col-lg-4 col-sm-4 form-group">
                            <img  id="zoomableImage" src="{{ asset('storage/uploads/certificate_file/' . $item->certificate_file) }}" class="big-logo">
                        </div> --}}


                        <div class="col-lg-4 col-sm-4 form-group image-container">
                            <img src="{{ asset('storage/uploads/certificate_file/' . $item->certificate_file) }}" alt="image" class="image" id="zoomableImage">
                        </div>
                        
                        <div id="fullscreenContainer" class="fullscreen" style="display: none;">
                            <img src="{{ asset('storage/uploads/certificate_file/' . $item->certificate_file) }}" alt="image" id="fullscreenImage">
                        </div>


                        <div class="col-lg-2 col-sm-2 form-group">
                            {!! Form::open(['method' => 'DELETE', 'route' => ['delete.training.user', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                                <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                            {!! Form::close() !!}
                        </div>
                        @endforeach
                    </div>
                    <form method="post" action="{{ route('add.training.user') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Training topic')}}</label>
                                    <input class="form-control" name="training_topic" type="text" id="training_topic" placeholder="{{ __('Training topic') }}" value="" required >
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Institution')}}</label>
                                    <input class="form-control" name="institution" type="text" id="institution" placeholder="{{ __('Institution') }}" value="" required >
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Start date')}}</label>
                                    <input class="form-control" name="start_date" type="date" id="start_date" placeholder="{{ __('Start date') }}" value="" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label text-dark">{{__('Hours')}}</label>
                                    <input class="form-control" name="hours" type="number" id="hours" placeholder="{{ __('Hours') }}" value="" required>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <input type="file" class="form-control " name="certificate_file" required >
                                    <span class="text-xs text-muted">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.')}}</span>
                                </div>

                            </div>

                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Add Training')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif

            @if ($employee != null || $userDetail->type == 'user')
            <div id="certificateUser" class="card">
                <div class="card-header">
                    <h5>{{__('Certificate')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($certificateUser as $item)
                        {{-- <div class="col-lg-4 col-sm-4 form-group">
                            <img id="image" src="{{ asset('storage/uploads/certificate_file/' . $item->certificate_file) }}" class="big-logo">
                        </div> --}}

                        
                        <div class="col-lg-4 col-sm-4 form-group image-container">
                            <img src="{{ asset('storage/uploads/certificate_file/' . $item->certificate_file) }}" alt="image" class="image" id="zoomableImage">
                        </div>
                        
                        <div id="fullscreenContainer" class="fullscreen" style="display: none;">
                            <img src="{{ asset('storage/uploads/certificate_file/' . $item->certificate_file) }}" alt="image" id="fullscreenImage">
                        </div>


                        <div class="col-lg-2 col-sm-2 form-group">
                            {!! Form::open(['method' => 'DELETE', 'route' => ['delete.certificate.user', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                                <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                            {!! Form::close() !!}
                        </div>
                        @endforeach
                    </div>
                    <form method="post" action="{{ route('add.certificate.user') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <input type="file" class="form-control " name="certificate_file" required >
                                    <span class="text-xs text-muted">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.')}}</span>
                                </div>
                            </div>

                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Add Certificate')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif

            @if ($employee != null || $userDetail->type == 'user')
            <div id="cvUser" class="card">
                <div class="card-header">
                    <h5>{{__('CV / Resume')}}<x-required></x-required></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($cvUser as $item)
                       
                        <div class="col-md-6 row">
                            <div class="info text-sm col-8">
                                <span><a href="{{ (!empty($item->cv_file)?asset('storage/uploads/cv_file').'/'.$item->cv_file:'') }}" target="_blank">{{ (!empty($item->cv_file)?$item->cv_file:'') }}</a></span>
                            </div>
                            <div class="col-lg-4 col-sm-4 form-group">
                                {!! Form::open(['method' => 'DELETE', 'route' => ['delete.cv.user', $item->id],'id'=>'delete-form-'.$item->id]) !!}
                                    <a href="#" class="btn btn-sm align-items-center bs-pass-para bg-danger" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$item->id}}').submit();"><i class="ti ti-trash text-white"></i></a>
                                {!! Form::close() !!}
                            </div>
                        </div>

                        
                        @endforeach
                    </div>
                    <form method="post" action="{{ route('add.cv.user') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <input type="file" class="form-control " name="cv_file" required >
                                    <span class="text-xs text-muted">{{ __('Please upload a valid file. Size of image should not be more than 2MB.')}}</span>
                                </div>
                            </div>

                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Add CV')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            @endif
            

            <div id="change_password" class="card">
                <div class="card-header">
                    <h5>{{__('Change Password')}}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{route('update.password')}}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-sm-6 form-group">
                                <label for="old_password" class="col-form-label text-dark">{{ __('Old Password') }}</label>
                                <input class="form-control" name="old_password" type="password" id="old_password" required autocomplete="old_password" placeholder="{{ __('Enter Old Password') }}">
                            </div>

                            <div class="col-lg-6 col-sm-6 form-group">
                                <label for="password" class="col-form-label text-dark">{{ __('New Password') }}</label>
                                <input class="form-control" name="password" type="password" required autocomplete="new-password" id="password" placeholder="{{ __('Enter Your New Password') }}">
                            </div>
                            <div class="col-lg-6 col-sm-6 form-group">
                                <label for="password_confirmation" class="col-form-label text-dark">{{ __('New Confirm Password') }}</label>
                                <input class="form-control" name="password_confirmation" type="password" required autocomplete="new-password" id="password_confirmation" placeholder="{{ __('Enter Your Confirm Password') }}">
                            </div>
                            <div class="col-lg-12 text-end">
                                <input type="submit" value="{{__('Change Password')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                            </div>
                        </div>
                    </form>
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

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let images = document.querySelectorAll(".image");
                let fullscreenContainer = document.getElementById("fullscreenContainer");
                let fullscreenImage = document.getElementById("fullscreenImage");

                images.forEach(image => {
                    image.addEventListener("click", function() {
                        fullscreenImage.src = this.src; 
                        fullscreenContainer.style.display = "flex";
                    });
                });

                fullscreenContainer.addEventListener("click", function() {
                    fullscreenContainer.style.display = "none";
                });
            });
        </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            $(document).ready(function() {
                $(document).ready(function() {
                $('#country-select').on('change', function() {
                    var country = $(this).val();

                    if (country) {
                        $.ajax({
                            url: '/universities-by-country', 
                            data: { country: country },
                            method: 'GET',
                            success: function(response) {
                                $('#university-select').empty();

                                $.each(response, function(index, university) {
                                    $('#university-select').append('<option value="'+ university.name +'">'+ university.name +'</option>');
                                });
                            },
                            error: function(xhr, status, error) {
                                console.log('Error:', error);
                            }
                        });
                    } else {
                        $('#university-select').empty();
                        $('#university-select').append('<option value="">Select University or Educational Institution</option>');
                    }
                });
            });
});
           
        </script>

@endsection
