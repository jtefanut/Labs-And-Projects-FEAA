<?php

return [
    'anpc_link' => 'ANPC: informații despre SAL',
    'website_link' => 'Site-ul ANPC',
    'sal_link' => 'Platforma SAL pentru consumatori',
    'order_consent' => 'Confirm că am citit și accept Termenii și condițiile. Înțeleg că această comandă demo nu activează un serviciu și nu transferă bani.',
    'draft_notice' => 'Documente demonstrative. Înlocuiți câmpurile dintre paranteze cu date verificate și cereți o verificare juridică înainte de publicare sau vânzare.',
    'company_fields' => 'Date de completat: [denumirea legală], [forma juridică], [CUI], [număr Registrul Comerțului], [sediu], [e-mail], [telefon], [date de facturare și TVA].',
    'pages' => [
        'terms' => [
            'title' => 'Termeni și condiții',
            'intro' => 'Acest model descrie cadrul general al serviciilor ELOQUENCE HOSTING. Condițiile comerciale finale trebuie completate și validate înainte de utilizare.',
            'sections' => [
                ['title' => 'Furnizorul', 'body' => 'Serviciile sunt oferite de [denumirea legală], [forma juridică], CUI [CUI], înregistrată la Registrul Comerțului cu nr. [număr], cu sediul în [adresă]. Contact: [e-mail] / [telefon].'],
                ['title' => 'Servicii și comenzi', 'body' => 'Catalogul poate include găzduire web, WordPress administrat, VPS, cloud, e-mail, domenii, backup și certificate SSL. Comanda devine contractuală numai după confirmarea furnizorului și comunicarea condițiilor specifice serviciului. Acest site este o demonstrație: comenzile și plățile afișate nu activează servicii comerciale și nu transferă bani.'],
                ['title' => 'Prețuri, taxe și facturare', 'body' => 'Prețul, moneda, durata, taxele aplicabile, frecvența facturării, reînnoirea și orice cost suplimentar trebuie afișate clar înainte de plasarea unei comenzi. Valorile din această demonstrație sunt orientative. Completați regimul TVA și regulile efective de facturare: [detalii preț și taxe].'],
                ['title' => 'Utilizare și disponibilitate', 'body' => 'Clientul trebuie să folosească serviciile conform legii și să păstreze securitatea datelor de acces. Resursele, limitele, mentenanța, asistența, backup-ul și nivelul de disponibilitate trebuie stabilite în descrierea fiecărui plan sau într-un SLA separat; nu se garantează aici o disponibilitate ori o politică de backup.'],
                ['title' => 'Încetare și soluționarea disputelor', 'body' => 'Durata, anularea, suspendarea, rambursările și consecințele încetării trebuie completate pentru fiecare tip de serviciu: [procedură și termene]. Drepturile legale ale consumatorilor rămân aplicabile. Pentru reclamații folosiți [adresă de contact]; detalii suplimentare sunt în pagina Reclamații.'],
            ],
        ],
        'privacy' => [
            'title' => 'Informare privind confidențialitatea',
            'intro' => 'Această informare este un șablon pentru prelucrarea datelor personale în cont, comenzi, facturare și suport.',
            'sections' => [
                ['title' => 'Operator și contact', 'body' => 'Operator: [denumirea legală], [adresă], CUI [CUI]. Contact pentru date personale: [e-mail privacy]. Responsabilul cu protecția datelor, dacă este desemnat: [date de contact].'],
                ['title' => 'Date, scopuri și temeiuri', 'body' => 'Pot fi prelucrate date de identificare și contact, date de cont, comenzi și servicii, facturi și plăți, precum și conversații de suport. Scopurile și temeiurile trebuie mapate concret: administrarea contului și contractului [temei], obligații contabile și fiscale [temei], securitate și prevenirea abuzului [interes legitim și evaluare], respectiv marketing numai cu temeiul și opțiunea legală necesare.'],
                ['title' => 'Destinatari și transferuri', 'body' => 'Aplicația încarcă fonturi de la Google Fonts; includeți acest furnizor și eventualele transferuri de date în informare. Completați furnizorii care acționează ca persoane împuternicite (hosting, e-mail, plăți, suport, contabilitate), datele lor și eventualele transferuri în afara SEE, împreună cu garanțiile aplicabile: [listă și detalii].'],
                ['title' => 'Păstrare și drepturile persoanei', 'body' => 'Stabiliți perioade concrete de păstrare pentru conturi, documente financiar-contabile, tichete și jurnale: [perioade]. Persoanele vizate pot solicita acces, rectificare, ștergere, restricționare și portabilitatea datelor, unde dreptul se aplică, și se pot opune prelucrărilor bazate pe interes legitim, își pot retrage consimțământul unde acesta este temeiul și pot depune o plângere la ANSPDCP. Contact: [e-mail privacy].'],
                ['title' => 'Securitate și actualizări', 'body' => 'Sunt aplicate măsuri tehnice și organizatorice proporționale cu riscurile. Descrieți măsurile efectiv implementate și actualizați această informare când se schimbă prelucrările.'],
            ],
        ],
        'cookies' => [
            'title' => 'Politica privind cookie-urile',
            'intro' => 'Aplicația demonstrativă folosește cookie-uri tehnice necesare sesiunii și protecției formularelor. Tema vizuală este păstrată local în browser. Nu sunt configurate în prezent cookie-uri de analiză sau publicitate.',
            'sections' => [
                ['title' => 'Cookie-uri necesare', 'body' => 'Cookie-urile de sesiune și token-ul CSRF susțin autentificarea, navigarea și protecția cererilor. Acestea sunt tehnice și nu sunt folosite pentru profilare publicitară.'],
                ['title' => 'Preferință de temă', 'body' => 'Preferința light/dark este salvată în stocarea locală a browserului, nu într-un cookie.'],
                ['title' => 'Servicii opționale', 'body' => 'Dacă vor fi adăugate analitice, publicitate, hărți, chat sau conținut de la terți, lista furnizorilor, scopurile, duratele și mecanismul de consimțământ trebuie actualizate înainte de activare.'],
            ],
        ],
        'withdrawal' => [
            'title' => 'Dreptul de retragere',
            'intro' => 'Informații pentru consumatorii care încheie contracte la distanță pentru servicii. Aplicabilitatea și calculul termenului depind de contract și de circumstanțele concrete.',
            'sections' => [
                ['title' => 'Termen și notificare', 'body' => 'În cazurile prevăzute de lege, consumatorul se poate retrage în termen de 14 zile de la încheierea contractului de servicii, fără a justifica decizia, printr-o declarație neechivocă trimisă la [e-mail/adresă]. Poate folosi formularul de mai jos, fără ca acesta să fie obligatoriu.'],
                ['title' => 'Începerea serviciului în perioada de retragere', 'body' => 'Dacă un consumator solicită expres începerea prestării înainte de expirarea perioadei de retragere, informațiile despre plata proporțională și pierderea dreptului după executarea integrală trebuie prezentate și acceptate separat în forma cerută de lege. Nu aplicați o renunțare generală sau o clauză automată de nerambursare. Excepțiile legale se aplică numai când sunt îndeplinite condițiile lor.'],
                ['title' => 'Efecte și rambursare', 'body' => 'Când dreptul este exercitat valid, rambursarea și eventualele sume proporționale pentru servicii începute se calculează potrivit legii și informațiilor comunicate înainte de contract. Completați metoda și termenul operațional de rambursare: [procedură].'],
                ['title' => 'Model de formular', 'body' => 'Către [denumirea legală, adresă și e-mail]: Vă informez prin prezenta cu privire la retragerea mea din contractul pentru următorul serviciu: [serviciu]. Comandat la/contractat la: [data]. Nume: [nume]. Adresă: [adresă]. Data notificării: [data]. Semnătura (doar pentru notificare pe hârtie): [semnătura].'],
            ],
        ],
        'complaints' => [
            'title' => 'Reclamații și soluționarea litigiilor',
            'intro' => 'Trimite mai întâi o sesizare furnizorului, cu numărul comenzii sau tichetului și o descriere clară a situației.',
            'sections' => [
                ['title' => 'Contact direct', 'body' => 'E-mail: [adresă de suport]. Adresă poștală: [sediul furnizorului]. Telefon: [număr]. Termenul și etapele de răspuns: [procedură internă]. Păstrează confirmarea transmiterii și documentele relevante.'],
                ['title' => 'Autorități și SAL', 'body' => 'Consumatorii pot consulta informațiile ANPC despre soluționarea alternativă a litigiilor (SAL) și pot verifica eligibilitatea cazului și procedura aplicabilă. Platforma SAL este disponibilă la reclamatiisal.anpc.ro; informații generale la anpc.ro/sal/. Depunerea unei solicitări nu înlocuiește condițiile legale de admisibilitate.'],
                ['title' => 'Datele comerciantului', 'body' => 'Înainte de publicare completați identitatea juridică, CUI, numărul de înregistrare, sediul și datele de contact în Termeni și condiții și în această pagină.'],
            ],
        ],
    ],
];
