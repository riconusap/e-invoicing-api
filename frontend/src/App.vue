<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AppLayout from './components/AppLayout.vue'

const route = useRoute()

const isLoginPage = computed(() => route.name === 'Login')
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <template v-if="!isLoginPage">
      <AppLayout>
        <RouterView />
      </AppLayout>
    </template>
    <template v-else>
      <RouterView />
    </template>
  </div>
</template>
