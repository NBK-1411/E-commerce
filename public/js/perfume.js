// Form section visibility
const addProductBtn = document.getElementById("addProductBtn")
const formSection = document.getElementById("addProductFormSection")
const closeFormBtn = document.getElementById("closeFormBtn")
const cancelFormBtn = document.getElementById("cancelFormBtn")
let editingProductId = null

function showForm(productData = null) {
  editingProductId = productData ? productData.id : null
  const form = document.getElementById("addPerfumeForm")
  const formTitle = document.querySelector(".add-product-form-section .admin-card-title")
  const formSubtitle = document.querySelector(".form-subtitle")
  const submitBtn = form.querySelector('button[type="submit"]')
  
  if (productData) {
    // Editing mode
    formTitle.textContent = "Edit Product"
    formSubtitle.textContent = "Update the product details below."
    submitBtn.textContent = "Update Product"
    
    // Populate form fields
    document.getElementById("name").value = productData.name || ""
    document.getElementById("price").value = productData.price || ""
    document.getElementById("original_price").value = productData.original_price || ""
    document.getElementById("description").value = productData.description || ""
    document.getElementById("notes").value = productData.notes || productData.description || ""
    document.getElementById("keyword").value = productData.keyword || ""
    document.getElementById("badge").value = productData.badge || ""
    document.getElementById("imageUrl").value = productData.image || ""
    document.getElementById("productId").value = productData.id || ""
    
    // Load categories as checkboxes and select the product's categories (multiple selection)
    // Ensure category_ids is an array of numbers
    let selectedCategoryIds = productData.category_ids || (productData.category_id ? [productData.category_id] : [])
    selectedCategoryIds = selectedCategoryIds.map(id => parseInt(id)).filter(id => !isNaN(id) && id > 0)
    
    console.log("Selected category IDs for edit:", selectedCategoryIds)
    
    // Show form first, then load categories
    formSection.classList.remove("hidden")
    
    // Load categories with selected IDs
    loadCategoriesForCheckboxes(selectedCategoryIds).then(() => {
      console.log("Categories loaded, now loading brands")
      // Load brands after categories are loaded
      setTimeout(() => {
        // Load brands for the first selected category (if any)
        const firstCategoryId = selectedCategoryIds.length > 0 ? selectedCategoryIds[0] : null
        loadBrandsForDropdown(firstCategoryId).then(() => {
          setTimeout(() => {
            const brandSelect = document.getElementById("brandSelect")
            if (brandSelect && productData.brand_id) {
              brandSelect.value = productData.brand_id
              console.log("Brand set to:", productData.brand_id)
            }
          }, 300)
        })
        
        // Also load all brands as fallback
        loadBrandsForDropdown().then(() => {
          setTimeout(() => {
            const brandSelect = document.getElementById("brandSelect")
            if (brandSelect && productData.brand_id) {
              brandSelect.value = productData.brand_id
            }
          }, 500)
        })
      }, 300)
    })
  } else {
    // Adding mode
    formTitle.textContent = "Add New Product"
    formSubtitle.textContent = "Fill in the details to add a new perfume."
    submitBtn.textContent = "Add Product"
    
    // Reset form
    form.reset()
    document.getElementById("productId").value = ""
    
    // Show form first, then load categories
    formSection.classList.remove("hidden")
    
    // Wait a moment for form to be visible, then load categories and brands
    // Ensure form section is visible first
    setTimeout(() => {
      const container = document.getElementById("categoryCheckboxes")
      console.log("Loading categories for new product, container exists:", !!container)
      
      if (container) {
        loadCategoriesForCheckboxes([]).then(() => {
          console.log("Categories loaded for new product")
        }).catch(err => {
          console.error("Error loading categories:", err)
        })
      } else {
        console.error("Category checkboxes container not found when adding product!")
        // Try again after a longer delay
        setTimeout(() => {
          loadCategoriesForCheckboxes([])
        }, 500)
      }
      
      loadBrandsForDropdown().then(() => {
        console.log("Brands loaded for new product")
      }).catch(err => {
        console.error("Error loading brands:", err)
      })
    }, 200)
  }
  
  // Scroll to form
  formSection.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function hideForm() {
  formSection.classList.add("hidden")
  const form = document.getElementById("addPerfumeForm")
  if (form) {
    form.reset()
  }
  document.getElementById("addMessage")?.classList.add("hidden")
  editingProductId = null
}

addProductBtn?.addEventListener("click", () => showForm())
closeFormBtn?.addEventListener("click", hideForm)
cancelFormBtn?.addEventListener("click", hideForm)

// Fetch and display perfumes
async function loadPerfumes() {
  try {
    const response = await fetch("../actions/fetch_perfume_action.php")
    const data = await response.json()

    if (data.success) {
      const tbody = document.getElementById("perfumesTable")
      const countEl = document.getElementById("productsCount")
      tbody.innerHTML = ""

      if (data.data && data.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">0 products found</td></tr>'
        if (countEl) countEl.textContent = "All Products (0)"
        return
      }

      if (countEl && data.data) {
        countEl.textContent = `All Products (${data.data.length})`
      }

      if (data.data && data.data.length > 0) {
        data.data.forEach((perfume) => {
          const row = document.createElement("tr")
          
          // Price display with original price if available
          let priceDisplay = `$${Number.parseFloat(perfume.price || 0).toFixed(2)}`
          if (perfume.original_price && parseFloat(perfume.original_price) > parseFloat(perfume.price)) {
            priceDisplay += ` <span class="price-strikethrough">$${Number.parseFloat(perfume.original_price).toFixed(2)}</span>`
          }
          
          // Badge display
          let badgeDisplay = "-"
          if (perfume.badge) {
            const badgeClass = perfume.badge.toLowerCase()
            badgeDisplay = `<span class="product-badge badge-${badgeClass}">${escapeHtml(perfume.badge)}</span>`
          }
          
          row.innerHTML = `
            <td><strong>${escapeHtml(perfume.name)}</strong></td>
            <td>${escapeHtml(perfume.category || "Uncategorized")}</td>
            <td>${priceDisplay}</td>
            <td>${escapeHtml(perfume.notes || perfume.description || "-")}</td>
            <td>${badgeDisplay}</td>
            <td>
              <div class="action-buttons">
                <button onclick="editProduct(${perfume.id})" class="action-btn" aria-label="Edit" title="Edit">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                  </svg>
                </button>
                <button onclick="deletePerfume(${perfume.id})" class="action-btn" aria-label="Delete" title="Delete">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  </svg>
                </button>
              </div>
            </td>
          `
          tbody.appendChild(row)
        })
      }
    } else {
      console.error("Failed to load perfumes:", data.message)
      const tbody = document.getElementById("perfumesTable")
      if (tbody) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-error">Error: ' + escapeHtml(data.message || 'Failed to load products') + '</td></tr>'
      }
    }
  } catch (error) {
    console.error("Error loading perfumes:", error)
    const tbody = document.getElementById("perfumesTable")
    if (tbody) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-error">Error loading products</td></tr>'
    }
  }
}

