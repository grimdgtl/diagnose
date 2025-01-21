@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-2xl font-bold text-orange mb-4">Moja garaža</h1>

    @if($cars->count() > 0)
        <table class="min-w-full bg-gray-800 text-white">
            <thead>
                <tr class="bg-gray-700">
                    <th class="px-4 py-2">Proizvođač</th>
                    <th class="px-4 py-2">Model</th>
                    <th class="px-4 py-2">Godište</th>
                    <th class="px-4 py-2">Akcije</th>
                </tr>
            </thead>
            <tbody>
            @foreach($cars as $car)
                <tr class="border-b border-gray-700">
                    <td class="px-4 py-2">{{ $car->brand }}</td>
                    <td class="px-4 py-2">{{ $car->model }}</td>
                    <td class="px-4 py-2">{{ $car->year }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('profile.garage.edit', $car->id) }}" class="text-blue-400 hover:underline">Izmeni</a>
                        <form action="{{ route('profile.garage.delete', $car->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline ml-2">Obriši</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>Još uvek niste dodali nijedan auto.</p>
    @endif

    {{-- Ovde možeš dodati link ili formu za dodavanje novog auta, ako to nudiš --}}
</div>
@endsection
