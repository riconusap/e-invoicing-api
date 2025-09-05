# E-Invoicing Management System

A full-stack web application for managing invoices, employees, clients, and placements built with Laravel (backend) and Vue.js (frontend).

## 📁 Project Structure

```
e-invoicing/
├── frontend/              # Vue.js frontend application
│   ├── src/              # Vue.js source code
│   ├── package.json      # Frontend dependencies
│   ├── .env.local        # Frontend environment variables
│   └── README.md         # Frontend documentation
├── app/                  # Laravel application code
├── routes/               # API routes
├── database/             # Database migrations and seeders
├── config/               # Laravel configuration
├── .env                  # Backend environment variables
├── composer.json         # Backend dependencies
└── README.md            # This file (backend documentation)
```

## 🚀 Tech Stack

### Backend (Laravel API)
- **Framework:** Laravel 8.x
- **Language:** PHP 8.x
- **Database:** MySQL
- **Authentication:** JWT (JSON Web Tokens)
- **API:** RESTful API with resource controllers

### Frontend (Vue.js SPA)
- **Framework:** Vue 3 with Composition API
- **Language:** TypeScript
- **Build Tool:** Vite
- **Styling:** Tailwind CSS
- **State Management:** Pinia
- **UI Components:** Headless UI + Heroicons

## 🛠️ Backend Setup (Laravel API)

### Prerequisites
- PHP 8.0 or higher
- Composer
- MySQL database
- Node.js (for frontend)

### Installation

1. **Install PHP dependencies:**
   ```bash
   composer install
   ```

2. **Environment setup:**
   ```bash
   cp .env.example .env
   ```
   
   Configure your database and other settings in `.env`:
   ```env
   DB_DATABASE=e_invoicing
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
   JWT_SECRET=your-jwt-secret-key
   FRONTEND_URL=http://localhost:3000
   ```

3. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

4. **Run database migrations:**
   ```bash
   php artisan migrate
   ```

5. **Start the development server:**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`

## 📚 API Documentation

### Authentication Endpoints
```
POST /api/auth/login          # User login
POST /api/auth/register       # User registration
POST /api/auth/logout         # User logout
GET  /api/auth/me            # Get current user
POST /api/auth/refresh       # Refresh JWT token
```

### Resource Endpoints (Protected)
```
# Employees
GET    /api/employees         # List employees
POST   /api/employees         # Create employee
GET    /api/employees/{id}    # Get employee
PUT    /api/employees/{id}    # Update employee
DELETE /api/employees/{id}    # Delete employee

# Clients
GET    /api/clients           # List clients
POST   /api/clients           # Create client
GET    /api/clients/{id}      # Get client
PUT    /api/clients/{id}      # Update client
DELETE /api/clients/{id}      # Delete client

# Placements
GET    /api/placements        # List placements
POST   /api/placements        # Create placement
GET    /api/placements/{id}   # Get placement
PUT    /api/placements/{id}   # Update placement
DELETE /api/placements/{id}   # Delete placement

# Invoices
GET    /api/invoices          # List invoices
POST   /api/invoices          # Create invoice
GET    /api/invoices/{id}     # Get invoice
PUT    /api/invoices/{id}     # Update invoice
DELETE /api/invoices/{id}     # Delete invoice
```

## 🚀 Frontend Setup

The frontend is a separate Vue.js application located in the `frontend/` directory.

**Quick start:**
```bash
cd frontend
npm install
cp .env.example .env.local
npm run dev
```

For detailed frontend setup instructions, see [frontend/README.md](frontend/README.md)

## 🔧 Development

### Running Both Applications

1. **Start the Laravel backend:**
   ```bash
   php artisan serve
   ```

2. **Start the Vue.js frontend:**
   ```bash
   cd frontend
   npm run dev
   ```

3. **Access the applications:**
   - Backend API: http://localhost:8000
   - Frontend App: http://localhost:5173

### Database Seeding

```bash
# Create and run seeders
php artisan db:seed
```

### Testing

```bash
# Run backend tests
php artisan test

# Run frontend tests
cd frontend
npm run test
```

## 🌟 Features

### 🔐 Authentication
- JWT-based authentication
- User registration and login
- Protected routes
- Token refresh mechanism

### 👥 Employee Management
- CRUD operations for employees
- Employee profile management
- Search and filtering

### 🏢 Client Management
- Client information management
- Company details tracking
- Contact management

### 📍 Placement Management
- Employee placement tracking
- Client-employee relationships
- Placement status management

### 🧾 Invoice Management
- Invoice creation and management
- Status tracking (Paid, Unpaid, Overdue)
- PDF generation (future feature)
- Payment tracking

## 📝 Environment Variables

### Backend (.env)
```env
# Application
APP_NAME="E-Invoicing API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_invoicing
DB_USERNAME=root
DB_PASSWORD=

# JWT
JWT_SECRET=your-jwt-secret-key
JWT_TTL=60

# CORS
FRONTEND_URL=http://localhost:3000
```

### Frontend (.env.local)
```env
# API Configuration
VITE_API_BASE_URL=http://localhost:8000/api
VITE_BACKEND_URL=http://localhost:8000

# Application
VITE_APP_NAME="E-Invoicing Frontend"
```

## 🚀 Deployment

### Backend Deployment
1. Configure production environment variables
2. Run migrations: `php artisan migrate --force`
3. Clear and cache config: `php artisan config:cache`
4. Set up web server (Nginx/Apache)

### Frontend Deployment
1. Build for production: `npm run build`
2. Deploy `dist/` folder to static hosting
3. Configure environment variables for production API

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
