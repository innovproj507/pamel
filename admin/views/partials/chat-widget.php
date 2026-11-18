<?php
/**
 * Burbuja del chat para el manager: conversaciones del alumnado, hilo en globos
 * y respuesta sin salir del panel. En vivo por WebSocket (ver Services\ChatRealtime)
 * y, si no hay conexión, por sondeo. Solo se pinta para quien puede atender el chat.
 */
$cwUsuario = \Core\Auth::getInstance()->user();
if (!$cwUsuario || !(new \Plugins\Elearning\Models\Chat())->puedeAtender((int) $cwUsuario['id'])) { return; }
$cwToken = \Core\Security::generateCsrfToken();
?>
<style>
#cw-btn{position:fixed;right:22px;bottom:22px;z-index:60;width:56px;height:56px;border-radius:50%;background:#2563eb;color:#fff;border:0;cursor:pointer;box-shadow:0 8px 24px rgba(37,99,235,.4);display:flex;align-items:center;justify-content:center}
#cw-btn:hover{background:#1d4ed8}
@keyframes cwparpadeo{0%,100%{background:#2563eb;box-shadow:0 0 0 0 rgba(239,68,68,.6)}50%{background:#ef4444;box-shadow:0 0 0 14px rgba(239,68,68,0)}}
#cw-btn.cw-parpadea{animation:cwparpadeo 1s ease-in-out infinite}
#cw-badge{position:absolute;top:-4px;right:-4px;min-width:22px;height:22px;padding:0 5px;border-radius:99px;background:#ef4444;color:#fff;font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center}
#cw-badge.oculto{display:none}
#cw-panel{position:fixed;right:22px;bottom:92px;z-index:60;width:380px;max-width:calc(100vw - 24px);height:540px;max-height:calc(100vh - 112px);display:none;flex-direction:column;background:#fff;border-radius:16px;box-shadow:0 14px 44px rgba(10,22,40,.3);overflow:hidden}
#cw-panel.abierto{display:flex}
#cw-panel header{background:#0a1628;color:#fff;padding:12px 14px;display:flex;align-items:center;gap:10px}
#cw-panel header b{flex:1;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
#cw-panel header small{display:block;font-weight:600;opacity:.65;font-size:11px}
#cw-panel header button{color:#fff;opacity:.85;font-size:18px;line-height:1;background:none;border:0;cursor:pointer}
#cw-vivo{width:8px;height:8px;border-radius:50%;background:#94a3b8}
#cw-vivo.on{background:#22c55e}
#cw-body{flex:1;overflow-y:auto;background:#f1f5f9;padding:12px;display:flex;flex-direction:column;gap:8px}
.cw-item{background:#fff;border-radius:12px;padding:10px 12px;cursor:pointer;border:1px solid #e2e8f0;text-align:left;width:100%}
.cw-item:hover{border-color:#2563eb}
.cw-item b{font-size:13px;color:#0f172a;display:flex;justify-content:space-between;gap:8px}
.cw-item small{display:block;color:#64748b;font-size:11px;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cw-dot{background:#ef4444;color:#fff;border-radius:99px;font-size:10px;padding:1px 6px;flex:none}
.cw-msg{max-width:84%;padding:8px 12px;border-radius:14px;font-size:13px;line-height:1.35;white-space:pre-wrap;word-break:break-word}
.cw-msg.mio{align-self:flex-end;background:#2563eb;color:#fff;border-bottom-right-radius:4px}
.cw-msg.otro{align-self:flex-start;background:#fff;color:#0f172a;border-bottom-left-radius:4px;border:1px solid #e2e8f0}
.cw-msg i{display:block;font-style:normal;font-size:10px;font-weight:800;opacity:.65;margin-bottom:2px}
.cw-msg time{display:block;font-size:10px;opacity:.6;margin-top:2px;text-align:right}
#cw-form{display:flex;gap:8px;padding:10px;border-top:1px solid #e2e8f0;background:#fff;align-items:flex-end}
#cw-form textarea{flex:1;resize:none;border:1px solid #cbd5e1;border-radius:12px;padding:8px 10px;font-size:13px;max-height:90px;outline:none}
#cw-form textarea:focus{border-color:#2563eb}
#cw-form button{background:#2563eb;color:#fff;border:0;border-radius:12px;padding:8px 14px;font-weight:700;font-size:13px;cursor:pointer}
#cw-form button:disabled{opacity:.5}
.cw-aviso{text-align:center;color:#64748b;font-size:12px;padding:24px 10px}
.cw-err{color:#dc2626;font-size:12px;text-align:center}
#cw-toast{position:fixed;right:22px;bottom:92px;z-index:61;width:320px;max-width:calc(100vw - 24px);background:#0a1628;color:#fff;border-radius:14px;padding:12px 14px;box-shadow:0 12px 36px rgba(10,22,40,.35);cursor:pointer;display:none}
#cw-toast.visible{display:block;animation:cwsube .25s ease-out}
#cw-toast b{display:block;font-size:12px;color:#93c5fd;margin-bottom:2px}
#cw-toast span{font-size:13px;line-height:1.3;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
@keyframes cwsube{from{transform:translateY(10px);opacity:0}to{transform:none;opacity:1}}
</style>

<div id="cw-toast" role="status"></div>
<button type="button" id="cw-btn" title="Mensajes" aria-label="Mensajes">
    <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4-.8L3 20l1.3-3.9A7.6 7.6 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    <span id="cw-badge" class="oculto"></span>
</button>
<div id="cw-panel" role="dialog" aria-label="Mensajes">
    <header>
        <button type="button" id="cw-atras" style="display:none" aria-label="Volver">&#8592;</button>
        <b id="cw-titulo">Mensajes<small id="cw-sub"></small></b>
        <span id="cw-vivo" title="Sin conexión en vivo"></span>
        <button type="button" id="cw-cerrar" aria-label="Cerrar">&times;</button>
    </header>
    <div id="cw-body"></div>
    <form id="cw-form" style="display:none">
        <textarea id="cw-texto" rows="1" maxlength="2000" placeholder="Escribe tu respuesta…"></textarea>
        <button type="submit">Enviar</button>
    </form>
</div>

<script>
(function () {
    const TOKEN = <?= json_encode($cwToken) ?>;
    const $ = id => document.getElementById(id);
    const btn = $('cw-btn'), panel = $('cw-panel'), body = $('cw-body'), form = $('cw-form'), texto = $('cw-texto'),
          titulo = $('cw-titulo'), sub = $('cw-sub'), atras = $('cw-atras'), badge = $('cw-badge'), vivoPunto = $('cw-vivo'), toast = $('cw-toast');
    const H = { 'X-Requested-With': 'XMLHttpRequest' };
    let vista = 'lista', convId = 0, ultimo = 0, sondeo = null, enVivo = false, toastTimer = null;

    const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const hora = f => { const d = new Date(String(f).replace(' ', 'T')); return isNaN(d) ? '' : d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}); };
    const get = async u => { const r = await fetch(u, { credentials: 'same-origin', headers: H }); if (!r.ok) throw new Error(r.status); return r.json(); };

    function bajar() { body.scrollTop = body.scrollHeight; }
    function pararSondeo() { if (sondeo) { clearInterval(sondeo); sondeo = null; } }

    // La burbuja parpadea mientras haya mensajes sin leer y el panel esté cerrado
    function parpadeo() {
        const pendientes = !badge.classList.contains('oculto');
        btn.classList.toggle('cw-parpadea', pendientes && !panel.classList.contains('abierto'));
    }

    async function contar() {
        if (document.hidden) return;
        try {
            const n = parseInt((await get('/manager/chat/no-leidos')).count, 10) || 0;
            badge.textContent = n > 99 ? '99+' : String(n);
            badge.classList.toggle('oculto', n === 0);
            parpadeo();
            document.title = document.title.replace(/^\(\d+\)\s/, '');
            if (n > 0) document.title = '(' + n + ') ' + document.title;
        } catch (e) {}
    }

    async function mostrarLista() {
        vista = 'lista'; convId = 0; pararSondeo();
        atras.style.display = 'none'; form.style.display = 'none'; titulo.firstChild.textContent = 'Mensajes'; sub.textContent = '';
        body.innerHTML = '<div class="cw-aviso">…</div>';
        try {
            const d = await get('/manager/chat/widget');
            if (!d.conversaciones.length) { body.innerHTML = '<div class="cw-aviso">No hay conversaciones todavía. Cuando un alumno escriba, aparecerá aquí.</div>'; return; }
            body.innerHTML = d.conversaciones.map(c =>
                '<button type="button" class="cw-item" data-id="' + c.id + '"><b><span>' + esc(c.student_name) + ' · ' + esc(c.subject) + '</span>'
                + (c.sin_leer > 0 ? '<span class="cw-dot">' + c.sin_leer + '</span>' : '') + '</b><small>' + esc(c.course_title || 'Consulta general') + '</small></button>').join('');
            body.querySelectorAll('.cw-item').forEach(b => b.onclick = () => abrir(parseInt(b.dataset.id, 10)));
        } catch (e) { body.innerHTML = '<div class="cw-aviso cw-err">No se pudo cargar. Recarga la página.</div>'; }
    }

    function pintar(m) {
        const d = document.createElement('div');
        d.className = 'cw-msg ' + (m.mio ? 'mio' : 'otro'); d.dataset.id = m.id;
        d.innerHTML = (m.mio ? '' : '<i>' + esc(m.sender_name) + '</i>') + esc(m.body) + '<time>' + esc(hora(m.created_at)) + '</time>';
        body.appendChild(d); ultimo = Math.max(ultimo, parseInt(m.id, 10));
    }

    async function cargar(despues) {
        const d = await get('/manager/chat/' + convId + '/hilo?after=' + despues);
        if (!d.ok) return;
        if (despues === 0) {
            body.innerHTML = '';
            titulo.firstChild.textContent = d.conversacion.student_name || d.conversacion.subject;
            sub.textContent = (d.conversacion.subject || '') + (d.conversacion.course_title ? ' · ' + d.conversacion.course_title : '');
        }
        const abajo = body.scrollHeight - body.scrollTop - body.clientHeight < 60 || despues === 0;
        d.mensajes.forEach(m => { if (!body.querySelector('[data-id="' + m.id + '"]')) pintar(m); });
        if (abajo) bajar();
    }

    async function abrir(id) {
        vista = 'hilo'; convId = id; ultimo = 0; pararSondeo();
        atras.style.display = ''; form.style.display = 'flex';
        body.innerHTML = '<div class="cw-aviso">…</div>';
        try { await cargar(0); } catch (e) { body.innerHTML = '<div class="cw-aviso cw-err">No se pudo abrir la conversación.</div>'; return; }
        let tic = 0;
        sondeo = setInterval(() => {
            if (document.hidden) return;
            if (enVivo && ++tic % 12) return;      // con el chat en vivo basta un sondeo lento de seguridad
            cargar(ultimo).catch(() => {});
        }, 5000);
        texto.focus(); contar();
    }

    form.onsubmit = async ev => {
        ev.preventDefault();
        const cuerpo = texto.value.trim(); if (!cuerpo || !convId) return;
        const b = form.querySelector('button'); b.disabled = true;
        const f = new FormData(); f.append('csrf_token', TOKEN); f.append('body', cuerpo);
        let r = { ok: false, error: 'No se pudo enviar.' };
        try { r = await (await fetch('/manager/chat/' + convId + '/enviar', { method: 'POST', credentials: 'same-origin', headers: H, body: f })).json(); } catch (e) {}
        b.disabled = false;
        if (r.ok) { texto.value = ''; texto.style.height = ''; await cargar(ultimo).catch(() => {}); bajar(); }
        else { const e = document.createElement('div'); e.className = 'cw-err'; e.textContent = r.error || 'No se pudo enviar.'; body.appendChild(e); bajar(); }
    };
    texto.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.requestSubmit(); } });
    texto.addEventListener('input', () => { texto.style.height = 'auto'; texto.style.height = Math.min(texto.scrollHeight, 90) + 'px'; });

    atras.onclick = mostrarLista;
    $('cw-cerrar').onclick = () => { panel.classList.remove('abierto'); pararSondeo(); parpadeo(); };
    btn.onclick = () => {
        toast.classList.remove('visible');
        if (panel.classList.toggle('abierto')) { (vista === 'hilo' && convId) ? abrir(convId) : mostrarLista(); }
        else pararSondeo();
        parpadeo();
    };

    // ── Aviso emergente: quién escribió y qué ──
    async function avisar(cid) {
        try {
            const d = await get('/manager/chat/widget');
            const c = d.conversaciones.find(x => x.id === cid) || d.conversaciones.find(x => x.sin_leer > 0);
            if (!c || !(c.sin_leer > 0)) return;
            toast.innerHTML = '<b>Nuevo mensaje · ' + esc(c.student_name) + '</b><span>' + esc(c.ultimo || c.subject) + '</span>';
            toast.onclick = () => { toast.classList.remove('visible'); panel.classList.add('abierto'); abrir(c.id); };
            toast.classList.add('visible');
            clearTimeout(toastTimer); toastTimer = setTimeout(() => toast.classList.remove('visible'), 8000);
            btn.animate([{ transform: 'scale(1)' }, { transform: 'scale(1.18)' }, { transform: 'scale(1)' }], { duration: 500, iterations: 3 });
        } catch (e) {}
    }

    function alLlegarAviso(cid) {
        const abierto = panel.classList.contains('abierto');
        if (abierto && vista === 'hilo' && convId && (!cid || cid === convId)) {
            cargar(ultimo).then(() => contar()).catch(() => {});
            return;
        }
        contar();
        if (abierto && vista === 'lista') mostrarLista();
        if (!abierto && cid) avisar(cid);
    }

    // ── Conexión en vivo ──
    let ws = null, latido = null, espera = 2000;
    async function conectar() {
        let d;
        try { const r = await fetch('/manager/chat/ticket', { credentials: 'same-origin', headers: H }); if (!r.ok) return reintentar(); d = await r.json(); }
        catch (e) { return reintentar(); }
        if (!d.ok) return;                                  // chat en vivo apagado: queda el sondeo
        try { ws = new WebSocket(d.url + '?t=' + encodeURIComponent(d.t)); } catch (e) { return reintentar(); }
        ws.onopen = () => {
            enVivo = true; espera = 2000; vivoPunto.classList.add('on'); vivoPunto.title = 'En vivo';
            latido = setInterval(() => { if (ws.readyState === 1) ws.send('ping'); }, 30000);
        };
        ws.onmessage = ev => {
            if (ev.data === 'pong') return;
            try { const m = JSON.parse(ev.data); if (m.t === 'msg') alLlegarAviso(parseInt(m.cid, 10) || 0); } catch (e) {}
        };
        ws.onclose = () => { clearInterval(latido); enVivo = false; vivoPunto.classList.remove('on'); vivoPunto.title = 'Sin conexión en vivo'; reintentar(); };
        ws.onerror = () => { try { ws.close(); } catch (e) {} };
    }
    function reintentar() { setTimeout(conectar, espera); espera = Math.min(espera * 2, 60000); }

    contar();
    setInterval(() => { if (!enVivo) contar(); }, 30000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) { contar(); } });
    conectar();
})();
</script>
