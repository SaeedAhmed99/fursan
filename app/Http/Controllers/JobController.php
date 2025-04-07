<?php

namespace App\Http\Controllers;

use App\Events\VerifyReCaptchaToken;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Branch;
use App\Models\CustomQuestion;
use App\Models\CVUser;
use App\Models\Edu;
use App\Models\Job;
use App\Models\JobStage;
use App\Models\Utility;
use App\Models\JobApplication;
use App\Models\JobApplicationNote;
use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Role;
use App\Models\ExperienceCertificate;
use App\Models\GenerateOfferLetter;
use App\Models\JoiningLetter;
use App\Models\NOC;
use App\Models\Skill;
use App\Models\UserLanguage;
use App\Models\WorkExperience;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Auth;
use Carbon\Carbon;

class JobController extends Controller
{

    public function index()
    {
        if(\Auth::user()->can('manage job'))
        {
            $jobs = Job::where('created_by', '=', \Auth::user()->creatorId())->with('branches','createdBy')->get();

            $data['total']     = Job::where('created_by', '=', \Auth::user()->creatorId())->count();
            $data['active']    = Job::where('status', 'active')->where('created_by', '=', \Auth::user()->creatorId())->count();
            $data['in_active'] = Job::where('status', 'in_active')->where('created_by', '=', \Auth::user()->creatorId())->count();

            return view('job.index', compact('jobs', 'data'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        $categories = JobCategory::where('created_by', \Auth::user()->creatorId())->get()->pluck('title', 'id');
        $categories->prepend('--', '');

        $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $branches->prepend('All', 0);

        $status = Job::$status;

        $customQuestion = CustomQuestion::where('created_by', \Auth::user()->creatorId())->get();

        return view('job.create', compact('categories', 'status', 'branches', 'customQuestion'));
    }

    public function store(Request $request)
    {

        if(\Auth::user()->can('create job'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'title' => 'required',
                                   'branch' => 'required',
                                   'category' => 'required',
                                   'skill' => 'required',
                                   'position' => 'required|integer',
                                   'start_date' => 'required',
                                   'end_date' => 'required',
                                   'description' => 'required',
                                   'requirement' => 'required',
                                   'job_type' => 'required',
                                   'years_of_experience' => 'required',
                                   'show_pay_by' => 'required',
                                   'starting_salary' => 'required',
                                   'currency' => 'required',
                                   // 'rate' => 'required',
                                   'major' => 'required',
                                   'degree' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job                  = new Job();
            $job->title           = $request->title;
            $job->branch          = $request->branch;
            $job->category        = $request->category;
            $job->skill           = $request->skill;
            $job->position        = $request->position;
            $job->status          = $request->status;
            $job->start_date      = $request->start_date;
            $job->end_date        = $request->end_date;
            $job->description     = $request->description;
            $job->requirement     = $request->requirement;
            $job->job_type     = $request->job_type;
            $job->years_of_experience     = $request->years_of_experience;
            $job->starting_salary     = $request->starting_salary;
            $job->show_pay_by     = $request->show_pay_by;
            $job->currency     = $request->currency;
            // $job->rate     = $request->rate;
            $job->major     = $request->major;
            $job->degree     = $request->degree;
            $job->code            = uniqid();
            $job->applicant       = !empty($request->applicant) ? implode(',', $request->applicant) : '';
            $job->visibility      = !empty($request->visibility) ? implode(',', $request->visibility) : '';
            $job->custom_question = !empty($request->custom_question) ? implode(',', $request->custom_question) : '';
            $job->created_by      = \Auth::user()->creatorId();
            $job->save();

            return redirect()->route('job.index')->with('success', __('Job  successfully created.'));
        }
        else
        {
            return redirect()->route('job.index')->with('error', __('Permission denied.'));
        }
    }

    public function show(Job $job)
    {
        $status          = Job::$status;
        $job->applicant  = !empty($job->applicant) ? explode(',', $job->applicant) : '';
        $job->visibility = !empty($job->visibility) ? explode(',', $job->visibility) : '';
        $job->skill      = !empty($job->skill) ? explode(',', $job->skill) : '';

        return view('job.show', compact('status', 'job'));
    }

    public function edit(Job $job)
    {
        $categories = JobCategory::where('created_by', \Auth::user()->creatorId())->get()->pluck('title', 'id');
        $categories->prepend('--', '');

        $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $branches->prepend('All', 0);

        $status = Job::$status;

        $job->applicant       = explode(',', $job->applicant);
        $job->visibility      = explode(',', $job->visibility);
        $job->custom_question = explode(',', $job->custom_question);

        $customQuestion = CustomQuestion::where('created_by', \Auth::user()->creatorId())->get();

        return view('job.edit', compact('categories', 'status', 'branches', 'job', 'customQuestion'));
    }

    public function update(Request $request, Job $job)
    {
        if(\Auth::user()->can('edit job'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'title' => 'required',
                                   'branch' => 'required',
                                   'category' => 'required',
                                   'skill' => 'required',
                                   'position' => 'required|integer',
                                   'start_date' => 'required',
                                   'end_date' => 'required',
                                   'description' => 'required',
                                   'requirement' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job->title           = $request->title;
            $job->branch          = $request->branch;
            $job->category        = $request->category;
            $job->skill           = $request->skill;
            $job->position        = $request->position;
            $job->status          = $request->status;
            $job->start_date      = $request->start_date;
            $job->end_date        = $request->end_date;
            $job->description     = $request->description;
            $job->requirement     = $request->requirement;

            $job->job_type     = $request->job_type;
            $job->years_of_experience     = $request->years_of_experience;
            $job->starting_salary     = $request->starting_salary;
            $job->show_pay_by     = $request->show_pay_by;
            $job->currency     = $request->currency;
            // $job->rate     = $request->rate;
            $job->major     = $request->major;
            $job->degree     = $request->degree;

            $job->applicant       = !empty($request->applicant) ? implode(',', $request->applicant) : '';
            $job->visibility      = !empty($request->visibility) ? implode(',', $request->visibility) : '';
            $job->custom_question = !empty($request->custom_question) ? implode(',', $request->custom_question) : '';
            $job->save();

            return redirect()->route('job.index')->with('success', __('Job  successfully updated.'));
        }
        else
        {
            return redirect()->route('job.index')->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Job $job)
    {
        $application = JobApplication::where('job', $job->id)->get()->pluck('id');
        JobApplicationNote::whereIn('application_id', $application)->delete();
        JobApplication::where('job', $job->id)->delete();
        $job->delete();

        return redirect()->route('job.index')->with('success', __('Job  successfully deleted.'));
    }

    public function career($id, $lang)
    {
        $jobs= Job::where('created_by', $id)->with(['branches','createdBy'])->orderBy('created_at', 'desc')->get();

        \Session::put('lang', $lang);

        App::setLocale($lang);

        $companySettings['title_text']      = \DB::table('settings')->where('created_by', $id)->where('name', 'title_text')->first();
        $companySettings['footer_text']     = \DB::table('settings')->where('created_by', $id)->where('name', 'footer_text')->first();
        $companySettings['company_favicon'] = \DB::table('settings')->where('created_by', $id)->where('name', 'company_favicon')->first();
        $companySettings['company_logo']    = \DB::table('settings')->where('created_by', $id)->where('name', 'company_logo')->first();
        $languages                          = Utility::languages();

        $currantLang = \Session::get('lang');
        if(empty($currantLang))
        {
            $user        = User::find($id);
            $currantLang = !empty($user) && !empty($user->lang) ? $user->lang : 'en';
        }


        return view('job.career', compact('companySettings', 'jobs', 'languages', 'currantLang','id'));
    }


    public function ShowUserLogin($lang = '') {
        if($lang == '')
        {
            $lang = Utility::getValByName('default_language');
        }

        $langList = Utility::languages()->toArray();
        $lang = array_key_exists($lang, $langList) ? $lang : 'en';

        \App::setLocale($lang);

        $settings = Utility::settings();

        return view('authuserapply.login', compact('lang','settings'));
    }

    public function showRegistrationForm(Request $request, $ref = '' , $lang = '')
    {
        $settings = Utility::settings();

        if($settings['enable_signup'] == 'on')
        {
            $langList = Utility::languages()->toArray();
            $lang = array_key_exists($lang, $langList) ? $lang : 'en';

            if($lang == '')
            {
                $lang = Utility::getValByName('default_language');
            }
            \App::setLocale($lang);
            if($ref == '')
            {
                $ref = 0;
            }

            $plan = null;
            if($request->plan){
                $plan = $request->plan;
            }
            return view('authuserapply.register', compact('lang' , 'ref', 'plan'));
        }
        else
        {
            return \Redirect::to('job.apply.user.login');
        }
    }


        /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function newUserStore(Request $request)
    {
        if (!Role::where('name', 'user')->exists()) {
            Role::create([
                'name' => 'user',
                'guard_name' => 'web',
                'created_by' => 0,
            ]);
        }
        $settings = Utility::settings();
        //ReCpatcha
        $validation = [];
        if(isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'on')
        {
            if($settings['google_recaptcha_version'] == 'v2-checkbox'){
                $validation['g-recaptcha-response'] = 'required|captcha';
            }
            elseif($settings['google_recaptcha_version'] == 'v3-checkbox'){
                $result = event(new VerifyReCaptchaToken($request));

                if (!isset($result[0]['status']) || $result[0]['status'] != true) {
                    $key = 'g-recaptcha-response';
                    $request->merge([$key => null]); // Set the key to null

                    $validation['g-recaptcha-response'] = 'required';
                }
            }else{
                $validation = [];
            }
        }else{
            $validation = [];
        }

        $this->validate($request, $validation);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string',
                         'min:8','confirmed', Rules\Password::defaults()],
            'terms' => 'required',
        ]);

        do {
            $code = rand(100000, 999999);
        } while (User::where('referral_code', $code)->exists());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'user',
            'default_pipeline' => 1,
            'plan' => 1,
            'lang' => Utility::getValByName('default_language'),
            'avatar' => '',
            'referral_code'=> 0,
            'used_referral_code'=> 0,
            'created_by' => 1,
            'email_verified_at' => Carbon::now(), 
        ]);
        \Auth::login($user);
         return redirect()->route('profile');


    }

    public function jobRequirement($code, $lang)
    {
        $job = Job::where('code', $code)->first();
        if($job->status == 'in_active')
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        \Session::put('lang', $lang);

        \App::setLocale($lang);

        $companySettings['title_text']      = \DB::table('settings')->where('created_by', $job->created_by)->where('name', 'title_text')->first();
        $companySettings['footer_text']     = \DB::table('settings')->where('created_by', $job->created_by)->where('name', 'footer_text')->first();
        $companySettings['company_favicon'] = \DB::table('settings')->where('created_by', $job->created_by)->where('name', 'company_favicon')->first();
        $companySettings['company_logo']    = \DB::table('settings')->where('created_by', $job->created_by)->where('name', 'company_logo')->first();
        $languages                          = Utility::languages();

        $currantLang = \Session::get('lang');
        if(empty($currantLang))
        {
            $currantLang = !empty($job->createdBy) ? $job->createdBy->lang : 'en';
        }


        return view('job.requirement', compact('companySettings', 'job', 'languages', 'currantLang'));
    }

    public function jobApply($code, $lang)
    {
        \Session::put('lang', $lang);

        \App::setLocale($lang);

        $job                                = Job::where('code', $code)->first();
        if (!$job) {
            return redirect()->back()->with('error', __('Job Not Found.'));
        }
        $companySettings['title_text']      = \DB::table('settings')->where('created_by', $job->created_by)->where('name', 'title_text')->first();
        $companySettings['footer_text']     = \DB::table('settings')->where('created_by', $job->created_by)->where('name', 'footer_text')->first();
        $companySettings['company_favicon'] = \DB::table('settings')->where('created_by', $job->created_by)->where('name', 'company_favicon')->first();
        $companySettings['company_logo']    = \DB::table('settings')->where('created_by', $job->created_by)->where('name', 'company_logo')->first();

        $customQuestionIds = explode(',', $job->custom_question);
        $questions = CustomQuestion::whereIn('id', $customQuestionIds)->where('created_by', $job->created_by)->get();

        $languages = Utility::languages();

        $currantLang = \Session::get('lang');
        if(empty($currantLang))
        {
            $currantLang = !empty($job->createdBy) ? $job->createdBy->lang : 'en';
        }


        return view('job.apply', compact('companySettings', 'job', 'questions', 'languages', 'currantLang'));
    }


    public function checkProfileCompletion()
{
    $userDetail = \Auth::user();
    $user = User::findOrFail($userDetail['id']);

    $basicInfoComplete = $user->name && $user->email && $user->phone && $user->dob && $user->country_of_residence && $user->nationality && $user->address && $user->gender;
    $languageComplete = UserLanguage::where('user_id', $user->id)->get()->count() > 0;
    $educationComplete = Edu::where('user_id', $user->id)->get()->count() > 0;
    $cvComplete = CVUser::where('user_id', $user->id)->get()->count() > 0;

    $isProfileComplete = $basicInfoComplete && $languageComplete && $educationComplete && $cvComplete;

    return $isProfileComplete;
}


    public function jobApplyDataDirect(Request $request, $code)
    {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $job = Job::where('code', $code)->first();

        if ($this->checkProfileCompletion() == false) {
            return redirect()->back()->with('error', __('You must complete your profile before applying for the job.'));
        }

        $checkIfApply = JobApplication::where('job', $job->id)->where('user_id', $user->id)->get();
        if ($checkIfApply->count() >= 1) {
            return redirect()->back()->with('success', __('You have already applied for this job.'));
        }
        
        $stage=JobStage::where('created_by',$job->created_by)->first();
        $jobApplication                  = new JobApplication();
        $jobApplication->job             = $job->id;
        $jobApplication->name            = $user->name;
        $jobApplication->email           = $user->email;
        $jobApplication->phone           = $user->phone;
        $jobApplication->profile         = null;
        $jobApplication->resume          = null;
        $jobApplication->cover_letter    = null;
        $jobApplication->dob             = $user->dob;
        $jobApplication->gender          = $user->gender;
        $jobApplication->country         = $user->country_of_residence;
        $jobApplication->state           = null;
        $jobApplication->city            = null;
        $jobApplication->custom_question = null;
        $jobApplication->created_by      = $job->created_by;
        $jobApplication->stage           = $stage->id;
        $jobApplication->user_id         = $user->id;
        $jobApplication->cover_letter    = $request->cover_letter;
        $jobApplication->save();

        return redirect()->back()->with('success', __('Job application successfully send'));

    }


    public function jobApplyData(Request $request, $code)
    {

        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required',
                               'email' => 'required',
                               'phone' => 'required',
//                               'profile' => 'mimes:jpeg,png,jpg,gif,svg|max:20480',
//                               'resume' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,zip|max:20480',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $job = Job::where('code', $code)->first();

        if(!empty($request->profile))
        {

            //storage limit
            $image_size = $request->file('profile')->getSize();
            $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
            if($result==1)
            {
                $filenameWithExt = $request->file('profile')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('profile')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $dir        = 'uploads/job/profile';

                $image_path = $dir . $filenameWithExt;
                if (\File::exists($image_path)) {
                    \File::delete($image_path);
                }
                $url = '';
                $path = Utility::upload_file($request,'profile',$fileNameToStore,$dir,[]);
            }
            else
            {
                $fileNameToStore ='';
            }


        }


        if(!empty($request->resume))
        {

            //storage limit
            $image_size = $request->file('resume')->getSize();
            $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);


            if($result==1)
            {

                $filenameWithExt1 = $request->file('resume')->getClientOriginalName();
                $filename1        = pathinfo($filenameWithExt1, PATHINFO_FILENAME);
                $extension1       = $request->file('resume')->getClientOriginalExtension();
                $fileNameToStore1 = $filename1 . '_' . time() . '.' . $extension1;

                $dir        = 'uploads/job/resume';

                $image_path = $dir . $filenameWithExt1;
                if (\File::exists($image_path)) {
                    \File::delete($image_path);
                }
                $url = '';
                $path = Utility::upload_file($request,'resume',$fileNameToStore1,$dir,[]);
            }

            else
            {
                $fileNameToStore1 ='';
            }


        }


        $stage=JobStage::where('created_by',$job->created_by)->first();
        $jobApplication                  = new JobApplication();
        $jobApplication->job             = $job->id;
        $jobApplication->name            = $request->name;
        $jobApplication->email           = $request->email;
        $jobApplication->phone           = $request->phone;
        $jobApplication->profile         = !empty($request->profile) ? $fileNameToStore : '';
        $jobApplication->resume          = !empty($request->resume) ? $fileNameToStore1 : '';
        $jobApplication->cover_letter    = $request->cover_letter;
        $jobApplication->dob             = $request->dob;
        $jobApplication->gender          = $request->gender;
        $jobApplication->country         = $request->country;
        $jobApplication->state           = $request->state;
        $jobApplication->city            = $request->city;
        $jobApplication->custom_question = json_encode($request->question);
        $jobApplication->created_by      = $job->created_by;
        $jobApplication->stage           = $stage->id;
        $jobApplication->save();


        return redirect()->back()->with('success', __('Job application successfully send'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));

    }


}
