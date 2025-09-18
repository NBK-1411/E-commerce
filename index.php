<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home</title>
  <!-- Tailwind (CDN, no build step) -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-6" style="background:linear-gradient(135deg,#ede9fe,#dbeafe)">
  <!-- top right nav -->
  <div class="absolute top-4 right-4 flex gap-3">
    <a href="public/register.php" class="text-gray-700 hover:text-gray-900 text-sm font-medium">Register</a>
    <a href="public/login.php" class="text-gray-700 hover:text-gray-900 text-sm font-medium">Login</a>
  </div>

  <div class="w-full max-w-xl">
    <div class="bg-white/95 backdrop-blur rounded-2xl shadow-xl border border-gray-100 p-10 text-center">
      <div class="mx-auto mb-4 h-12 w-12 rounded-xl bg-indigo-100 flex items-center justify-center">
        <!-- simple brand icon -->
        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
        </svg>
      </div>
      <h1 class="text-3xl font-semibold text-gray-900">Welcome</h1>
      <p class="text-sm text-gray-600 mt-2">
        Create an account or sign in to continue.
      </p>

      <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
        <a href="public/register.php"
           class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
          Create account
        </a>
        <a href="public/login.php"
           class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-800 shadow hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-200">
          Sign in
        </a>
      </div>

      <!-- optional quick links -->
      <div class="mt-6 text-xs text-gray-500">
        Having trouble? <a href="public/login.php" class="text-indigo-600 hover:text-indigo-700 font-medium">Try signing in</a>
      </div>
    </div>
  </div>
</body>
</html>