// Edit product - fetch product data and show form
async function editProduct(id) {
  try {
    const response = await fetch(`../actions/get_perfume_action.php?id=${id}`)
    const data = await response.json()
    
    if (data.success && data.data) {
      showForm(data.data)
    } else {
      alert("Failed to load product details")
    }
  } catch (error) {
    console.error("Error loading product:", error)
    alert("Error loading product details")
  }
}

// Lab requirement: Load brands for dropdown (organized by category)
async function loadBrandsForDropdown(categoryId = null) {
  try {
    const response = await fetch("../actions/fetch_brand_action.php")
    const data = await response.json()

    const select = document.getElementById("brandSelect")
    if (!select) {
      console.error("Brand select element not found!")
      return Promise.resolve()
    }

    if (data.success && data.data && Array.isArray(data.data)) {
      select.innerHTML = '<option value="">Select brand</option>'
      
      // Filter by category if provided, otherwise show all brands
      // Include brands with NULL category_id (available for all categories) when filtering
      const brands = categoryId 
        ? data.data.filter(b => b.category_id == categoryId || b.category_id == null)
        : data.data
      
      brands.forEach((brand) => {
        if (brand && brand.id && brand.name) {
          const option = document.createElement("option")
          option.value = brand.id
          option.textContent = brand.name
          select.appendChild(option)
        }
      })
      
      // If no brands found for selected category, show message
      if (categoryId && brands.length === 0) {
        const option = document.createElement("option")
        option.value = ""
        option.textContent = "⚠️ No brands in this category"
        option.disabled = true
        option.style.color = "#ef4444"
        select.appendChild(option)
      }
      
      return Promise.resolve()
    } else {
      select.innerHTML = '<option value="">Error loading brands</option>'
      return Promise.resolve()
    }
  } catch (error) {
    console.error("Error loading brands:", error)
    const select = document.getElementById("brandSelect")
    if (select) {
      select.innerHTML = '<option value="">Error loading brands</option>'
    }
    return Promise.resolve()
  }
}

