# Interview task
Commission payment calculator.

## Usage
```bash
git clone repo
cd repo
php index.php ./input.csv
```
Replace input.csv with your desired unit tests.

# Reikalavimai

- [ ] **2019-05-19** laikui griežtų apribojimų nėra, tačiau neprarandam ryšio - jei susidursi su problemomis ar nerasi laisvo laiko, susisiek
- [x] **7.3.5** užduotis turi būti atlikta PHP kalba, versiją pasirink laisvai
- [x] galima naudoti išorines priklausomybes, įrankius, karkasus, jei tai atrodo reikalinga. Rekomenduojame naudoti `composer` net jei nesinaudosi išorinėmis bibliotekomis dėl autoloading'o - rekomenduojame naudoti PSR-4 standartą
- [ ] sistema turėtų būti palaikoma:
  - [x] aiškios priklausomybės tarp kodo dalių
  - [ ] sistema testuojama ir ištestuota
  - [x] kodas suprantamas, paprastas
- [ ] sistema turi būti plečiama:
  - [ ] naujo funkcionalumo pridėjimui ar egzistuojančio keitimui neturėtų reikti perrašyti visos sistemos
- [x] kodas turėtų atitikti PSR-1 ir PSR-2
- [ ] turėtų būti pateikiama minimali dokumentacija:
  - [x] kaip paleisti sistemą (kokią komandą vykdyti)
  - [ ] kaip paleisti sistemos testus (kokią komandą vykdyti)
  - [x] funkcionalumo trumpas aprašymas mažiau aiškiose vietose gali būti pačiame kode

## Natural

- Įprastas komisinis - 0.3 % nuo sumos.
- 1000.00 EUR per savaitę (nuo pirmadienio iki sekmadienio) galima išsiimti nemokamai.
- Jei suma viršijama - komisinis skaičiuojamas tik nuo viršytos sumos (t.y. vis dar galioja 1000 EUR be komiso).
- Ši nuolaida taikoma tik pirmoms 3 išėmimo operacijoms per savaitę - jei išsiimama 4-tą ir paskesnius kartus, komisinis toms operacijoms skaičiuojamas įprastai - taisyklė dėl 1000 EUR galioja tik pirmiesiems trims išgryninimams.
