# E-Invoicing Frontend

Modern Vue.js frontend application for the E-Invoicing Management System.

## 🚀 Tech Stack

- **Framework:** Vue 3 with Composition API and `<script setup>`
- **Language:** TypeScript
- **Build Tool:** Vite
- **Styling:** Tailwind CSS
- **UI Components:** Headless UI + Heroicons
- **HTTP Client:** Axios
- **Routing:** Vue Router 4
- **State Management:** Pinia
- **Package Manager:** npm

## 📁 Project Structure

```
frontend/
├── public/                 # Static assets
├── src/
│   ├── assets/            # Stylesheets and assets
│   │   └── main.css       # Tailwind CSS + custom styles
│   ├── components/        # Reusable Vue components
│   │   └── AppLayout.vue  # Main application layout
│   ├── stores/            # Pinia stores for state management
│   │   ├── auth.ts        # Authentication store
│   │   ├── employees.ts   # Employees store
│   │   ├── clients.ts     # Clients store
│   │   ├── placements.ts  # Placements store
│   │   ├── invoices.ts    # Invoices store
│   │   └── index.ts       # Store exports
│   ├── views/             # Page components
│   │   ├── Dashboard.vue  # Dashboard with statistics
│   │   ├── LoginView.vue  # Authentication page
│   │   ├── EmployeesView.vue
│   │   ├── ClientsView.vue
│   │   ├── PlacementsView.vue
│   │   └── InvoicesView.vue
│   ├── services/          # API services
│   │   └── api.ts         # Axios configuration and endpoints
│   ├── router/            # Vue Router configuration
│   │   └── index.ts       # Routes and navigation guards
│   ├── App.vue            # Root component
│   └── main.ts            # Application entry point
├── .env.local             # Environment variables (local)
├── .env.example           # Environment variables template
├── tailwind.config.js     # Tailwind CSS configuration
├── postcss.config.js      # PostCSS configuration
├── vite.config.ts         # Vite configuration
├── package.json           # Dependencies and scripts
└── README.md              # This file
```

## 🛠️ Development Setup

### Prerequisites

- Node.js (v16 or higher)
- npm (v7 or higher)
- Running Laravel backend API

### Installation

1. **Clone and navigate to frontend directory:**
   ```bash
   cd frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Environment setup:**
   ```bash
   cp .env.example .env.local
   ```
   
   Edit `.env.local` and configure your API endpoints:
   ```env
   VITE_API_BASE_URL=http://localhost:8000/api
   VITE_BACKEND_URL=http://localhost:8000
   ```

4. **Start development server:**
   ```bash
   npm run dev
   ```

5. **Access the application:**
   - Frontend: http://localhost:5173 (or available port)
   - Ensure backend is running on http://localhost:8000

### Available Scripts

```bash
# Development server with hot reload
npm run dev

# Build for production
npm run build

# Preview production build locally
npm run preview

# Type checking
npm run type-check

