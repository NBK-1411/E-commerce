(function(){
  const form = document.getElementById('register-form');
  const msg  = document.getElementById('reg-msg');
  function setMsg(text, ok){ msg.textContent = text; msg.className = 'note ' + (ok?'success':'error'); }
  form.addEventListener('submit', function(e){
    e.preventDefault();
    const data = new FormData(form);
    const email = data.get('email')||'';
    const contact = data.get('contact')||'';
    const password = data.get('password')||'';
    const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    const phoneOk = /^[0-9+\-\s]{7,20}$/.test(contact);
    if (!emailOk) return setMsg('Enter a valid email', false);
    if (!phoneOk) return setMsg('Enter a valid contact number', false);
    if (password.length < 8) return setMsg('Password must be at least 8 characters', false);
    fetch('../actions/register_customer_action.php', { method:'POST', body:data })
      .then(r=>r.json()).then(res=>{
        if(res.status==='success'){ setMsg('Registered! Redirecting...', true); setTimeout(()=>{ location.href='login.php'; }, 700); }
        else setMsg(res.message||'Registration failed', false);
      }).catch(()=> setMsg('Network error', false));
  });
})();