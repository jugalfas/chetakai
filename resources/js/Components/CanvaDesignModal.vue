<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    show: Boolean,
    quoteText: String,
    canvaClientId: String,
})

const emit = defineEmits(['close', 'designSaved'])

const loading = ref(false)
const sdkLoaded = ref(false)
const canvaApi = ref(null)

// Load Canva Design Button SDK dynamically (only once globally)
const loadSDK = () => {
    return new Promise((resolve, reject) => {
        // Check if SDK is already loaded globally
        if (window.Canva && window.Canva.DesignButton) {
            console.log('Canva SDK already loaded globally')
            sdkLoaded.value = true
            resolve()
            return
        }

        // Prevent duplicate script loading
        if (document.querySelector('script[src*="designbutton/v2/api.js"]')) {
            console.log('Canva script already exists, waiting for load...')
            // Wait for existing script to load
            const checkLoaded = setInterval(() => {
                if (window.Canva && window.Canva.DesignButton) {
                    clearInterval(checkLoaded)
                    console.log('Canva SDK loaded from existing script')
                    sdkLoaded.value = true
                    resolve()
                }
            }, 100)
            return
        }

        console.log('Loading Canva SDK script...')
        const script = document.createElement('script')
        script.src = 'https://sdk.canva.com/designbutton/v2/api.js'
        script.onload = () => {
            console.log('Canva SDK script loaded, checking for DesignButton...')
            // Wait a bit for the SDK to initialize
            setTimeout(() => {
                if (window.Canva && window.Canva.DesignButton) {
                    console.log('Canva DesignButton available')
                    sdkLoaded.value = true
                    resolve()
                } else {
                    console.error('Canva SDK loaded but DesignButton not available')
                    reject(new Error('Canva DesignButton not available after script load'))
                }
            }, 100)
        }
        script.onerror = (error) => {
            console.error('Failed to load Canva SDK script:', error)
            reject(error)
        }
        document.head.appendChild(script)
    })
}

// Initialize Canva API (only once per session)
const initializeAPI = async () => {
    console.log('Initializing Canva API with clientId:', props.canvaClientId)

    if (!props.canvaClientId) {
        throw new Error('Canva App ID is required')
    }

    // Load SDK if not already loaded
    if (!sdkLoaded.value) {
        console.log('Loading Canva SDK...')
        await loadSDK()
        console.log('Canva SDK loaded successfully')
    }

    // Initialize API only once
    if (!canvaApi.value) {
        console.log('Initializing Canva DesignButton API...')
        try {
            console.log('Calling window.Canva.DesignButton.initialize with apiKey:', props.canvaClientId)
            const initResult = await window.Canva.DesignButton.initialize({
                apiKey: props.canvaClientId,
            })
            console.log('window.Canva.DesignButton.initialize returned:', initResult)
            canvaApi.value = initResult
            console.log('Canva API initialized successfully:', canvaApi.value)

            // Verify the API object has the expected methods
            if (!canvaApi.value || typeof canvaApi.value.createDesign !== 'function') {
                throw new Error('Canva API initialized but createDesign method not available')
            }
        } catch (initError) {
            console.error('Failed to initialize Canva API:', initError)
            console.error('window.Canva:', window.Canva)
            console.error('window.Canva.DesignButton:', window.Canva?.DesignButton)
            throw initError
        }
    } else {
        console.log('Canva API already initialized')
    }
}

// Create and open design
const createDesign = async () => {
    console.log('createDesign called, canvaApi.value:', canvaApi.value)

    // Ensure API is initialized before creating design
    if (!canvaApi.value) {
        console.log('API not initialized, calling initializeAPI...')
        await initializeAPI()
        console.log('After initializeAPI, canvaApi.value:', canvaApi.value)
    }

    if (!canvaApi.value) {
        throw new Error('Canva API failed to initialize')
    }

    console.log('About to call createDesign with config:', {
        type: 'poster',
        title: 'Quote Design'
    })

    try {
        // Try to create design directly - let Canva handle authentication
        canvaApi.value.createDesign({
            type: 'poster',
            title: 'Quote Design',
            onPublish: (result) => {
                console.log('Design published:', result)
                emit('designSaved', {
                    designId: result.designId,
                    editUrl: result.editUrl,
                    exportUrl: result.exportUrl || null,
                })
                emit('close')
            },
            onClose: () => {
                console.log('Canva editor closed')
                emit('close')
            },
        })
        console.log('createDesign call completed successfully')
    } catch (createError) {
        console.error('Error during createDesign call:', createError)

        // Show the actual error message
        const errorMsg = createError.message || 'Unknown Canva error'
        console.error('Full error object:', createError)

        alert(`Canva Error: ${errorMsg}\n\nThis usually means:\n• Your Canva app is not a Design Button app\n• The app is not published\n• Wrong Client ID\n\nCheck Canva Developer Portal and recreate as Design Button app.`)

        emit('close')
    }
}

// Watch for modal show
watch(() => props.show, async (newShow) => {
    if (newShow) {
        console.log('CanvaDesignModal opened with props:', {
            show: props.show,
            quoteText: props.quoteText,
            canvaClientId: props.canvaClientId
        })

        loading.value = true

        try {
            await createDesign()
        } catch (error) {
            console.error('Failed to open Canva editor:', error)
            emit('close')
        } finally {
            loading.value = false
        }
    }
})
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full h-full max-w-screen-lg max-h-screen flex flex-col">
            <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Design Quote with Canva</h2>
                <button @click="emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-grow flex items-center justify-center">
                <div v-if="loading" class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-4"></div>
                    <p class="text-gray-600 dark:text-gray-400">Loading Canva editor...</p>
                </div>
                <div v-else class="text-gray-500 dark:text-gray-400">
                    <p>Opening Canva editor...</p>
                </div>
            </div>
        </div>
    </div>
</template>
