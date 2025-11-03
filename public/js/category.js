// Lab requirement: Validate category information, check type
function validateCategoryName(name) {
  if (!name || name.trim() === '') {
    return { valid: false, message: 'Category name is required' }
  }
  
  if (typeof name !== 'string') {
    return { valid: false, message: 'Category name must be a string' }
  }
  
  if (name.length < 2) {
    return { valid: false, message: 'Category name must be at least 2 characters' }
  }
  
  if (name.length > 100) {
    return { valid: false, message: 'Category name must be less than 100 characters' }
  }
  
  // Check for valid characters (letters, numbers, spaces, hyphens)
  if (!/^[a-zA-Z0-9\s\-]+$/.test(name)) {
    return { valid: false, message: 'Category name contains invalid characters' }
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

// Lab requirement: Asynchronously invoke fetch_category_action.php
async function loadCategories() {
  try {
    const response = await fetch('../actions/fetch_category_action.php')
    const data = await response.json()

    if (data.success) {
      const tbody = document.getElementById('categoriesTable')
      tbody.innerHTML = ''

      if (data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-4 text-center text-slate-500">No categories found</td></tr>'
        return
      }

      data.data.forEach((category) => {
        const row = document.createElement('tr')
        row.innerHTML = `
          <td class="px-6 py-4">${category.id}</td>
          <td class="px-6 py-4">${escapeHtml(category.name)}</td>
          <td class="px-6 py-4 space-x-2">
            <button onclick="editCategory(${category.id}, '${escapeHtml(category.name).replace(/'/g, "\\'")}')" 
                    class="text-blue-600 hover:text-blue-800 font-semibold">Edit</button>
            <button onclick="deleteCategory(${category.id})" 
                    class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
          </td>
        `
        tbody.appendChild(row)
      })
    } else {
      showModal(data.message || 'Failed to load categories', 'error')
    }
  } catch (error) {
    console.error('Error loading categories:', error)
    showModal('Error loading categories', 'error')
  }
}

// Lab requirement: Asynchronously invoke add_category_action.php
document.getElementById('addCategoryForm')?.addEventListener('submit', async (e) => {
  e.preventDefault()
  const formData = new FormData(e.target)
  const name = formData.get('name') || ''
  const messageDiv = document.getElementById('addMessage')

  // Lab requirement: Validate category information
  const validation = validateCategoryName(name)
  if (!validation.valid) {
    messageDiv.classList.remove('hidden')
    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
    messageDiv.textContent = validation.message
    showModal(validation.message, 'error')
    return
  }

  try {
    const response = await fetch('../actions/add_category_action.php', {
      method: 'POST',
      body: formData,
    })

    const data = await response.json()
    messageDiv.classList.remove('hidden')

    if (data.success) {
      messageDiv.className = 'p-4 rounded-lg text-sm bg-green-100 text-green-700'
      messageDiv.textContent = 'Category added successfully!'
      showModal('Category added successfully!', 'success')
      e.target.reset()
      loadCategories()
    } else {
      messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
      messageDiv.textContent = data.message
      showModal(data.message || 'Failed to add category', 'error')
    }
  } catch (error) {
    console.error('Error adding category:', error)
    messageDiv.classList.remove('hidden')
    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
    messageDiv.textContent = 'An error occurred'
    showModal('An error occurred while adding category', 'error')
  }
})

// Lab requirement: Asynchronously invoke update_category_action.php
function editCategory(id, name) {
  document.getElementById('editCategoryId').value = id
  document.getElementById('editCategoryName').value = name
  document.getElementById('editCategoryForm').classList.remove('hidden')
}

function cancelEdit() {
  document.getElementById('editCategoryForm').classList.add('hidden')
  document.getElementById('updateCategoryForm').reset()
  document.getElementById('updateMessage').classList.add('hidden')
}

document.getElementById('updateCategoryForm')?.addEventListener('submit', async (e) => {
  e.preventDefault()
  const formData = new FormData(e.target)
  const name = formData.get('name') || ''
  const messageDiv = document.getElementById('updateMessage')

  // Lab requirement: Validate category information
  const validation = validateCategoryName(name)
  if (!validation.valid) {
    messageDiv.classList.remove('hidden')
    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
    messageDiv.textContent = validation.message
    showModal(validation.message, 'error')
    return
  }

  try {
    const response = await fetch('../actions/update_category_action.php', {
      method: 'POST',
      body: formData,
    })

    const data = await response.json()
    messageDiv.classList.remove('hidden')

    if (data.success) {
      messageDiv.className = 'p-4 rounded-lg text-sm bg-green-100 text-green-700'
      messageDiv.textContent = 'Category updated successfully!'
      showModal('Category updated successfully!', 'success')
      cancelEdit()
      loadCategories()
    } else {
      messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
      messageDiv.textContent = data.message
      showModal(data.message || 'Failed to update category', 'error')
    }
  } catch (error) {
    console.error('Error updating category:', error)
    messageDiv.classList.remove('hidden')
    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700'
    messageDiv.textContent = 'An error occurred'
    showModal('An error occurred while updating category', 'error')
  }
})

// Lab requirement: Asynchronously invoke delete_category_action.php
async function deleteCategory(id) {
  if (!confirm('Are you sure you want to delete this category?')) return

  const formData = new FormData()
  formData.append('id', id)

  try {
    const response = await fetch('../actions/delete_category_action.php', {
      method: 'POST',
      body: formData,
    })

    const data = await response.json()
    if (data.success) {
      showModal('Category deleted successfully!', 'success')
      loadCategories()
    } else {
      showModal(data.message || 'Failed to delete category', 'error')
    }
  } catch (error) {
    console.error('Error deleting category:', error)
    showModal('An error occurred while deleting category', 'error')
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

// Load categories on page load
loadCategories()
