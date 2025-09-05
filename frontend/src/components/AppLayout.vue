<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  Bars3Icon,
  XMarkIcon,
  HomeIcon,
  UsersIcon,
  BuildingOfficeIcon,
  MapPinIcon,
  DocumentTextIcon,
  ArrowRightOnRectangleIcon,
} from '@heroicons/vue/24/outline'

const router = useRouter()
const sidebarOpen = ref(false)

const navigation = [
  { name: 'Dashboard', href: '/', icon: HomeIcon, current: true },
  { name: 'Employees', href: '/employees', icon: UsersIcon, current: false },
  { name: 'Clients', href: '/clients', icon: BuildingOfficeIcon, current: false },
  { name: 'Placements', href: '/placements', icon: MapPinIcon, current: false },
  { name: 'Invoices', href: '/invoices', icon: DocumentTextIcon, current: false },
]

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>

<template>
  <div class="flex h-screen bg-gray-50">
    <!-- Mobile sidebar -->
    <div v-if="sidebarOpen" class="fixed inset-0 z-40 flex md:hidden" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
      <div class="relative flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4">
        <div class="absolute top-0 right-0 -mr-12 pt-2">
          <button
            type="button"
            class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
            @click="sidebarOpen = false"
          >
            <span class="sr-only">Close sidebar</span>
            <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
          </button>
        </div>
        <div class="flex flex-shrink-0 items-center px-4">
          <h1 class="text-xl font-bold text-gray-900">E-Invoicing</h1>
        </div>
        <div class="mt-5 h-0 flex-1 overflow-y-auto">
          <nav class="space-y-1 px-2">
            <router-link
              v-for="item in navigation"
              :key="item.name"
              :to="item.href"
              class="group flex items-center px-2 py-2 text-base font-medium rounded-md"
              :class="[
                $route.path === item.href
                  ? 'bg-blue-100 text-blue-900'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
              ]"
            >
              <component
                :is="item.icon"
                class="mr-4 h-6 w-6 flex-shrink-0"
                :class="[
                  $route.path === item.href ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'
                ]"
                aria-hidden="true"
              />
              {{ item.name }}
            </router-link>
          </nav>
        </div>
      </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
      <div class="flex flex-grow flex-col overflow-y-auto bg-white pt-5 shadow-sm">
        <div class="flex flex-shrink-0 items-center px-4">
          <h1 class="text-xl font-bold text-gray-900">E-Invoicing</h1>
        </div>
        <div class="mt-5 flex flex-grow flex-col">
          <nav class="flex-1 space-y-1 px-2 pb-4">
            <router-link
              v-for="item in navigation"
              :key="item.name"
              :to="item.href"
              class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
              :class="[
                $route.path === item.href
                  ? 'bg-blue-100 text-blue-900'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
              ]"
            >
              <component
                :is="item.icon"
                class="mr-3 h-6 w-6 flex-shrink-0"
                :class="[
                  $route.path === item.href ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'
                ]"
                aria-hidden="true"
              />
              {{ item.name }}
            </router-link>
          </nav>
          <div class="flex flex-shrink-0 border-t border-gray-200 p-4">
            <button
              @click="logout"
              class="group flex w-full items-center px-2 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900"
            >
              <ArrowRightOnRectangleIcon
                class="mr-3 h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-500"
                aria-hidden="true"
              />
              Sign out
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="flex flex-1 flex-col md:pl-64">
      <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow">
        <button
          type="button"
          class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 md:hidden"
          @click="sidebarOpen = true"
        >
          <span class="sr-only">Open sidebar</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>
        <div class="flex flex-1 justify-between px-4">
          <div class="flex flex-1">
            <!-- Search bar could go here -->
          </div>
          <div class="ml-4 flex items-center md:ml-6">
            <!-- Profile dropdown could go here -->
            <div class="text-sm text-gray-700">
              Welcome back!
            </div>
          </div>
        </div>
      </div>

      <main class="flex-1">
        <div class="py-6">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
            <slot />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>