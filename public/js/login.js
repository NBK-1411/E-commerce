(function(){
  const form = document.getElementById('login-form');
  const msg  = document.getElementById('login-msg');
  function setMsg(text, ok){ msg.textContent = text; msg.className = 'note ' + (ok?'success':'error'); }
  form.addEventListener('submit', function(e){
    e.preventDefault();
    const data = new FormData(form);
    fetch('../actions/login_action.php', { method:'POST', body:data })
      .then(r=>r.json()).then(res=>{
        if(res.status==='success'){ setMsg('Logged in! Redirecting...', true); setTimeout(()=>{ location.href='../index.php'; }, 700); }
        else setMsg(res.message||'Login failed', false);
      }).catch(()=> setMsg('Network error', false));
  });
})();