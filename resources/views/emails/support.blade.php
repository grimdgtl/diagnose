<!-- resources/views/emails/support.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Podrška</title>
</head>
<body>
    <h2>Nova poruka podrške</h2>
    <p><strong>Od:</strong> {{ $supportData['user']->first_name }} {{ $supportData['user']->last_name }} ({{ $supportData['user']->email }})</p>
    <p><strong>Predmet:</strong> {{ $supportData['subject'] }}</p>
    <p><strong>Poruka:</strong></p>
    <p>{{ $supportData['message'] }}</p>
</body>
</html>
