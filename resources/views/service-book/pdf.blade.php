<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Servisna Knjiga - {{ $car->brand }} {{ $car->model }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Red Hat Display', sans-serif; /* Font koji podržava srpske karaktere */
            background-color: #0a0a0a; /* Crna pozadina */
            color: #FFFFFF; /* Bela boja teksta */
            margin: 0; /* Uklanjanje margina */
            padding: 0; /* Uklanjanje paddinga */
            height: 100%; /* Osigurava punu visinu */
            width: 100%; /* Osigurava punu širinu */
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #FF5C00; /* Narandžasta boja za header */
            border-bottom: 2px solid #FF5C00;
        }

        .header img {
            height: 50px; /* Veličina loga, prilagodite prema vašem logu */
            width: auto;
        }

        .header h1 {
            color: #FFFFFF; /* Bela boja teksta za naslov */
            margin: 0;
            font-size: 1.5rem;
            text-transform: uppercase;
            font-weight: 900;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }

        /* Informacije o vozilu */
        .car-info {
            padding: 20px;
            background-color: #1a1a1a; /* Tamno siva/crna pozadina */
            border-bottom: 2px solid #FF5C00; /* Narandžasta ivica */
            margin-bottom: 20px;
            font-size: 1.1rem;
            color: #FFFFFF;
        }

        /* Tabela */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            padding: 20px;
        }

        th, td {
            border: 1px solid #FF5C00; /* Narandžasta ivica */
            padding: 12px;
            text-align: left;
            color: #FFFFFF; /* Bela boja teksta */
        }

        th {
            background-color: #FF5C00; /* Narandžasta pozadina za zaglavlja */
            color: #FFFFFF; /* Bela boja teksta za zaglavlja */
            font-weight: 700;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #1a1a1a; /* Tamno siva/crna pozadina za parne redove */
        }

        tr:nth-child(odd) {
            background-color: #0a0a0a; /* Crna pozadina za neparne redove */
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #FF5C00; /* Narandžasta pozadina za footer */
            color: #FFFFFF; /* Bela boja teksta */
            font-size: 0.9rem;
            border-top: 2px solid #FF5C00;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Header sa logom i naslovom -->
    <div class="header">
        <span style="color: #FFFFFF; font-weight: 900; font-style:italic;">DIJAGNOZA</span>
        <h1>Servisna Knjiga</h1>
    </div>

    <!-- Informacije o vozilu -->
    <div class="car-info">
        Proizvodjac: {{ $car->brand }}<br>
        Model: {{ $car->model }}<br>
        Godina: {{ $car->year }}<br>
        Zapremina motora: {{ $car->engine_capacity}}<br>
        Snaga motora: {{ $car->engine_power }}<br>
        Gorivo: {{ $car->fuel_type }}<br>
        Menjac: {{ $car->transmission }}<br>
    </div>

    <!-- Tabela sa servisnim zapisima -->
    <table>
        <thead>
            <tr>
                <th>Datum</th>
                <th>Opis</th>
                <th>Kilometraža</th>
                <th>Cena</th>
                <th>Napomene</th>
            </tr>
        </thead>
        <tbody>
            @foreach($serviceRecords as $record)
                <tr>
                    <td>{{ $record->service_date }}</td>
                    <td>{{ $record->description }}</td>
                    <td>{{ $record->mileage }}</td>
                    <td>{{ $record->cost ? number_format($record->cost, 2) . ' RSD' : '-' }}</td>
                    <td>{{ $record->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Generisano: {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>