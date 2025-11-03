// Admin panel functionality
document.addEventListener("DOMContentLoaded", () => {
  // Sidebar toggle functionality
  const menuToggle = document.getElementById("sidebarToggle") || document.querySelector(".admin-menu-toggle")
  const sidebar = document.querySelector(".admin-sidebar")
  const mainContent = document.querySelector(".admin-main")

  // Sidebar starts CLOSED by default
  // Check localStorage for sidebar state (default is closed)
  const sidebarWasOpen = localStorage.getItem("sidebarOpen") === "true"

  if (sidebarWasOpen && sidebar && mainContent) {
    sidebar.classList.add("open")
    mainContent.classList.add("sidebar-open")
  }

  if (menuToggle && sidebar && mainContent) {
    menuToggle.addEventListener("click", (e) => {
      e.stopPropagation()
      sidebar.classList.toggle("open")
      mainContent.classList.toggle("sidebar-open")
      
      // Save state to localStorage
      localStorage.setItem("sidebarOpen", sidebar.classList.contains("open"))
    })
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", (event) => {
    if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains("open")) {
      if (!sidebar.contains(event.target) && !menuToggle?.contains(event.target)) {
        sidebar.classList.remove("open")
        if (mainContent) mainContent.classList.remove("sidebar-open")
        localStorage.setItem("sidebarOpen", "false")
      }
    }
  })

  // Active navigation highlighting
  const navItems = document.querySelectorAll(".admin-nav-item")
  navItems.forEach((item) => {
    item.addEventListener("click", function (e) {
      if (this.getAttribute("href").startsWith("#")) {
        e.preventDefault()
        navItems.forEach((nav) => nav.classList.remove("active"))
        this.classList.add("active")
      }
    })
  })

  // Search functionality
  const searchInput = document.querySelector(".admin-search input")
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      console.log("[v0] Search query:", this.value)
      // Add search functionality here
    })
  }
})
