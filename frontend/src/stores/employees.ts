import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { resourceAPI } from '../services/api'

export interface Employee {
  id: number
  name: string
  email: string
  phone?: string
  position?: string
  created_at: string
  updated_at: string
}

export interface CreateEmployeeData {
  name: string
  email: string
  phone?: string
  position?: string
}

export interface UpdateEmployeeData extends CreateEmployeeData {
  id: number
}

export const useEmployeesStore = defineStore('employees', () => {
  // State
  const employees = ref<Employee[]>([])
  const currentEmployee = ref<Employee | null>(null)
  const loading = ref(false)
  const error = ref('')
  const searchQuery = ref('')

  // Getters
  const filteredEmployees = computed(() => {
    if (!searchQuery.value.trim()) {
      return employees.value
    }
    
    const query = searchQuery.value.toLowerCase()
    return employees.value.filter(employee =>
      employee.name.toLowerCase().includes(query) ||
      employee.email.toLowerCase().includes(query) ||
      (employee.position && employee.position.toLowerCase().includes(query))
    )
  })

  const employeesCount = computed(() => employees.value.length)
  
  const getEmployeeById = computed(() => {
    return (id: number) => employees.value.find(employee => employee.id === id)
  })

  // Actions
  const fetchEmployees = async () => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.getEmployees()
      employees.value = response.data.data || []
      
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to load employees'
      console.error('Error loading employees:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const fetchEmployee = async (id: number) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.getEmployee(id)
      currentEmployee.value = response.data.data || response.data
      
      return { success: true, data: currentEmployee.value }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to load employee'
      console.error('Error loading employee:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const createEmployee = async (employeeData: CreateEmployeeData) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.createEmployee(employeeData)
      const newEmployee = response.data.data || response.data
      
      // Add to the list
      employees.value.push(newEmployee)
      
      return { success: true, data: newEmployee }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create employee'
      console.error('Error creating employee:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const updateEmployee = async (id: number, employeeData: Partial<CreateEmployeeData>) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.updateEmployee(id, employeeData)
      const updatedEmployee = response.data.data || response.data
      
      // Update in the list
      const index = employees.value.findIndex(emp => emp.id === id)
      if (index !== -1) {
        employees.value[index] = updatedEmployee
      }
      
      // Update current employee if it's the same
      if (currentEmployee.value?.id === id) {
        currentEmployee.value = updatedEmployee
      }
      
      return { success: true, data: updatedEmployee }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update employee'
      console.error('Error updating employee:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const deleteEmployee = async (id: number) => {
    try {
      loading.value = true
      error.value = ''

      await resourceAPI.deleteEmployee(id)
      
      // Remove from the list
      const index = employees.value.findIndex(emp => emp.id === id)
      if (index !== -1) {
        employees.value.splice(index, 1)
      }
      
      // Clear current employee if it's the same
      if (currentEmployee.value?.id === id) {
        currentEmployee.value = null
      }
      
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete employee'
      console.error('Error deleting employee:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const setSearchQuery = (query: string) => {
    searchQuery.value = query
  }

  const clearError = () => {
    error.value = ''
  }

  const clearCurrentEmployee = () => {
    currentEmployee.value = null
  }

  return {
    // State
    employees,
    currentEmployee,
    loading,
    error,
    searchQuery,
    
    // Getters
    filteredEmployees,
    employeesCount,
    getEmployeeById,
    
    // Actions
    fetchEmployees,
    fetchEmployee,
    createEmployee,
    updateEmployee,
    deleteEmployee,
    setSearchQuery,
    clearError,
    clearCurrentEmployee,
  }
})