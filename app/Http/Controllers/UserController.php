<?php

namespace App\Http\Controllers;

use App\Models\CertificateUser;
use App\Models\CustomField;
use App\Models\CVUser;
use App\Models\Edu;
use App\Models\Employee;
use App\Models\ExperienceCertificate;
use App\Models\GenerateOfferLetter;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JoiningLetter;
use App\Models\LoginDetail;
use App\Models\Membership;
use App\Models\NOC;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserToDo;
use App\Models\Utility;
use Auth;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Lab404\Impersonate\Impersonate;
use Spatie\Permission\Models\Role;
use App\Models\ReferralTransaction;
use App\Models\ReferralSetting;
use App\Models\Skill;
use App\Models\Traning;
use App\Models\UserLanguage;
use App\Models\WorkExperience;
use Illuminate\Validation\Rule;
use App\Providers\UniversityService;

class UserController extends Controller
{

    protected $universityService;

    public function __construct(UniversityService $universityService)
    {
        $this->universityService = $universityService;
    }


    public function index(Request $request)
    {
        User::defaultEmail();

        $user = \Auth::user();
       
        if (\Auth::user()->can('manage user')) {
            if (\Auth::user()->type == 'super admin') {
                $users = User::where('created_by', '=', $user->creatorId())->where('type', '=', 'company');
            } else {
                //$users = User::where('created_by', '=', $user->creatorId())->where('type', '!=', 'client')->with(['currentPlan'])->get();
                $users = User::where('created_by', '=', $user->creatorId())->where('type', '!=', 'client');
            }

            if ($request->filled('email')) {
                $users->where('email', 'LIKE', '%' . $request->input('email') . '%');
            }

            if ($request->filled('name')) {
                $users->where('name', 'LIKE', '%' . $request->input('name') . '%');
            }
        
            if ($request->filled('nationality')) {
                $users->where('nationality', 'LIKE', '%' . $request->input('nationality') . '%');
            }
        
            if ($request->filled('country_of_residence')) {
                $users->where('country_of_residence', 'LIKE', '%' . $request->input('country_of_residence') . '%');
            }

            switch ($request->input('sort')) {
                case 'oldest':
                    $users->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                    $users->orderBy('created_at', 'desc');
                    break;
                case 'age_asc':
                    $users->orderBy('dob', 'asc');
                    break;
                case 'age_desc':
                    $users->orderBy('dob', 'desc');
                    break;
                case 'name_asc':
                    $users->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $users->orderBy('name', 'desc');
                    break;
                default:
                    $users->orderBy('created_at', 'asc'); 
            }
            $users = $users->with(['currentPlan'])->get();

            return view('user.index')->with('users', $users);
        } else {
            return redirect()->back();
        }

    }

