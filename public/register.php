<?php
require_once __DIR__ . '/../settings/core.php';

if (is_logged_in()) {
    header('Location: /index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Perfume Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet"/>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-2xl p-8">
                <h1 class="text-3xl font-serif font-bold text-slate-900 mb-2">Create Account</h1>
                <p class="text-slate-600 mb-8">Join our luxury perfume community</p>

                <form id="registerForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Password (min 8 characters)</label>
                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Country</label>
                        <select name="country" id="countrySelect" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition">
                            <option value="">Select a country</option>
                            <option value="United States">United States</option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="Canada">Canada</option>
                            <option value="Australia">Australia</option>
                            <option value="France">France</option>
                            <option value="Germany">Germany</option>
                            <option value="Italy">Italy</option>
                            <option value="Spain">Spain</option>
                            <option value="Japan">Japan</option>
                            <option value="China">China</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">City</label>
                        <input type="text" name="city" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Contact Number</label>
                        <input type="tel" name="contact" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition">
                    </div>

                    <div id="message" class="hidden p-4 rounded-lg text-sm"></div>

                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 rounded-lg transition duration-200">
                        Create Account
                    </button>
                </form>

                <p class="text-center text-slate-600 mt-6">
                    Already have an account? <a href="/public/login.php" class="text-amber-600 hover:text-amber-700 font-semibold">Login here</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect('#countrySelect', {
            create: false,
            placeholder: 'Select a country'
        });

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const messageDiv = document.getElementById('message');

            try {
                const response = await fetch('/actions/register_customer_action.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                messageDiv.classList.remove('hidden');

                if (data.success) {
                    messageDiv.className = 'p-4 rounded-lg text-sm bg-green-100 text-green-700';
                    messageDiv.textContent = 'Registration successful! Redirecting to login...';
                    setTimeout(() => {
                        window.location.href = '/public/login.php';
                    }, 2000);
                } else {
                    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700';
                    messageDiv.textContent = data.message;
                }
            } catch (error) {
                messageDiv.classList.remove('hidden');
                messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700';
                messageDiv.textContent = 'An error occurred. Please try again.';
            }
        });
    </script>
</body>
</html>
