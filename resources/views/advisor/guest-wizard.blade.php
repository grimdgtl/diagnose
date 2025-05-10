@extends('layouts.app')

@section('content')
<div class="bg-black border-orange radius h-full main-child mobile-height">
    
    <div id="loader" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50 hidden">
      <div class="text-center">
        <img class="pulse" src="{{ asset('assets/images/logo-neon.png') }}">
        <p class="text-white uppercase font-black">
          Treba mi 10s da razmislim, molim sačekajte...
        </p>
      </div>
    </div>

    <h1 class="page-title mb-4">Savetnik za kupovinu automobila</h1>
    <p class="mb-6 text-gray-400 text-center max-w-2xl">
        Unesite podatke o vozilu o kojem želite da saznate više informacija. Možete uporediti najvise 3 vozila.
    </p>
    
    <div class="flex items-center justify-between auth-form mb-4">
        <div id="carCounter" class="mb-2 text-right font-bold text-orange">
            Dodato vozila: <span id="carCount">0</span>/3
        </div>
        <button type="button" id="clearCar" class="btn-orange second-btn">Ukloni vozila</button>
    </div>

    <form id="vehicleForm" class="auth-form">
        @csrf
        <div class="grid md:grid-cols-2 gap-4">
            <input name="brand" class="input-field" placeholder="Proizvođač">
            <input name="model" class="input-field" placeholder="Model">
            <input name="year" type="number" class="input-field" placeholder="Godište">
            <div class="relative">
                <input name="mileage" type="number" class="input-field" placeholder="Pređeni kilometri">
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm">KM</span>
            </div>
            <div class="relative">
                <input name="engine_capacity" class="input-field" placeholder="Kubikaža">
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm">ccm</span>
            </div>
            <div class="relative">
                <input name="engine_power" type="number" class="input-field pr-12" placeholder="Snaga">
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm">kW</span>
            </div>
            <select name="fuel_type" class="input-field bg-gray-700">
                <option value="">Vrsta goriva</option>
                @foreach (['Benzin', 'Dizel', 'Električni', 'Hibridni'] as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
            <select name="transmission" class="input-field bg-gray-700">
                <option value="">Tip menjača</option>
                @foreach (['Manuelni', 'Automatski'] as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-4 justify-center pt-4">
            <button type="button" id="addCar" class="btn-orange">+ Dodaj još vozilo</button>
            <button type="button" id="start" class="btn-orange">Pošalji</button>
        </div>
    </form>
</div>

<script>
const form      = document.getElementById('vehicleForm');
const addBtn    = document.getElementById('addCar');
const clearBtn  = document.getElementById('clearCar');
const startBtn  = document.getElementById('start');
const countSpan = document.getElementById('carCount');
const csrf      = document.querySelector('meta[name="csrf-token"]').content;

function serialize(f){ return Object.fromEntries(new FormData(f).entries()); }

async function saveCar(data){
    const res = await fetch('{{ route('advisor.guest.wizard.store') }}', {
        method : 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type':'application/json', 'Accept':'application/json' },
        body   : JSON.stringify(data)
    });
    if (res.status === 403) {
        const j = await res.json();
        window.location = j.redirectUrl;
        return null;
    }
    if (!res.ok) throw new Error(await res.text());
    return res.json();
}

function updateCounter(n){ countSpan.textContent = n; addBtn.disabled = n >= 3; }

addBtn.addEventListener('click', async () => {
    try {
        const data = serialize(form);
        const j = await saveCar(data);
        if (!j) return;
        updateCounter(j.count);
        form.reset();
    } catch(e){ alert(e.message); }
});

clearBtn.addEventListener('click', async () => {
    try{
        const res = await fetch('{{ route('advisor.guest.wizard.clear') }}', {
            method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}
        });
        if (!res.ok) throw new Error('Greška pri brisanju vozila');
        const j = await res.json();
        updateCounter(j.count);
        form.reset();
    } catch(e){ alert(e.message); }
});

startBtn.addEventListener('click', async () => {
    document.getElementById('loader').classList.remove('hidden');
    try{
        const data = serialize(form);
        if (Object.values(data).some(v=>v.trim()!=='')) await saveCar(data);

        const res = await fetch('{{ route('advisor.guest.wizard.start') }}', {
            method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}
        });
        if (res.status === 403){
            const j = await res.json();
            window.location = j.redirectUrl;
            return;
        }
        if (!res.ok) throw new Error(await res.text());
        const j = await res.json();
        window.location = j.redirectUrl;

    } catch(e){ alert(e.message); }
    finally { document.getElementById('loader').classList.add('hidden'); }
});
</script>
@endsection
