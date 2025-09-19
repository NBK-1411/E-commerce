(function(){
  const form = document.getElementById('login-form');
  const msg  = document.getElementById('login-msg');

  function setMsg(t, ok){ msg.textContent=t; msg.className='text-sm mt-2 ' + (ok?'text-green-600':'text-red-600'); }

  form.addEventListener('submit', function(e){
    e.preventDefault();
    const data = new FormData(form);
    const email = (data.get('email')||'').trim();
    const password = data.get('password')||'';

    const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);  // type check
    if (!emailOk) return setMsg('Enter a valid email', false);
    if (!password) return setMsg('Password is required', false);

    fetch('../actions/login_customer_action.php', { method:'POST', body:data })
      .then(r=>r.json())
      .then(res=>{
        if(res.status==='success'){
          setMsg('Welcome back!', true);
          location.href = '../index.php'; // success → landing page (per spec)
        } else {
          setMsg(res.message || 'Login failed', false);
        }
      })
      .catch(()=> setMsg('Network error', false));
  });
})();
