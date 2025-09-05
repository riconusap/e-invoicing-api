// Export all stores for easy importing
export { useAuthStore } from './auth'
export { useEmployeesStore } from './employees'
export { useClientsStore } from './clients'
export { usePlacementsStore } from './placements'
export { useInvoicesStore } from './invoices'

// Export types
export type { User, LoginCredentials, RegisterData } from './auth'
export type { Employee, CreateEmployeeData, UpdateEmployeeData } from './employees'
export type { Client, CreateClientData } from './clients'
export type { Placement, CreatePlacementData } from './placements'
export type { Invoice, CreateInvoiceData } from './invoices'