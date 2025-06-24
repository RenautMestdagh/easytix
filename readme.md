# Easytix - Event Ticketing Service

This project is a robust ticketing system designed to streamline event management, ticket sales, payment processing, and visitor analytics. The system provides organizers with a dedicated dashboard for efficient management of their events and the ability to host events under their own (sub)domain.

---

## Functional Requirements

### Event Management
* **CRUD Functionality for Events**: Full Create, Read, Update, Delete (CRUD) functionality for events, including details such as name, location, date, and availability.
* **Various Ticket Types**: Ability to create diverse ticket types per event, such as VIP, standard, and early bird tickets.

### Ticket Sales & QR Code Generation
* **Ticket Purchase**: Customers can easily purchase tickets via an intuitive frontend.
* **Confirmation Email**: A confirmation email is sent to the customer after a ticket purchase.
* **QR Code Generation**: After purchase, customers receive a unique QR code for each ticket.
* **QR Code Scanning**: Efficient scanning functionality for QR codes to grant access at events.

### Payment Functionality
* **Stripe/PayPal Integration**: Seamless integration with leading payment gateways like Stripe and PayPal for secure transactions.
* **Discount Codes**: Support for creating and applying discount codes to ticket orders.

### Statistics and Visitor Analytics
* **Tickets Sold**: Detailed reporting of the number of tickets sold per event.
* **Revenue Reports**: Clear revenue reports per event.
* **Visitor Demographics**: Insights into the demographic data of visitors.

### User Roles & Access Management
* **Organizer Dashboard**: Each organizer gets a personalized dashboard to manage their venues, events, ticket types, discount codes, and statistics.
* **Subdomain/Custom Domain per Event**: Each event can be linked to its own subdomain or custom domain, providing a personalized event experience.
* **Superadmin Functionality**: A superadmin has the ability to register, manage, and oversee multiple organizers.

---

## Extra Features

* **CRUD Functionality for Venues**: Full Create, Read, Update, Delete (CRUD) functionality for venues. You can select a venue when creating an event.
* **Organization Personalization**: The homepage of an organization can be personalized.
* **Event Personalization**: The page of an event can be personalized.
* **Superadmin Login As**: The superadmin can log in as any user.
* **Error Handling**: Users are redirected to an appropriate page if an error occurs.
* **Permissions**: The system incorporates robust access control through permissions.
* **Force HTTPS in production**: In a production environment, all requests are forced to use HTTPS.
* **Automated Publishing**: Events and ticket types are automatically published based on their predefined schedules, including options for event-dependent ticket type publishing.

---

## Limitations

* **No Queue System**: There is no built-in queue functionality for ticket purchases, which can lead to performance issues during a sudden surge in demand.
* **Limited Frontend Caching**: The system currently does not utilize extensive frontend caching. This means that with a very high number of concurrent users trying to buy tickets, the website may slow down or even become unreachable due to high server load.
* **No Refund Option**: There is no built-in functionality for processing refunds for purchased tickets.
* **Limited Order and Ticket Management**: The system provides a basic view for orders but lacks advanced management functionalities such as modifying, canceling, resending, or transferring individual tickets or entire orders by organizers.
* **Events are One Day**: Events can only be set for one specific date and time; support for multi-day events or events with multiple time slots on different days is not present.
* **No Map Pinning for Locations**: When creating a location (venue), coordinates can be entered manually, but there is no interactive map integration to visually pin the location.
* **Single Language, Currency, and Timezone**: The system currently supports only one language and one currency. Event publishing is tied to the server's timezone, without options for specific event timezones.

---

## Technology Stack

This project is built using the following technologies and key Laravel packages:

* **Laravel**: PHP framework for backend development.
* **Livewire**: A full-stack framework for Laravel that simplifies building dynamic interfaces without needing JavaScript.
* **Volt**: A new and faster alternative for creating Livewire components with using the Volt syntax.
* **Tailwind CSS**: A modern CSS framework for front-end development.
* **Alpine.js**: A modern JavaScript framework for front-end development.
* **Spatie/Laravel-permission**: For managing user roles and permissions.
* **Asantibanez/Livewire-charts**: For generating event statistics and revenue reports.
* **Dasundev/Livewire-dropzone**: For uploading media (e.g., event images, organization logos).
* **Stripe/stripe-php**: Official PHP library for Stripe integration.
* **Simplesoftwareio/simple-qrcode**: For generating QR codes for tickets.
* **Spatie/Laravel-pdf**: For generating downloadable tickets (PDFs).
* **mebjas/html5-qrcode**: For scanning QR codes via the web browser.

---

## Database Schema Overview

The system uses a relational database with the following main tables:

* `users`: System users, including superadmins and organizers.
* `organizations`: Organizations that host events.
* `venues`: Locations where events take place.
* `events`: Event details, including links to organizations and locations.
* `ticket_types`: Different types of tickets available per event.
* `tickets`: Individual tickets, linked to ticket types and orders. Also includes `qr_code`, `scanned_at`, and `scanned_by` fields.
* `discount_codes`: Information about discount codes.
* `temporary_orders`: Temporary orders for the checkout process.
* `orders`: Orders placed by customers.
* `discount_code_order`: Links discount codes to orders.
* `customers`: Customer information for orders.

