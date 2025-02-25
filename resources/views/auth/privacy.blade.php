@extends('layouts.app')

@section('content')

<div class="max-w-7xl terms-and-privacy bg-black mx-8 my-16 px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-orange mb-4">Politika Privatnosti</h1>
    <p class="mb-4">
        Vaša privatnost nam je važna. Molimo vas da pročitate našu politiku privatnosti kako biste razumeli kako prikupljamo, koristimo i štitimo vaše podatke.
    </p>
    <div class="space-y-6 text-gray-300">
        <p><strong>Poslednje ažuriranje:</strong> 25.2.2025.</p>
        <p>
            <strong>Kompanija:</strong> Nikola Milić PR Labart, Ruže Šulman 17, 23000 Zrenjanin, Srbija<br>
            <strong>Kontakt:</strong> <a href="mailto:support@dijagnoza.com" class="text-orange hover:underline">support@dijagnoza.com</a>
        </p>

        <h2 class="text-2xl font-semibold text-orange">1. Uvod</h2>
        <p>
            Nikola Milić PR Labart (u daljem tekstu: "Mi" ili "Kompanija") posvećen je zaštiti privatnosti korisnika web aplikacije "Dijagnoza". Ova Politika privatnosti objašnjava kako prikupljamo, koristimo, čuvamo i štitimo vaše lične podatke, kao i koja prava imate u vezi sa njima. Korišćenjem aplikacije na adresi <a href="https://app.dijagnoza.com" class="text-orange hover:underline">app.dijagnoza.com</a> pristajete na postupke opisane u ovoj Politici.
        </p>

        <h2 class="text-2xl font-semibold text-orange">2. Informacije Koje Prikupljamo</h2>
        <p>Prikupljamo sledeće vrste podataka:</p>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>2.1. Podaci koje dobrovoljno pružate:</strong>
                <ul class="list-circle list-inside ml-4">
                    <li>Prilikom registracije: ime, prezime, e-mail adresa, lozinka, grad, država, broj telefona (opciono).</li>
                    <li>Podaci o vozilu: proizvođač, model, godište, zapremina motora, snaga motora, vrsta goriva, vrsta menjača.</li>
                    <li>Opis problema sa vozilom koji unesete radi dijagnostike.</li>
                </ul>
            </li>
            <li><strong>2.2. Podaci koji se automatski prikupljaju:</strong>
                <ul class="list-circle list-inside ml-4">
                    <li>Kolačići i tehnologije praćenja: Koristimo Google Analytics i Facebook Pixel za prikupljanje podataka o korišćenju aplikacije (npr. tip uređaja, IP adresa, obrasci korišćenja). Ovi podaci se koriste isključivo za analitiku, ne za marketinške svrhe.</li>
                </ul>
            </li>
            <li><strong>2.3. Napomena:</strong> Nije obavezno da nam pružite lične podatke, ali ako odbijete, možda nećete moći da se registrujete ili koristite sve funkcionalnosti Aplikacije.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">3. Kako Koristimo Vaše Podatke</h2>
        <p>Vaše podatke koristimo u sledeće svrhe:</p>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>3.1.</strong> Za pružanje usluga dijagnostike problema sa vozilom i generisanje preporuka putem ChatGPT tehnologije.</li>
            <li><strong>3.2.</strong> Za statističku analizu (npr. broj korisnika po zemlji), bez identifikacije pojedinačnih korisnika.</li>
            <li><strong>3.3.</strong> Za slanje obaveštenja o ažuriranjima Aplikacije putem e-maila (sa mogućnošću odjave).</li>
            <li><strong>3.4.</strong> Podaci se ne koriste za marketinške svrhe osim za gore navedene newsletter-e vezane za Aplikaciju.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">4. Deljenje Podataka</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>4.1.</strong> Vaši podaci se ne dele sa trećim stranama, osim u slučaju zakonske obaveze (npr. sudski nalog).</li>
            <li><strong>4.2.</strong> OpenAI (ChatGPT) obrađuje unete podatke za generisanje odgovora, ali mi ne snosimo odgovornost za njihovu upotrebu tih podataka u skladu sa njihovom politikom.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">5. Sigurnost Podataka</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>5.1.</strong> Koristimo SSL enkripciju za zaštitu podataka tokom prenosa.</li>
            <li><strong>5.2.</strong> Podaci o plaćanju zaštićeni su dodatnim sigurnosnim merama putem platforme Lemon Squeezy.</li>
            <li><strong>5.3.</strong> U slučaju povrede podataka (npr. neovlašćeni pristup), imamo procedure za obaveštavanje korisnika i nadležnih organa u roku propisanom zakonom (npr. 72 sata prema GDPR-u).</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">6. Vaša Prava</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>6.1.</strong> Možete pristupiti, ispraviti ili obrisati svoje podatke putem sekcije "Moj profil" i "Moja garaža" u Aplikaciji.</li>
            <li><strong>6.2.</strong> Imate pravo da zatražite brisanje svih podataka slanjem zahteva na <a href="mailto:support@dijagnoza.com" class="text-orange hover:underline">support@dijagnoza.com</a>.</li>
            <li><strong>6.3.</strong> U skladu sa GDPR-om (za korisnike iz EU), imate pravo na informacije, ispravku, brisanje i prenosivost podataka.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">7. Kolačići i Praćenje</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>7.1.</strong> Koristimo kolačiće, Google Analytics i Facebook Pixel isključivo za analitičke svrhe (npr. praćenje broja poseta, performansi aplikacije).</li>
            <li><strong>7.2.</strong> Ovi podaci ne služe za ciljano oglašavanje niti za kreiranje marketinških profila.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">8. Period Zadržavanja Podataka</h2>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>8.1.</strong> Podaci se čuvaju koliko je potrebno za pružanje usluga ili dok korisnik ne zatraži brisanje naloga.</li>
            <li><strong>8.2.</strong> Nakon brisanja naloga, podaci se trajno uklanjaju iz naše baze, osim ako zakon ne zahteva njihovo čuvanje.</li>
        </ul>

        <h2 class="text-2xl font-semibold text-orange">9. Maloletnici</h2>
        <p>
            <strong>9.1.</strong> Aplikacija nije namenjena osobama mlađim od 13 godina. Ukoliko saznamo da smo prikupili podatke od takvih osoba bez saglasnosti roditelja, odmah ćemo ih obrisati.
        </p>

        <h2 class="text-2xl font-semibold text-orange">10. Promene Politike</h2>
        <p>
            <strong>10.1.</strong> Zadržavamo pravo da izmenimo ovu Politiku. O promenama ćete biti obavešteni putem e-maila na adresu koju ste naveli prilikom registracije.
        </p>

        <h2 class="text-2xl font-semibold text-orange">11. Kontakt Informacije</h2>
        <p>Za sva pitanja ili zahteve u vezi sa vašim podacima, kontaktirajte nas:</p>
        <ul class="list-disc list-inside space-y-2">
            <li><strong>E-mail:</strong> <a href="mailto:support@dijagnoza.com" class="text-orange hover:underline">support@dijagnoza.com</a></li>
            <li><strong>Adresa:</strong> Nikola Milić PR Labart, Ruže Šulman 17, 23000 Zrenjanin, Srbija</li>
        </ul>
    </div>
</div>
@endsection