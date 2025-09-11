# E-Invoicing Management System

A comprehensive backend API application designed for managing invoices, employees, clients, and placements. The system is built with a Laravel API backend.

## 🚀 Features

- **Authentication:** JWT-based user authentication (registration, login, logout, token refresh).
- **Employee Management:** CRUD operations for employee profiles.
- **Client Management:** Track client information, company details, and contacts.
- **Placement Management:** Manage employee placements, client-employee relationships, and placement status.
- **Invoice Management:** Create and manage invoices, track status (Paid, Unpaid, Overdue).

## 📁 Project Structure

This project is a Laravel backend API application.

```
e-invoicing/
├── app/                  # Laravel application code
├── routes/               # API routes
├── database/             # Database migrations and seeders
├── config/               # Laravel configuration
├── .env                  # Backend environment variables
└── docker-compose.yml    # Docker setup for the entire application
```

## 🛠️ Tech Stack

### Backend (Laravel API)
- **Framework:** Laravel 8.x
- **Language:** PHP 8.x
- **Database:** MySQL
- **Authentication:** JWT (JSON Web Tokens)
- **API:** RESTful API

## 🐳 Docker Setup

This project can be easily set up and run using Docker and Docker Compose. This setup includes the Laravel application (PHP-FPM), Nginx (web server), and MySQL database.

### Prerequisites
- Docker
- Docker Compose

### Installation and Running

This project is fully containerized with Docker. A simple installation script is provided to automate the entire setup process.

1.  **Clone the repository (if you haven't already):**
    ```bash
    git clone <repository-url>
    cd e-invoicing-api
    ```

2.  **Run the installation script:**
    First, make the script executable:
    ```bash
    chmod +x install.sh
    ```
    Then, run the script:
    ```bash
    ./install.sh
    ```

The script will guide you through the installation and automate the following steps:
- Building and starting the Docker containers.
- Installing Composer dependencies.
- Creating the `.env` file from `.env.example`.
- Generating the application key (`APP_KEY`).
- Generating the JWT secret key (`JWT_SECRET`).
- Waiting for the database container to be ready.
- Creating the database.
- Running database migrations and seeders.
- Creating the storage symlink.

### Accessing the Application

-   **API:** The API is available at `http://localhost:8080`. Endpoints are prefixed with `/api`. For example, the login endpoint is a POST request to `http://localhost:8080/api/auth/login`.
-   **Database:** The MySQL database is accessible from your host machine on `localhost:3032`.

## ⚙️ Configuration

### Environment Variables (`.env`)

After copying `.env.example` to `.env`, configure your application's environment variables. Key database settings are:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=e_invoicing
DB_USERNAME=root
DB_PASSWORD=rootpass
```

*Note: `DB_HOST` is set to `mysql` because that is the service name of the database container within the Docker network.* `DB_PASSWORD` should match the `MYSQL_ROOT_PASSWORD` set in `docker-compose.yml`.

## 🚀 Development

### Running the Application (without Docker for backend)

If you prefer to run the Laravel backend directly on your host machine:

1.  **Start the Laravel backend:**
    ```bash
    php artisan serve
    ```
    The API will be available at `http://localhost:8000`.

### Database Seeding

To run database seeding, execute the following command inside the `app` Docker container:
```bash
docker-compose exec app php artisan db:seed
```

### Testing

To run backend tests, execute the following command inside the `app` Docker container:
```bash
docker-compose exec app php artisan test
```

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1.  Fork the repository.
2.  Create a new feature branch (`git checkout -b feature/YourFeatureName`).
3.  Make your changes.
4.  Add tests for your changes if applicable.
5.  Commit your changes (`git commit -m 'Add new feature'`).
6.  Push to the branch (`git push origin feature/YourFeatureName`).
7.  Open a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### Running Migrations One by One

This project provides a script to run migration files one by one in sequence. This can be useful for debugging or if you need more control over the migration process.

To use this script, run the following command from your terminal:

```bash
./run_migrations.sh
```

**Note:** This is not the standard way to run migrations in Laravel. The standard command is `php artisan migrate`. Use this script only if you have a specific need to run migrations individually.