---

## Installation and Setup

Follow the steps below to set up the project locally:

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/RenautMestdagh/easytix](https://github.com/RenautMestdagh/easytix)
    cd easytix
    ```

2.  **Install Composer dependencies:**
    ```bash
    composer install
    ```

3.  **Copy `.env.example` to `.env` and configure your environment variables:**
    ```bash
    cp .env.example .env
    ```
    Edit the `.env` file and fill in your database credentials and other necessary configurations (e.g., App-domain, Mail credentials, Stripe API keys, Node and NPM path). Ensure `APP_DOMAIN` is correctly set for subdomain routing.
    `NODE_PATH` and `NPM_PATH` are necessary for downloading tickets.

4.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```

5.  **Migrate the database and seed the data:**
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
        * Email: `renaut.mestdagh+superadmin@hotmail.com`
        * Password: `123456789`
    * For each organization, the following users are created and roles are assigned:
        * One admin user with the email format `renaut.mestdagh+admin{OrgId}@hotmail.com` and password `123456789`.
        * Between 1 and 3 additional random admin users.
        * One organizer user with the email format `renaut.mestdagh+organizer{OrgId}@hotmail.com` and password `123456789`.
        * Between 0 and 3 additional random organizer users.

    **Events:**
    * For each organization, between 2 and 20 events are created.
    * The first two events created for each organization will have predefined subdomains: `event1` and `event2`.

6.  **Install NPM dependencies and compile assets:**
    ```bash
    npm install
    npm run dev
    ```
    Or for production:
    ```bash
    npm run build
    ```

7.  **Laravel Herd Setup for Local Development:**

    For an easy local development environment, I recommend using **Laravel Herd**. Herd is a lightning-fast, lightweight, and efficient Laravel development environment for macOS and Windows. It includes PHP, Nginx, DnsMasq, and more, all pre-configured.

    * **Installation**: Download and install Laravel Herd from the [official website](https://herd.laravel.com/).

    * **Add Project (Link)**:
        After installing and launching Herd, open the Herd application. Click the `Add Site` button or simply drag your project folder (`[YOUR_PROJECT_FOLDER]`) into the Herd window. Herd will automatically create a local URL (e.g., `yourproject.test`).

    * **HTTPS (Secure)**:
        To access your local site via HTTPS, click the padlock icon next to the site name in Herd. Herd will then automatically generate and install an SSL certificate for your local domain.

    * **Add Aliases (for Subdomains)**:
        This project extensively uses subdomains. To make them work locally with Herd, you need to set up aliases. Go to your site's settings in Herd (click on the site name in the list). In the `Domains` tab, you can add extra domains. Wildcards are not supported, so each subdomain must be precisely configured. Add aliases here that you want to make available locally (don't forget to click the padlock again to ensure HTTPS is used).

8.  **Database Management with DBngin:**

    For easy local database management, we recommend **DBngin**. DBngin is a free and powerful tool for macOS and Windows that allows you to quickly set up and manage different versions of database servers (such as MySQL, PostgreSQL, Redis) without conflicts.

    * **Installation**: Download and install DBngin from the [official website](https://dbngin.com/).

    * **Create Database Server**:
        Open DBngin and click the `+` button to create a new database server. Choose the type of database your project uses (e.g., MySQL). Ensure the version matches the requirements of your Laravel project. Start the server.

    * **Create Database**:
        Once your database server is running, you need to create a database for your project. You can do this via a database management tool like TablePlus, DBeaver, MySQL Workbench, or PhpMyAdmin, or via the command line. Create a database with the name you configured in your `.env` file (e.g., `DB_DATABASE=your_project_db`).

    * **Configuration in `.env`**:
        Ensure your `.env` file contains the correct database connection details matching your DBngin setup (host, port, database name, username, password). For example:

        ```dotenv
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306 # Or the port DBngin assigns
        DB_DATABASE=your_project_db
        DB_USERNAME=root # Or the username you set
        DB_PASSWORD= # Or the password you set
        ```

9.  **Start the Queue Worker:**

    Laravel projects often use queues for tasks that need to be executed in the background (e.g., sending emails). To process these tasks, the queue worker must be active. Open a new terminal and navigate to the root of your project folder. Then execute the following command:

    ```bash
    php artisan queue:work
    ```

    This command will start the queue worker and listen for new tasks. Keep this terminal window open as long as you are developing locally and want queued tasks to be processed. For production environments, you would use a process manager like Supervisor to keep the queue worker running permanently.

---

## Usage

* Navigate to the main URL (e.g., `https://easytix.test`) for the welcome page.
* Log in with a superadmin account to manage organizations (e.g., `https://easytix.test/login`).
* Log in with an organizer account to manage events, ticket types, discount codes, and statistics via the dashboard (e.g., `https://kompass.easytix.test/login`).
* Organizer homepages are accessible via their own subdomain (e.g., `https://kompass.easytix.test`).
* Events are accessible via their subdomains (e.g., `https://eventname.kompass.easytix.test` or `https://kompass.easytix.test/event/{eventuniqid}`).

---

## Live Demo

A live version of this project is available at: [https://easytix.duckdns.org/](https://easytix.duckdns.org/)
