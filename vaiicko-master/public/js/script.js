/*
 Simple AJAX helpers for Menu page
 - Provides inline AJAX edit/delete for menu items using links with classes
   `ajax-edit` and `ajax-delete` and cards with id `menu-item-<id>`.
 - After a successful server response the script updates the DOM and
   writes a small notification into localStorage (key `menu-change`) so
   other tabs can apply the same change via the storage event.
 - The code degrades to normal links/forms if JavaScript is disabled.
*/

(function(){
    'use strict';

    // --- applyChange -------------------------------------------------------
    // Apply a change object received from another tab (via localStorage
    // storage event). The object shape is { t: 'edit'|'delete', p: { id, ... } }
    // - For 'delete' it removes the card element with id 'menu-item-<id>'
    // - For 'edit' it updates the .card-title and .card-text inside the card
    function applyChange(msg){
        if (!msg || !msg.t) return;
        if (msg.t === 'delete'){
            var id = msg.p && msg.p.id;
            if (!id) return;
            var el = document.getElementById('menu-item-' + id);
            if (el) el.remove();
        } else if (msg.t === 'edit'){
            var id2 = msg.p && msg.p.id;
            if (!id2) return;
            var el2 = document.getElementById('menu-item-' + id2);
            if (el2){
                var titleEl = el2.querySelector('.card-title');
                var textEl = el2.querySelector('.card-text');
                if (titleEl && typeof msg.p.title === 'string') titleEl.textContent = msg.p.title;
                if (textEl && typeof msg.p.text === 'string') textEl.innerHTML = (msg.p.text||'').replace(/\n/g,'<br>');
            }
        }
    }

    // --- notify ------------------------------------------------------------
    // Write a small packet to localStorage to notify other tabs.
    // Packet shape: { t: type, p: payload, ts: timestamp }
    function notify(type, payload){
        try{
            var packet = { t: type, p: payload, ts: Date.now() };
            localStorage.setItem('menu-change', JSON.stringify(packet));
        }catch(e){/*ignore*/}
    }

    // --- storage listener --------------------------------------------------
    // Listen for 'storage' events from other tabs/windows and apply changes.
    window.addEventListener('storage', function(e){
        if (!e.key || e.key !== 'menu-change') return;
        try{ var d = JSON.parse(e.newValue || '{}'); applyChange(d); } catch(err){ /* ignore parse errors */ }
    });

    // --- isHtmlLoginLike ---------------------------------------------------
    // Heuristic check whether an HTML response looks like a login page.
    // Used to avoid treating a redirected login page as a successful AJAX result.
    function isHtmlLoginLike(html){
        if (!html || typeof html !== 'string') return false;
        return /<form[^>]+(login|signin)|<input[^>]+name=["']?(password|pwd)["']?/i.test(html);
    }

    // --- parseResponse -----------------------------------------------------
    // Normalize and verify fetch responses:
    // - reject non-ok status
    // - accept PRG redirects that point back to the menu index as success
    // - return parsed JSON when content-type is application/json
    // - otherwise return an object { __html: text }
    function parseResponse(res){
        if (!res.ok) return Promise.reject(new Error('Request failed'));
        // allow redirect back to menu index as success
        if (res.redirected){
            try{ var u = new URL(res.url, location.origin); var qs = u.search || ''; if (qs.indexOf('c=menu') !== -1 || qs.indexOf('a=index') !== -1 || u.pathname.indexOf('menu') !== -1) return Promise.resolve({ ok: true }); } catch(e){}
            return Promise.reject(new Error('Redirected'));
        }
        var ct = (res.headers.get('content-type')||'').toLowerCase();
        if (ct.indexOf('application/json') !== -1) return res.json();
        return res.text().then(function(t){ return { __html: t }; });
    }

    // --- bindDeletes ------------------------------------------------------
    // Attach click handlers to links with class 'ajax-delete'. Handler:
    // - sends POST confirm=1 to the link href via fetch
    // - on verified success removes the card from DOM and notifies other tabs
    // - on failure shows a simple alert (keeps behavior small and explicit)
    function bindDeletes(){
        document.querySelectorAll('a.ajax-delete').forEach(function(a){
            if (a.__bound) return; a.__bound = true;
            a.addEventListener('click', function(ev){
                if (ev.button !== 0) return; ev.preventDefault();
                if (!confirm('Are you sure you want to delete this item?')) return;
                var id = a.dataset.id;
                fetch(a.href, { method: 'POST', body: new URLSearchParams({ confirm: '1' }), credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(parseResponse)
                    .then(function(data){
                        if (data && data.__html && isHtmlLoginLike(data.__html)) throw new Error('Login returned');
                        if (id){ var el = document.getElementById('menu-item-'+id); if (el) el.remove(); else location.reload(); }
                        else { var c = a.closest('.post') || a.closest('.card'); if (c) c.remove(); else location.reload(); }
                        notify('delete', { id: id });
                    })
                    .catch(function(){ alert('Delete failed or not authorized'); });
            });
        });
    }

    // --- bindEdits --------------------------------------------------------
    // Attach click handlers to links with class 'ajax-edit'. Handler:
    // - prompts the user for new title/text (quick inline edit)
    // - sends POST with title/text/submit=1 to href via fetch
    // - on verified success updates DOM and notifies other tabs
    function bindEdits(){
        document.querySelectorAll('a.ajax-edit').forEach(function(a){
            if (a.__boundEdit) return; a.__boundEdit = true;
            a.addEventListener('click', function(ev){
                if (ev.button !== 0) return; ev.preventDefault();
                var id = a.dataset.id; var el = document.getElementById('menu-item-'+id);
                var curTitle = el ? (el.querySelector('.card-title')||{}).textContent : '';
                var curText = el ? (el.querySelector('.card-text')||{}).textContent : '';
                var newTitle = prompt('Edit title:', curTitle); if (newTitle === null) return;
                var newText = prompt('Edit text:', curText); if (newText === null) return;
                var params = new URLSearchParams(); params.append('title', newTitle); params.append('text', newText); params.append('submit', '1');
                fetch(a.href, { method: 'POST', body: params, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(parseResponse)
                    .then(function(data){
                        if (data && data.__html && isHtmlLoginLike(data.__html)) throw new Error('Login returned');
                        if (el){ var t = el.querySelector('.card-title'); var x = el.querySelector('.card-text'); if (t) t.textContent = newTitle; if (x) x.innerHTML = newText.replace(/\n/g,'<br>'); }
                        else location.reload();
                        notify('edit', { id: id, title: newTitle, text: newText });
                    })
                    .catch(function(){ alert('Edit failed or not authorized'); });
            });
        });
    }

    // --- initialization ---------------------------------------------------
    function bindAll(){ bindDeletes(); bindEdits(); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bindAll); else bindAll();
})();
