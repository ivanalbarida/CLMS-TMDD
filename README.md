# Computer Lab Management System (CLMS)

## About This Project

This is a comprehensive web application designed to manage the inventory, maintenance, and service requests for a university's computer laboratories and departments. It was built with the Laravel framework to provide a centralized platform for tracking all hardware components, logging maintenance, and managing a formal service request workflow.

This project was developed as a collaboration between the user and a large language model, demonstrating a modern approach to software development, problem-solving, and iterative feature implementation.

### Key Features
-   **Role-Based Access Control:** Differentiated permissions for **Admin**, **Technician**, and **Custodian** roles, ensuring users only see and interact with what is relevant to their job.
-   **Inventory Management:** Full CRUD (Create, Read, Update, Delete) for labs and equipment, including detailed component tracking and a robust bulk CSV import feature.
-   **Advanced Maintenance Logging:**
    -   Separate workflows for **Corrective** (break-fix) and **Preventive** (scheduled) maintenance.
    -   An interactive, date-aware checklist for daily, weekly, monthly, and quarterly preventive tasks that tracks completions.
    -   Ability to log a single maintenance record against multiple pieces of equipment.
-   **Service Request Module:** A full-featured ticketing system that digitizes the official Service Report Form, allowing users to request new equipment, repairs, or condemnations, with a multi-stage approval and verification workflow.
-   **Comprehensive Audit Trail:** A centralized activity log tracks all major actions (creations, updates, completions) performed by users, providing a full history for any piece of equipment, lab, or user.
-   **Dynamic Reporting:** A filterable reporting engine that can generate maintenance reports for specific date ranges, types, and statuses, with a "Print to PDF" and "Export to CSV" functionality.
-   **Custom Branding:** The application is fully branded with the university's logo and name.

---

## Deployment Guide (Windows & XAMPP)

This guide provides the step-by-step instructions for deploying the CLMS application on a dedicated Windows server using XAMPP.

### 1. Server Environment Setup

**Prerequisites:**
-   **XAMPP:** Install a version that includes **PHP 8.2**. [Download XAMPP](https://www.apachefriends.org/index.html)
-   **Git:** Install Git for version control. [Download Git](https://git-scm.com/downloads)
-   **Composer:** Install Composer, the PHP package manager. [Download Composer](https://getcomposer.org/download/)
-   **Node.js & npm:** Install the LTS version of Node.js. [Download Node.js](https://nodejs.org/en/)

**Initial Server Configuration:**
1.  **Static IP:** Ensure the server has a static local IP address (e.g., `192.168.5.47`). This is configured in your network router or by the IT department.
2.  **Stop Conflicting Services:** Use `services.msc` to find and disable any other Apache or web server services to free up Port 80.
3.  **Start XAMPP:** Open the XAMPP Control Panel and start the **Apache** and **MySQL** modules.
4.  **Firewall Rule:** Create an Inbound Rule in Windows Defender Firewall to allow traffic on **TCP Port 80**.

### 2. Project Deployment & Setup

1.  **Clone the Repository:**
    -   Open a terminal (Git Bash is recommended) and navigate to the XAMPP webroot:
        ```bash
        cd C:/xampp/htdocs
        ```
    -   Clone the project:
        ```bash
        git clone https://github.com/ivanalbarida/CLMS-TMDD
        ```

2.  **Install Dependencies:**
    -   Navigate into the project folder: `cd clms`
    -   Install dependencies:
        ```bash
        composer install --optimize-autoloader --no-dev
        npm install
        ```

3.  **Configure Environment (`.env`) File:**
    -   Create the `.env` file: `cp .env.example .env`
    -   Generate a unique application key: `php artisan key:generate`
    -   Open `.env` and update the following variables:
        ```env
        APP_NAME="SLU CLMS"
        APP_ENV=production
        APP_DEBUG=false
        APP_URL=http://<your-server-ip-address>

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=clms_db
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Set Up the Database:**
    -   Using phpMyAdmin (`http://localhost/phpmyadmin/`), create a new, empty database named `clms_db`.
    -   In your terminal, run the migrations and seeders to create all tables and the initial admin account:
        ```bash
        php artisan migrate:fresh --seed
        ```

### 3. Build Frontend Assets

Compile the CSS and JavaScript for production. This is a one-time step.
```bash
npm run build

### 4. Configure Apache Web Server

1.  **Enable Virtual Hosts:**
    -   Open the main Apache configuration file: `C:/xampp/apache/conf/httpd.conf`.
    -   Find and uncomment the following line by removing the `#` at the beginning:
        ```apache
        Include conf/extra/httpd-vhosts.conf
        ```

2.  **Create the Virtual Host:**
    -   Open the virtual hosts configuration file: `C:/xampp/apache/conf/extra/httpd-vhosts.conf`.
    -   Add the following block to the end of the file. **Replace `<your-server-ip-address>` with the server's actual static IP** and ensure the `DocumentRoot` path matches your project's `public` folder.

    ```apache
    <VirtualHost *:80>
        ServerName <your-server-ip-address>
        DocumentRoot "C:/xampp/htdocs/clms/public"
        <Directory "C:/xampp/htdocs/clms/public">
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>
    ```

3.  **Restart Apache:**
    -   Go to the XAMPP Control Panel.
    -   Click **"Stop"** on the Apache module.
    -   Once it has stopped, click **"Start"** again.

### 5. Accessing the Application

The server is now fully configured and live. Any user on the same network can access the application by typing the server's static IP address into their web browser:

`http://<your-server-ip-address>`

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).