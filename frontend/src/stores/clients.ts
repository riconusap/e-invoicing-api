import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { resourceAPI } from '../services/api'

export interface Client {
  id: number
  name: string
  email: string
  phone?: string
  address?: string
  company?: string
  created_at: string
  updated_at: string
}

export interface CreateClientData {
  name: string
  email: string
  phone?: string
  address?: string
  company?: string
}

export const useClientsStore = defineStore('clients', () => {
  // State
  const clients = ref<Client[]>([])
  const currentClient = ref<Client | null>(null)
  const loading = ref(false)
  const error = ref('')
  const searchQuery = ref('')

  // Getters
  const filteredClients = computed(() => {
    if (!searchQuery.value.trim()) {
      return clients.value
    }
    
    const query = searchQuery.value.toLowerCase()
    return clients.value.filter(client =>
      client.name.toLowerCase().includes(query) ||
      client.email.toLowerCase().includes(query) ||
      (client.company && client.company.toLowerCase().includes(query))
    )
  })

  const clientsCount = computed(() => clients.value.length)
  
  const getClientById = computed(() => {
    return (id: number) => clients.value.find(client => client.id === id)
  })

  // Actions
  const fetchClients = async () => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.getClients()
      clients.value = response.data.data || []
      
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to load clients'
      console.error('Error loading clients:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const fetchClient = async (id: number) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.getClient(id)
      currentClient.value = response.data.data || response.data
      
      return { success: true, data: currentClient.value }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to load client'
      console.error('Error loading client:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const createClient = async (clientData: CreateClientData) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.createClient(clientData)
      const newClient = response.data.data || response.data
      
      // Add to the list
      clients.value.push(newClient)
      
      return { success: true, data: newClient }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create client'
      console.error('Error creating client:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const updateClient = async (id: number, clientData: Partial<CreateClientData>) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.updateClient(id, clientData)
      const updatedClient = response.data.data || response.data
      
      // Update in the list
      const index = clients.value.findIndex(client => client.id === id)
      if (index !== -1) {
        clients.value[index] = updatedClient
      }
      
      // Update current client if it's the same
      if (currentClient.value?.id === id) {
        currentClient.value = updatedClient
      }
      
      return { success: true, data: updatedClient }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update client'
      console.error('Error updating client:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const deleteClient = async (id: number) => {
    try {
      loading.value = true
      error.value = ''

      await resourceAPI.deleteClient(id)
      
      // Remove from the list
      const index = clients.value.findIndex(client => client.id === id)
      if (index !== -1) {
        clients.value.splice(index, 1)
      }
      
      // Clear current client if it's the same
      if (currentClient.value?.id === id) {
        currentClient.value = null
      }
      
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete client'
      console.error('Error deleting client:', err)
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

  const clearCurrentClient = () => {
    currentClient.value = null
  }

  return {
    // State
    clients,
    currentClient,
    loading,
    error,
    searchQuery,
    
    // Getters
    filteredClients,
    clientsCount,
    getClientById,
    
    // Actions
    fetchClients,
    fetchClient,
    createClient,
    updateClient,
    deleteClient,
    setSearchQuery,
    clearError,
    clearCurrentClient,
  }
})