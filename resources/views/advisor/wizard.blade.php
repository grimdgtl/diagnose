{{-- resources/views/advisor/wizard.blade.php --}}
@extends('layouts.app')

@section('content')
@php
    // Trenutno stanje iz sesije (ako je korisnik već dodavao vozila)
    $currentCount = count(session('advisor.cars', []));
    $tokensLeft   = Auth::user()->num_of_questions_left;
@endphp

<div class="page-format relative">
  {{-- ceo ekran kao kolona – header + ostatak --}}
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between chat-header p-0 md:p-4">
            <h1 class="page-title">
                Dodaj vozilo
            </h1>
            <span id="questions-left" class="bg-orange text-white px-3 py-1 rounded-md">
                <b>Broj preostalih tokena: {{ $tokensLeft }}</b>
            </span>
        </div>

    {{-- === LOADER === --}}
    <div id="loader" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50 hidden">
        <div class="text-center">
            <img class="pulse" src="{{ asset('assets/images/logo-neon.png') }}">
            <p class="text-white uppercase font-black">Treba mi par sekundi da razmislim, molim sačekajte…</p>
        </div>
    </div>

    @if($tokensLeft > 0)
        {{-- indikator --}}
        <div class="flex items-center justify-between auth-form mb-4 mt-0 md:mt-8 vehicle-form relative self-center">
            <div id="carCounter" class="mb-2 text-right font-bold text-orange">
                Dodato vozila: <span id="carCount">{{ $currentCount }}</span>/3
            </div>
            <button type="button" id="clearCar" class="btn-orange second-btn">Ukloni vozila</button>
        </div>

        <form id="vehicleForm" class="auth-form mx-8 vehicle-form self-center">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <input  name="brand"           class="input-field" placeholder="Proizvođač">
                <input  name="model"           class="input-field" placeholder="Model">
                <input  name="year"  type="number" class="input-field" placeholder="Godište">

                <div class="relative">
                    <input name="mileage" type="number" class="input-field" placeholder="Pređeni kilometri">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm">KM</span>
                </div>

                <div class="relative">
                    <input name="engine_capacity" class="input-field" placeholder="Kubikaža (1499)">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm">ccm</span>
                </div>

                <div class="relative">
                    <input name="engine_power" type="number" class="input-field pr-12" placeholder="Snaga">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm">kW</span>
                </div>

                {{-- gorivo --}}
                <select name="fuel_type" class="input-field bg-gray-700">
                    <option value="">Vrsta goriva</option>
                    @foreach ([
                        'Benzin',
                        'Dizel',
                        'Benzin + Gas (TNG)',
                        'Benzin + Metan (CNG)',
                        'Električni pogon',
                        'Hibridni pogon'
                    ] as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>

                {{-- menjač --}}
                <select name="transmission" class="input-field bg-gray-700">
                    <option value="">Tip menjača</option>
                    @foreach ([
                        'Manuelni 4 brzine',
                        'Manuelni 5 brzina',
                        'Manuelni 6 brzina',
                        'Automatski / poluautomatski'
                    ] as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-4 justify-center pt-4">
                <button type="button" id="addCar"   class="btn-orange">+ Dodaj još vozilo</button>
                <button type="button" id="start"    class="btn-orange">Pošalji</button>
            </div>
        </form>

        <script>
        const form      = document.getElementById('vehicleForm');
        const addBtn    = document.getElementById('addCar');
        const clearBtn  = document.getElementById('clearCar');
        const startBtn  = document.getElementById('start');
        const countSpan = document.getElementById('carCount');
        const loader    = document.getElementById('loader');
        const csrf      = document.querySelector('meta[name="csrf-token"]').content;

        /* Disable dugme "Dodaj još vozilo" ako je već 3/3 (server side) */
        toggleAddBtn(parseInt(countSpan.textContent, 10));

        /* -------------------------------------------------- helpers */
        function serialize(frm){
            return Object.fromEntries(new FormData(frm).entries());
        }

        async function saveCar(data){
            const res = await fetch('{{ route('advisor.wizard.store') }}', {
                method : 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept'      : 'application/json'
                },
                body: JSON.stringify(data)
            });

            if(!res.ok){
                const msg = await res.text();
                throw new Error(msg || 'Greška pri čuvanju vozila');
            }
            return res.json(); // {count: x}
        }

        function updateCounter(n){
            countSpan.textContent = n;
            toggleAddBtn(n);
        }

        function toggleAddBtn(n){
            addBtn.disabled = n >= 3;
        }

        /* -------------------------------------------------- events */
        addBtn.addEventListener('click', async () => {
            try{
                const data = serialize(form);
                const d    = await saveCar(data);
                updateCounter(d.count);

                form.reset();
                alert(`Vozilo dodato (${d.count}/3)`);
            }catch(e){
                alert(e.message);
            }
        });

        clearBtn.addEventListener('click', async () => {
            try{
                const res = await fetch('{{ route('advisor.wizard.clear') }}', {
                    method : 'POST',
                    headers: {'X-CSRF-TOKEN': csrf, 'Accept':'application/json'}
                });

                if(!res.ok) throw new Error('Greška pri brisanju vozila');

                const j = await res.json();      // {count: 0}
                updateCounter(j.count);
                form.reset();
                alert('Sva vozila su obrisana.');
            }catch(e){
                alert(e.message);
            }
        });

        startBtn.addEventListener('click', async () => {
            try{
                const data   = serialize(form);
                const filled = Object.values(data).some(v => v.trim() !== '');

                // ako je forma popunjena (korisnik unio još jedno vozilo) – snimi ga
                if(filled){
                    const d = await saveCar(data);
                    updateCounter(d.count);
                }

                // prikaz loader-a pre slanja start zahteva
                loader.classList.remove('hidden');

                const res = await fetch('{{ route('advisor.wizard.start') }}', {
                    method : 'POST',
                    headers: {'X-CSRF-TOKEN': csrf, 'Accept':'application/json'}
                });

                if(!res.ok){
                    const msg = await res.text();
                    throw new Error(msg || 'Neuspešan start');
                }

                const j = await res.json();
                window.location = j.redirect;

            }catch(e){
                alert(e.message);
                loader.classList.add('hidden');
            }
        });
        </script>
    @else
        <div class="flex flex-col items-center justify-center h-full default-width">
            <p class="text-gray-400 mb-4 text-center">Nemate tokena.</p>
            <a href="{{ route('profile.subscription') }}" class="btn-orange px-6 py-2 text-black hover:bg-orange-500">
                Kupi tokene
            </a>
        </div>
    @endif
</div>
</div>
@endsection
