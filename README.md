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

1.  **Build and run the Docker containers:**
    ```bash
    docker-compose up --build -d
    ```
    This command will:
    -   Build the Laravel application image.
    -   Start the Nginx web server.
    -   Start the MySQL database.

2.  **Access the Application:**
    -   The Laravel application serves as a backend API. Direct access to the root URL (`http://localhost:8080`) will result in a `404 Not Found` error as there are no web routes defined.
    -   API endpoints are accessible under the `/api` prefix. For example, you can access the authentication login endpoint via `http://localhost:8080/api/auth/login` (POST request).
    -   The MySQL database is accessible on port `3032` from your host machine (`localhost:3032`).

3.  **Run Laravel Migrations and Seeders:**
    Once the containers are running, execute the following command to set up your database schema and seed initial data:
    ```bash
    docker-compose exec app php artisan migrate --seed
    ```

4.  **Generate Application Key:**
    Generate a unique application key for Laravel:
    ```bash
    docker-compose exec app php artisan key:generate
    ```
5. **Symlink Setup:**
     ```bash
    docker-compose exec app php artisan storage:link
    ```

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