    public function create()
    {

        $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();
        $user = \Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->where('name', '!=', 'client')->get()->pluck('name', 'id');
        if (\Auth::user()->can('create user')) {
            return view('user.create', compact('roles', 'customFields'));
        } else {
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {

        if (\Auth::user()->can('create user')) {
            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->where('created_by', '=', \Auth::user()->creatorId())->first();
            $objUser = \Auth::user()->creatorId();

            if (\Auth::user()->type == 'super admin') {
                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $enableLogin = 0;
                if (!empty($request->password_switch) && $request->password_switch == 'on') {
                    $enableLogin = 1;
                    $validator = \Validator::make(
                        $request->all(), ['password' => 'required|min:6']
                    );

                    if ($validator->fails()) {
                        return redirect()->back()->with('error', $validator->errors()->first());
                    }
                }
                $userpassword = $request->input('password');
                $settings = Utility::settings();

                do {
                    $code = rand(100000, 999999);
                } while (User::where('referral_code', $code)->exists());

                $user = new User();
                $user['name'] = $request->name;
                $user['email'] = $request->email;
                $psw = $request->password;
                $user['password'] = !empty($userpassword)?\Hash::make($userpassword) : null;
                $user['type'] = 'company';
                $user['default_pipeline'] = 1;
                $user['plan'] = 1;
                $user['lang'] = !empty($default_language) ? $default_language->value : 'en';
                $user['referral_code'] = $code;
                $user['created_by'] = \Auth::user()->creatorId();
                $user['plan'] = Plan::first()->id;
                if ($settings['email_verification'] == 'on') {

                    $user['email_verified_at'] = null;
                } else {
                    $user['email_verified_at'] = date('Y-m-d H:i:s');
                }
                $user['is_enable_login'] = $enableLogin;

                $user->save();
                $role_r = Role::findByName('company');
                $user->assignRole($role_r);
                //                $user->userDefaultData();
                $user->userDefaultDataRegister($user->id);
                $user->userWarehouseRegister($user->id);

                //default bank account for new company
                $user->userDefaultBankAccount($user->id);

                Utility::chartOfAccountTypeData($user->id);
                // Utility::chartOfAccountData($user);
                // default chart of account for new company
                Utility::chartOfAccountData1($user->id);

                Utility::pipeline_lead_deal_Stage($user->id);
                Utility::project_task_stages($user->id);
                Utility::labels($user->id);
                Utility::sources($user->id);
                Utility::jobStage($user->id);
                GenerateOfferLetter::defaultOfferLetterRegister($user->id);
                ExperienceCertificate::defaultExpCertificatRegister($user->id);
                JoiningLetter::defaultJoiningLetterRegister($user->id);
                NOC::defaultNocCertificateRegister($user->id);
            } else {
                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users',
                        //    'email' => 'required|email|unique:users,email,NULL,id,created_by,' . $objUser,
                        'role' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $enableLogin = 0;
                if (!empty($request->password_switch) && $request->password_switch == 'on') {
                    $enableLogin = 1;
                    $validator = \Validator::make(
                        $request->all(), ['password' => 'required|min:6']
                    );

                    if ($validator->fails()) {
                        return redirect()->back()->with('error', $validator->errors()->first());
                    }
                }

                $objUser = User::find($objUser);
                $user = User::find(\Auth::user()->created_by);
                $total_user = $objUser->countUsers();
                $plan = Plan::find($objUser->plan);
                $userpassword = $request->input('password');
                if ($total_user < $plan->max_users || $plan->max_users == -1) {
                    $role_r = Role::findById($request->role);
                    $psw = $request->password;
                    $request['password'] = !empty($userpassword)?\Hash::make($userpassword) : null;
                    $request['type'] = $role_r->name;
                    $request['lang'] = !empty($default_language) ? $default_language->value : 'en';
                    $request['created_by'] = \Auth::user()->creatorId();
                    $request['email_verified_at'] = date('Y-m-d H:i:s');
                    $request['is_enable_login'] = $enableLogin;

                    $user = User::create($request->all());
                    $user->assignRole($role_r);
                    if ($request['type'] != 'client') {
                        \App\Models\Utility::employeeDetails($user->id, \Auth::user()->creatorId());
                    }

                } else {
                    return redirect()->back()->with('error', __('Your user limit is over, Please upgrade plan.'));
                }
            }
            // Send Email
            $setings = Utility::settings();
            if ($setings['new_user'] == 1) {

                $user->password = $psw;
                $user->type = $role_r->name;
                $user->userDefaultDataRegister($user->id);

                $userArr = [
                    'email' => $user->email,
                    'password' => $user->password,
                ];
                $resp = Utility::sendEmailTemplate('new_user', [$user->id => $user->email], $userArr);

                if (\Auth::user()->type == 'super admin') {
                    return redirect()->route('users.index')->with('success', __('Company successfully created.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                } else {
                    return redirect()->route('users.index')->with('success', __('User successfully created.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

                }
            }
            if (\Auth::user()->type == 'super admin') {
                return redirect()->route('users.index')->with('success', __('Company successfully created.'));
            } else {
                return redirect()->route('users.index')->with('success', __('User successfully created.'));

            }

        } else {
            return redirect()->back();
        }

    }
    public function show()
    {
        return redirect()->route('user.index');
    }

    public function edit($id)
    {
        $user = \Auth::user();
        $roles = Role::where('created_by', '=', $user->creatorId())->where('name', '!=', 'client')->get()->pluck('name', 'id');
        if (\Auth::user()->can('edit user')) {
            $user = User::findOrFail($id);
            $user->customField = CustomField::getData($user, 'user');
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();

            return view('user.edit', compact('user', 'roles', 'customFields'));
        } else {
            return redirect()->back();
        }

    }

    public function update(Request $request, $id)
    {

        if (\Auth::user()->can('edit user')) {
            if (\Auth::user()->type == 'super admin') {
                $user = User::findOrFail($id);
                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $id,
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                //                $role = Role::findById($request->role);
                $role = Role::findByName('company');
                $input = $request->all();
                $input['type'] = $role->name;

                $user->fill($input)->save();
                CustomField::saveData($user, $request->customField);

                $roles[] = $role->id;
                $user->roles()->sync($roles);

                return redirect()->route('users.index')->with(
                    'success', 'company successfully updated.'
                );
            } else {
                $user = User::findOrFail($id);
                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $id,
                        // 'email' => 'required|email|unique:users,email,' . $id . ',id,created_by,' . \Auth::user()->creatorId(),
                        'role' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $role = Role::findById($request->role);
                $input = $request->all();
                $input['type'] = $role->name;
                $user->fill($input)->save();
                Utility::employeeDetailsUpdate($user->id, \Auth::user()->creatorId());
                CustomField::saveData($user, $request->customField);

                $roles[] = $request->role;
                $user->roles()->sync($roles);

                return redirect()->route('users.index')->with(
                    'success', 'User successfully updated.'
                );
            }
        } else {
            return redirect()->back();
        }
    }

    public function destroy($id)
    {

        if (\Auth::user()->can('delete user')) {
            if ($id == 2) {
                return redirect()->back()->with('error', __('You can not delete By default Company'));
            }

            $user = User::find($id);
            if ($user) {
                if (\Auth::user()->type == 'super admin') {
                    // $referralSetting = ReferralSetting::where('created_by' , 1)->first();
                    // $users = ReferralTransaction::where('company_id' , $id)->first();
                    // $plan = Plan::find($users->plan_id);
                    // Utility::commissionAmount($plan , $referralSetting , $users->referral_code , 'minus');

                    $transaction = ReferralTransaction::where('company_id' , $id)->delete();

                    $users = User::where('created_by', $id)->delete();
                    $employee = Employee::where('created_by', $id)->delete();

                    $user->delete();

                    return redirect()->back()->with('success', __('Company Successfully deleted'));
                }

                if (\Auth::user()->type == 'company') {

                    $employee = Employee::where(['user_id' => $user->id])->delete();
                    if ($employee) {
                        $delete_user = User::where(['id' => $user->id])->delete();

                        if ($delete_user) {
                            return redirect()->route('users.index')->with('success', __('User successfully deleted .'));
                        } else {
                            return redirect()->back()->with('error', __('Something is wrong.'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('Something is wrong.'));
                    }
                }
                return redirect()->route('users.index')->with('success', __('User successfully deleted .'));
            } else {
                return redirect()->back()->with('error', __('Something is wrong.'));
            }
        } else {
            return redirect()->back();
        }
    }

    public function allJobs() {
        $userDetail = \Auth::user();
        $userDetail->customField = CustomField::getData($userDetail, 'user');
        $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first();

        $jobs = Job::with('branches','createdBy')->get();
        return view('user.allJobs', compact('userDetail', 'customFields', 'user', 'employee', 'jobs'));
    }

    public function allJobsAplly() {
        $userDetail = \Auth::user();
        $jobsApplay = JobApplication::where('user_id', $userDetail->id)->get();
        
        return view('user.allJobsapply', compact('jobsApplay'));
    }

    public function index01(Request $request)
    {
        $availableCountries = $this->universityService->getAvailableCountries(); 
        $selectedCountry = $request->country ?? 'Egypt';
        $universities = $this->universityService->getUniversitiesByCountry($selectedCountry);
        $universities = collect($universities);
        // dd($universities[0]);
        return view('user.university', compact('availableCountries', 'selectedCountry', 'universities'));
    }


    public function getUniversitiesByCountry(Request $request)
    {
        $country = $request->query('country');
        $universities = $this->universityService->getUniversitiesByCountry($country);

        return response()->json($universities);
    }


    public function profile()
    {
        $userDetail = \Auth::user();
        $userDetail->customField = CustomField::getData($userDetail, 'user');
        $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'user')->get();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first();
        $workExperiences = $employee->workExperiences ?? [];
        $skills = $employee->skills ?? [];
        $edus = $employee->edus ?? [];
        $langUser = $employee->languageUser ?? [];
        $membershipUser = $employee->membership ?? [];
        $traningUser = $employee->traning ?? [];
        $certificateUser = $employee->certificateUser ?? [];
        $cvUser = $employee->cvUser ?? [];
        $availableCountries = $this->universityService->getAvailableCountries(); 
        if ($user->type == 'user') {
            $workExperiences = WorkExperience::where('user_id', $user->id)->get();
            $skills = Skill::where('user_id', $user->id)->get();
            $edus = Edu::where('user_id', $user->id)->get();
            $langUser = UserLanguage::where('user_id', $user->id)->get();
            $membershipUser = Membership::where('user_id', $user->id)->get();
            $traningUser = Traning::where('user_id', $user->id)->get();
            $certificateUser = CertificateUser::where('user_id', $user->id)->get();
            $cvUser = CVUser::where('user_id', $user->id)->get();
        }
        return view('user.profile', compact('userDetail', 'customFields', 'workExperiences', 'skills', 'edus', 'employee', 'langUser', 'membershipUser' , 'traningUser' , 'certificateUser','cvUser' , 'availableCountries'));
    }


    public function deleteskill($id) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);

        $item = Skill::findOrFail($id);
        $item->delete();

        return redirect()->back()->with(
            'success', 'Delete skill successfully.'
        );
    }

    public function deleteLangUser($id) {
        $item = UserLanguage::findOrFail($id);
        $item->delete();

        return redirect()->back()->with(
            'success', 'Delete language successfully.'
        );
    }

    public function addskill(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first();

        
        if ($userDetail->type == 'company') {
            $userDetail = User::findOrFail($request->employeeUser);
            $user = User::findOrFail($userDetail['id']);
            $employee = $user->employee()->first();
        }
        $validator = \Validator::make(
            $request->all(), [
                'skill' => 'required|string|max:255',
                'skill_level' => 'required|string|max:255',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($user->type == 'user') {
            $skills = Skill::create([
                'skill' => $request['skill'],
                'level' => $request['skill_level'],
                'user_id' => $user->id,
            ]);
        } else {
            $skills = $employee->skills()->create([
                'skill' => $request['skill'],
                'level' => $request['skill_level'],
                'user_id' => $request->employeeUser,

            ]);
            $skills->save();
        }
        
        return redirect()->back()->with(
            'success', 'Add skill successfully.'
        );
    }


    public function addMembershipUser(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first();

        if ($userDetail->type == 'company') {
            $userDetail = User::findOrFail($request->employeeUser);
            $user = User::findOrFail($userDetail['id']);
            $employee = $user->employee()->first();
        }

        $validator = \Validator::make(
            $request->all(), [
                'organization_name' => 'required|string|max:255',
                'role_in_organization' => 'required|string|max:255',
                'member_since' => 'required|date',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($user->type == 'user') {
            $membershipUser = Membership::create([
                'organization_name' => $request['organization_name'],
                'role_in_organization' => $request['role_in_organization'],
                'member_since' => $request['member_since'],
                'user_id' => $user->id,
            ]);
        } else {
            $membershipUser = $employee->membership()->create([
                'organization_name' => $request['organization_name'],
                'role_in_organization' => $request['role_in_organization'],
                'member_since' => $request['member_since'],
                'user_id' => $request->employeeUser,

            ]);
            $membershipUser->save();
        }
        
        return redirect()->back()->with(
            'success', 'Add membership successfully.'
        );
    }

    public function deleteMembershipUser($id) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);

        $item = Membership::findOrFail($id);
        $item->delete();

        return redirect()->back()->with(
            'success', 'Delete membership successfully.'
        );
    }

    public function addTrainingUser(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first();

        if ($userDetail->type == 'company') {
            $userDetail = User::findOrFail($request->employeeUser);
            $user = User::findOrFail($userDetail['id']);
            $employee = $user->employee()->first();
        }

        $validator = \Validator::make(
            $request->all(), [
                'training_topic' => 'required|string|max:255',
                'institution' => 'required|string|max:255',
                'certificate_file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'start_date' => 'required|date',
                'hours' => 'required|integer',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->hasFile('certificate_file')) {
            $filenameWithExt = $request->file('certificate_file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('certificate_file')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if ($settings['storage_setting'] == 'local') {
                $dir = 'uploads/certificate_file/';
            } else {
                $dir = 'uploads/certificate_file';
            }

            $url = '';
            $path = Utility::upload_file($request, 'certificate_file', $fileNameToStore, $dir, []);
        }

        if ($user->type == 'user') {
            $traningUser = Traning::create([
                'training_topic' => $request['training_topic'],
                'institution' => $request['institution'],
                'start_date' => $request['start_date'],
                'hours' => $request['hours'],
                'certificate_file' => $fileNameToStore,
                'user_id' => $user->id,
            ]);
        } else {
            $traningUser = $employee->traning()->create([
                'training_topic' => $request['training_topic'],
                'institution' => $request['institution'],
                'start_date' => $request['start_date'],
                'hours' => $request['hours'],
                'certificate_file' => $fileNameToStore,
                'user_id' => $request->employeeUser,

            ]);
            $traningUser->save();
        }
        
        return redirect()->back()->with(
            'success', 'Add training successfully.'
        );
    }

    public function addCertificateUser(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first();

        if ($userDetail->type == 'company') {
            $userDetail = User::findOrFail($request->employeeUser);
            $user = User::findOrFail($userDetail['id']);
            $employee = $user->employee()->first();
        }

        $validator = \Validator::make(
            $request->all(), [
                'certificate_file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->hasFile('certificate_file')) {
            $filenameWithExt = $request->file('certificate_file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('certificate_file')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if ($settings['storage_setting'] == 'local') {
                $dir = 'uploads/certificate_file/';
            } else {
                $dir = 'uploads/certificate_file';
            }

            $url = '';
            $path = Utility::upload_file($request, 'certificate_file', $fileNameToStore, $dir, []);
        }

        if ($user->type == 'user') {
            $certificateUser = CertificateUser::create([
                'certificate_file' => $fileNameToStore,
                'user_id' => $user->id,
            ]);
        } else {
            $certificateUser = $employee->certificateUser()->create([
                'certificate_file' => $fileNameToStore,
                'user_id' => $request->employeeUser,

            ]);
            $certificateUser->save();
        }
        
        return redirect()->back()->with(
            'success', 'Add certificate successfully.'
        );
    }

    public function addCVUser(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first();

        if ($userDetail->type == 'company') {
            $userDetail = User::findOrFail($request->employeeUser);
            $user = User::findOrFail($userDetail['id']);
            $employee = $user->employee()->first();
        }
        
        $validator = \Validator::make(
            $request->all(), [
                'cv_file' => 'required'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->hasFile('cv_file')) {
            $filenameWithExt = $request->file('cv_file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('cv_file')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if ($settings['storage_setting'] == 'local') {
                $dir = 'uploads/cv_file/';
            } else {
                $dir = 'uploads/cv_file';
            }

            $url = '';
            $path = Utility::upload_file($request, 'cv_file', $fileNameToStore, $dir, []);
        }

        if ($user->type == 'user') {
            $cvUser = CVUser::create([
                'cv_file' => $fileNameToStore,
                'user_id' => $user->id,
            ]);
        } else {
            $cvUser = $employee->cvUser()->create([
                'cv_file' => $fileNameToStore,
                'user_id' => $request->employeeUser,

            ]);
            $cvUser->save();
        }
        
        return redirect()->back()->with(
            'success', 'Add CV successfully.'
        );
    }


    public function deleteCertificateUser($id) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);

        $item = CertificateUser::findOrFail($id);
        $item->delete();

        return redirect()->back()->with(
            'success', 'Delete certificate successfully.'
        );
    }

    public function deleteCVUser($id) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);

        $item = CVUser::findOrFail($id);
        $item->delete();

        return redirect()->back()->with(
            'success', 'Delete CV successfully.'
        );
    }

    public function deleteTrainingUser($id) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);

        $item = Traning::findOrFail($id);
        $item->delete();

        return redirect()->back()->with(
            'success', 'Delete training successfully.'
        );
    }

    public function addLangUser(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first();

        if ($userDetail->type == 'company') {
            $userDetail = User::findOrFail($request->employeeUser);
            $user = User::findOrFail($userDetail['id']);
            $employee = $user->employee()->first();
        }

        $validator = \Validator::make(
            $request->all(), [
                'language_name' => 'required|string|max:255',
                'level' => 'required|string|max:255',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($user->type == 'user') {
            $langUser = UserLanguage::create([
                'language_name' => $request['language_name'],
                'level' => $request['level'],
                'user_id' => $user->id,
            ]);
        } else {
            $langUser = $employee->languageUser()->create([
                'language_name' => $request['language_name'],
                'level' => $request['level'],
                'user_id' => $request->employeeUser,
            ]);
            $langUser->save();
        }
        
        return redirect()->back()->with(
            'success', 'Add language successfully.'
        );
    }

    public function deleteeducation($id) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);

        $item = Edu::findOrFail($id);
        $item->delete();

        return redirect()->back()->with(
            'success', 'Delete education successfully.'
        );
    }
    
    public function addeducation(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first() ?? null;

        if ($userDetail->type == 'company') {
            $userDetail = User::findOrFail($request->employeeUser);
            $user = User::findOrFail($userDetail['id']);
            $employee = $user->employee()->first();
        }
        $validator = \Validator::make(
            $request->all(), [
                'educational_level' => 'required|string|max:255',
                'university' => 'required|string|max:255',
                'academic_major' => 'required|string|max:255',
                'country_of_graduation' => 'required|string|max:255',
                'graduation_date' => 'required|date|before:today',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($user->type == 'user') {
            $education = Edu::create([
                'educational_level' => $request['educational_level'],
                'university' => $request['university'],
                'academic_major' => $request['academic_major'],
                'country_of_graduation' => $request['country_of_graduation'],
                'graduation_date' => $request['graduation_date'],
                'user_id' => $user->id,
            ]); 
        } else {
            $education = $employee->edus()->create([
                'educational_level' => $request['educational_level'],
                'university' => $request['university'],
                'academic_major' => $request['academic_major'],
                'country_of_graduation' => $request['country_of_graduation'],
                'graduation_date' => $request['graduation_date'],
                'user_id' => $request->employeeUser,

            ]);
            $education->save();
        }
        
        return redirect()->back()->with(
            'success', 'Add education successfully.'
        );
    }

    public function deleteworkexperience($id) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);

        $item = WorkExperience::findOrFail($id);
        $item->delete();

        return redirect()->back()->with(
            'success', 'Delete work experience successfully.'
        );
    }

    public function addworkexperience(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first() ?? null;

        if ($userDetail->type == 'company') {
            $userDetail = User::findOrFail($request->employeeUser);
            $user = User::findOrFail($userDetail['id']);
            $employee = $user->employee()->first();
        }

        $validator = \Validator::make(
            $request->all(), [
                'job_title' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'start_date' => 'required|date|before:today',
                'end_date' => 'nullable|date|before:today',
                'job_detail' => 'nullable|string|max:1000',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($user->type == 'user') {
            $experiences = WorkExperience::create([
                'job_title' => $request['job_title'],
                'company_name' => $request['company_name'],
                'start_date' => $request['start_date'],
                'job_detail' => $request['job_detail'],
                'user_id' => $user->id,
            ]);
        } else {
            $experiences = $employee->workExperiences()->create([
                'job_title' => $request['job_title'],
                'company_name' => $request['company_name'],
                'start_date' => $request['start_date'],
                'job_detail' => $request['job_detail'],
                'user_id' => $request->employeeUser,

            ]);
        }

        if (!$request->has('still_working')) {
            if ($request['end_date']) {
                $experiences['end_date'] = $request['end_date'];
                $experiences->save();
            } else {
                return redirect()->back()->with('error', 'please add end date.');
            }
        }
       
        return redirect()->back()->with(
            'success', 'Add work experience successfully.'
        );

    }

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = $user->employee()->first();

        if ($userDetail->type == 'company') {
            $userDetail = User::findOrFail($request->employeeUser);
            $user = User::findOrFail($userDetail['id']);
            $employee = $user->employee()->first();
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:120',
                'dob' => 'required|date|before:today',
                'country_of_residence' => 'required|string|max:255',
                'nationality' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'gender' => 'required|in:male,female',
                'phone' => 'required',
                'email' => 'required|email|unique:users,email,' . $userDetail['id'],
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->hasFile('profile')) {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if ($settings['storage_setting'] == 'local') {
                $dir = 'uploads/avatar/';
            } else {
                $dir = 'uploads/avatar';
            }

            $image_path = $dir . $userDetail['avatar'];

            if (File::exists($image_path)) {
                File::delete($image_path);
            }

            $url = '';
            $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
            if ($path['flag'] == 1) {
                $url = $path['url'];
            } else {
                return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
            }
        }

        if (!empty($request->profile)) {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name'] = $request['name'];
        $user['email'] = $request['email'];
        $user['country_of_residence'] = $request['country_of_residence'] ?? null;
        $user['nationality'] = $request['nationality'] ?? null;
        $user['dob'] = $request['dob'] ?? null;
        $user['gender'] = $request['gender'] ?? null;
        $user['address'] = $request['address'] ?? null;
        $user['phone'] = $request['phone'] ?? null;
        $user->save();
        CustomField::saveData($user, $request->customField);

        if ($employee) {
            $employee['dob'] = $request['dob'] ?? null;
            $employee['country_of_residence'] = $request['country_of_residence'] ?? null;
            $employee['nationality'] = $request['nationality'] ?? null;
            $employee['address'] = $request['address'] ?? null;
            $employee['gender'] = $request['gender'] ?? null;
            $employee['phone'] = $request['phone'] ?? null;
            $employee['email'] = $request['email'] ?? null;
            $employee['name'] = $request['name'] ?? null;
            $employee->save();
        }

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }

    public function updatePassword(Request $request)
    {

        if (Auth::Check()) {

            $validator = \Validator::make(
                $request->all(), [
                    'old_password' => 'required',
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|same:password',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $objUser = Auth::user();
            $request_data = $request->All();
            $current_password = $objUser->password;
            if (Hash::check($request_data['old_password'], $current_password)) {
                $user_id = Auth::User()->id;
                $obj_user = User::find($user_id);
                $obj_user->password = Hash::make($request_data['password']);
                $obj_user->save();

                return redirect()->route('profile', $objUser->id)->with('success', __('Password successfully updated.'));
            } else {
                return redirect()->route('profile', $objUser->id)->with('error', __('Please enter correct current password.'));
            }
        } else {
            return redirect()->route('profile', \Auth::user()->id)->with('error', __('Something is wrong.'));
        }
    }
    // User To do module
    public function todo_store(Request $request)
    {
        $request->validate(
            ['title' => 'required|max:120']
        );

        $post = $request->all();
        $post['user_id'] = Auth::user()->id;
        $todo = UserToDo::create($post);

        $todo->updateUrl = route(
            'todo.update', [
                $todo->id,
            ]
        );
        $todo->deleteUrl = route(
            'todo.destroy', [
                $todo->id,
            ]
        );

        return $todo->toJson();
    }

    public function todo_update($todo_id)
    {
        $user_todo = UserToDo::find($todo_id);
        if ($user_todo->is_complete == 0) {
            $user_todo->is_complete = 1;
        } else {
            $user_todo->is_complete = 0;
        }
        $user_todo->save();
        return $user_todo->toJson();
    }

    public function todo_destroy($id)
    {
        $todo = UserToDo::find($id);
        $todo->delete();

        return true;
    }

    // change mode 'dark or light'
    public function changeMode()
    {
        $usr = \Auth::user();
        if ($usr->mode == 'light') {
            $usr->mode = 'dark';
            $usr->dark_mode = 1;
        } else {
            $usr->mode = 'light';
            $usr->dark_mode = 0;
        }
        $usr->save();

        return redirect()->back();
    }

    public function upgradePlan($user_id)
    {
        $user = User::find($user_id);
        $plans = Plan::get();
        $admin_payment_setting = Utility::getAdminPaymentSetting();

        return view('user.plan', compact('user', 'plans', 'admin_payment_setting'));
    }
    public function activePlan($user_id, $plan_id)
    {

        $plan = Plan::find($plan_id);
        if($plan->is_disable == 0)
        {
            return redirect()->back()->with('error', __('You are unable to upgrade this plan because it is disabled.'));
        }

        $user = User::find($user_id);
        $assignPlan = $user->assignPlan($plan_id, $user_id);
        if ($assignPlan['is_success'] == true && !empty($plan)) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $orderID,
                    'name' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $plan->price,
                    'price_currency' => isset(\Auth::user()->planPrice()['currency'])?\Auth::user()->planPrice()['currency'] : '',
                    'txn_id' => '',
                    'payment_status' => 'success',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );

            return redirect()->back()->with('success', 'Plan successfully upgraded.');
        } else {
            return redirect()->back()->with('error', 'Plan fail to upgrade.');
        }

    }

    public function userPassword($id)
    {
        $eId = \Crypt::decrypt($id);
        $user = User::find($eId);

        return view('user.reset', compact('user'));

    }

    public function userPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $user = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
            'is_enable_login' => 1,
        ])->save();

        if(\Auth::user()->type == 'super admin')
        {
        return redirect()->route('users.index')->with(
            'success', 'Company Password successfully updated.'
        );
    }
    else
    {
        return redirect()->route('users.index')->with(
            'success', 'User Password successfully updated.'
        );
    }

    }

    //start for user login details
    public function userLog(Request $request)
    {
        $filteruser = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $filteruser->prepend('Select User', '');

        $query = DB::table('login_details')
            ->join('users', 'login_details.user_id', '=', 'users.id')
            ->select(DB::raw('login_details.*, users.id as user_id , users.name as user_name , users.email as user_email ,users.type as user_type'))
            ->where(['login_details.created_by' => \Auth::user()->id]);

        if (!empty($request->month)) {
            $query->whereMonth('date', date('m', strtotime($request->month)));
            $query->whereYear('date', date('Y', strtotime($request->month)));
        } else {
            $query->whereMonth('date', date('m'));
            $query->whereYear('date', date('Y'));
        }

        if (!empty($request->users)) {
            $query->where('user_id', '=', $request->users);
        }
        $userdetails = $query->get();
        $last_login_details = LoginDetail::where('created_by', \Auth::user()->creatorId())->get();

        return view('user.userlog', compact('userdetails', 'last_login_details', 'filteruser'));
    }

    public function userLogView($id)
    {
        $users = LoginDetail::find($id);

        return view('user.userlogview', compact('users'));
    }

    public function userLogDestroy($id)
    {
        $users = LoginDetail::where('user_id', $id)->delete();
        return redirect()->back()->with('success', 'User successfully deleted.');
    }

    public function LoginWithCompany(Request $request, User $user, $id)
    {
        $user = User::find($id);
        if ($user && auth()->check()) {
            Impersonate::take($request->user(), $user);
            return redirect('/account-dashboard');
        }
    }

    public function ExitCompany(Request $request)
    {
        \Auth::user()->leaveImpersonation($request->user());
        return redirect('/dashboard');
    }

    public function companyInfo(Request $request, $id)
    {
        $user = User::find($request->id);
        $status = $user->delete_status;
        $userData = User::where('created_by', $id)->where('type', '!=', 'client')->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_disable = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_disable = 1 THEN 1 ELSE 0 END) as active_users')->first();

        return view('user.company_info', compact('userData', 'id', 'status'));
    }

    public function userUnable(Request $request)
    {
        User::where('id', $request->id)->update(['is_disable' => $request->is_disable]);
        $userData = User::where('created_by', $request->company_id)->where('type', '!=', 'client')->selectRaw('COUNT(*) as total_users, SUM(CASE WHEN is_disable = 0 THEN 1 ELSE 0 END) as disable_users, SUM(CASE WHEN is_disable = 1 THEN 1 ELSE 0 END) as active_users')->first();

        if ($request->is_disable == 1) {

            return response()->json(['success' => __('User successfully unable.'), 'userData' => $userData]);

        } else {
            return response()->json(['success' => __('User successfully disable.'), 'userData' => $userData]);
        }
    }

    public function LoginManage($id)
    {
        $eId = \Crypt::decrypt($id);
        $user = User::find($eId);
        $authUser = \Auth::user();

        if ($user->is_enable_login == 1) {
            $user->is_enable_login = 0;
            $user->save();

            if($authUser->type == 'super admin')
            {
                return redirect()->back()->with('success', __('Company login disable successfully.'));
            }
            else
            {
                return redirect()->back()->with('success', __('User login disable successfully.'));
            }
        } else {
            $user->is_enable_login = 1;
            $user->save();
            if($authUser->type == 'super admin')
            {
                return redirect()->back()->with('success', __('Company login enable successfully.'));
            }
            else
            {
                return redirect()->back()->with('success', __('User login enable successfully.'));
            }
        }
    }
}
