@extends('layouts.app')

@section('content')
<div class="max-w-7xl terms-and-privacy bg-black mx-8 my-12 px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-orange mb-4">Uslovi Korišćenja</h1>
    <p class="mb-4">
        Dobrodošli na našu aplikaciju. Molimo vas da pažljivo pročitate uslove korišćenja pre nego što počnete koristiti našu uslugu.
    </p>
    <div class="space-y-6 text-gray-300">
        <p><strong>Poslednje ažuriranje:</strong> 25.2.2025.</p>
        <p>
            <strong>Licencor:</strong> Nikola Milić PR Labart, Ruže Šulman 17, 23000 Zrenjanin, Srbija, PIB: 113343290, MB: 66755657<br>
            <strong>Web platforma:</strong> app.dijagnoza.com<br>
            <strong>Kontakt:</strong> <a href="mailto:support@dijagnoza.com" class="text-orange hover:underline">support@dijagnoza.com</a>
        </p>

        <h2 class="text-2xl font-semibold text-orange">1. Uvod</h2>
        <p>
            Web aplikacija "Dijagnoza" (u daljem tekstu: "Aplikacija") je kreirana da pomogne korisnicima u dijagnostikovanju problema sa automobilima, pružanju preporuka za delove, auto-mehaničare i uputstava za samostalnu popravku. Aplikacija koristi OpenAI ChatGPT tehnologiju za generisanje odgovora na osnovu podataka koje korisnik unese (opis problema i informacije o vozilu). <strong>Saveti koje pruža aplikacija su informativnog karaktera i ne predstavljaju zamenu za profesionalne mehaničarske usluge.</strong> Korišćenjem Aplikacije prihvatate ove Uslove korišćenja u celosti.
        </p>

        <h2 class="text-2xl font-semibold text-orange">2. Opseg Licence</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>2.1.</strong> Korisniku se dodeljuje neekskluzivna, neprenosiva, nepodlicencirana licenca za korišćenje Aplikacije na uređajima u svom vlasništvu ili pod svojom kontrolom.</li>
            <li><strong>2.2.</strong> Ova licenca obuhvata sva ažuriranja Aplikacije, osim ukoliko novo ažuriranje ne dolazi sa posebnom licencom.</li>
            <li><strong>2.3.</strong> Zabranjeno je deljenje, prodaja, iznajmljivanje, preprodaja ili redistribucija Aplikacije trećim licima bez pismene saglasnosti Licencora.</li>
            <li><strong>2.4.</strong> Zabranjeno je reverzno inženjerstvo, dekompilacija, modifikacija ili pokušaj izdvajanja izvornog koda Aplikacije bez prethodne pisane saglasnosti Licencora.</li>
            <li><strong>2.5.</strong> Licencor zadržava pravo da izmeni ove uslove u bilo kom trenutku, uz obaveštenje korisnika putem e-maila.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">3. Tehnički Zahtevi</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>3.1.</strong> Aplikacija zahteva moderni web pregledač (npr. Chrome, Firefox, Safari) sa omogućenim JavaScript-om i stabilnu internet konekciju.</li>
            <li><strong>3.2.</strong> Licencor nastoji da održava kompatibilnost sa novim verzijama pregledača, ali korisnik je odgovoran za proveru tehničke kompatibilnosti svog uređaja.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">4. Registracija i Nalozi</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>4.1.</strong> Za korišćenje Aplikacije potrebna je registracija sa sledećim podacima: ime, prezime, e-mail adresa, lozinka, grad, država i opciono broj telefona.</li>
            <li><strong>4.2.</strong> Korisnik je odgovoran za čuvanje poverljivosti svojih podataka za prijavu i za sve aktivnosti koje se obavljaju pod njegovim nalogom.</li>
            <li><strong>4.3.</strong> Korisnik može upravljati svojim podacima i podacima o vozilu (proizvođač, model, godište, zapremina motora, snaga motora, vrsta goriva, vrsta menjača) putem sekcije "Moj profil" i "Moja garaža" unutar Aplikacije.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">5. Plaćanje i Povraćaj Novca</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>5.1.</strong> Aplikacija nudi dva paketa: <strong>Basic</strong> i <strong>Pro</strong>, koji se plaćaju putem kartičnog plaćanja preko platforme Lemon Squeezy. Nakon uspešne kupovine, korisnik odmah dobija pristup kupljenom proizvodu.</li>
            <li><strong>5.2.</strong> Korisnici mogu zatražiti povraćaj novca ako:
                <ul class="list-circle list-inside ml-4">
                    <li>Nisu potrošili više od 10% kupljenih pitanja.</li>
                    <li>Zahtev je podnet unutar 5 radnih dana od trenutka kupovine.</li>
                </ul>
                Zahtevi za povraćaj šalju se na <a href="mailto:support@dijagnoza.com" class="text-orange hover:underline">support@dijagnoza.com</a>.
            </li>
            <li><strong>5.3.</strong> Podaci o karticama i transakcijama zaštićeni su dodatnim sigurnosnim merama pored SSL-a, u skladu sa standardima platforme Lemon Squeezy.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">6. Intelektualna Svojina</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>6.1.</strong> Logo i naziv "Dijagnoza" su vlasništvo Nikola Milić PR Labart i zaštićeni su relevantnim zakonima o intelektualnoj svojini.</li>
            <li><strong>6.2.</strong> Svaka neovlašćena upotreba, kopiranje, distribucija ili modifikacija ovih elemenata je strogo zabranjena i može dovesti do pravnih posledica.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">7. Ograničenje Odgovornosti</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>7.1.</strong> Aplikacija koristi ChatGPT (OpenAI) za generisanje saveta na osnovu unetih podataka. <strong>Licencor nije odgovoran za bilo kakve greške, netačnosti ili štete nastale usled primene saveta dobijenih putem Aplikacije.</strong></li>
            <li><strong>7.2.</strong> Korisnici koriste Aplikaciju na sopstveni rizik. Saveti su informativnog karaktera i ne treba ih smatrati profesionalnim mehaničarskim uslugama. Licencor ne snosi odgovornost za bilo kakvu štetu na vozilu ili ličnu štetu nastalu usled pogrešne primene ovih saveta.</li>
            <li><strong>7.3.</strong> Aplikacija se pruža "kako jeste" i "kako je dostupna", bez ikakvih izričitih ili podrazumevanih garancija, uključujući garancije za tačnost, pouzdanost ili podobnost za određenu svrhu.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">8. Održavanje i Podrška</h2>
        <p>
            <strong>8.1.</strong> Licencor je odgovoran za održavanje i pružanje tehničke podrške za Aplikaciju. Korisnici mogu kontaktirati podršku na <a href="mailto:support@dijagnoza.com" class="text-orange hover:underline">support@dijagnoza.com</a>.
        </p>

        <h2 class="text-2xl font-semibold text-orange">9. Sporovi i Medijacija</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>9.1.</strong> U slučaju spora između korisnika i Licencora, stranke se obavezuju da prvo pokušaju rešiti spor putem medijacije uz pomoć neutralnog medijatora. Troškovi medijacije dele se između stranaka, osim ako nije drugačije dogovoreno.</li>
            <li><strong>9.2.</strong> Ako medijacija ne uspe, sporovi će se rešavati pred nadležnim sudom u Zrenjaninu, Srbija, u skladu sa zakonima Republike Srbije.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">10. Raskid</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>10.1.</strong> Licenca važi dok je ne raskine Licencor ili korisnik. Licencor može raskinuti licencu bez prethodne najave ako korisnik prekrši ove Uslove.</li>
            <li><strong>10.2.</strong> Nakon raskida, korisnik je dužan da prestane sa korišćenjem Aplikacije.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">11. Promene Uslova</h2>
        <p>
            <strong>11.1.</strong> Licencor može izmeniti ove Uslove u bilo kom trenutku. Korisnici će biti obavešteni o promenama putem e-maila na adresu koju su naveli prilikom registracije.
        </p>
    </div>
</div>
@endsection