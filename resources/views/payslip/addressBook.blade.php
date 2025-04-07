<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب إلى البنك الإسلامي الأردني</title>
    <!-- إضافة رابط Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl; /* تغيير اتجاه النص إلى اليمين */
        }
        .content {
            padding: 30px;
        }
        .bordered-box {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
        }
        /* تصميم النقاط */
        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 200px;
            height: 1px;
        }
        .label-dotted {
            display: inline-block;
            width: 200px;
            height: 30px;
            border-bottom: 1px dotted #000;
        }
        .full-width {
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="container content">

        <!-- الرقم والنقاط لادخال القيمة -->
        <div class="row ">
            <div class="col-6">
                <label for="number" class="form-label">الرقم:</label>
                <span class="dotted-line"></span>
            </div>
        </div>

        <!-- التاريخ والنقاط لادخال التاريخ -->
        <div class="row mb-4">
            <div class="col-6">
                <label for="date" class="form-label">التاريخ:</label>
                <span class="dotted-line"></span> <!-- النقاط لادخال التاريخ -->
            </div>
        </div>

        <!-- عنوان البنك -->
        <div class="text-right mb-2">
            <h3>
            <b>السادة البنك الإسلامي الأردني المحترمين</b>
            </h3>
        </div>
        <div class="row ">
            <div class="col-6">
                <label for="number" class="form-label"><b>فرع:</b></label>
                <span class="dotted-line"></span>
            </div>
        </div>
        <div class="text-right mb-2">
            <b style="font-size: 20px;">السلام عليكم ورحمة الله وبركاته</b>
        </div>

        <!-- النص -->
        <div class="">
            <p>بناءا على طلب السيد / الفاضلة: ……………………………………… الموظف
            <br>
            <p>لدينا من تاريخ …./…./……..م ويشغل حالية وظيفة ………………………… سنقوم بتحويل صافي راتبه</p>
            <br>
            <p>الشهري الاجمالي لحسابه لديكم رقم (.........................) ابتداء من راتب شهر …./…./……..م وسوف نستمر</p>
            <br>
            <p>في تحويل صافي الراتب الاجمالي واي زيادات تطرأ عليه للحساب المذكور الما انه على رأس عمله والى ان يردنا</p>
            <br>
            <p> اشعار منكم بالتوقف, علما بأن صافي الراتب الاجمالي الحالي بعد الاقتطاعات القانونية مبلغ وقدره ……………</p>

    
        </div>

        <!-- التقدير والاحترام -->
        <div class="text-center mb-4">
            <h4>وتفضلوا بقبول فائق الاحترام والتقدير</h4>
        </div>

        <!-- الجهة المحولة للراتب -->
        <div class="d-flex justify-content-start align-items-start">
            <div class="col-6 ">
                <h5>الجهة المحولة للراتب</h5>
                <label for="sender" class="form-label">الاسم:</label>
                <span class="dotted-line"></span>
                <br>
                <label for="sender-date" class="form-label">التاريخ:</label>
                <span class="dotted-line"></span>
                <br>
                <label for="seal" class="form-label">الختم والتوقيع:</label>
                <span class="dotted-line"></span>
            </div>
        </div>
        
    </div>

    <!-- إضافة رابط Bootstrap JS (اختياري) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
