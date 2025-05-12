<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>التحقق من الإيميل || Tusk </title>
</head>

<body>

    <h3> مرحباً {{ $first_name }} {{ $last_name }}
    </h3>

    {{-- <p>We received a request to access your Google Account {{ $email }} through your email address.
    </p> --}}
    <p>
        من أجل تأكيد دخولك إلى تطبيقنا
        @if ($guard == 'lab_manager')
            كمدير مخبر تعويضات سنية
        @endif
        @if ($guard == 'dentist')
            كطبيب أسنان
        @endif
        {{-- @if ($guard == 'accountant')
            كمحاسب
        @endif
        @if ($guard == 'inventory_employee')
            كموظف مخزون
        @endif
        @if ($guard == 'secratary')
            كموظف سكرتاريا
        @endif
        @if ($guard == 'admin')
            كمدير منصة
        @endif --}}
        ,
        لقد تلقينا طلبًا للوصول إلى حسابك على Google
        من خلال عنوان بريدك الإلكتروني التالي :
    </p>

    <h3><b style="color: rgb(68, 149, 30);">
            {{ $email }}
        </b></h3>
    <p>
        رمز التحقق الخاص بك هو :
    </p>
    <h1><b style="color: rgb(7, 121, 220);">
            <pre>{{ $verification_code }}</pre>
        </b></h1>

    {{-- <p>If you did not request this code, it is possible that someone else is trying to
    </p> --}}
    <p>إذا لم تقم بإرسال هذا الطلب، فمن المحتمل أن هنالك شخصاً آخر يحاول
        إنشاء حساب ولكن كتب إيميله بشكل خاطئ، فالرجاء تجاهل هذا الإيميل
    </p>
    {{-- <p>make an Account but he write his email wrongly,so please ignore this email.
    </p> --}}

    <h4>
        تفضلوا بقبول فائق الاحترام .
    </h4>

    <h2><b style="color: rgb(208, 137, 14);">
            منصة Tusk
        </b></h2>
</body>

</html>
