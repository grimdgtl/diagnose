<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Servisna Knjiga - {{ $car->brand }} {{ $car->model }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <style>
        /* ------------------------------------------------
           OSNOVNI RESET + TIPOGRAFIJA
        ------------------------------------------------ */
        body {
            font-family: "DejaVu Sans", sans-serif;
            background-color: #ffffff;   /* BELO */
            color: #000000;              /* CRNO */
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
        h1, h2, h3, h4, h5, h6 {
            margin: 0;
            padding: 0;
            color: #000000;              /* CRNI NASLOVI */
        }
        /* ------------------------------------------------
           HEADER
        ------------------------------------------------ */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #ffffff;   /* BELO */
            border-bottom: 1px solid #000000; /* TANAK CRNI OKVIR */
        }
        .header span {
            font-weight: 900;
            color: #000000;              /* CRNO */
        }
        .header h1 {
            font-size: 1.5rem;
            text-transform: uppercase;
            font-weight: 900;
            color: #000000;              /* CRNO */
        }
        /* ------------------------------------------------
           INFORMACIJE O VOZILU
        ------------------------------------------------ */
        .car-info {
            padding: 20px;
            background-color: #ffffff;   /* BELO */
            border-bottom: 1px solid #000000; /* TANAK CRNI OKVIR */
            margin-bottom: 20px;
            font-size: 1.1rem;
            color: #000000;              /* CRNO */
        }
        .car-info p {
            margin: 4px 0;
        }
        /* ------------------------------------------------
           TABELA
        ------------------------------------------------ */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            padding: 0;
        }
        th, td {
            border: 1px solid #000000; /* CRNI OKVIR */
            padding: 10px;
            text-align: left;
            color: #000000;            /* CRNO */
        }
        th {
            background-color: #f0f0f0; /* SVETLO SIVA pozadina za zaglavlja */
            font-weight: 700;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #ffffff; /* BELO */
        }
        tr:nth-child(odd) {
            background-color: #f9f9f9; /* BLAGO SVETLO SIVA */
        }
        /* ------------------------------------------------
           FOOTER
        ------------------------------------------------ */
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #ffffff;   /* BELO */
            color: #000000;              /* CRNO */
            font-size: 0.9rem;
            border-top: 1px solid #000000; /* TANAK CRNI OKVIR */
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Header sa logom i naslovom -->
    <div class="header">
        <h1>DIJAGNOZA</h1>
        <span>Servisna Knjiga</span>
    </div>

    <!-- Informacije o vozilu -->
    <div class="car-info">
        <p>Proizvođač: {{ $car->brand }}</p>
        <p>Model: {{ $car->model }}</p>
        <p>Godina: {{ $car->year }}</p>
        <p>Zapremina motora: {{ $car->engine_capacity }}</p>
        <p>Snaga motora: {{ $car->engine_power }}</p>
        <p>Gorivo: {{ $car->fuel_type }}</p>
        <p>Menjač: {{ $car->transmission }}</p>
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