// Load categories as checkboxes for multiple selection
async function loadCategoriesForCheckboxes(selectedIds = []) {
  try {
    // Ensure selectedIds is an array of numbers
    selectedIds = Array.isArray(selectedIds) ? selectedIds.map(id => parseInt(id)).filter(id => !isNaN(id) && id > 0) : []
    console.log("🔵 loadCategoriesForCheckboxes called with selected IDs:", selectedIds)
    
    const response = await fetch("../actions/fetch_category_action.php")
    console.log("🔵 Fetch response status:", response.status, response.statusText)
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    console.log("🔵 Category response data:", data)
    console.log("🔵 Response success:", data.success)
    console.log("🔵 Response data array:", data.data)
    console.log("🔵 Number of categories:", data.data ? data.data.length : 0)

    const container = document.getElementById("categoryCheckboxes")
    console.log("🔵 Container element:", container)
    
    if (!container) {
      console.error("❌ Category checkboxes container not found! Waiting and retrying...")
      // Retry after a short delay in case form isn't visible yet
      setTimeout(() => {
        const retryContainer = document.getElementById("categoryCheckboxes")
        if (retryContainer) {
          console.log("✅ Category checkboxes container found on retry")
          // Retry loading
          loadCategoriesForCheckboxes(selectedIds)
        } else {
          console.error("❌ Category checkboxes container still not found after retry")
        }
      }, 500)
      return Promise.resolve()
    }
    
    console.log("✅ Category checkboxes container found, loading categories")

    if (data.success) {
      // Clear existing content
      container.innerHTML = ''
      console.log("🔵 Cleared container, now checking data.data")
      
      if (data.data && Array.isArray(data.data) && data.data.length > 0) {
        console.log(`✅ Loading ${data.data.length} categories as checkboxes`)
        let loadedCount = 0
        
        data.data.forEach((category) => {
          console.log("🔵 Processing category:", category)
          
          if (category && category.id && category.name) {
            const checkboxItem = document.createElement("div")
            checkboxItem.className = "category-checkbox-item"
            
            const checkbox = document.createElement("input")
            checkbox.type = "checkbox"  // Checkboxes for multiple selection
            checkbox.name = "category_ids[]"  // Array notation for multiple values
            checkbox.value = category.id
            checkbox.id = `category_${category.id}`
            // Note: Required validation is handled in validateProduct() function
            // Checkboxes allow multiple categories to be selected
            
            // Check if this category is in selectedIds (for multiple selection)
            // Ensure both are compared as numbers
            const categoryId = parseInt(category.id)
            const selectedIdsAsNumbers = selectedIds.map(id => parseInt(id))
            if (selectedIdsAsNumbers.includes(categoryId)) {
              checkbox.checked = true
              console.log("✅ Checking category:", category.name, "with ID:", categoryId)
            }
            
            const label = document.createElement("label")
            label.htmlFor = `category_${category.id}`
            label.textContent = category.name
            
            checkboxItem.appendChild(checkbox)
            checkboxItem.appendChild(label)
            container.appendChild(checkboxItem)
            loadedCount++
            console.log(`✅ Added category checkbox: ${category.name} (ID: ${category.id})`)
          } else {
            console.warn("⚠️ Skipping invalid category:", category)
          }
        })
        console.log(`✅ Successfully loaded ${loadedCount} category checkboxes`)
        
        // Verify they're actually in the DOM
        const checkboxesInDOM = container.querySelectorAll('input[type="checkbox"]')
        console.log(`✅ Checkboxes in DOM: ${checkboxesInDOM.length}`)
      } else {
        console.warn("⚠️ No categories available in response or data.data is not an array")
        console.warn("⚠️ data.data type:", typeof data.data, "isArray:", Array.isArray(data.data))
        container.innerHTML = '<div style="text-align: center; color: #ef4444; padding: 1rem;">⚠️ No categories available. Please <a href="category.php" style="color: #ef4444; text-decoration: underline;">create a category first</a> before adding products.</div>'
      }
      
      return Promise.resolve()
    } else {
      console.error("❌ Failed to load categories:", data.message)
      container.innerHTML = '<div style="text-align: center; color: #ef4444; padding: 1rem;">Error loading categories: ' + escapeHtml(data.message || 'Failed to load') + '</div>'
      
      return Promise.resolve()
    }
  } catch (error) {
    console.error("❌ Error loading categories:", error)
    console.error("❌ Error stack:", error.stack)
    const container = document.getElementById("categoryCheckboxes")
    if (container) {
      container.innerHTML = '<div style="text-align: center; color: #ef4444; padding: 1rem;">Error: ' + escapeHtml(error.message) + '</div>'
    }
    return Promise.resolve()
  }
}

