<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CarDetail;
use App\Models\Chat;
use App\Models\Rating;

class ProfileController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user(); // Dohvati trenutno ulogovanog korisnika
        return view('profile.my-data', compact('user')); // Prikaz view-a sa podacima korisnika
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            // ...
        ]);
        $user->update($request->all());
        return back()->with('success','Podaci uspešno izmenjeni!');
    }

    public function deleteProfile()
    {
        $user = Auth::user();
        // Brisemo user-a i sve podatke. 
        // Ako su definisani onDelete('cascade') FK, sve ce da se obrise automatski
        $user->delete();

        Auth::logout();
        return redirect('/')->with('info','Vaš profil i svi podaci su obrisani.');
    }

    // Moja garaža
    public function showGarage()
    {
        $user = Auth::user();
        $cars = CarDetail::where('user_id',$user->id)->get();
        return view('profile.garage', compact('cars'));
    }

    public function editCar($id)
    {
        $car = CarDetail::findOrFail($id);
        return view('profile.car-edit', compact('car'));
    }

    public function updateCar(Request $request, $id)
    {
        $car = CarDetail::findOrFail($id);
        // Validiraj i update-uj
        $car->update($request->all());
        return redirect()->route('profile.garage')->with('success','Podaci o automobilu su izmenjeni!');
    }

    public function deleteCar($id)
    {
        $car = CarDetail::findOrFail($id);
        $car->delete();
        return back()->with('info','Automobil obrisan iz garaže.');
    }

    // Istorija chatova
    public function showHistory()
    {
        $user = Auth::user();
        $closedChats = Chat::where('user_id',$user->id)->where('status','closed')->get();
        return view('profile.history', compact('closedChats'));
    }

    // Pregled arhiviranog chata (read-only)
    public function showArchivedChat($chatId)
    {
        $user = Auth::user();
        $chat = Chat::where('id',$chatId)->where('user_id',$user->id)->where('status','closed')->firstOrFail();
        $questions = $chat->questions; // ->load('responses')
        return view('profile.archived-chat', compact('chat','questions'));
    }

    // Oceni aplikaciju
    
    public function showRateForm()
    {
        return view('profile.rate');
    }
    
    public function rateApp(Request $request)
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string'
        ]);

        $rating = Rating::create([
            'user_id' => Auth::id(),
            'rating'  => $request->rating,
            'feedback'=> $request->feedback,
        ]);

        return back()->with('success','Hvala na oceni!');
    }

    public function subscription()
    {
        $user = Auth::user();
        return view('profile.subscription', compact('user'));
    }

}

