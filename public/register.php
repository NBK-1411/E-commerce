<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>

  <!-- Tailwind (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Tom Select (kept) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css">
  <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-6" style="background:linear-gradient(135deg,#ede9fe,#dbeafe)">
  <div class="absolute top-4 right-4 flex gap-3">
    <a href="../index.php" class="text-gray-700 hover:text-gray-900 text-sm font-medium">Home</a>
    <a href="login.php" class="text-gray-700 hover:text-gray-900 text-sm font-medium">Login</a>
  </div>

  <div class="w-full max-w-md">
    <div class="bg-white/95 backdrop-blur rounded-2xl shadow-xl border border-gray-100">
      <div class="px-8 pt-8">
        <button type="button" onclick="location.href='../index.php'" class="inline-flex items-center text-gray-500 hover:text-gray-800 text-sm">
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          Back
        </button>
        <h1 class="text-2xl font-semibold text-gray-900 mt-4">Create account</h1>
        <p class="text-sm text-gray-600 mt-1 mb-4">Enter your details to get started.</p>
      </div>

      <div class="px-8 pb-8">
        <form id="register-form" novalidate class="space-y-4">
          <!-- Full name -->
          <div class="space-y-1">
            <label class="text-sm font-medium text-gray-800" for="name">Full Name</label>
            <div class="relative">
              <svg class="absolute left-3 top-3.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <input id="name" name="name" type="text" placeholder="Enter your full name" required
                     class="w-full h-12 bg-gray-100 rounded-md pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
          </div>

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
              <input id="password" name="password" type="password" placeholder="Minimum 8 characters" required
                     class="w-full h-12 bg-gray-100 rounded-md pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
          </div>

          <!-- Country (Tom Select kept; no overlay icons to avoid drift) -->
          <div class="space-y-1">
            <label class="text-sm font-medium text-gray-800" for="country">Country</label>
            <div class="relative">
              <!-- Keep your select; Tom Select will replace/hide it -->
              <select id="country" name="country" required class="w-full">
                <!-- we include a blank option but DO NOT mark it selected;
                     we’ll clear selection in JS so TS shows placeholder -->
                <option value="">Select your country</option>
              </select>
            </div>
          </div>

          <!-- City -->
          <div class="space-y-1">
            <label class="text-sm font-medium text-gray-800" for="city">City</label>
            <div class="relative">
              <svg class="absolute left-3 top-3.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 21h18M5 21V8m4 13V4m4 17V12m4 9V6"/></svg>
              <input id="city" name="city" type="text" placeholder="City" required
                     class="w-full h-12 bg-gray-100 rounded-md pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
          </div>

          <!-- Contact -->
          <div class="space-y-1">
            <label class="text-sm font-medium text-gray-800" for="contact">Contact Number</label>
            <div class="relative">
              <svg class="absolute left-3 top-3.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92V21a1 1 0 0 1-1.09 1A19.86 19.86 0 0 1 3 5.09 1 1 0 0 1 4 4h4.09a1 1 0 0 1 1 .75l1 3a1 1 0 0 1-.29 1L8.4 10.6a16 16 0 0 0 5 5l1.85-1.39a1 1 0 0 1 1-.11l3 1a1 1 0 0 1 .65 1V21"/></svg>
              <input id="contact" name="contact" type="text" placeholder="+233 ..." required
                     class="w-full h-12 bg-gray-100 rounded-md pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
          </div>

          <input type="hidden" name="role" value="2">

          <button type="submit" class="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            Sign Up
          </button>
          <div id="reg-msg" class="text-sm mt-2 text-gray-600"></div>
        </form>

        <p class="text-sm text-gray-600 mt-6">
          Already have an account?
          <a href="login.php" class="text-indigo-600 hover:text-indigo-700 font-medium">Sign in</a>
        </p>
      </div>
    </div>
  </div>

  <!-- Countries + Tom Select init -->
  <script>
    (function(){
      const sel = document.getElementById('country');

      // ensure no option starts pre-selected (TS will show placeholder instead)
      for (const o of sel.options) o.selected = false;

      // populate options
      fetch('https://cdn.jsdelivr.net/npm/world-countries/countries.json')
        .then(r => r.json())
        .then(list => {
          list.sort((a,b)=> a.name.common.localeCompare(b.name.common));
          list.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.name.common;
            opt.textContent = c.name.common;
            sel.appendChild(opt);
          });

          const ts = new TomSelect('#country', {
            placeholder: 'Select your country',
            allowEmptyOption: true,
            searchField: ['text'],
            maxOptions: 1000,
            items: [] // start empty (prevents first option auto-select)
          });

          // Make TS look EXACTLY like your inputs (height/bg/radius/focus)
          ts.wrapper.classList.add('w-full');
          ts.control.classList.add('rounded-md');
          // add utility classes directly
          ts.control.style.background = '#f3f4f6';      // bg-gray-100
          ts.control.style.minHeight = '3rem';          // h-12
          ts.control.style.padding = '.75rem 1rem';     // px-4 py-3-ish
          ts.control.style.boxShadow = 'none';
        })
        .catch(() => {
          // offline fallback
          ['Ghana','Nigeria','Kenya','United Kingdom','United States'].forEach(c=>{
            const opt = document.createElement('option'); opt.value = c; opt.textContent = c; sel.appendChild(opt);
          });
          const ts = new TomSelect('#country', { placeholder:'Select your country', allowEmptyOption:true, items: [] });
          ts.wrapper.classList.add('w-full');
          ts.control.classList.add('rounded-md');
          ts.control.style.background = '#f3f4f6';
          ts.control.style.minHeight = '3rem';
          ts.control.style.padding = '.75rem 1rem';
          ts.control.style.boxShadow = 'none';
        });
    })();
  </script>

  <!-- LAST: high-specificity CSS overrides so nothing can override the look -->
  <style>
    .ts-wrapper{ width:100%; }
    .ts-control{
      background:#f3f4f6 !important;  /* match inputs */
      border:0 !important;
      border-radius:.5rem !important;  /* rounded-md */
      min-height:3rem;                 /* h-12 */
      padding:.75rem 1rem;             /* px-4 py-3 */
      box-shadow:none !important;
      display:flex; align-items:center;
      margin:.375rem 0;                /* my-1.5 */
    }
    .ts-wrapper.focus .ts-control{
      box-shadow:0 0 0 2px rgba(99,102,241,.35) !important; /* ring-2 ring-indigo-300 */
    }
    .ts-control .item, .ts-control input{
      padding:0;                       /* we set padding on the control */
      font-size:.875rem; line-height:1.25rem; /* text-sm */
    }
    .ts-control input::placeholder{ color:#9ca3af !important; } /* placeholder gray */
    .ts-wrapper.single .clear-button{ display:none; }
    .ts-dropdown{
      background:#fff; border:1px solid #e5e7eb; border-radius:.75rem;
      box-shadow:0 10px 20px rgba(0,0,0,.08); overflow:hidden;
    }
    .ts-dropdown .option{ font-size:.875rem; padding:.5rem .75rem; }
    .ts-dropdown .active{ background:#f1f5f9; color:#111827; }
  </style>

  <!-- Your existing JS (unchanged) -->
  <script src="js/register.js"></script>
</body>
</html>
