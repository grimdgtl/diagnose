<!-- resources/views/emails/reset-password-email.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Resetovanje lozinke - Dijagnoza</title>
</head>
<body>
    <h1>Pozdrav!</h1>
    <p>Primili ste ovaj email jer smo dobili zahtev za resetovanje lozinke za vaš nalog.</p>
    <a href="{{ $actionUrl }}" style="background-color: #FF5C00; color: white; padding: 10px 20px; text-decoration: none; border-radius: 10px;">
        Resetuj Lozinku
    </a>
    <br>
    <p>Ovaj link za resetovanje lozinke ističe za 60 minuta.</p>
    <p>Ako niste zatražili resetovanje lozinke, nije potrebna dalja akcija.</p>
    <p>S poštovanjem,<br>Dijagnoza Tim</p>
    <p>Ako imate problema sa kliktanjem na dugme "Resetuj Lozinku", kopirajte i zalepite sledeći URL u vaš pretraživač:<br>{{ $actionUrl }}</p>
</body>
</html>