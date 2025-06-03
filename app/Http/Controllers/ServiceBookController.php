<?php

namespace App\Http\Controllers;

use App\Models\CarDetail;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\PDF;

class ServiceBookController extends Controller
{
    /* -----------------------------------------------------------------
     |  GARAŽA / SERVISNA KNJIGA – HOME
     |----------------------------------------------------------------- */
    public function index(Request $request)
    {
        $user = Auth::user();
        $cars = CarDetail::where('user_id', $user->id)->get();

        /* ------------------------------------------------------------
         |  1)  Ako korisnik eksplicitno traži  ?add=1
         |      šaljemo ga u "Moja garaža" sa otvorenom formom.
         * ----------------------------------------------------------- */
        if ($request->boolean('add')) {
            return redirect()->route('profile.garage', ['add' => 1]);
        }

        /* ------------------------------------------------------------
         |  2)  Nema nijednog vozila ➜ samo info poruka + CTA ka garaži
         * ----------------------------------------------------------- */
        if ($cars->isEmpty()) {
            return view('service-book.index', compact('cars')); // view će sam prikazati poruku
        }

        /* ------------------------------------------------------------
         |  3)  Postoje vozila ➜ lista automobila za izbor
         * ----------------------------------------------------------- */
        return view('service-book.index', compact('cars'));
    }

    /* -----------------------------------------------------------------
     |  DODAVANJE VOZILA (koristi se i iz garaže i iz servisne knjige)
     |----------------------------------------------------------------- */
    public function storeGarageCar(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'brand'           => 'required|string',
            'model'           => 'required|string',
            'year'            => 'required|integer',
            'mileage'         => 'required|integer|min:0',
            'engine_capacity' => 'required|string',
            'engine_power'    => 'required|string',
            'fuel_type'       => 'required|string',
            'transmission'    => 'required|string',
        ]);

        CarDetail::create($data + ['user_id' => $user->id]);

        /* Vraćamo korisnika na Moja garaža da odmah vidi novo vozilo */
        return redirect()
            ->route('profile.garage')
            ->with('success', 'Vozilo je uspešno dodato u garažu.');
    }

    /* -----------------------------------------------------------------
     |  SERVISNI ZAPISI
     |----------------------------------------------------------------- */
    public function create($car_id)
    {
        $user = Auth::user();
        $car  = CarDetail::findOrFail($car_id);
        abort_unless($car->user_id === $user->id, 403);

        $cars = CarDetail::where('user_id', $user->id)->get();

        return view('service-book.create', compact('car', 'cars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'car_detail_id' => 'required|exists:car_details,id',
            'service_date'  => 'required|date',
            'description'   => 'required|string',
            'mileage'       => 'required|integer',
            'cost'          => 'nullable|numeric',
            'notes'         => 'nullable|string',
        ]);

        ServiceRecord::create($request->only([
            'car_detail_id',
            'service_date',
            'description',
            'mileage',
            'cost',
            'notes',
        ]));

        return redirect()
            ->route('service-book.show', $request->car_detail_id)
            ->with('success', 'Servisni zapis uspešno dodat.');
    }

    public function show($car_id)
    {
        $user = Auth::user();
        $car  = CarDetail::findOrFail($car_id);
        abort_unless($car->user_id === $user->id, 403);

        $serviceRecords = ServiceRecord::where('car_detail_id', $car_id)
                                       ->orderBy('service_date', 'desc')
                                       ->get();

        return view('service-book.records', compact('car', 'serviceRecords'));
    }

    public function exportPdf($car_id)
    {
        $user = Auth::user();
        $car  = CarDetail::findOrFail($car_id);
        abort_unless($car->user_id === $user->id, 403);

        $serviceRecords = ServiceRecord::where('car_detail_id', $car_id)
                                       ->orderBy('service_date', 'desc')
                                       ->get();

        $pdf = PDF::loadView('service-book.pdf', compact('car', 'serviceRecords'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("servisna-knjiga-{$car->brand}-{$car->model}.pdf");
    }

    public function destroy($id)
    {
        $record = ServiceRecord::findOrFail($id);
        abort_unless($record->carDetail->user_id === Auth::id(), 403);

        $record->delete();

        return back()->with('success', 'Servisni zapis obrisan.');
    }
}
