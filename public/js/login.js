// File: auth_lab_dbforlab/public/js/login.js
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm") || document.getElementById("login-form");
  const feedback = document.getElementById("loginFeedback") || document.getElementById("login-msg");

  const setMsg = (t, ok) => {
    if (!feedback) return;
    feedback.textContent = t;
    feedback.className = "text-sm mt-2 " + (ok ? "text-green-600" : "text-red-600");
  };
  const isValidEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v).toLowerCase());

  form?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email")?.value?.trim() || "";
    const password = document.getElementById("password")?.value || "";

    if (!isValidEmail(email)) return setMsg("Enter a valid email", false);
    if (!password) return setMsg("Password is required", false);

    try {
      const fd = new FormData();
      fd.append("email", email);
      fd.append("password", password);

      const res = await fetch("../actions/login_action.php", {
        method: "POST",
        body: fd,
        cache: "no-store",
      });

      const text = await res.text();
      let data;
      try { data = JSON.parse(text); } catch { data = { success: false, message: text || "Invalid server response" }; }

      const ok = data && (data.success === true || data.status === "success");
      if (!ok) return setMsg(data?.message || "Login failed", false);

      // role can be in data.data.role or data.role
      const payload = data.data || {};
      const role = (payload.role !== undefined) ? payload.role : data.role;

      const isAdmin =
        role === 1 || role === "1" ||
        (typeof role === "string" && ["admin","administrator","superadmin"].includes(role.toLowerCase()));

      setMsg("Welcome back!", true);
      const dest = isAdmin ? "../admin/category.php" : "../index.php";
      window.location.assign(dest);
    } catch (err) {
      setMsg("Network error", false);
    }
  });
});
