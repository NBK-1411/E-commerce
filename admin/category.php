<?php
// Admin-only guard
require_once __DIR__ . '/../settings/core.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Categories Management</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height:100vh; padding:20px; }
        .container { max-width:1200px; margin:0 auto; }
        .admin-panel { background:white; border-radius:16px; box-shadow:0 20px 40px rgba(0,0,0,0.1); overflow:hidden; }
        .header { background:linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color:white; padding:30px 40px; text-align:center; }
        .header h1 { font-size:2.5rem; font-weight:700; margin-bottom:10px; }
        .header p { opacity:0.9; font-size:1.1rem; }
        .main-content { display:flex; min-height:500px; }
        .form-section { flex:1; padding:40px; border-right:1px solid #e5e7eb; }
        .categories-section { flex:1; padding:40px; }
        .section-title { font-size:1.5rem; font-weight:600; color:#1f2937; margin-bottom:25px; padding-bottom:15px; border-bottom:2px solid #e5e7eb; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:8px; font-weight:600; color:#374151; }
        .form-group input { width:100%; padding:12px 16px; border:2px solid #e5e7eb; border-radius:8px; font-size:1rem; transition:border-color .3s ease; }
        .form-group input:focus { outline:none; border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,.1); }
        .form-actions { display:flex; gap:12px; margin-top:20px; }
        .btn { padding:12px 24px; border:none; border-radius:8px; font-size:1rem; font-weight:600; cursor:pointer; transition:all .3s ease; }
        .btn-primary { background:linear-gradient(135deg,#4f46e5 0%, #7c3aed 100%); color:white; }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 10px 20px rgba(79,70,229,.3); }
        .btn-secondary { background:#6b7280; color:white; }
        .btn-secondary:hover { background:#4b5563; }
        .btn-danger { background:#ef4444; color:white; }
        .btn-danger:hover { background:#dc2626; }
        .category-list { display:flex; flex-direction:column; gap:15px; }
        .category-item { background:#f9fafb; border:2px solid #e5e7eb; border-radius:12px; padding:20px; display:flex; justify-content:space-between; align-items:center; transition:all .3s ease; }
        .category-item:hover { border-color:#4f46e5; transform:translateX(5px); }
        .category-name { font-size:1.1rem; font-weight:600; color:#1f2937; }
        .category-actions { display:flex; gap:10px; }
        .category-actions button { padding:8px 16px; border:none; border-radius:6px; font-size:.9rem; font-weight:600; cursor:pointer; transition:all .2s ease; }
        .edit-btn { background:#3b82f6; color:white; }
        .edit-btn:hover { background:#2563eb; }
        .delete-btn { background:#ef4444; color:white; }
        .delete-btn:hover { background:#dc2626; }
        .empty-state { text-align:center; padding:40px; color:#6b7280; }
        .empty-state p { font-size:1.1rem; margin-top:10px; }
        .notification { position:fixed; top:20px; right:20px; padding:16px 24px; border-radius:8px; color:white; font-weight:600; box-shadow:0 10px 25px rgba(0,0,0,0.1); transform:translateX(200%); transition:transform .3s ease; z-index:1000; }
        .notification.show { transform:translateX(0); }
        .notification.success { background:linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .notification.error { background:linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        @media (max-width:768px){ .main-content{flex-direction:column;} .form-section{border-right:none; border-bottom:1px solid #e5e7eb;} .header h1{font-size:2rem;} }
    </style>
</head>
<body>
<div class="container">
  <div class="admin-panel">
    <div class="header">
      <h1>Categories Management</h1>
      <p>Manage your website categories efficiently</p>
      <!-- Logout button (top-right) -->
        <a href="../actions/logout.php"
            class="btn btn-secondary"
            style="position:absolute; top:16px; right:16px; text-decoration:none; display:inline-block;">
            Logout
        </a>
    </div>

    <div class="main-content">
      <div class="form-section">
        <h2 class="section-title">Add/Edit Category</h2>
        <form id="categoryForm">
          <input type="hidden" id="categoryId" value="">
          <div class="form-group">
            <label for="categoryName">Category Name</label>
            <input type="text" id="categoryName" placeholder="Enter category name" required />
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Category</button>
            <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
          </div>
        </form>
      </div>

      <div class="categories-section">
        <h2 class="section-title">Existing Categories</h2>
        <div id="categoriesList" class="category-list"></div>
      </div>
    </div>
  </div>
</div>

<div id="notification" class="notification"></div>

<script>
  // DOM refs
  const categoryForm = document.getElementById('categoryForm');
  const categoryIdInput = document.getElementById('categoryId');
  const categoryNameInput = document.getElementById('categoryName');
  const resetBtn = document.getElementById('resetBtn');
  const categoriesList = document.getElementById('categoriesList');
  const notification = document.getElementById('notification');

  // Helpers
  function showNotification(message, type) {
    notification.textContent = message;
    notification.className = `notification ${type} show`;
    setTimeout(() => notification.classList.remove('show'), 2500);
  }
  function resetForm() {
    categoryIdInput.value = '';
    categoryNameInput.value = '';
    categoryNameInput.focus();
  }
  function escapeHTML(s){return String(s).replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));}

  // Render list
  function renderCategories(rows) {
    if (!rows || rows.length === 0) {
      categoriesList.innerHTML = `
        <div class="empty-state">
          <h3>No categories found</h3>
          <p>Add your first category using the form above</p>
        </div>`;
      return;
    }
    categoriesList.innerHTML = rows.map(c => `
      <div class="category-item" data-id="${c.category_id}">
        <span class="category-name">${escapeHTML(c.category_name || '')}</span>
        <div class="category-actions">
          <button class="edit-btn" data-action="edit">Edit</button>
          <button class="delete-btn" data-action="delete">Delete</button>
        </div>
      </div>
    `).join('');
  }

  // API
  async function loadCategories() {
    try {
      const res = await fetch('../actions/fetch_category_action.php', { method:'POST' });
      const data = await res.json();
      if (!data.success) throw new Error(data.message || 'Failed to load');
      renderCategories(data.data || []);
    } catch (e) {
      renderCategories([]);
      showNotification(e.message || 'Error loading categories', 'error');
    }
  }
  async function addCategory(name) {
    const fd = new FormData(); fd.append('category_name', name);
    const res = await fetch('../actions/add_category_action.php', { method:'POST', body:fd });
    return res.json();
  }
  async function updateCategory(id, name) {
    const fd = new FormData(); fd.append('category_id', id); fd.append('category_name', name);
    const res = await fetch('../actions/update_category_action.php', { method:'POST', body:fd });
    return res.json();
  }
  async function deleteCategory(id) {
    const fd = new FormData(); fd.append('category_id', id);
    const res = await fetch('../actions/delete_category_action.php', { method:'POST', body:fd });
    return res.json();
  }

  // Events
  document.addEventListener('DOMContentLoaded', loadCategories);

  categoryForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = categoryIdInput.value.trim();
    const name = categoryNameInput.value.trim();
    if (!name) return showNotification('Category name cannot be empty!', 'error');

    try {
      const resp = id ? await updateCategory(id, name) : await addCategory(name);
      if (!resp.success) throw new Error(resp.message || 'Operation failed');
      showNotification(resp.message || (id ? 'Category updated' : 'Category added'), 'success');
      resetForm(); loadCategories();
    } catch (err) {
      showNotification(err.message || 'Request failed', 'error');
    }
  });

  resetBtn.addEventListener('click', resetForm);

  categoriesList.addEventListener('click', async (e) => {
    const btn = e.target.closest('button'); if (!btn) return;
    const row = e.target.closest('.category-item'); if (!row) return;
    const id = row.getAttribute('data-id');

    if (btn.dataset.action === 'edit') {
      const name = row.querySelector('.category-name').textContent;
      categoryIdInput.value = id;
      categoryNameInput.value = name;
      categoryNameInput.focus();
    }
    if (btn.dataset.action === 'delete') {
      if (!confirm('Are you sure you want to delete this category?')) return;
      try {
        const resp = await deleteCategory(id);
        if (!resp.success) throw new Error(resp.message || 'Delete failed');
        showNotification(resp.message || 'Category deleted', 'success');
        if (categoryIdInput.value === id) resetForm();
        loadCategories();
      } catch (err) {
        showNotification(err.message || 'Delete failed', 'error');
      }
    }
  });
</script>
</body>
</html>