# Lint code
npm run lint
```

## 🎨 Features

### 🔐 Authentication
- JWT-based authentication
- Auto token refresh
- Route protection
- Persistent login state

### 📊 Dashboard
- Real-time statistics
- Quick action cards
- Responsive design
- Loading states

### 👥 Employee Management
- CRUD operations
- Search and filter
- Responsive table
- Real-time updates

### 🏢 Client Management
- Client information management
- Search functionality
- Company details tracking

### 📍 Placement Management
- Employee placement tracking
- Status management
- Client-employee relationships

### 🧾 Invoice Management
- Invoice creation and management
- Status tracking (Paid, Unpaid, Overdue)
- Currency formatting (IDR)
- Due date tracking

## 🎯 State Management (Pinia)

The application uses Pinia for centralized state management:

### Stores Available:
- **Auth Store** (`useAuthStore`) - User authentication and session
- **Employees Store** (`useEmployeesStore`) - Employee data management
- **Clients Store** (`useClientsStore`) - Client data management
- **Placements Store** (`usePlacementsStore`) - Placement data management
- **Invoices Store** (`useInvoicesStore`) - Invoice data management

### Store Features:
- Reactive state
- Computed getters
- Async actions
- Error handling
- Loading states
- Search functionality

## 🌐 API Integration

### Base Configuration
```typescript
// Configured in src/services/api.ts
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})
```

### Available Endpoints
- **Authentication:** `/auth/login`, `/auth/logout`, `/auth/me`
- **Employees:** `/employees` (CRUD operations)
- **Clients:** `/clients` (CRUD operations)
- **Placements:** `/placements` (CRUD operations)
- **Invoices:** `/invoices` (CRUD operations)

## 🎨 UI/UX Design

### Design System
- **Primary Color:** Blue (#2563eb)
- **Success:** Green (#059669)
- **Warning:** Yellow (#d97706)
- **Error:** Red (#dc2626)
- **Neutral:** Gray scale

### Components
- Modern card-based layouts
- Responsive tables
- Loading skeletons
- Toast notifications
- Modal dialogs
- Form validation

### Responsive Design
- Mobile-first approach
- Collapsible sidebar
- Responsive tables
- Touch-friendly interactions

## 🔧 Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `VITE_API_BASE_URL` | Backend API base URL | `http://localhost:8000/api` |
| `VITE_API_TIMEOUT` | API request timeout (ms) | `10000` |
| `VITE_APP_NAME` | Application name | `"E-Invoicing Frontend"` |
| `VITE_BACKEND_URL` | Backend base URL | `http://localhost:8000` |
| `VITE_ENABLE_DEBUG` | Enable debug mode | `true` |

### Tailwind Configuration
Custom utility classes are defined in `src/assets/main.css`:
- `.btn-primary` - Primary button styling
- `.btn-secondary` - Secondary button styling
- `.card` - Card component styling
- `.table-row` - Table row styling

## 🚀 Deployment

### Production Build
```bash
npm run build
```

### Environment Setup for Production
1. Update `.env.local` with production API URLs
2. Set `VITE_NODE_ENV=production`
3. Configure proper CORS settings in backend

### Deployment Options
- **Static Hosting:** Netlify, Vercel, GitHub Pages
- **Server Hosting:** Nginx, Apache
- **CDN:** CloudFlare, AWS CloudFront

## 🔍 Demo Credentials

```
Email: rikonusapratama@gmail.com
Password: password
```

## 🐛 Troubleshooting

### Common Issues

1. **API Connection Failed**
   - Check if backend server is running
   - Verify `VITE_API_BASE_URL` in `.env.local`
   - Check CORS configuration in backend

2. **Build Errors**
   - Clear node_modules and reinstall: `rm -rf node_modules package-lock.json && npm install`
   - Check TypeScript errors: `npm run type-check`

3. **Styling Issues**
   - Ensure Tailwind CSS is properly configured
   - Check if PostCSS is processing styles correctly
   - Verify import order in `main.ts`

## 📝 Development Guidelines

### Code Style
- Use TypeScript for type safety
- Follow Vue 3 Composition API patterns
- Use `<script setup>` syntax
- Implement proper error handling
- Add loading states for async operations

### Component Structure
```vue
<script setup lang="ts">
// Imports
// Stores
// Reactive data
// Computed properties
// Methods
// Lifecycle hooks
</script>

<template>
  <!-- Template content -->
</template>
```

### Store Pattern
```typescript
export const useExampleStore = defineStore('example', () => {
  // State
  const items = ref([])
  const loading = ref(false)
  const error = ref('')

  // Getters
  const itemsCount = computed(() => items.value.length)

  // Actions
  const fetchItems = async () => {
    // Implementation
  }

  return {
    // State
    items, loading, error,
    // Getters
    itemsCount,
    // Actions
    fetchItems
  }
})
```

## 🤝 Contributing

1. Follow the existing code style
2. Add proper TypeScript types
3. Include error handling
4. Add loading states
5. Write descriptive commit messages
6. Test your changes thoroughly

## 📄 License

This project is part of the E-Invoicing Management System.