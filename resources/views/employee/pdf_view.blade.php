<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التوقيع الإلكتروني</title>
</head>
<body>
    <h2>إضافة توقيع</h2>
    <canvas id="signature-pad" width="600" height="400" style="border:1px solid #000;"></canvas>
    <button onclick="saveSignature()">حفظ التوقيع</button>
    <form id="signature-form" action="{{ route('save.signature') }}" method="POST">
        @csrf
        <input type="hidden" name="image" id="signature-image">
        <input type="hidden" name="pdf_file" value="{{ $fileName }}">
    </form>

    <script>
        const canvas = document.getElementById('signature-pad');
        const ctx = canvas.getContext('2d');
        let drawing = false;

        canvas.addEventListener('mousedown', () => drawing = true);
        canvas.addEventListener('mouseup', () => drawing = false);
        canvas.addEventListener('mousemove', draw);

        function draw(event) {
            if (!drawing) return;
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = 'black';
            ctx.lineTo(event.offsetX, event.offsetY);
            ctx.stroke();
        }

        function saveSignature() {
            document.getElementById('signature-image').value = canvas.toDataURL();
            document.getElementById('signature-form').submit();
        }
    </script>
</body>
</html>
