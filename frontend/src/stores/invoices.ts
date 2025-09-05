import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { resourceAPI } from '../services/api'

export interface Invoice {
  id: number
  invoice_number: string
  client_id: number
  amount: number
  status: string
  issue_date: string
  due_date: string
  created_at: string
  updated_at: string
  client?: any
}

export interface CreateInvoiceData {
  invoice_number: string
  client_id: number
  amount: number
  status: string
  issue_date: string
  due_date: string
}

export const useInvoicesStore = defineStore('invoices', () => {
  // State
  const invoices = ref<Invoice[]>([])
  const currentInvoice = ref<Invoice | null>(null)
  const loading = ref(false)
  const error = ref('')
  const searchQuery = ref('')

  // Getters
  const filteredInvoices = computed(() => {
    if (!searchQuery.value.trim()) {
      return invoices.value
    }
    
    const query = searchQuery.value.toLowerCase()
    return invoices.value.filter(invoice =>
      invoice.invoice_number.toLowerCase().includes(query) ||
      invoice.status.toLowerCase().includes(query) ||
      (invoice.client?.name && invoice.client.name.toLowerCase().includes(query))
    )
  })

  const invoicesCount = computed(() => invoices.value.length)
  
  const totalAmount = computed(() => 
    invoices.value.reduce((sum, invoice) => sum + invoice.amount, 0)
  )

  const paidInvoicesCount = computed(() => 
    invoices.value.filter(i => i.status.toLowerCase() === 'paid').length
  )

  const unpaidInvoicesCount = computed(() => 
    invoices.value.filter(i => i.status.toLowerCase() === 'unpaid').length
  )

  const overdueInvoicesCount = computed(() => 
    invoices.value.filter(i => i.status.toLowerCase() === 'overdue').length
  )

  const getInvoiceById = computed(() => {
    return (id: number) => invoices.value.find(invoice => invoice.id === id)
  })

  const getInvoicesByStatus = computed(() => {
    return (status: string) => invoices.value.filter(i => i.status.toLowerCase() === status.toLowerCase())
  })

  // Actions
  const fetchInvoices = async () => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.getInvoices()
      invoices.value = response.data.data || []
      
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to load invoices'
      console.error('Error loading invoices:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const fetchInvoice = async (id: number) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.getInvoice(id)
      currentInvoice.value = response.data.data || response.data
      
      return { success: true, data: currentInvoice.value }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to load invoice'
      console.error('Error loading invoice:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const createInvoice = async (invoiceData: CreateInvoiceData) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.createInvoice(invoiceData)
      const newInvoice = response.data.data || response.data
      
      // Add to the list
      invoices.value.push(newInvoice)
      
      return { success: true, data: newInvoice }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create invoice'
      console.error('Error creating invoice:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const updateInvoice = async (id: number, invoiceData: Partial<CreateInvoiceData>) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.updateInvoice(id, invoiceData)
      const updatedInvoice = response.data.data || response.data
      
      // Update in the list
      const index = invoices.value.findIndex(invoice => invoice.id === id)
      if (index !== -1) {
        invoices.value[index] = updatedInvoice
      }
      
      // Update current invoice if it's the same
      if (currentInvoice.value?.id === id) {
        currentInvoice.value = updatedInvoice
      }
      
      return { success: true, data: updatedInvoice }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update invoice'
      console.error('Error updating invoice:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const deleteInvoice = async (id: number) => {
    try {
      loading.value = true
      error.value = ''

      await resourceAPI.deleteInvoice(id)
      
      // Remove from the list
      const index = invoices.value.findIndex(invoice => invoice.id === id)
      if (index !== -1) {
        invoices.value.splice(index, 1)
      }
      
      // Clear current invoice if it's the same
      if (currentInvoice.value?.id === id) {
        currentInvoice.value = null
      }
      
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete invoice'
      console.error('Error deleting invoice:', err)
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

  const clearCurrentInvoice = () => {
    currentInvoice.value = null
  }

  return {
    // State
    invoices,
    currentInvoice,
    loading,
    error,
    searchQuery,
    
    // Getters
    filteredInvoices,
    invoicesCount,
    totalAmount,
    paidInvoicesCount,
    unpaidInvoicesCount,
    overdueInvoicesCount,
    getInvoiceById,
    getInvoicesByStatus,
    
    // Actions
    fetchInvoices,
    fetchInvoice,
    createInvoice,
    updateInvoice,
    deleteInvoice,
    setSearchQuery,
    clearError,
    clearCurrentInvoice,
  }
})