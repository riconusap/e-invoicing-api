import axios, { AxiosResponse } from 'axios'
import type { AxiosInstance } from 'axios'

// Create axios instance
const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle token refresh and errors
api.interceptors.response.use(
  (response: AxiosResponse) => {
    return response
  },
  (error) => {
    if (error.response?.status === 401) {
      // Token expired, remove from localStorage
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      // Redirect to login if needed
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// Auth API functions
export const authAPI = {
  login: (credentials: { email: string; password: string }) =>
    api.post('/auth/login', credentials),
  
  register: (userData: { name: string; email: string; password: string; password_confirmation: string }) =>
    api.post('/auth/register', userData),
  
  logout: () => api.post('/auth/logout'),
  
  me: () => api.get('/auth/me'),
  
  forgotPassword: (email: string) =>
    api.post('/auth/forgot-password', { email }),
  
  resetPassword: (data: { email: string; password: string; password_confirmation: string; token: string }) =>
    api.post('/auth/reset-password', data),
}

// Resource API functions
export const resourceAPI = {
  // Employees
  getEmployees: () => api.get('/employees'),
  getEmployee: (id: number) => api.get(`/employees/${id}`),
  createEmployee: (data: any) => api.post('/employees', data),
  updateEmployee: (id: number, data: any) => api.put(`/employees/${id}`, data),
  deleteEmployee: (id: number) => api.delete(`/employees/${id}`),

  // Clients
  getClients: () => api.get('/clients'),
  getClient: (id: number) => api.get(`/clients/${id}`),
  createClient: (data: any) => api.post('/clients', data),
  updateClient: (id: number, data: any) => api.put(`/clients/${id}`, data),
  deleteClient: (id: number) => api.delete(`/clients/${id}`),

  // Placements
  getPlacements: () => api.get('/placements'),
  getPlacement: (id: number) => api.get(`/placements/${id}`),
  createPlacement: (data: any) => api.post('/placements', data),
  updatePlacement: (id: number, data: any) => api.put(`/placements/${id}`, data),
  deletePlacement: (id: number) => api.delete(`/placements/${id}`),

  // Contract Clients
  getContractClients: () => api.get('/contract-clients'),
  getContractClient: (id: number) => api.get(`/contract-clients/${id}`),
  createContractClient: (data: any) => api.post('/contract-clients', data),
  updateContractClient: (id: number, data: any) => api.put(`/contract-clients/${id}`, data),
  deleteContractClient: (id: number) => api.delete(`/contract-clients/${id}`),

  // Contract Employees
  getContractEmployees: () => api.get('/contract-employees'),
  getContractEmployee: (id: number) => api.get(`/contract-employees/${id}`),
  createContractEmployee: (data: any) => api.post('/contract-employees', data),
  updateContractEmployee: (id: number, data: any) => api.put(`/contract-employees/${id}`, data),
  deleteContractEmployee: (id: number) => api.delete(`/contract-employees/${id}`),

  // Invoices
  getInvoices: () => api.get('/invoices'),
  getInvoice: (id: number) => api.get(`/invoices/${id}`),
  createInvoice: (data: any) => api.post('/invoices', data),
  updateInvoice: (id: number, data: any) => api.put(`/invoices/${id}`, data),
  deleteInvoice: (id: number) => api.delete(`/invoices/${id}`),

  // PIC Externals
  getPicExternals: () => api.get('/pic-externals'),
  getPicExternal: (id: number) => api.get(`/pic-externals/${id}`),
  createPicExternal: (data: any) => api.post('/pic-externals', data),
  updatePicExternal: (id: number, data: any) => api.put(`/pic-externals/${id}`, data),
  deletePicExternal: (id: number) => api.delete(`/pic-externals/${id}`),
}

export default api