// Legacy function name for backward compatibility
// NOTE: This now loads checkboxes, not a dropdown
async function loadCategoriesForDropdown() {
  console.log("⚠️ loadCategoriesForDropdown() called - redirecting to loadCategoriesForCheckboxes()")
  // This function is kept for compatibility but now loads checkboxes
  return loadCategoriesForCheckboxes([])
}

// Lab requirement: Upload image file
async function uploadProductImage(file, productId = 0) {
  try {
    const formData = new FormData()
    formData.append("image", file)
    if (productId > 0) {
      formData.append("product_id", productId)
    }

    const response = await fetch("../actions/upload_product_image_action.php", {
      method: "POST",
      body: formData,
    })

    const data = await response.json()
    if (data.success) {
      return data.data.path // Return the image path
    } else {
      throw new Error(data.message || "Failed to upload image")
    }
  } catch (error) {
    console.error("Error uploading image:", error)
    throw error
  }
}

// Lab requirement: Validate product information (type checks, required fields)
function validateProduct(formData) {
  const name = formData.get("name")?.trim()
  const category_id = formData.get("category_id")
  const brand_id = formData.get("brand_id")
  const price = formData.get("price")
  const imageFile = formData.get("image")
  const imageUrl = formData.get("image_url")?.trim()

  // Required field checks
  if (!name || typeof name !== 'string' || name.length < 2) {
    return { valid: false, message: 'Product Title is required and must be at least 2 characters' }
  }

  // Check for category_ids (multiple categories)
  const categoryIds = formData.getAll("category_ids[]")
  if (!categoryIds || categoryIds.length === 0) {
    return { valid: false, message: 'At least one category is required' }
  }

  if (!brand_id || brand_id === '') {
    return { valid: false, message: 'Brand is required' }
  }

  const priceNum = parseFloat(price)
  if (!price || isNaN(priceNum) || priceNum <= 0) {
    return { valid: false, message: 'Product Price is required and must be a positive number' }
  }

  // Image validation: either file upload or URL must be provided
  if (!imageFile || imageFile.size === 0) {
    if (!imageUrl || imageUrl.trim() === '') {
      return { valid: false, message: 'Product Image is required (upload file or provide URL)' }
    }
  }

  // File type validation if file is provided
  if (imageFile && imageFile.size > 0) {
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']
    if (!allowedTypes.includes(imageFile.type)) {
      return { valid: false, message: 'Invalid image type. Only JPEG, PNG, GIF, and WebP are allowed' }
    }

    const maxSize = 5 * 1024 * 1024 // 5MB
    if (imageFile.size > maxSize) {
      return { valid: false, message: 'Image file size exceeds 5MB limit' }
    }
  }

  return { valid: true, message: 'Valid' }
}

