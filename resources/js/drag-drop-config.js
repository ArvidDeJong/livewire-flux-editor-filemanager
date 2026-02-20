/**
 * Drag & Drop Configuration Helper
 * 
 * This module provides configuration for drag & drop and paste functionality.
 * Configuration can be set via data attributes on the editor element.
 */

export function getDragDropConfig(editorElement) {
    const config = {
        method: editorElement?.dataset?.dragDropMethod || 'base64',
        uploadUrl: editorElement?.dataset?.uploadUrl || '/filemanager/upload',
        maxFileSize: parseInt(editorElement?.dataset?.maxFileSize || '5242880'),
        allowedTypes: (editorElement?.dataset?.allowedTypes || 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml').split(','),
    }

    return config
}

/**
 * Upload image to server
 * 
 * @param {File} file - Image file to upload
 * @param {string} uploadUrl - Server upload endpoint
 * @returns {Promise<string>} - URL of uploaded image
 */
export async function uploadImageToServer(file, uploadUrl) {
    const formData = new FormData()
    formData.append('upload', file)
    formData.append('type', 'Images')

    try {
        const response = await fetch(uploadUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })

        if (!response.ok) {
            throw new Error(`Upload failed: ${response.statusText}`)
        }

        const data = await response.json()

        // Laravel Filemanager returns URL in different formats
        return data.url || data.link || data.path || null
    } catch (error) {
        console.error('Image upload failed:', error)
        throw error
    }
}

/**
 * Convert file to base64
 * 
 * @param {File} file - Image file to convert
 * @returns {Promise<string>} - Base64 data URL
 */
export function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader()

        reader.onload = (e) => {
            resolve(e.target.result)
        }

        reader.onerror = (error) => {
            reject(error)
        }

        reader.readAsDataURL(file)
    })
}

/**
 * Process image file based on configuration
 * 
 * @param {File} file - Image file to process
 * @param {Object} config - Drag & drop configuration
 * @returns {Promise<string>} - Image URL (base64 or server URL)
 */
export async function processImageFile(file, config) {
    // Validate file size
    if (file.size > config.maxFileSize) {
        const maxSizeMB = (config.maxFileSize / 1024 / 1024).toFixed(1)
        throw new Error(`Image too large. Maximum size is ${maxSizeMB}MB`)
    }

    // Validate file type
    if (!config.allowedTypes.includes(file.type)) {
        throw new Error(`File type ${file.type} is not allowed`)
    }

    // Process based on method
    if (config.method === 'upload') {
        return await uploadImageToServer(file, config.uploadUrl)
    } else {
        return await fileToBase64(file)
    }
}
