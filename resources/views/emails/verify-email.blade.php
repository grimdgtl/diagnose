<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikacija naloga</title>
</head>
<body>
    <h1>Zdravo, {{ $user->first_name }}!</h1>

    <p>
        Hvala što ste se registrovali. Kliknite na link ispod da verifikujete svoj nalog:
    </p>
    <p>
        <a href="{{ $link }}">
            Verifikuj nalog
        </a>
    </p>
    <p>
        Ukoliko link ne radi, kopirajte ovu adresu u svoj browser:<br>
        {{ $link }}
    </p>
</body>
</html>
