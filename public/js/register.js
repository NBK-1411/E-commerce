// File: auth_lab_dbforlab/public/js/register.js
document.addEventListener("DOMContentLoaded", () => {
  const form =
    document.getElementById("registerForm") ||
    document.getElementById("register-form") ||
    document.querySelector('form[action*="register"]');

  const feedback =
    document.getElementById("registerFeedback") ||
    document.getElementById("register-msg");

  const show = (t, ok) => {
    if (!feedback) return;
    feedback.textContent = t;
    feedback.className = "text-sm mt-2 " + (ok ? "text-green-600" : "text-red-600");
  };

  const isValidEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v).toLowerCase());

  form?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const fullName = document.getElementById("full_name")?.value?.trim()
      || document.getElementById("customer_name")?.value?.trim()
      || document.getElementById("name")?.value?.trim()
      || "";
    const email = document.getElementById("email")?.value?.trim() || "";
    const password = document.getElementById("password")?.value || "";
    const country = document.getElementById("country")?.value?.trim() || "";
    const city = document.getElementById("city")?.value?.trim() || "";
    const contact = document.getElementById("contact")?.value?.trim()
      || document.getElementById("contact_number")?.value?.trim()
      || document.getElementById("phone")?.value?.trim()
      || "";

    if (!fullName || !email || !password || !country || !city || !contact) return show("All fields are required", false);
    if (!isValidEmail(email)) return show("Enter a valid email", false);
    if (password.length < 8) return show("Password must be at least 8 characters", false);

    try {
      const fd = new FormData();
      fd.append("full_name", fullName);
      fd.append("email", email);
      fd.append("password", password);
      fd.append("country", country);
      fd.append("city", city);
      fd.append("contact", contact);

      const res = await fetch("../actions/register_customer_action.php", {
        method: "POST",
        body: fd,
        cache: "no-store",
      });

      const text = await res.text();
      let data;
      try { data = JSON.parse(text); } catch { data = { success: false, message: text || "Invalid server response" }; }

      if (data && (data.success === true || data.status === "success")) {
        show("Account created. Redirecting…", true);
        // Compute a reliable URL for login.php from the current page
        const loginUrl = new URL("login.php", window.location.origin + window.location.pathname).toString();
        window.location.assign(loginUrl);
      } else {
        show(data?.message || "Could not create account", false);
      }
    } catch (err) {
      show("Network error", false);
    }
  });
});
