(function(){
  'use strict';

  const apply = msg => {
    if(!msg || msg.t !== 'delete') return;
    const el = document.getElementById('menu-item-' + msg.id);
    if(el) el.remove();
  };

  // Ak iná karta zapíše localStorage, aplikujeme zmenu
  window.addEventListener('storage', e => {
    if(e.key !== 'menu-change') return;
    try { apply(JSON.parse(e.newValue)); } catch(_) {}
  });

  function bindDeletes(){
    document.querySelectorAll('a.ajax-delete').forEach(a => {
      if(a.__bound) return; a.__bound = true;
      a.addEventListener('click', ev => {
        ev.preventDefault();
        if(!confirm('Naozaj zmazat?')) return;
        const id = a.dataset.id;
        fetch(a.href, { method: 'POST', credentials: 'same-origin', headers: {'X-Requested-With':'XMLHttpRequest'} })
          .then(res => {
            if(!res.ok) throw new Error('server');
            if(id){ const el = document.getElementById('menu-item-' + id); if(el) el.remove(); }
            try { localStorage.setItem('menu-change', JSON.stringify({ t: 'delete', id: id, ts: Date.now() })); } catch(_) {}
          })
          .catch(() => alert('Zmazanie zlyhalo alebo nie si prihlaseny'));
      });
    });
  }

  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bindDeletes);
  else bindDeletes();
})();
