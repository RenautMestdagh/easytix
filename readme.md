<p align="center">
  <img src="resources/images/noBgColor.png" alt="Easytix Logo" width="200"/>
</p>

# Easytix - ticketing service voor evenementen

Dit project is een robuust ticketing systeem, ontworpen om evenementbeheer, ticketverkoop, betalingsverwerking en bezoekersanalyse te stroomlijnen. Het systeem biedt organisatoren een eigen dashboard voor efficiënt beheer van hun evenementen en de mogelijkheid om evenementen onder hun eigen (sub)domein te hosten.

## Functionele Vereisten

### Evenementenbeheer
* **CRUD-functionaliteit voor Evenementen**: Volledige Create, Read, Update, Delete (CRUD) functionaliteit voor evenementen, inclusief details zoals naam, locatie, datum en beschikbaarheid.
* **Verschillende Tickettypes**: Mogelijkheid om diverse tickettypes per evenement aan te maken, zoals VIP, standaard en early bird tickets.

### Ticketverkoop & QR-code Generatie
* **Ticket Aankoop**: Klanten kunnen eenvoudig tickets kopen via een intuïtief frontend.
* **Bevestigingsmail**: Een bevestigingsmail wordt verzonden naar de klant na aankoop van een ticket.
* **QR-code Generatie**: Na aankoop ontvangen klanten een unieke QR-code voor elk ticket.
* **QR-code Scanning**: Efficiënte scanfunctionaliteit voor QR-codes om toegang te verlenen bij evenementen.

### Betaalfunctionaliteit
* **Stripe/PayPal Integratie**: Naadloze integratie met toonaangevende betalingsgateways zoals Stripe en PayPal voor veilige transacties.
* **Kortingscodes**: Ondersteuning voor het aanmaken en toepassen van kortingscodes op ticketbestellingen.

### Statistieken en Bezoekersanalyse
* **Verkochte Tickets**: Gedetailleerde rapportage van het aantal verkochte tickets per evenement.
* **Inkomstenrapporten**: Overzichtelijke inkomstenrapporten per evenement.
* **Bezoekersdemografie**: Inzichten in de demografische gegevens van bezoekers.

### Gebruikersrollen & Toegangsbeheer
* **Organisator Dashboard**: Elke organisator krijgt een gepersonaliseerd dashboard om hun venues, evenementen, tickettypes, kortingscodes en statistieken te beheren.
* **Subdomein/Custom Domein per Evenement**: Elk evenement kan worden gekoppeld aan een eigen subdomein of custom domein, wat zorgt voor een gepersonaliseerde evenementervaring.
* **Superadmin Functionaliteit**: Een superadmin heeft de mogelijkheid om meerdere organisatoren te registreren, beheren en te overzien.

## Extra Features

* **CRUD-functionaliteit voor Venues:** Volledige Create, Read, Update, Delete (CRUD) functionaliteit voor venues. Tijdens het aanmaken van een evenement kan je een venue selecteren.
* **Personalisatie organisatie:** De homepagina van een organisatie kan gepersonaliseerd worden.
* **Personalisatie evenement:** De pagina van een evenement kan gepersonaliseerd worden.
* **Superadmin log in als:** De superadmin kan zich inloggen als elke gebruiker.
* **Error handling:** Gebruikers worden doorgeleid naar een gepaste pagina als er een fout opkomt.
* **Permissions:** The system incorporates robust access control through permissions.
* **Force HTTPS in production:** In a production environment, all requests are forced to use HTTPS.
* **Automated Publishing:** Events and ticket types are automatically published based on their predefined schedules, including options for event-dependent ticket type publishing.


## Beperkingen
* **Geen wachtrij**: Er is geen ingebouwde wachtrijfunctionaliteit voor de aankoop van tickets, wat bij een plotselinge grote vraag tot prestatieproblemen kan leiden.
* **Beperkte frontend caching**: Het systeem maakt momenteel geen gebruik van uitgebreide frontend caching. Dit betekent dat bij een zeer hoog aantal gelijktijdige gebruikers die tickets proberen te kopen, de website mogelijk trager wordt of zelfs onbereikbaar wordt door de hoge belasting op de server.
* **Geen refund mogelijkheid**: Er is geen functionaliteit ingebouwd voor het verwerken van terugbetalingen van gekochte tickets.
* **Beperkt order- en ticketmanagement**: Het systeem biedt een basisweergave voor orders, maar mist geavanceerde beheerfunctionaliteiten zoals het wijzigen, annuleren, opnieuw verzenden of overdragen van individuele tickets of hele bestellingen door organisatoren.
* **Evenementen zijn één dag**: Evenementen kunnen slechts op één specifieke datum en tijd worden ingesteld; ondersteuning voor meerdaagse evenementen of evenementen met meerdere tijdslots op verschillende dagen is niet aanwezig.
* **Geen kaartpinning voor locaties**: Bij het aanmaken van een locatie (venue) kunnen coördinaten handmatig worden ingevoerd, maar er is geen interactieve kaartintegratie om de locatie visueel te pinnen.

