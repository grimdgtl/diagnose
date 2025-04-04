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
    <p>
        Kao poseban poklon za tebe, evo tvog koda za <b>20% popusta</b> na bilo koji paket pitanja:<br>
        Unesi kod <b><span style="color: #FF5C00">GEDORA20</span></b> na stranici za plaćanje nakon što odabereš željeni paket i otključaj <b>20% popusta</b> kao pravi automehaničar!
    </p>
</body>
</html>
