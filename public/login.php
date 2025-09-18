<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-muted/30 flex items-center justify-center p-6" style="background:linear-gradient(135deg,#ede9fe,#dbeafe)">
  <div class="absolute top-4 right-4 flex gap-3">
    <a href="../index.php" class="text-gray-700 hover:text-gray-900 text-sm font-medium">Home</a>
    <a href="register.php" class="text-gray-700 hover:text-gray-900 text-sm font-medium">Register</a>
  </div>

  <div class="w-full max-w-md">
    <div class="bg-white/95 backdrop-blur rounded-2xl shadow-xl border border-gray-100">
      <div class="px-8 pt-8">
        <button type="button" onclick="location.href='../index.php'" class="inline-flex items-center text-gray-500 hover:text-gray-800 text-sm">
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          Back
        </button>
        <h1 class="text-2xl font-semibold text-gray-900 mt-4">Sign in</h1>
        <p class="text-sm text-gray-600 mt-1 mb-4">Use your email and password.</p>
      </div>

      <div class="px-8 pb-8">
        <form id="login-form" novalidate class="space-y-4">
          <!-- Email -->
          <div class="space-y-1">
            <label class="text-sm font-medium text-gray-800" for="email">Email</label>
            <div class="relative">
              <svg class="absolute left-3 top-3.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M21 8v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8"/></svg>
              <input id="email" name="email" type="email" placeholder="you@example.com" required
                class="w-full h-12 bg-gray-100 rounded-md pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
          </div>

          <!-- Password -->
          <div class="space-y-1">
            <label class="text-sm font-medium text-gray-800" for="password">Password</label>
            <div class="relative">
              <svg class="absolute left-3 top-3.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="12" height="8" x="6" y="11" rx="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4"/></svg>
              <input id="password" name="password" type="password" placeholder="Your password" required
                class="w-full h-12 bg-gray-100 rounded-md pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
          </div>

          <button type="submit" class="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            Sign In
          </button>
          <div id="login-msg" class="text-sm mt-2 text-gray-600"></div>
        </form>

        <p class="text-sm text-gray-600 mt-6">
          New here?
          <a href="register.php" class="text-indigo-600 hover:text-indigo-700 font-medium">Create an account</a>
        </p>
      </div>
    </div>
  </div>

  <script src="js/login.js"></script>
</body>
</html>
