<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>مرحباً بك في منصّتنا || Tusk </title>
</head>

<body>
    <h3> مرحباً بك {{ $first_name }} {{ $last_name }} في منصة Tusk </h3>
    تمّت الموافقة على طلب انضمامك
    @if ($modelName == 'LabManager')
        كمدير مخبر تعويضات سنية .
    @endif
    @if ($modelName == 'Dentist')
        كطبيب أسنان .
    @endif

    {{-- <pre>So <b>{{ $welcome_message }}</b> 🥰 </pre> --}}
    <p>نتمنى أن تنال المنصة إعجابك 😉 .</p>
    <p>👋 نراك قريباً 👋</p>
    {{-- <h3>/*{{ env('APP_NAME') }} منصة </h3> --}}
    <h3><b style="color: rgb(208, 137, 14);">
            <pre>منصة Tusk </pre>
        </b></h3>
</body>

</html>
