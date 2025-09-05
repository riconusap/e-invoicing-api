import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { resourceAPI } from '../services/api'

export interface Placement {
  id: number
  employee_id: number
  client_id: number
  start_date: string
  end_date?: string
  status: string
  created_at: string
  updated_at: string
  employee?: any
  client?: any
}

export interface CreatePlacementData {
  employee_id: number
  client_id: number
  start_date: string
  end_date?: string
  status: string
}

export const usePlacementsStore = defineStore('placements', () => {
  // State
  const placements = ref<Placement[]>([])
  const currentPlacement = ref<Placement | null>(null)
  const loading = ref(false)
  const error = ref('')
  const searchQuery = ref('')

  // Getters
  const filteredPlacements = computed(() => {
    if (!searchQuery.value.trim()) {
      return placements.value
    }
    
    const query = searchQuery.value.toLowerCase()
    return placements.value.filter(placement =>
      placement.status.toLowerCase().includes(query) ||
      (placement.employee?.name && placement.employee.name.toLowerCase().includes(query)) ||
      (placement.client?.name && placement.client.name.toLowerCase().includes(query))
    )
  })

  const placementsCount = computed(() => placements.value.length)
  
  const activePlacementsCount = computed(() => 
    placements.value.filter(p => p.status.toLowerCase() === 'active').length
  )

  const getPlacementById = computed(() => {
    return (id: number) => placements.value.find(placement => placement.id === id)
  })

  const getPlacementsByStatus = computed(() => {
    return (status: string) => placements.value.filter(p => p.status.toLowerCase() === status.toLowerCase())
  })

  // Actions
  const fetchPlacements = async () => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.getPlacements()
      placements.value = response.data.data || []
      
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to load placements'
      console.error('Error loading placements:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const fetchPlacement = async (id: number) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.getPlacement(id)
      currentPlacement.value = response.data.data || response.data
      
      return { success: true, data: currentPlacement.value }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to load placement'
      console.error('Error loading placement:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const createPlacement = async (placementData: CreatePlacementData) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.createPlacement(placementData)
      const newPlacement = response.data.data || response.data
      
      // Add to the list
      placements.value.push(newPlacement)
      
      return { success: true, data: newPlacement }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create placement'
      console.error('Error creating placement:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const updatePlacement = async (id: number, placementData: Partial<CreatePlacementData>) => {
    try {
      loading.value = true
      error.value = ''

      const response = await resourceAPI.updatePlacement(id, placementData)
      const updatedPlacement = response.data.data || response.data
      
      // Update in the list
      const index = placements.value.findIndex(placement => placement.id === id)
      if (index !== -1) {
        placements.value[index] = updatedPlacement
      }
      
      // Update current placement if it's the same
      if (currentPlacement.value?.id === id) {
        currentPlacement.value = updatedPlacement
      }
      
      return { success: true, data: updatedPlacement }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update placement'
      console.error('Error updating placement:', err)
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const deletePlacement = async (id: number) => {
    try {
      loading.value = true
      error.value = ''

      await resourceAPI.deletePlacement(id)
      
      // Remove from the list
      const index = placements.value.findIndex(placement => placement.id === id)
      if (index !== -1) {
        placements.value.splice(index, 1)
      }
      
      // Clear current placement if it's the same
      if (currentPlacement.value?.id === id) {
        currentPlacement.value = null
      }
      
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete placement'
      console.error('Error deleting placement:', err)
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

  const clearCurrentPlacement = () => {
    currentPlacement.value = null
  }

  return {
    // State
    placements,
    currentPlacement,
    loading,
    error,
    searchQuery,
    
    // Getters
    filteredPlacements,
    placementsCount,
    activePlacementsCount,
    getPlacementById,
    getPlacementsByStatus,
    
    // Actions
    fetchPlacements,
    fetchPlacement,
    createPlacement,
    updatePlacement,
    deletePlacement,
    setSearchQuery,
    clearError,
    clearCurrentPlacement,
  }
})