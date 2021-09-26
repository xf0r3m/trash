# TRASH - Przeglądarka plików przez www.

**TRASH** - Prosta przeglądarka plików przez www, wykorzystująca technologie WEB, takie jak PHP czy Bootstrap 4. Jej metoda działania bazuje na możliwościach UNIX-owych systemów plików. Mozemy udostępnić każdą lokalizacje w systemie wystarczy aby grupa *inni* miała prawa odczytu.

**INSTALACJA**

1. `$ sudo apt install apache2 apache2-utils`
2. `$ sudo apt install php7.3 libapache2-mod-php7.3 php-common php7.3-cli php7.3-common php7.3-json php7.3-opcache php7.3-readline`
3. `$ git clone https://git.morketsmerke.net/xf0r3m/trash.git`
4. `$ sudo rm /var/www/html/*`
5. `$ sudo cp -rvv trash/* /var/www/html`
6. `$ sudo chown -R www-data:www-data /var/www/html`

**KONFIGURACJA**

1. Przechodzimy pod adres: *http(s)://adres_serwera_trash/admin*
2. Tworzymy użytkownika dostępowego.
3. Zmieniamy scieżkę, ewentualnie tło trasha, dobieramy odpowiedni kolor tekstu do tła, możemy również zabezpieczyć stronę hasłem.