// Add/Update perfume - Lab requirement: Async invoke add/update product action scripts
document.getElementById("addPerfumeForm")?.addEventListener("submit", async (e) => {
  e.preventDefault()
  const formData = new FormData(e.target)
  const messageDiv = document.getElementById("addMessage")

  // Lab requirement: Validate product information
  const validation = validateProduct(formData)
  if (!validation.valid) {
    messageDiv.classList.remove("hidden")
    messageDiv.className = "alert alert-error"
    messageDiv.textContent = validation.message
    showModal(validation.message, 'error')
    return
  }

  try {
    let imagePath = ""
    const imageFile = formData.get("image")
    const imageUrl = formData.get("image_url")?.trim()

    // Lab requirement: Upload image if file is provided
    if (imageFile && imageFile.size > 0) {
      messageDiv.classList.remove("hidden")
      messageDiv.className = "alert"
      messageDiv.textContent = "Uploading image..."
      
      try {
        imagePath = await uploadProductImage(imageFile, editingProductId || 0)
      } catch (uploadError) {
        messageDiv.className = "alert alert-error"
        messageDiv.textContent = uploadError.message || "Failed to upload image"
        showModal(uploadError.message || "Failed to upload image", 'error')
        return
      }
    } else if (imageUrl) {
      // Use provided URL
      imagePath = imageUrl
    }

    // Prepare form data for submission
    const formDataToSend = new FormData()
    formDataToSend.append("name", formData.get("name")?.trim())
    formDataToSend.append("brand_id", formData.get("brand_id"))
    
    // Collect all selected category IDs
    const categoryIds = formData.getAll("category_ids[]")
    categoryIds.forEach(catId => {
      formDataToSend.append("category_ids[]", catId)
    })
    
    formDataToSend.append("price", formData.get("price"))
    formDataToSend.append("description", formData.get("description")?.trim() || "")
    formDataToSend.append("image", imagePath)
    formDataToSend.append("keyword", formData.get("keyword")?.trim() || "")
    formDataToSend.append("notes", formData.get("notes")?.trim() || "")
    formDataToSend.append("badge", formData.get("badge")?.trim() || "")

    // If editing, add the ID
    if (editingProductId) {
      formDataToSend.append("id", editingProductId)
    }

    // Lab requirement: Use add_product_action.php or update_product_action.php
    const url = editingProductId 
      ? "../actions/update_product_action.php" 
      : "../actions/add_product_action.php"
    
    const response = await fetch(url, {
      method: "POST",
      body: formDataToSend,
    })

    // Check if response is ok before parsing JSON
    if (!response.ok) {
      const errorText = await response.text()
      throw new Error(`Server error (${response.status}): ${errorText.substring(0, 200)}`)
    }

    // Check if response is actually JSON
    const contentType = response.headers.get("content-type")
    if (!contentType || !contentType.includes("application/json")) {
      const text = await response.text()
      throw new Error(`Expected JSON but got: ${contentType}. Response: ${text.substring(0, 200)}`)
    }

    const data = await response.json()
    messageDiv.classList.remove("hidden")

    if (data.success) {
      messageDiv.className = "alert alert-success"
      messageDiv.textContent = editingProductId 
        ? "Product updated successfully!" 
        : "Product added successfully!"
      
      showModal(messageDiv.textContent, 'success')
      
      e.target.reset()
      loadCategoriesForCheckboxes([])
      loadBrandsForDropdown()
      loadPerfumes()
      
      // Hide form after 1.5 seconds
      setTimeout(() => {
        hideForm()
      }, 1500)
    } else {
      messageDiv.className = "alert alert-error"
      messageDiv.textContent = data.message || (editingProductId ? "Failed to update product" : "Failed to add product")
      showModal(messageDiv.textContent, 'error')
      console.error("Product operation error:", data)
    }
  } catch (error) {
    console.error("Error:", error)
    messageDiv.classList.remove("hidden")
    messageDiv.className = "alert alert-error"
    messageDiv.textContent = "An error occurred: " + error.message
    showModal("An error occurred: " + error.message, 'error')
  }
})

