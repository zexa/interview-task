# Interview task

Commission payment calculator.

## Usage

```bash
git clone repo
cd repo
composer install
php index.php tests/inputs/input.csv # test input
```

## Testing

```bash
vendor/bin/phpunit --testdox tests
```

# Reikalavimai

- [x] **2019-05-21** laikui griežtų apribojimų nėra, tačiau neprarandam ryšio - jei susidursi su problemomis ar nerasi laisvo laiko, susisiek
- [x] **7.3.5** užduotis turi būti atlikta PHP kalba, versiją pasirink laisvai
- [x] galima naudoti išorines priklausomybes, įrankius, karkasus, jei tai atrodo reikalinga. Rekomenduojame naudoti `composer` net jei nesinaudosi išorinėmis bibliotekomis dėl autoloading'o - rekomenduojame naudoti PSR-4 standartą
- [x] sistema turėtų būti palaikoma:
  - [x] aiškios priklausomybės tarp kodo dalių
  - [x] sistema testuojama ir ištestuota
  - [x] kodas suprantamas, paprastas
- [x] sistema turi būti plečiama:
  - [x] **Priklauso kokio masto pakeitimai** naujo funkcionalumo pridėjimui ar egzistuojančio keitimui neturėtų reikti perrašyti visos sistemos
- [x] kodas turėtų atitikti PSR-1 ir PSR-2
- [x] turėtų būti pateikiama minimali dokumentacija:
  - [x] kaip paleisti sistemą (kokią komandą vykdyti)
  - [x] kaip paleisti sistemos testus (kokią komandą vykdyti)
  - [x] funkcionalumo trumpas aprašymas mažiau aiškiose vietose gali būti pačiame kode

## Response

1. [x] Ištaisyti output rezultatą, kad apvalintų visas valiutas teisingai.
2. [x] Patvarkyti, kad padavus nevalidžius (nepalaikomus) duomenis į input failą, būtų grąžinama klaida, o ne tiesiog komisinis = 0. Klaidų apdorojimui rekomenduojama naudoti Exceptions.
3. [x] Parašyti Unit testus.
