<?php

namespace App\Http\Controllers;

use App\Models\CarDetail;
use App\Models\ServiceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\PDF;

class ServiceBookController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cars = CarDetail::where('user_id', $user->id)->get();
    
        if ($cars->isEmpty()) {
            return redirect()->route('garage.index')->with('error', 'Nemate dodatih automobila. Dodajte automobil da biste koristili servisnu knjigu.');
        }
    
        return view('service-book.index', compact('cars'));
    }

    public function create($car_id)
    {
        $user = Auth::user();
        $car = CarDetail::findOrFail($car_id);

        if ($car->user_id !== $user->id) {
            abort(403, 'Nemate dozvolu za ovaj automobil.');
        }

        $cars = CarDetail::where('user_id', $user->id)->get();
        return view('service-book.create', compact('car', 'cars'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'car_detail_id' => 'required|exists:car_details,id',
            'service_date' => 'required|date',
            'description' => 'required|string',
            'mileage' => 'required|integer',
            'cost' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        ServiceRecord::create([
            'car_detail_id' => $request->car_detail_id,
            'service_date' => $request->service_date,
            'description' => $request->description,
            'mileage' => $request->mileage,
            'cost' => $request->cost,
            'notes' => $request->notes,
        ]);

        return redirect()->route('service-book.index', $request->car_detail_id)->with('success', 'Servisni zapis uspešno dodat.');
    }

    public function exportPdf($car_id)
    {
        $user = Auth::user();
        if ($user->num_of_questions_left <= 0) {
            return redirect()->route('subscriptions.purchase')->with('error', 'Nemate preostalih pitanja. Molimo vas da kupite paket da biste nastavili koristiti servisnu knjigu.');
        }

        $car = CarDetail::findOrFail($car_id);
        if ($car->user_id !== $user->id) {
            abort(403, 'Nemate dozvolu za ovaj automobil.');
        }

        $serviceRecords = ServiceRecord::where('car_detail_id', $car_id)->orderBy('service_date', 'desc')->get();

        $pdf = PDF::loadView('service-book.pdf', compact('car', 'serviceRecords'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['margin_top' => 0, 'margin_bottom' => 0, 'margin_left' => 0, 'margin_right' => 0]);

        return $pdf->download('servisna-knjiga-' . $car->brand . '-' . $car->model . '.pdf');
    }

    public function destroy($id)
    {
        $record = ServiceRecord::findOrFail($id);
        $carId = $record->car_detail_id;

        if ($record->carDetail->user_id !== Auth::id()) {
            abort(403, 'Nemate dozvolu za brisanje ovog zapisa.');
        }

        $record->delete();

        return redirect()->route('service-book.index', $carId)->with('success', 'Servisni zapis uspešno obrisan.');
    }

    public function show($car_id)
    {
        $user = Auth::user();
        $car = CarDetail::findOrFail($car_id);
    
        if ($car->user_id !== $user->id) {
            abort(403, 'Nemate dozvolu za ovaj automobil.');
        }
    
        $serviceRecords = ServiceRecord::where('car_detail_id', $car_id)->orderBy('service_date', 'desc')->get();
    
        return view('service-book.records', compact('car', 'serviceRecords'));
    }
}