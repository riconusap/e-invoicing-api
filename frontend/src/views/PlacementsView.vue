<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { resourceAPI } from '../services/api'
import {
  PlusIcon,
  PencilIcon,
  TrashIcon,
  MagnifyingGlassIcon,
  MapPinIcon,
} from '@heroicons/vue/24/outline'

interface Placement {
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

const placements = ref<Placement[]>([])
const loading = ref(true)
const error = ref('')
const searchQuery = ref('')

const filteredPlacements = ref<Placement[]>([])

const loadPlacements = async () => {
  try {
    loading.value = true
    const response = await resourceAPI.getPlacements()
    placements.value = response.data.data || []
    filteredPlacements.value = placements.value
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load placements'
    console.error('Error loading placements:', err)
  } finally {
    loading.value = false
  }
}

const searchPlacements = () => {
  if (!searchQuery.value.trim()) {
    filteredPlacements.value = placements.value
    return
  }
  
  const query = searchQuery.value.toLowerCase()
  filteredPlacements.value = placements.value.filter(placement =>
    placement.status.toLowerCase().includes(query) ||
    (placement.employee?.name && placement.employee.name.toLowerCase().includes(query)) ||
    (placement.client?.name && placement.client.name.toLowerCase().includes(query))
  )
}

const deletePlacement = async (id: number) => {
  if (!confirm('Are you sure you want to delete this placement?')) {
    return
  }

  try {
    await resourceAPI.deletePlacement(id)
    await loadPlacements()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete placement')
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getStatusColor = (status: string) => {
  switch (status.toLowerCase()) {
    case 'active':
      return 'bg-green-100 text-green-800'
    case 'inactive':
      return 'bg-red-100 text-red-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

onMounted(() => {
  loadPlacements()
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Placements</h1>
        <p class="mt-1 text-sm text-gray-600">
          Manage employee placements and assignments
        </p>
      </div>
      <div class="mt-4 sm:mt-0">
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
          Add Placement
        </button>
      </div>
    </div>

    <!-- Search -->
    <div class="mb-6">
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </div>
        <input
          v-model="searchQuery"
          @input="searchPlacements"
          type="text"
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          placeholder="Search placements..."
        />
      </div>
    </div>

    <!-- Error message -->
    <div v-if="error" class="mb-6 rounded-md bg-red-50 p-4">
      <div class="text-sm text-red-700">
        {{ error }}
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="animate-pulse">
        <div v-for="i in 5" :key="i" class="px-4 py-4 sm:px-6 border-b border-gray-200">
          <div class="flex items-center space-x-4">
            <div class="h-4 bg-gray-300 rounded w-1/4"></div>
            <div class="h-4 bg-gray-300 rounded w-1/3"></div>
            <div class="h-4 bg-gray-300 rounded w-1/4"></div>
            <div class="h-4 bg-gray-300 rounded w-1/6"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Placements table -->
    <div v-else-if="filteredPlacements.length > 0" class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Employee
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Client
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Start Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                End Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="placement in filteredPlacements" :key="placement.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ placement.employee?.name || `Employee #${placement.employee_id}` }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ placement.client?.name || `Client #${placement.client_id}` }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(placement.start_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ placement.end_date ? formatDate(placement.end_date) : '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[getStatusColor(placement.status), 'inline-flex px-2 py-1 text-xs font-semibold rounded-full']">
                  {{ placement.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                  <button
                    type="button"
                    class="text-blue-600 hover:text-blue-900 p-1 rounded-md hover:bg-blue-50"
                  >
                    <PencilIcon class="h-4 w-4" aria-hidden="true" />
                  </button>
                  <button
                    type="button"
                    @click="deletePlacement(placement.id)"
                    class="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50"
                  >
                    <TrashIcon class="h-4 w-4" aria-hidden="true" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="text-center py-12">
      <MapPinIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No placements found</h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ searchQuery ? 'Try adjusting your search terms.' : 'Get started by adding a new placement.' }}
      </p>
      <div v-if="!searchQuery" class="mt-6">
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
          Add Placement
        </button>
      </div>
    </div>
  </div>
</template>