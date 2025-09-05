# E-Invoicing Frontend

Frontend aplikasi E-Invoicing Management System yang dibangun dengan Vue 3, TypeScript, dan Tailwind CSS.

## Tech Stack

- **Framework:** Vue 3 dengan Composition API dan `<script setup>`
- **Language:** TypeScript
- **Build Tool:** Vite
- **Styling:** Tailwind CSS
- **UI Components:** Headless UI + Heroicons
- **HTTP Client:** Axios
- **Routing:** Vue Router 4

## Struktur Proyek

```
src/
├── assets/
│   └── main.css          # Tailwind CSS dan custom styles
├── components/
│   └── AppLayout.vue     # Layout utama dengan sidebar navigation
├── views/
│   ├── Dashboard.vue     # Halaman dashboard dengan statistik
│   ├── LoginView.vue     # Halaman login
│   ├── EmployeesView.vue # Manajemen karyawan
│   ├── ClientsView.vue   # Manajemen klien
│   ├── PlacementsView.vue# Manajemen penempatan
│   └── InvoicesView.vue  # Manajemen invoice
├── services/
│   └── api.ts           # Axios configuration dan API functions
├── router/
│   └── index.ts         # Vue Router configuration
├── App.vue              # Root component
└── main.ts              # Entry point
```

## Fitur

### 🔐 Authentication
- Login dengan email dan password
- Token-based authentication (JWT)
- Auto logout saat token expired
- Route protection

### 📊 Dashboard
- Statistik real-time (employees, clients, placements, invoices)
- Quick actions untuk navigasi cepat
- Loading states dan error handling

### 👥 Employee Management
- Tabel daftar karyawan dengan search
- CRUD operations (Create, Read, Update, Delete)
- Responsive table design

### 🏢 Client Management
- Manajemen data klien
- Search dan filter functionality
- Modern table interface

### 📍 Placement Management
- Tracking penempatan karyawan
- Status management (Active, Inactive, Pending)
- Relasi dengan employee dan client data

### 🧾 Invoice Management
- Manajemen invoice dan billing
- Format currency Indonesia (IDR)
- Status tracking (Paid, Unpaid, Pending, Overdue)

## Setup dan Installation

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Setup environment variables:**
   Buat file `.env.local`:
   ```
   VITE_API_BASE_URL=http://localhost:8000/api
   ```

3. **Start development server:**
   ```bash
   npm run dev
   ```

4. **Build for production:**
   ```bash
   npm run build
   ```

## API Integration

Aplikasi ini terintegrasi dengan Laravel backend API dengan endpoints:

### Authentication
- `POST /auth/login` - Login user
- `POST /auth/logout` - Logout user
- `GET /auth/me` - Get current user info

### Resources (Protected)
- `GET /employees` - List employees
- `GET /clients` - List clients  
- `GET /placements` - List placements
- `GET /invoices` - List invoices
- CRUD operations untuk semua resource

## Design System

### Colors
- **Primary:** Blue (blue-600, blue-700)
- **Success:** Green (green-100, green-800)
- **Warning:** Yellow (yellow-100, yellow-800)
- **Error:** Red (red-100, red-800)
- **Neutral:** Gray shades

### Components
- **Buttons:** Primary, Secondary dengan hover states
- **Cards:** White background dengan subtle shadow
- **Tables:** Responsive dengan hover effects
- **Forms:** Clean input styling dengan focus states

## Demo Credentials

```
Email: rikonusapratama@gmail.com
Password: password
```

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge

## Performance Features

- Lazy loading untuk routes
- Optimized bundle dengan Vite
- Tree shaking untuk unused code
- Modern CSS dengan Tailwind

## Development Notes

- Menggunakan TypeScript untuk type safety
- Responsive design untuk mobile dan desktop
- Loading states untuk better UX
- Error handling yang comprehensive
- Modern Vue 3 patterns dengan Composition API