// Show modal/popup for messages
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
  
  setTimeout(() => {
    if (modal.parentElement) {
      modal.remove()
    }
  }, 3000)
}

// Delete perfume
async function deletePerfume(id) {
  if (!confirm("Are you sure you want to delete this product? This action cannot be undone.")) return

  const formData = new FormData()
  formData.append("id", id)

  try {
    const response = await fetch("../actions/delete_perfume_action.php", {
      method: "POST",
      body: formData,
    })

    const data = await response.json()
    if (data.success) {
      loadPerfumes()
    } else {
      alert(data.message || "Failed to delete product")
    }
  } catch (error) {
    console.error("Error deleting product:", error)
    alert("Error deleting product")
  }
}

// Escape HTML
function escapeHtml(text) {
  if (!text) return ""
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  }
  return String(text).replace(/[&<>"']/g, (m) => map[m])
}

// Update brands when category checkboxes change
// Listen to changes on the category checkboxes container
document.addEventListener('change', (e) => {
  if (e.target && e.target.type === 'checkbox' && e.target.name === 'category_ids[]') {
    // When a category checkbox is checked/unchecked, reload brands
    // Get the first checked category to filter brands
    const checkedCategories = Array.from(document.querySelectorAll('input[name="category_ids[]"]:checked'))
    const firstCategoryId = checkedCategories.length > 0 ? checkedCategories[0].value : null
    if (firstCategoryId) {
      loadBrandsForDropdown(parseInt(firstCategoryId))
    } else {
      // If no categories selected, show all brands
      loadBrandsForDropdown()
    }
  }
})

// Load data on page load
document.addEventListener("DOMContentLoaded", () => {
  // Hide form initially
  if (formSection) {
    formSection.classList.add("hidden")
  }
  
  // Load categories and brands immediately (as empty for now)
  loadCategoriesForCheckboxes([])
  loadBrandsForDropdown()
  loadPerfumes()
  
  // Check if there's an edit parameter in URL (from admin.php)
  const urlParams = new URLSearchParams(window.location.search)
  const editId = urlParams.get('edit')
  if (editId) {
    // Wait a bit for page to fully load, then load the product
    setTimeout(() => {
      editProduct(parseInt(editId))
    }, 800) // Wait for page and initial data to load
  }
  
  // Reload categories and brands when form is shown
  const originalShowForm = showForm
  showForm = function(productData) {
    const selectedIds = productData?.category_ids || (productData?.category_id ? [productData.category_id] : [])
    loadCategoriesForCheckboxes(selectedIds) // Reload categories as checkboxes when showing form
    const firstCategoryId = selectedIds.length > 0 ? selectedIds[0] : null
    loadBrandsForDropdown(firstCategoryId) // Load brands for first selected category
    originalShowForm(productData)
  }
})
