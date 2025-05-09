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
    /* -----------------------------------------------------------------
     |  MOJ PROFIL
     |----------------------------------------------------------------- */
    public function showProfile()
    {
        $user = Auth::user();
        return view('profile.my-data', compact('user'));
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

        return back()->with('success', 'Podaci uspešno izmenjeni!');
    }

    public function deleteProfile()
    {
        $user = Auth::user();
        $user->delete();

        Auth::logout();

        return redirect('/')->with('info', 'Vaš profil i svi podaci su obrisani.');
    }

    /* -----------------------------------------------------------------
     |  MOJA GARAŽA
     |----------------------------------------------------------------- */
    public function showGarage(Request $request)   // <-- dodato $request
    {
        $user = Auth::user();
        $cars = CarDetail::where('user_id', $user->id)->get();

        // prikaži formu ako je garaža prazna *ili* je pozvano  ?add=1
        $showForm = $cars->isEmpty() || $request->boolean('add');

        return view('profile.garage', compact('cars', 'showForm'));
    }

    public function editCar($id)
    {
        $car = CarDetail::findOrFail($id);
        return view('profile.car-edit', compact('car'));
    }

    public function updateCar(Request $request, $id)
    {
        $car = CarDetail::findOrFail($id);
        $car->update($request->all());

        return redirect()
            ->route('profile.garage')
            ->with('success', 'Podaci o automobilu su izmenjeni!');
    }

    public function deleteCar($id)
    {
        $car = CarDetail::findOrFail($id);
        $car->delete();

        return back()->with('info', 'Automobil obrisan iz garaže.');
    }

    /* -----------------------------------------------------------------
     |  ISTORIJA CHATOVA
     |----------------------------------------------------------------- */
    public function showHistory()
    {
        $user = Auth::user();
        $closedChats = Chat::where('user_id', $user->id)
                           ->where('status', 'closed')
                           ->get();

        return view('profile.history', compact('closedChats'));
    }

    public function showArchivedChat($chatId)
    {
        $user = Auth::user();

        $chat = Chat::where('id', $chatId)
                    ->where('user_id', $user->id)
                    ->where('status', 'closed')
                    ->firstOrFail();

        $questions = $chat->questions;

        return view('profile.archived-chat', compact('chat', 'questions'));
    }

    /* -----------------------------------------------------------------
     |  OCENJIVANJE APLIKACIJE
     |----------------------------------------------------------------- */
    public function showRateForm()
    {
        return view('profile.rate');
    }

    public function rateApp(Request $request)
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        Rating::create([
            'user_id'  => Auth::id(),
            'rating'   => $request->rating,
            'feedback' => $request->feedback,
        ]);

        return back()->with('success', 'Hvala na oceni!');
    }

    /* -----------------------------------------------------------------
     |  PRETPLATA
     |----------------------------------------------------------------- */
    public function subscription()
    {
        $user = Auth::user();

        $countryCode = ($user->country === 'Serbia' || $user->country === 'Srbija')
                     ? 'RS'
                     : $user->country;

        $checkoutBasic = $user->checkout('714137', [
            'data' => ['billing' => ['address' => ['country' => $countryCode]]],
        ]);

        $checkoutPro = $user->checkout('714199', [
            'data' => ['billing' => ['address' => ['country' => $countryCode]]],
        ]);

        return view('profile.subscription', compact('user', 'checkoutBasic', 'checkoutPro'));
    }
}
