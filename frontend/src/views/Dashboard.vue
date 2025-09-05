<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useEmployeesStore, useClientsStore, usePlacementsStore, useInvoicesStore } from '../stores'
import {
  UsersIcon,
  BuildingOfficeIcon,
  MapPinIcon,
  DocumentTextIcon,
} from '@heroicons/vue/24/outline'

const employeesStore = useEmployeesStore()
const clientsStore = useClientsStore()
const placementsStore = usePlacementsStore()
const invoicesStore = useInvoicesStore()

const stats = computed(() => [
  { 
    name: 'Total Employees', 
    value: employeesStore.employeesCount.toString(), 
    icon: UsersIcon, 
    color: 'bg-blue-500' 
  },
  { 
    name: 'Total Clients', 
    value: clientsStore.clientsCount.toString(), 
    icon: BuildingOfficeIcon, 
    color: 'bg-green-500' 
  },
  { 
    name: 'Active Placements', 
    value: placementsStore.activePlacementsCount.toString(), 
    icon: MapPinIcon, 
    color: 'bg-yellow-500' 
  },
  { 
    name: 'Total Invoices', 
    value: invoicesStore.invoicesCount.toString(), 
    icon: DocumentTextIcon, 
    color: 'bg-purple-500' 
  },
])

const loading = computed(() => 
  employeesStore.loading || 
  clientsStore.loading || 
  placementsStore.loading || 
  invoicesStore.loading
)

const loadDashboardData = async () => {
  await Promise.all([
    employeesStore.fetchEmployees(),
    clientsStore.fetchClients(),
    placementsStore.fetchPlacements(),
    invoicesStore.fetchInvoices(),
  ])
}

onMounted(() => {
  loadDashboardData()
})
</script>

<template>
  <div>
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
      <p class="mt-1 text-sm text-gray-600">
        Welcome to the E-Invoicing Management System
      </p>
    </div>

    <!-- Stats -->
    <div v-if="loading" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <div v-for="i in 4" :key="i" class="bg-white overflow-hidden shadow rounded-lg animate-pulse">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="h-8 w-8 bg-gray-300 rounded"></div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
              <div class="h-6 bg-gray-300 rounded w-1/2"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <div v-for="item in stats" :key="item.name" class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div :class="[item.color, 'p-2 rounded-md']">
                <component :is="item.icon" class="h-6 w-6 text-white" aria-hidden="true" />
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">{{ item.name }}</dt>
                <dd class="text-lg font-medium text-gray-900">{{ item.value }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <router-link
          to="/employees"
          class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg shadow hover:shadow-md transition-shadow duration-200"
        >
          <div>
            <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-700 ring-4 ring-white">
              <UsersIcon class="h-6 w-6" aria-hidden="true" />
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900">
              <span class="absolute inset-0" aria-hidden="true"></span>
              Manage Employees
            </h3>
            <p class="mt-2 text-sm text-gray-500">
              Add, edit, and manage employee records
            </p>
          </div>
        </router-link>

        <router-link
          to="/clients"
          class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg shadow hover:shadow-md transition-shadow duration-200"
        >
          <div>
            <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
              <BuildingOfficeIcon class="h-6 w-6" aria-hidden="true" />
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900">
              <span class="absolute inset-0" aria-hidden="true"></span>
              Manage Clients
            </h3>
            <p class="mt-2 text-sm text-gray-500">
              Add, edit, and manage client information
            </p>
          </div>
        </router-link>

        <router-link
          to="/placements"
          class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg shadow hover:shadow-md transition-shadow duration-200"
        >
          <div>
            <span class="rounded-lg inline-flex p-3 bg-yellow-50 text-yellow-700 ring-4 ring-white">
              <MapPinIcon class="h-6 w-6" aria-hidden="true" />
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900">
              <span class="absolute inset-0" aria-hidden="true"></span>
              Manage Placements
            </h3>
            <p class="mt-2 text-sm text-gray-500">
              Track employee placements and assignments
            </p>
          </div>
        </router-link>

        <router-link
          to="/invoices"
          class="relative group bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg shadow hover:shadow-md transition-shadow duration-200"
        >
          <div>
            <span class="rounded-lg inline-flex p-3 bg-purple-50 text-purple-700 ring-4 ring-white">
              <DocumentTextIcon class="h-6 w-6" aria-hidden="true" />
            </span>
          </div>
          <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900">
              <span class="absolute inset-0" aria-hidden="true"></span>
              Manage Invoices
            </h3>
            <p class="mt-2 text-sm text-gray-500">
              Create and manage invoices
            </p>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>