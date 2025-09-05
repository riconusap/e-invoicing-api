<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { resourceAPI } from '../services/api'
import {
  PlusIcon,
  PencilIcon,
  TrashIcon,
  MagnifyingGlassIcon,
  DocumentTextIcon,
  EyeIcon,
} from '@heroicons/vue/24/outline'

interface Invoice {
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

const invoices = ref<Invoice[]>([])
const loading = ref(true)
const error = ref('')
const searchQuery = ref('')

const filteredInvoices = ref<Invoice[]>([])

const loadInvoices = async () => {
  try {
    loading.value = true
    const response = await resourceAPI.getInvoices()
    invoices.value = response.data.data || []
    filteredInvoices.value = invoices.value
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to load invoices'
    console.error('Error loading invoices:', err)
  } finally {
    loading.value = false
  }
}

const searchInvoices = () => {
  if (!searchQuery.value.trim()) {
    filteredInvoices.value = invoices.value
    return
  }
  
  const query = searchQuery.value.toLowerCase()
  filteredInvoices.value = invoices.value.filter(invoice =>
    invoice.invoice_number.toLowerCase().includes(query) ||
    invoice.status.toLowerCase().includes(query) ||
    (invoice.client?.name && invoice.client.name.toLowerCase().includes(query))
  )
}

const deleteInvoice = async (id: number) => {
  if (!confirm('Are you sure you want to delete this invoice?')) {
    return
  }

  try {
    await resourceAPI.deleteInvoice(id)
    await loadInvoices()
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete invoice')
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(amount)
}

const getStatusColor = (status: string) => {
  switch (status.toLowerCase()) {
    case 'paid':
      return 'bg-green-100 text-green-800'
    case 'unpaid':
      return 'bg-red-100 text-red-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'overdue':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

onMounted(() => {
  loadInvoices()
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Invoices</h1>
        <p class="mt-1 text-sm text-gray-600">
          Manage your invoices and billing
        </p>
      </div>
      <div class="mt-4 sm:mt-0">
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
          Create Invoice
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
          @input="searchInvoices"
          type="text"
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          placeholder="Search invoices..."
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

    <!-- Invoices table -->
    <div v-else-if="filteredInvoices.length > 0" class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Invoice Number
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Client
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Issue Date
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Due Date
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
            <tr v-for="invoice in filteredInvoices" :key="invoice.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ invoice.invoice_number }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ invoice.client?.name || `Client #${invoice.client_id}` }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatCurrency(invoice.amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(invoice.issue_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(invoice.due_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="[getStatusColor(invoice.status), 'inline-flex px-2 py-1 text-xs font-semibold rounded-full']">
                  {{ invoice.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                  <button
                    type="button"
                    class="text-green-600 hover:text-green-900 p-1 rounded-md hover:bg-green-50"
                    title="View Invoice"
                  >
                    <EyeIcon class="h-4 w-4" aria-hidden="true" />
                  </button>
                  <button
                    type="button"
                    class="text-blue-600 hover:text-blue-900 p-1 rounded-md hover:bg-blue-50"
                    title="Edit Invoice"
                  >
                    <PencilIcon class="h-4 w-4" aria-hidden="true" />
                  </button>
                  <button
                    type="button"
                    @click="deleteInvoice(invoice.id)"
                    class="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50"
                    title="Delete Invoice"
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
      <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">No invoices found</h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ searchQuery ? 'Try adjusting your search terms.' : 'Get started by creating a new invoice.' }}
      </p>
      <div v-if="!searchQuery" class="mt-6">
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
          Create Invoice
        </button>
      </div>
    </div>
  </div>
</template>