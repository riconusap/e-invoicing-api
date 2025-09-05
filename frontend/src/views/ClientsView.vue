<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { resourceAPI } from '../services/api'
import {
  PlusIcon,
  PencilIcon,
  TrashIcon,
  MagnifyingGlassIcon,
  BuildingOfficeIcon,
} from '@heroicons/vue/24/outline'

interface Client {
  id: number
  name: string
  email: string
  phone?: string
  address?: string
  company?: string
  created_at: string
  updated_at: string
}

const clients = ref<Client[]>([])
const loading = ref(true)
const error = ref('')
const searchQuery = ref('')

const filteredClients = ref<Client[]>([])

const loadClients = async () => {
  try {
    loading.value = true
    const response = await resourceAPI.getClients()
    clients.value = response.data.data || []
    filteredClients.value = clients.value
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load clients'
    console.error('Error loading clients:', err)
  } finally {
    loading.value = false
  }
}

const searchClients = () => {
  if (!searchQuery.value.trim()) {
    filteredClients.value = clients.value
    return
  }
  
  const query = searchQuery.value.toLowerCase()
  filteredClients.value = clients.value.filter(client =>
    client.name.toLowerCase().includes(query) ||
    client.email.toLowerCase().includes(query) ||
    (client.company && client.company.toLowerCase().includes(query))
  )
}

const deleteClient = async (id: number) => {
  if (!confirm('Are you sure you want to delete this client?')) {
    return
  }

  try {
    await resourceAPI.deleteClient(id)
    await loadClients()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete client')
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

onMounted(() => {
  loadClients()
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
        <p class="mt-1 text-sm text-gray-600">
          Manage your client records
        </p>
      </div>
      <div class="mt-4 sm:mt-0">
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
          Add Client
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
          @input="searchClients"
          type="text"
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          placeholder="Search clients..."
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

    <!-- Clients table -->
    <div v-else-if="filteredClients.length > 0" class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Phone
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Company
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="client in filteredClients" :key="client.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ client.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ client.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ client.phone || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ client.company || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(client.created_at) }}
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
                    @click="deleteClient(client.id)"
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
      <BuildingOfficeIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No clients found</h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ searchQuery ? 'Try adjusting your search terms.' : 'Get started by adding a new client.' }}
      </p>
      <div v-if="!searchQuery" class="mt-6">
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
          Add Client
        </button>
      </div>
    </div>
  </div>
</template>