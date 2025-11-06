// Lab requirement: Validate brand information, check type
function validateBrandName(name) {
  if (!name || name.trim() === '') {
    return { valid: false, message: 'Brand name is required' }
  }
  
  if (typeof name !== 'string') {
    return { valid: false, message: 'Brand name must be a string' }
  }
  
  if (name.length < 2) {
    return { valid: false, message: 'Brand name must be at least 2 characters' }
  }
  
  if (name.length > 100) {
    return { valid: false, message: 'Brand name must be less than 100 characters' }
  }
  
  // Check for valid characters (letters including accented/Unicode, numbers, spaces, hyphens, apostrophes, ampersand, periods)
  // Allow Unicode letters (including accented characters like è, é, à, etc.)
  if (!/^[\p{L}\p{N}\s\-'&.]+$/u.test(name)) {
    return { valid: false, message: 'Brand name contains invalid characters' }
  }
  
  return { valid: true, message: 'Valid' }
}

// Lab requirement: Show modal/popup for success/failure messages
function showModal(message, type = 'success') {
  const modal = document.createElement('div')
  modal.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
    type === 'success' ? 'bg-green-100 text-green-800 border border-green-400' : 'bg-red-100 text-red-800 border border-red-400'
  }`
  modal.innerHTML = `
    <div class="flex items-center justify-between">
      <span>${escapeHtml(message)}</span>
      <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-600 hover:text-gray-800">×</button>
    </div>
  `
  document.body.appendChild(modal)
  
  // Auto-remove after 3 seconds
  setTimeout(() => {
    if (modal.parentElement) {
      modal.remove()
    }
  }, 3000)
}

// Categories dropdown is no longer needed since brands are available for all categories

// Lab requirement: Asynchronously invoke fetch_brand_action.php
async function loadBrands() {
  try {
    const response = await fetch('../actions/fetch_brand_action.php')
    const data = await response.json()

    if (data.success) {
      const tbody = document.getElementById('brandsTable')
      tbody.innerHTML = ''

      if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-slate-500">No brands found</td></tr>'
        return
      }

      // Organize brands by category (Lab requirement: organized by categories)
      const brandsByCategory = {}
      data.data.forEach((brand) => {
        // Brands with NULL category_id are available for all categories
        const categoryName = brand.category_name || 'All Categories'
        if (!brandsByCategory[categoryName]) {
          brandsByCategory[categoryName] = []
        }
        brandsByCategory[categoryName].push(brand)
      })

      // Display brands organized by category
      Object.keys(brandsByCategory).sort().forEach(categoryName => {
        const categoryHeader = document.createElement('tr')
        categoryHeader.className = 'bg-slate-100'
        categoryHeader.innerHTML = `
          <td colspan="4" class="px-6 py-3 font-bold text-slate-700">${escapeHtml(categoryName)}</td>
        `
        tbody.appendChild(categoryHeader)

        brandsByCategory[categoryName].forEach((brand) => {
          const row = document.createElement('tr')
          row.innerHTML = `
            <td class="px-6 py-4">${brand.id}</td>
            <td class="px-6 py-4">${escapeHtml(brand.name)}</td>
            <td class="px-6 py-4">${escapeHtml(brand.category_name || 'All Categories')}</td>
            <td class="px-6 py-4 space-x-2">
              <button onclick="editBrand(${brand.id}, '${escapeHtml(brand.name).replace(/'/g, "\\'")}')" 
                      class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
              <button onclick="deleteBrand(${brand.id})" 
                      class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
            </td>
          `
          tbody.appendChild(row)
        })
      })
    } else {
      showModal(data.message || 'Failed to load brands', 'error')
    }
  } catch (error) {
    console.error('Error loading brands:', error)
    showModal('Error loading brands', 'error')
  }
}

// Lab requirement: Asynchronously invoke add_brand_action.php
document.getElementById('addBrandForm')?.addEventListener('submit', async (e) => {
  e.preventDefault()
  const formData = new FormData(e.target)
  const name = formData.get('name') || ''
  const messageDiv = document.getElementById('addMessage')

  // Lab requirement: Validate brand information
  const validation = validateBrandName(name)
  if (!validation.valid) {
    messageDiv.classList.remove('hidden')
    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
    messageDiv.textContent = validation.message
    showModal(validation.message, 'error')
    return
  }

  try {
    const response = await fetch('../actions/add_brand_action.php', {
      method: 'POST',
      body: formData,
    })

    const data = await response.json()
    messageDiv.classList.remove('hidden')

    if (data.success) {
      messageDiv.className = 'p-4 rounded-lg text-sm bg-green-100 text-green-700'
      messageDiv.textContent = 'Brand added successfully!'
      showModal('Brand added successfully!', 'success')
      e.target.reset()
      loadBrands()
    } else {
      messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
      messageDiv.textContent = data.message
      showModal(data.message || 'Failed to add brand', 'error')
    }
  } catch (error) {
    console.error('Error adding brand:', error)
    messageDiv.classList.remove('hidden')
    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
    messageDiv.textContent = 'An error occurred'
    showModal('An error occurred while adding brand', 'error')
  }
})

// Lab requirement: Asynchronously invoke update_brand_action.php
function editBrand(id, name) {
  document.getElementById('editBrandId').value = id
  document.getElementById('editBrandName').value = name
  document.getElementById('editBrandForm').classList.remove('hidden')
  
  // Scroll to edit form
  document.getElementById('editBrandForm').scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function cancelEdit() {
  document.getElementById('editBrandForm').classList.add('hidden')
  document.getElementById('updateBrandForm').reset()
  document.getElementById('updateMessage').classList.add('hidden')
}

document.getElementById('updateBrandForm')?.addEventListener('submit', async (e) => {
  e.preventDefault()
  const formData = new FormData(e.target)
  const name = formData.get('name') || ''
  const messageDiv = document.getElementById('updateMessage')

  // Lab requirement: Validate brand information
  const validation = validateBrandName(name)
  if (!validation.valid) {
    messageDiv.classList.remove('hidden')
    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
    messageDiv.textContent = validation.message
    showModal(validation.message, 'error')
    return
  }

  try {
    const response = await fetch('../actions/update_brand_action.php', {
      method: 'POST',
      body: formData,
    })

    const data = await response.json()
    messageDiv.classList.remove('hidden')

    if (data.success) {
      messageDiv.className = 'p-4 rounded-lg text-sm bg-green-100 text-green-700'
      messageDiv.textContent = 'Brand updated successfully!'
      showModal('Brand updated successfully!', 'success')
      cancelEdit()
      loadBrands()
    } else {
      messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
      messageDiv.textContent = data.message
      showModal(data.message || 'Failed to update brand', 'error')
    }
  } catch (error) {
    console.error('Error updating brand:', error)
    messageDiv.classList.remove('hidden')
    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
    messageDiv.textContent = 'An error occurred'
    showModal('An error occurred while updating brand', 'error')
  }
})

// Lab requirement: Asynchronously invoke delete_brand_action.php
async function deleteBrand(id) {
  if (!confirm('Are you sure you want to delete this brand?')) return

  const formData = new FormData()
  formData.append('id', id)

  try {
    const response = await fetch('../actions/delete_brand_action.php', {
      method: 'POST',
      body: formData,
    })

    const data = await response.json()
    if (data.success) {
      showModal('Brand deleted successfully!', 'success')
      loadBrands()
    } else {
      showModal(data.message || 'Failed to delete brand', 'error')
    }
  } catch (error) {
    console.error('Error deleting brand:', error)
    showModal('An error occurred while deleting brand', 'error')
  }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  }
  return String(text).replace(/[&<>"']/g, (m) => map[m])
}

// Load data on page load
document.addEventListener('DOMContentLoaded', () => {
  loadBrands()
})

