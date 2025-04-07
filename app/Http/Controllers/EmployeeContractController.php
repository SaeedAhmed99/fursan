<?php

namespace App\Http\Controllers;

use App\Models\ContractPDFDetails;
use App\Models\Employee;
use App\Models\SignedFiles;
use App\Models\User;
use Spatie\PdfToImage\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Auth;
use App\Models\Utility;

class EmployeeContractController extends Controller
{
    public function index() {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $contracts = ContractPDFDetails::orderBy('created_at', 'desc')->get();
        $employees = Employee::where('created_by', $user->id)->orderBy('created_at', 'desc')->get();

        return view('employee.contract', compact('contracts', 'employees'));
    }


    public function create(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'file_name' => 'required|mimes:pdf',
                'employees' => 'nullable|array',
                'employees.*' => 'exists:employees,id'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->hasFile('file_name')) {
            $filenameWithExt = $request->file('file_name')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file_name')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if ($settings['storage_setting'] == 'local') {
                $dir = 'uploads/employee_contract/';
            } else {
                $dir = 'uploads/employee_contract';
            }

            $url = '';
            $path = Utility::upload_file($request, 'file_name', $fileNameToStore, $dir, []);
        }

            ContractPDFDetails::create([
                'name' => $request['name'],
                'description' => $request['description'],
                'file_name' => $fileNameToStore,
                'assign_to_all' => $request->has('assign_to_all'),
                'selected_employees' => $request->has('assign_to_all') ? null : json_encode($request->employees),
            ]);
        
        
        return redirect()->back()->with(
            'success', 'Add successfully.'
        );
    }


    public function delete($id) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $item = ContractPDFDetails::findOrFail($id);
        $item->delete();

        return redirect()->back()->with(
            'success', 'Delete successfully.'
        );
    }


    public function contractsAssign() {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = Employee::where('user_id', $user->id)->first();

        $assignedFiles = ContractPDFDetails::where('assign_to_all', true)
            ->orWhereJsonContains('selected_employees', (string) $employee->id)
            ->get();
        return view('employee.contract_assign', compact('assignedFiles'));
    }

    public function contractsAssignUpload(Request $request) {
        $userDetail = \Auth::user();
        $user = User::findOrFail($userDetail['id']);
        $employee = Employee::where('user_id', $user->id)->first();

        $validator = \Validator::make(
            $request->all(), [
                'file_name' => 'required|mimes:pdf',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->hasFile('file_name')) {
            $filenameWithExt = $request->file('file_name')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file_name')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if ($settings['storage_setting'] == 'local') {
                $dir = 'uploads/employee_contract/assigned';
            } else {
                $dir = 'uploads/employee_contract/assigned';
            }

            $url = '';
            $path = Utility::upload_file($request, 'file_name', $fileNameToStore, $dir, []);
        }

        SignedFiles::create([
                'contract_p_d_f_details_id' => $request->contract_id,
                'employee_id' => $employee->id,
                'file_name' => $fileNameToStore,
            ]);
        
        
        return redirect()->back()->with(
            'success', 'Add successfully.'
        );

    }  
    

    public function contractsAssignAllAsgin(Request $request) {
        $companyID = \Auth::user()->id;
        // $assignedFiles = SignedFiles::orderBy('created_at', 'desc')->first();
        $assignedFiles = SignedFiles::whereHas('employee', function ($query) use ($companyID) {
            $query->where('created_by', $companyID);
        });
        if ($request->filled('contract')) {
            $assignedFiles->where('contract_p_d_f_details_id',$request->input('contract'));
        }
        if ($request->filled('employee')) {
            $assignedFiles->where('employee_id',$request->input('employee'));
        }
        $assignedFiles = $assignedFiles->get();
        $employees = Employee::where('created_by', $companyID)->orderBy('created_at', 'desc')->get();
        $contracts = ContractPDFDetails::orderBy('created_at', 'desc')->get();
        return view('employee.contract_assign_all_employee', compact('assignedFiles', 'employees', 'contracts'));
    }

    
}
