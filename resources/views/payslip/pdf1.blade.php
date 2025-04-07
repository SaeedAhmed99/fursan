@php
    $logo=\App\Models\Utility::get_file('uploads/logo');
    $company_logo = \App\Models\Utility::GetLogo();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Slip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pay-slip {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            background: #fff;
        }
        .table th, .table td {
            text-align: left;
        }
        .signature-box {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature {
            width: 45%;
            text-align: center;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
    </style>
<script>
    function toggleForm() {
        var form = document.getElementById('form');
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
</script>
</head>

<body class="bg-light">
    <div class="text-end m-2">
        <a href="#" class="btn btn-sm btn-primary me-1" onclick="saveAsPDF()"><span class="ti ti-download"></span></a>
        <a title="Mail Send" href="{{route('payslip.send',[$employee->id,$payslip->salary_month])}}" class="btn btn-sm btn-warning"><span class="ti ti-send"></span></a>
        <button onclick="toggleForm()" title="Custom mail Send"  class="btn btn-sm btn-warning"><span class="ti ti-send"></span></button>
    </div>
    <div class="d-flex justify-content-center align-items-center" >
        <form id="form" style="display: none;" method="POST" action="{{route('payslip.send.custom',[$employee->id,$payslip->salary_month])}}">
        @csrf
            <div class="">
                <div class="form-group">
                    <label for="email" class="col-form-label text-dark">{{__('Email')}}</label>
                    <input class="form-control" name="email" type="email" id="email" placeholder="{{ __('Enter Your Email Address') }}" value="{{ $userDetail->email ?? '' }}" required autocomplete="email">
                </div>
            </div>
            <div class="">
                <input type="submit" value="{{__('Send')}}" class="btn btn-print-invoice  btn-primary m-r-10">
            </div>
        </form>
    </div>
    <div class="invoice" id="printableArea">

        <div class="container mt-5">
            <div class="pay-slip shadow p-4">
                <!-- Header -->
                <div class="d-flex align-items-center mb-3">
                    <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo-dark.png')}}" alt="Company Logo" style="height: 60px;">
                
                    
                </div>
                <div class="flex-grow-1 text-center">
                    <h2 class="mt-2">PAY SLIP</h2>
                </div>
                
                <input type="number" value="{{$employee->id}}" name="emp_id" hidden>
                <!-- Employee Details -->
                <p><strong>Employee Name:</strong> <span>{{$employee->name}}</span></p>
                <p><strong>Title:</strong> <span>{{!empty($employee->designation)?$employee->designation->name:''}}</span></p>
                <p><strong>Pay Period:</strong> <span>{{ $payslip->salary_month}}</span></p>

                {{-- <div class="d-flex justify-content-between">
                    <p><strong>Hours/week:</strong> <span>40</span></p>
                    <p><strong>Pay Period:</strong> <span>{{ $payslip->salary_month}}</span></p>
                </div> --}}
                

                <!-- Salary Table -->
                {{-- <table class="table table-bordered mt-3">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th>Amount ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td>3,000</td>
                        </tr>
                        <tr>
                            <td>Health Insurance</td>
                            <td>-200</td>
                        </tr>
                        <tr>
                            <td>Tax Deductions</td>
                            <td>-300</td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Net Salary</td>
                            <td>2,500</td>
                        </tr>
                    </tbody>
                </table> --}}

                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card-body table-border-style">

                            <div class="table-responsive">
                                <table class="table table-md">
                                    <thead class="thead-dark">
                                        <tr class="font-weight-bold">
                                            <th>{{__('Earning')}}</th>
                                            <th>{{__('Title')}}</th>
                                            <th>{{__('Type')}}</th>
                                            <th class="text-end">{{__('Amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                    <tr>
                                        <td>{{__('Basic Salary')}}</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td class="text-end">{{  \Auth::user()->priceFormat( $payslip->basic_salary)}}</td>
                                    </tr>
                                    @foreach ($payslipDetail['earning']['allowance'] as $allowance)
                                        @php
                                            $employess = \App\Models\Employee::find($allowance->employee_id);
                                            $allowance = json_decode($allowance->allowance);
                                        @endphp
                                        @foreach ($allowance as $all)
                                            <tr>
                                                <td>{{ __('Allowance') }}</td>
                                                <td>{{ $all->title }}</td>
                                                <td>{{ ucfirst($all->type) }}</td>
                                                @if ($all->type != 'percentage')
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($all->amount) }}</td>
                                                @else
                                                    <td class="text-end">{{ $all->amount }}%
                                                        ({{ \Auth::user()->priceFormat(($all->amount * $payslip->basic_salary) / 100) }})
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    @foreach ($payslipDetail['earning']['commission'] as $commission)
                                        @php
                                            $employess = \App\Models\Employee::find($commission->employee_id);
                                            $commissions = json_decode($commission->commission);
                                        @endphp
                                        @foreach ($commissions as $empcom)
                                            <tr>
                                                <td>{{ __('Commission') }}</td>
                                                <td>{{ $empcom->title }}</td>
                                                <td>{{ ucfirst($empcom->type) }}</td>
                                                @if ($empcom->type != 'percentage')
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($empcom->amount) }}</td>
                                                @else
                                                    <td class="text-end">{{ $empcom->amount }}%
                                                        ({{ \Auth::user()->priceFormat(($empcom->amount * $payslip->basic_salary) / 100) }})
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    @foreach ($payslipDetail['earning']['otherPayment'] as $otherPayment)
                                        @php
                                            $employess = \App\Models\Employee::find($otherPayment->employee_id);
                                            $otherpay = json_decode($otherPayment->other_payment);
                                        @endphp
                                        @foreach ($otherpay as $op)
                                            <tr>
                                                <td>{{ __('Other Payment') }}</td>
                                                <td>{{ $op->title }}</td>
                                                <td>{{ ucfirst($op->type) }}</td>
                                                @if ($op->type != 'percentage')
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($op->amount) }}</td>
                                                @else
                                                    <td class="text-end">{{ $op->amount }}%
                                                        ({{ \Auth::user()->priceFormat(($op->amount * $payslip->basic_salary) / 100) }})
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    @foreach ($payslipDetail['earning']['overTime'] as $overTime)
                                        @php
                                            $arrayJson = json_decode($overTime->overtime);
                                            foreach ($arrayJson as $key => $overtime) {
                                                foreach ($arrayJson as $key => $overtimes) {
                                                    $overtitle = $overtimes->title;
                                                    $OverTime = $overtimes->number_of_days * $overtimes->hours * $overtimes->rate;
                                                }
                                            }
                                        @endphp
                                        @foreach ($arrayJson as $overtime)
                                            <tr>
                                                <td>{{ __('OverTime') }}</td>
                                                <td>{{ $overtime->title }}</td>
                                                <td>-</td>
                                                <td class="text-end">
                                                    {{ \Auth::user()->priceFormat($overtime->number_of_days * $overtime->hours * $overtime->rate) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-body table-border-style">

                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-md">
                                    <tbody>
                                    <tr class="font-weight-bold">
                                        <th>{{__('Deduction')}}</th>
                                        <th>{{__('Title')}}</th>
                                        <th>{{__('type')}}</th>
                                        <th class="text-end">{{__('Amount')}}</th>
                                    </tr>



                                    @foreach ($payslipDetail['deduction']['loan'] as $loan)
                                        @php
                                            $employess = \App\Models\Employee::find($loan->employee_id);
                                            $loans = json_decode($loan->loan);
                                        @endphp
                                        @foreach ($loans as $emploanss)
                                            <tr>
                                                <td>{{ __('Loan') }}</td>
                                                <td>{{ $emploanss->title }}</td>
                                                <td>{{ ucfirst($emploanss->type) }}</td>
                                                @if ($emploanss->type != 'percentage')
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($emploanss->amount) }}</td>
                                                @else
                                                    <td class="text-end">{{ $emploanss->amount }}%
                                                        ({{ \Auth::user()->priceFormat(($emploanss->amount * $payslip->basic_salary) / 100) }})
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    @foreach ($payslipDetail['deduction']['deduction'] as $deduction)
                                        @php
                                            $employess = \App\Models\Employee::find($deduction->employee_id);
                                            $deductions = json_decode($deduction->saturation_deduction);
                                        @endphp
                                        @foreach ($deductions as $saturationdeduc)
                                            <tr>
                                                <td>{{ __('Saturation Deduction') }}</td>
                                                <td>{{ $saturationdeduc->title }}</td>
                                                <td>{{ ucfirst($saturationdeduc->type) }}</td>
                                                @if ($saturationdeduc->type != 'percentage')
                                                    <td class="text-end">
                                                        {{ \Auth::user()->priceFormat($saturationdeduc->amount) }}
                                                    </td>
                                                @else
                                                    <td class="text-end">{{ $saturationdeduc->amount }}%
                                                        ({{ \Auth::user()->priceFormat(($saturationdeduc->amount * $payslip->basic_salary) / 100) }})
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Net Salary Summary -->
                <h4 class="text-center mt-4">{{ $payslip->net_payble }}</h4>
                @php
                    function convertAmountToWords($amountWithCurrency) {
                        $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);

                        preg_match('/([\d,\.]+)([A-Za-z$€£]*)/', $amountWithCurrency, $matches);

                        if (!isset($matches[1]) || !isset($matches[2])) {
                            return "Invalid amount format";
                        }

                        $amount = floatval(str_replace(',', '', $matches[1])); // إزالة الفواصل
                        $currency = strtoupper(trim($matches[2])); // استخراج العملة

                        $mainPart = floor($amount);
                        $fractionalPart = round(($amount - $mainPart) * 1000); // إذا كانت هناك قيم كسرية

                        $amountWords = ucfirst($formatter->format($mainPart));
                        $fractionalWords = $fractionalPart > 0 ? " and " . $formatter->format($fractionalPart) : "";

                        $currencyNames = [
                            "JD"  => "Jordanian Dinars",
                            "$"   => "US Dollars",
                            "EUR" => "Euros",
                            "£"   => "British Pounds",
                            "SAR" => "Saudi Riyals",
                            "AED" => "UAE Dirhams"
                        ];

                        $currencyName = $currencyNames[$currency] ?? "Unknown Currency";

                        // return "{$amountWords}{$fractionalWords} {$currencyName}";
                        return "{$amountWords}{$fractionalWords}";
                    }

                    $amountInWords = convertAmountToWords($payslip->net_payble);
                @endphp
                <h4 class="text-center mt-4">{{ $amountInWords  }}</h4>

                <!-- Signatures -->
                <div class="signature-box mt-5">
                    <div class="signature">Company Signature</div>
                    <div class="signature">Employee Signature</div>
                </div>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>

    // var filename = $('#filename').val()

    // function saveAsPDF() {
        
    //     var element = document.getElementById('printableArea');
    //     var opt = {
    //         margin: 0.3,
    //         filename: filename,
    //         image: {type: 'jpeg', quality: 1},
    //         html2canvas: {scale: 4, dpi: 72, letterRendering: true},
    //         jsPDF: {unit: 'in', format: 'A2'}
    //     };
        
    // }
</script>


<script>
    function saveAsPDF() {
    var filename = $('#filename').val();
    var element = document.getElementById('printableArea');
    var empId = $('input[name="emp_id"]').val();

    var opt = {
        margin: 0.3,
        filename: filename,
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 4, dpi: 72, letterRendering: true },
        jsPDF: { unit: 'in', format: 'A2' }
    };

    html2pdf().set(opt).from(element).toPdf().output('blob').then(function (pdfBlob) {
        var formData = new FormData();
        formData.append('pdf', pdfBlob, filename + ".pdf");
        formData.append('empId', empId);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: 'payslip/save-pdf',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {                    
                    var link = document.createElement('a');
                    link.href = response.download_url;
                    link.download = response.download_url.split('/').pop(); // استخراج اسم الملف تلقائيًا
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);               
                }
            },
            error: function (error) {
                console.log('خطأ في حفظ الملف:', error);
            }
        });
    });
}

</script>

</script>

</html>
