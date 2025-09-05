import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authAPI } from '../services/api'

export interface User {
  id: number
  name: string
  email: string
  created_at: string
  updated_at: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const loading = ref(false)
  const error = ref('')

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userName = computed(() => user.value?.name || '')
  const userEmail = computed(() => user.value?.email || '')

  // Actions
  const login = async (credentials: LoginCredentials) => {
    try {
      loading.value = true
      error.value = ''

      const response = await authAPI.login(credentials)
      
      if (response.data.access_token) {
        token.value = response.data.access_token
        user.value = response.data.user
        
        // Store in localStorage
        localStorage.setItem('auth_token', response.data.access_token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        
        return { success: true }
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Login failed. Please try again.'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const register = async (userData: RegisterData) => {
    try {
      loading.value = true
      error.value = ''

      const response = await authAPI.register(userData)
      
      if (response.data.access_token) {
        token.value = response.data.access_token
        user.value = response.data.user
        
        // Store in localStorage
        localStorage.setItem('auth_token', response.data.access_token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        
        return { success: true }
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Registration failed. Please try again.'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      if (token.value) {
        await authAPI.logout()
      }
    } catch (err) {
      console.error('Logout API call failed:', err)
    } finally {
      // Clear state regardless of API call result
      user.value = null
      token.value = null
      error.value = ''
      
      // Clear localStorage
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
    }
  }

  const fetchUser = async () => {
    if (!token.value) return

    try {
      loading.value = true
      const response = await authAPI.me()
      user.value = response.data
      localStorage.setItem('user', JSON.stringify(response.data))
    } catch (err: any) {
      console.error('Failed to fetch user:', err)
      // If token is invalid, logout
      if (err.response?.status === 401) {
        await logout()
      }
    } finally {
      loading.value = false
    }
  }

  const initializeAuth = () => {
    // Try to restore user from localStorage
    const storedUser = localStorage.getItem('user')
    const storedToken = localStorage.getItem('auth_token')
    
    if (storedToken && storedUser) {
      try {
        token.value = storedToken
        user.value = JSON.parse(storedUser)
      } catch (err) {
        console.error('Failed to parse stored user data:', err)
        logout()
      }
    }
  }

  const clearError = () => {
    error.value = ''
  }

  const forgotPassword = async (email: string) => {
    try {
      loading.value = true
      error.value = ''

      await authAPI.forgotPassword(email)
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to send password reset email.'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const resetPassword = async (data: { email: string; password: string; password_confirmation: string; token: string }) => {
    try {
      loading.value = true
      error.value = ''

      await authAPI.resetPassword(data)
      return { success: true }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to reset password.'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  return {
    // State
    user,
    token,
    loading,
    error,
    
    // Getters
    isAuthenticated,
    userName,
    userEmail,
    
    // Actions
    login,
    register,
    logout,
    fetchUser,
    initializeAuth,
    clearError,
    forgotPassword,
    resetPassword,
  }
})