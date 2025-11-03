// Mobile Menu Toggle
const menuToggle = document.querySelector(".menu-toggle")
const navLinks = document.querySelector(".nav-links")

menuToggle.addEventListener("click", () => {
  navLinks.style.display = navLinks.style.display === "flex" ? "none" : "flex"
  navLinks.style.flexDirection = "column"
  navLinks.style.position = "absolute"
  navLinks.style.top = "100%"
  navLinks.style.left = "0"
  navLinks.style.right = "0"
  navLinks.style.backgroundColor = "white"
  navLinks.style.padding = "1rem"
  navLinks.style.boxShadow = "0 4px 6px rgba(0, 0, 0, 0.1)"
})

// Quick Add to Cart functionality (only for buttons without onclick)
const quickAddButtons = document.querySelectorAll(".quick-add:not([onclick])")
const cartCount = document.querySelector(".cart-count")
let itemCount = 0

quickAddButtons.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault()
    itemCount++
    if (cartCount) {
      cartCount.textContent = itemCount
    }

    // Add animation feedback
    button.textContent = "Added!"
    button.style.backgroundColor = "#8b7355"
    button.style.color = "white"

    setTimeout(() => {
      button.textContent = "Quick Add"
      button.style.backgroundColor = "white"
      button.style.color = "#1a1a1a"
    }, 1500)
  })
})

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault()
    const target = document.querySelector(this.getAttribute("href"))
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  })
})

// Newsletter form submission
const newsletterForm = document.querySelector(".newsletter-form")
if (newsletterForm) {
  newsletterForm.addEventListener("submit", (e) => {
    e.preventDefault()
    const email = newsletterForm.querySelector('input[type="email"]').value

    // In production, this would send to your PHP backend
    console.log("Newsletter subscription:", email)

    // Show success message
    alert("Thank you for subscribing to our newsletter!")
    newsletterForm.reset()
  })
}

// Intersection Observer for fade-in animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
}

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = "1"
      entry.target.style.transform = "translateY(0)"
    }
  })
}, observerOptions)

// Observe elements for animation
document.querySelectorAll(".product-card, .category-card, .collection-card").forEach((el) => {
  el.style.opacity = "0"
  el.style.transform = "translateY(20px)"
  el.style.transition = "opacity 0.6s ease, transform 0.6s ease"
  observer.observe(el)
})
