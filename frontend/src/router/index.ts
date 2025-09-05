import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Dashboard from '../views/Dashboard.vue'
import LoginView from '../views/LoginView.vue'
import EmployeesView from '../views/EmployeesView.vue'
import ClientsView from '../views/ClientsView.vue'
import PlacementsView from '../views/PlacementsView.vue'
import InvoicesView from '../views/InvoicesView.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/employees',
    name: 'Employees',
    component: EmployeesView,
    meta: { requiresAuth: true }
  },
  {
    path: '/clients',
    name: 'Clients',
    component: ClientsView,
    meta: { requiresAuth: true }
  },
  {
    path: '/placements',
    name: 'Placements',
    component: PlacementsView,
    meta: { requiresAuth: true }
  },
  {
    path: '/invoices',
    name: 'Invoices',
    component: InvoicesView,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Initialize auth state if not already done
  if (!authStore.user && authStore.token) {
    authStore.initializeAuth()
  }

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!authStore.isAuthenticated) {
      next('/login')
    } else {
      next()
    }
  } else if (to.matched.some(record => record.meta.requiresGuest)) {
    if (authStore.isAuthenticated) {
      next('/')
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router