## Technologie Stack

Dit project is gebouwd met behulp van de volgende technologieën en belangrijke Laravel-pakketten:

* **Laravel**: PHP-framework voor backend-ontwikkeling.
* **Livewire**: Een full-stack framework voor Laravel dat het bouwen van dynamische interfaces vereenvoudigt zonder dat JavaScript nodig is.
* **Volt**: Een nieuw en sneller alternatief voor het creëren van Livewire componenten met behulp van de Volt syntax.
* **Tailwind CSS**: Een modern CSS-framework voor front-end ontwikkeling.
* **Alpine.js**: Een modern JavaScript-framework voor front-end ontwikkeling.
* **Spatie/Laravel-permission**: Voor het beheren van gebruikersrollen en -permissies.
* **Asantibanez/Livewire-charts**: Voor het genereren van evenementstatistieken en inkomstenrapporten.
* **Dasundev/Livewire-dropzone**: Voor het uploaden van media (bijv. evenementafbeeldingen, organisatielogo's).
* **Stripe/stripe-php**: Officiële PHP-bibliotheek voor Stripe-integratie.
* **Simplesoftwareio/simple-qrcode**: Voor het genereren van QR-codes voor tickets.
* **Spatie/Laravel-pdf**: Voor het genereren van downloadbare tickets (PDF's).
* **mebjas/html5-qrcode**: Voor het scannen van QR-codes via de webbrowser.

## Database Schema Overzicht

Het systeem maakt gebruik van een relationele database met de volgende belangrijke tabellen:

* `users`: Gebruikers van het systeem, inclusief superadmins en organisatoren.
* `organizations`: Organisaties die evenementen organiseren.
* `venues`: Locaties waar evenementen plaatsvinden.
* `events`: Evenementdetails, inclusief koppelingen naar organisaties en locaties.
* `ticket_types`: Verschillende soorten tickets die beschikbaar zijn per evenement.
* `tickets`: Individuele tickets, gekoppeld aan tickettypes en bestellingen. Bevat ook `qr_code`, `scanned_at` en `scanned_by` velden.
* `discount_codes`: Informatie over kortingscodes.
* `temporary_orders`: Tijdelijke bestellingen voor het checkout-proces.
* `orders`: Bestellingen geplaatst door klanten.
* `discount_code_order`: Koppelt kortingscodes aan bestellingen.
* `customers`: Klantinformatie voor bestellingen.

## Installatie en Setup

Volg de onderstaande stappen om het project lokaal in te stellen:

1.  **Kloon de repository:**
    ```bash
    git clone https://github.com/RenautMestdagh/easytix
    cd easytix
    ```

2.  **Installeer Composer-afhankelijkheden:**
    ```bash
    composer install
    ```

3.  **Kopieer de `.env.example` naar `.env` en configureer je omgevingsvariabelen:**
    ```bash
    cp .env.example .env
    ```
    Bewerk het `.env` bestand en vul je databasegegevens en andere benodigde configuraties in (bijv. App-domain, Mail credentials, Stripe API-sleutels, Node en NPM path). Zorg ervoor dat `APP_DOMAIN` correct is ingesteld voor subdomein routing.
    `NODE_PATH` en `NPM_PATH` zijn noodzakelijk voor het downloaden van tickets.


4.  **Genereer een applicatiesleutel:**
    ```bash
    php artisan key:generate
    ```

5.  **Migreer de database en zaai de data (indien aanwezig):**
    ```bash
    php artisan migrate:fresh --seed
    ```
    When seeding the database, the following fixed data is inserted:

    **Organizations:**
     * "Kompass Klub" with the subdomain "kompass"
     * "Modul'air" with the subdomain "modulair"
       In addition to these, 5 random organizations are also created.

    **Users:**
     * A superadmin user is created with the following credentials:
       * Name: Renaut Mestdagh
       * Email: renaut.mestdagh+superadmin@hotmail.com
       * Password: `123456789`
     * For each organization, the following users are created and roles are assigned:
       * One admin user with the email format "renaut.mestdagh+admin{OrgId}@hotmail.com" and password `123456789`.
       * Between 1 and 3 additional random admin users.
       * One organizer user with the email format "renaut.mestdagh+organizer{OrgId}@hotmail.com" and password `123456789`.
       * Between 0 and 3 additional random organizer users.

    **Events:**
    * For each organization, between 2 and 20 events are created.
    * The first two events created for each organization will have predefined subdomains: "event1" and "event2".


6.  **Installeer NPM-afhankelijkheden en compileer assets:**
    ```bash
    npm install
    npm run dev
    ```
    Of voor productie:
    ```bash
    npm run build
    ```

7.  **Laravel Herd Setup voor Lokale Ontwikkeling:**

    Voor een eenvoudige lokale ontwikkelingsomgeving raad ik aan Laravel Herd te gebruiken. Herd is een bliksemsnelle, lichte en efficiënte Laravel-ontwikkelomgeving voor macOS en Windows. Het omvat PHP, Nginx, DnsMasq, en meer, alles voorgeconfigureerd.

    * **Installatie**: Download en installeer Laravel Herd via de [officiële website](https://herd.laravel.com/).

    * **Project Toevoegen (Linken)**:
      Nadat je Herd hebt geïnstalleerd en opgestart, open je de Herd-applicatie.
      Klik op de knop `Add Site` of sleep eenvoudigweg je projectmap (`[JOUW_PROJECT_MAP]`) naar het Herd-venster. Herd zal automatisch een lokale URL aanmaken (bijv. `jouwproject.test`).

    * **HTTPS (Beveiligen)**:
      Om je lokale site via HTTPS te benaderen, klik je in Herd op het slotje naast de sitenaam. Herd zal dan automatisch een SSL-certificaat genereren en installeren voor je lokale domein.

    * **Aliassen Toevoegen (voor Subdomeinen)**:
      Dit project maakt uitgebreid gebruik van subdomeinen. Om deze lokaal te laten werken met Herd, moet je aliases instellen.
      Ga in Herd naar de instellingen van je site (klik op de sitenaam in de lijst). In het tabblad `Domains` kun je extra domeinen toevoegen. Het is niet mogelijk om wildcards te gebruiken. Ieder subdomein moet dus exact worden ingesteld.
      Voeg hier aliasen toe die je lokaal beschikbaar wilt maken (vergeet niet terug op het slotje te klikken zodat https wordt gebruikt).

      
8.  **Databasebeheer met DBngin:**

    Voor eenvoudig lokaal databasebeheer raden we DBngin aan. DBngin is een gratis en krachtige tool voor macOS en Windows waarmee je snel verschillende versies van databaseservers (zoals MySQL, PostgreSQL, Redis) kunt opzetten en beheren, zonder conflicten.

    * **Installatie**: Download en installeer DBngin via de [officiële website](https://dbngin.com/).

    * **Database Server Aanmaken**:
      Open DBngin en klik op de `+` knop om een nieuwe database server aan te maken.
      Kies het type database dat je project gebruikt (bijvoorbeeld MySQL). Zorg ervoor dat de versie overeenkomt met de vereisten van je Laravel-project.
      Start de server.

    * **Database Aanmaken**:
      Zodra je database server draait, moet je een database aanmaken voor je project. Dit kun je doen via een database management tool zoals TablePlus, DBeaver, MySQL Workbench of PhpMyAdmin, of via de command line.
      Maak een database aan met de naam die je in je `.env` bestand hebt geconfigureerd (bijv. `DB_DATABASE=your_project_db`).

    * **Configuratie in `.env`**:
      Zorg ervoor dat je `.env` bestand de juiste databaseconnectiegegevens bevat die overeenkomen met je DBngin-setup (host, poort, database naam, gebruikersnaam, wachtwoord). Bijvoorbeeld:

        ```dotenv
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306 # Of de poort die DBngin toewijst
        DB_DATABASE=your_project_db
        DB_USERNAME=root # Of de gebruikersnaam die je hebt ingesteld
        DB_PASSWORD= # Of het wachtwoord dat je hebt ingesteld
        ```

9.  **Start de Queue Worker:**

    Laravel-projecten maken vaak gebruik van queues voor taken die op de achtergrond moeten worden uitgevoerd (bijvoorbeeld het versturen van e-mails). Om deze taken te verwerken, moet de queue worker actief zijn.

    Open een nieuwe terminal en navigeer naar de root van je projectmap. Voer vervolgens het volgende commando uit:

    ```bash
    php artisan queue:work
    ```

    Dit commando zal de queue worker starten en luisteren naar nieuwe taken. Laat dit terminalvenster open zolang je lokaal aan het ontwikkelen bent en je wilt dat queued taken worden verwerkt. Voor productieomgevingen zou je een procesmanager zoals Supervisor gebruiken om de queue worker permanent te laten draaien.


## Gebruik

* Navigeer naar de hoofd-URL (bijv. `https://easytix.test`) voor de welkomstpagina.
* Log in met een superadmin-account om organisaties te beheren (bijv. `https://easytix.test/login`).
* Log in met een organisator-account om evenementen, tickettypes, kortingscodes en statistieken te beheren via het dashboard (bijv. `https://kompass.easytix.test/login`).
* De homepaginas van de organisatoren worden toegankelijk via hun eigen subdomein (bijv. `https://kompass.easytix.test`).
* Evenementen zijn toegankelijk via hun subdomeinen (bijv. `https://evenementnaam.kompass.easytix.test` of `https://kompass.easytix.test/event/{eventuniqid}`).

## Live Demo

Een live versie van dit project is beschikbaar op: [https://easytix.duckdns.org/](https://easytix.duckdns.org/)

## Licentie

[Voeg hier de licentie-informatie toe, bijv. MIT License]
