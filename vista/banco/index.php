<?php if(!isset($_SESSION['user_id'])){ header("Location: " . BASE_URL . "login"); exit(); } ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo BASE_URL; ?>">
    <title>Banco de Preguntas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root{--bg:#f2f2f7;--surface:#fff;--surface2:#f2f2f7;--border:rgba(0,0,0,.08);--border-strong:rgba(0,0,0,.13);--text:#1c1c1e;--text2:#636366;--text3:#aeaeb2;--accent:#007aff;--al:rgba(0,122,255,.09);--green:#34c759;--red:#ff3b30;--orange:#ff9500;--purple:#af52de;--tinder:#fe3c72;--sh:0 1px 3px rgba(0,0,0,.06);--shm:0 4px 16px rgba(0,0,0,.08);--r:18px;--rs:12px;--nav:64px;}
        *{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased;}
        body{font-family:'DM Sans',-apple-system,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}
        .navbar{position:sticky;top:0;z-index:200;height:var(--nav);background:rgba(255,255,255,.85);backdrop-filter:blur(24px);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 24px;}
        .nav-left{display:flex;align-items:center;gap:12px;}
        .nav-logo{width:34px;height:34px;background:linear-gradient(135deg,#1c1c1e,#3a3a3c);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:14px;}
        .nav-title{font-size:17px;font-weight:600;letter-spacing:-.3px;}
        .nav-close{display:flex;align-items:center;gap:6px;background:rgba(255,59,48,.08);color:var(--red);border:none;padding:7px 14px;border-radius:20px;font-family:inherit;font-size:13px;font-weight:500;cursor:pointer;}
        .nav-close:hover{background:rgba(255,59,48,.15);}
        .page-header{background:linear-gradient(145deg,#1c1c1e,#2c2c2e);padding:28px 24px 24px;position:relative;overflow:hidden;}
        .page-header::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 85% 0%,rgba(175,82,222,.25),transparent 55%);}
        .ph-inner{max-width:1000px;margin:0 auto;position:relative;}
        .ph-eyebrow{font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:6px;}
        .ph-title{font-family:'DM Serif Display',serif;font-size:24px;color:white;letter-spacing:-.5px;}
        .ph-sub{font-size:13px;color:rgba(255,255,255,.4);margin-top:4px;}
        /* SECTION SELECTOR - FIX BUG #4 */
        .section-selector-bar{background:white;border-bottom:1px solid var(--border);padding:14px 24px;}
        .section-selector-inner{max-width:1000px;margin:0 auto;display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
        .section-selector-label{font-size:13px;font-weight:600;color:var(--text2);}
        .section-select{padding:8px 14px;border-radius:10px;border:1.5px solid var(--border-strong);background:var(--surface);font-family:inherit;font-size:13px;color:var(--text);outline:none;min-width:200px;}
        .section-select:focus{border-color:var(--accent);}
        .section-hint{font-size:12px;color:var(--text3);}
        .content{max-width:1000px;margin:0 auto;padding:20px 16px;}
        .filters{display:flex;align-items:center;gap:10px;margin-bottom:18px;flex-wrap:wrap;}
        .filter-select{padding:8px 14px;border-radius:10px;border:1.5px solid var(--border-strong);background:var(--surface);font-family:inherit;font-size:13px;color:var(--text);outline:none;}
        .search-box{display:flex;align-items:center;gap:8px;background:var(--surface);border:1.5px solid var(--border-strong);border-radius:12px;padding:7px 14px;flex:1;max-width:300px;}
        .search-box i{color:var(--text3);font-size:13px;}
        .search-box input{border:none;outline:none;font-family:inherit;font-size:14px;color:var(--text);background:transparent;width:100%;}
        .count-badge{margin-left:auto;font-size:12px;color:var(--text3);background:var(--surface2);padding:5px 12px;border-radius:20px;}
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:12px;}
        .q-card{background:var(--surface);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--border);overflow:hidden;transition:box-shadow .2s,transform .2s;}
        .q-card:hover{box-shadow:var(--shm);transform:translateY(-2px);}
        .q-card-top{padding:16px 18px 12px;}
        .q-card-meta{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
        .q-type-tag{display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;}
        .q-type-tag.texto_corto,.q-type-tag.texto_largo{background:var(--al);color:var(--accent);}
        .q-type-tag.seleccion_unica{background:rgba(52,199,89,.1);color:var(--green);}
        .q-type-tag.seleccion_multiple{background:rgba(255,149,0,.1);color:var(--orange);}
        .q-type-tag.tinder{background:rgba(254,60,114,.12);color:var(--tinder);}
        .q-uso{font-size:11px;color:var(--text3);}
        .q-text{font-size:14px;font-weight:500;color:var(--text);line-height:1.4;margin-bottom:8px;}
        .q-options{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px;}
        .q-opt{font-size:11px;background:var(--surface2);color:var(--text2);padding:3px 9px;border-radius:20px;border:1px solid var(--border);}
        .q-cat{display:inline-flex;align-items:center;gap:4px;font-size:11px;color:var(--text3);background:var(--surface2);padding:3px 9px;border-radius:20px;}
        .q-card-actions{padding:10px 14px;border-top:1px solid var(--border);display:flex;gap:6px;}
        .q-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:9px 8px;border-radius:9px;font-family:inherit;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:all .15s;}
        .q-btn-import{background:var(--al);color:var(--accent);}
        .q-btn-import:hover{background:rgba(0,122,255,.16);}
        .q-btn-import:disabled{opacity:.4;cursor:not-allowed;}
        .q-btn-del{background:rgba(255,59,48,.08);color:var(--red);}
        .q-btn-del:hover{background:rgba(255,59,48,.15);}
        .empty{text-align:center;padding:60px 20px;color:var(--text3);grid-column:1/-1;}
        .empty i{font-size:48px;margin-bottom:16px;opacity:.2;display:block;}
        .toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--text);color:white;padding:12px 20px;border-radius:30px;font-size:14px;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.25);display:flex;align-items:center;gap:9px;z-index:1000;transition:transform .3s cubic-bezier(.34,1.56,.64,1),opacity .3s;opacity:0;}
        .toast.show{transform:translateX(-50%) translateY(0);opacity:1;}
        .toast.success .ti{color:var(--green);}
        .toast.error .ti{color:var(--red);}
        /* No-section warning */
        .no-section-warn{background:rgba(255,149,0,.08);border:1px solid rgba(255,149,0,.25);border-radius:12px;padding:12px 16px;font-size:13px;color:var(--orange);display:flex;align-items:center;gap:8px;margin-bottom:16px;}
    </style>
</head>
<body>
<nav class="navbar">
    <div class="nav-left">
        <div class="nav-logo"><i class="fas fa-database"></i></div>
        <span class="nav-title">Banco de Preguntas</span>
    </div>
    <button class="nav-close" onclick="window.close()"><i class="fas fa-times"></i> Cerrar</button>
</nav>

<div class="page-header">
    <div class="ph-inner">
        <div class="ph-eyebrow"><i class="fas fa-database"></i> Repositorio</div>
        <h1 class="ph-title">Banco de Preguntas</h1>
        <p class="ph-sub">Selecciona una sección y luego importa preguntas</p>
    </div>
</div>

<!-- FIX BUG #4: selector de sección ANTES de importar -->
<div class="section-selector-bar">
    <div class="section-selector-inner">
        <span class="section-selector-label"><i class="fas fa-layer-group" style="color:var(--accent);margin-right:5px;"></i>Importar a sección:</span>
        <select class="section-select" id="encabezadoDestino">
            <option value="">— Selecciona una sección —</option>
        </select>
        <span class="section-hint" id="section-hint">Elige la sección donde se agregará la pregunta importada</span>
    </div>
</div>

<div class="content">
    <div id="no-section-warn" class="no-section-warn" style="display:none;">
        <i class="fas fa-exclamation-triangle"></i> Selecciona una sección arriba antes de importar.
    </div>
    <div class="filters">
        <select class="filter-select" id="filtroCategoria" onchange="cargarPreguntas()">
            <option value="">Todas las categorías</option>
            <?php foreach($categorias as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
            <?php endforeach; ?>
        </select>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar pregunta..." oninput="filtrarLocal()">
        </div>
        <span class="count-badge" id="count-badge">— preguntas</span>
    </div>
    <div class="grid" id="grid-preguntas">
        <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text3);"><i class="fas fa-spinner fa-spin" style="font-size:24px;"></i></div>
    </div>
</div>

<div id="toast" class="toast"><span class="ti" id="ti"></span><span id="tmsg"></span></div>

<script>
// FIX BUG #4: encuesta_id viene de la URL, pero también necesitamos encabezado_id
// El usuario lo selecciona en el dropdown de secciones
const encuesta_id = <?php echo isset($_GET['encuesta_id']) ? intval($_GET['encuesta_id']) : 'null'; ?>;
let todasPreguntas = [];

const tipoLabel = {texto_corto:'Texto corto',texto_largo:'Texto largo',seleccion_unica:'Única',seleccion_multiple:'Múltiple',checkbox:'Casilla',tinder:'Card Slider',matriz:'Matriz'};

// Cargar secciones disponibles de la encuesta actual
function cargarSecciones() {
    if(!encuesta_id) return;
    fetch(`index.php?action=banco&subaction=listar_encabezados&encuesta_id=${encuesta_id}`)
        .then(r=>r.json()).then(data=>{
            if(!data.success) return;
            const sel = document.getElementById('encabezadoDestino');
            sel.innerHTML = '<option value="">— Selecciona una sección —</option>';
            data.data.forEach(enc => {
                sel.innerHTML += `<option value="${enc.id}">${escapeHtml(enc.titulo)}</option>`;
            });
        });
}

function cargarPreguntas() {
    const cat = document.getElementById('filtroCategoria').value;
    let url = 'index.php?action=banco&subaction=listar_preguntas_banco';
    if(cat) url += `&categoria=${encodeURIComponent(cat)}`;
    fetch(url).then(r=>r.json()).then(data=>{
        if(data.success){ todasPreguntas=data.data; filtrarLocal(); }
    });
}

function filtrarLocal() {
    const term = document.getElementById('searchInput').value.toLowerCase();
    const resultado = term ? todasPreguntas.filter(p=>p.pregunta.toLowerCase().includes(term)) : todasPreguntas;
    renderPreguntas(resultado);
}

function renderPreguntas(preguntas) {
    const grid = document.getElementById('grid-preguntas');
    document.getElementById('count-badge').textContent = `${preguntas.length} pregunta${preguntas.length!==1?'s':''}`;
    if(!preguntas.length){grid.innerHTML='<div class="empty"><i class="fas fa-inbox"></i><p>No hay preguntas guardadas</p></div>';return;}
    grid.innerHTML='';
    preguntas.forEach(preg=>{
        let opts='',optsArr=[];
        if(preg.opciones){try{optsArr=JSON.parse(preg.opciones);}catch(e){}}
        if(optsArr.length && preg.tipo_pregunta !== 'tinder'){
            const visible=optsArr.slice(0,3);
            opts=`<div class="q-options">${visible.map(o=>`<span class="q-opt">${escapeHtml(o)}</span>`).join('')}${optsArr.length>3?`<span class="q-opt">+${optsArr.length-3}</span>`:''}</div>`;
        }
        if(preg.tipo_pregunta==='tinder'&&optsArr.length){
            opts=`<div class="q-options">${optsArr.slice(0,3).map(c=>`<span class="q-opt" style="color:var(--tinder);border-color:rgba(254,60,114,.2);">🃏 ${escapeHtml((c.titulo||'').substring(0,15))}</span>`).join('')}${optsArr.length>3?`<span class="q-opt">+${optsArr.length-3}</span>`:''}</div>`;
        }
        const tipo = preg.tipo_pregunta;
        const card=document.createElement('div');card.className='q-card';
        card.innerHTML=`
            <div class="q-card-top">
                <div class="q-card-meta">
                    <span class="q-type-tag ${tipo}"><i class="fas fa-tag"></i>${tipoLabel[tipo]||tipo}</span>
                    <span class="q-uso"><i class="fas fa-chart-line"></i> ${preg.veces_usado} usos</span>
                </div>
                <div class="q-text">${escapeHtml(preg.pregunta)}</div>
                ${opts}
                ${preg.categoría?`<span class="q-cat"><i class="fas fa-folder"></i>${escapeHtml(preg.categoría)}</span>`:''}
            </div>
            <div class="q-card-actions">
                <button class="q-btn q-btn-import" onclick="importarPregunta(${preg.id})" id="import-btn-${preg.id}"><i class="fas fa-download"></i> Importar</button>
                <button class="q-btn q-btn-del" onclick="eliminarDelBanco(${preg.id})"><i class="fas fa-trash"></i></button>
            </div>`;
        grid.appendChild(card);
    });
}

// FIX BUG #3 + #4: importar ahora realmente inserta la pregunta en el encabezado seleccionado
function importarPregunta(id) {
    const encabezado_id = document.getElementById('encabezadoDestino').value;
    if(!encabezado_id) {
        document.getElementById('no-section-warn').style.display='flex';
        document.getElementById('encabezadoDestino').focus();
        return;
    }
    document.getElementById('no-section-warn').style.display='none';

    // Paso 1: obtener datos del banco
    fetch('index.php?action=banco&subaction=importar', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({id, encuesta_id})
    }).then(r=>r.json()).then(data=>{
        if(!data.success){toast('Error al obtener pregunta','error');return;}

        const preg = data.data;
        // Paso 2: insertar realmente en la encuesta (FIX: esto faltaba completamente)
        const fd = new URLSearchParams();
        fd.append('encabezado_id', encabezado_id);
        fd.append('pregunta', preg.pregunta);
        fd.append('tipo_pregunta', preg.tipo_pregunta);
        fd.append('opciones', preg.opciones || 'null');
        fd.append('requerido', 1);
        fd.append('guardar_banco', 0);
        fd.append('orden', 99);
        fd.append('pregunta_padre_id', '');
        fd.append('condicion_operador', 'igual');
        fd.append('condicion_valor', '');

        return fetch('index.php?action=banco&subaction=guardar_pregunta', {method:'POST', body:fd});
    }).then(r=>r && r.json()).then(data=>{
        if(!data) return;
        if(data.success){
            const btn = document.getElementById(`import-btn-${id}`);
            if(btn){btn.innerHTML='<i class="fas fa-check"></i> Importada';btn.style.background='rgba(52,199,89,.15)';btn.style.color='var(--green)';btn.disabled=true;}
            toast('Pregunta importada correctamente','success');
            if(window.opener && typeof window.opener.cargarEncuesta === 'function') {
                window.opener.cargarEncuesta();
            }
        } else {
            toast(data.message||'Error al importar','error');
        }
    }).catch(()=>toast('Error de conexión','error'));
}

function eliminarDelBanco(id) {
    if(!confirm('¿Eliminar esta pregunta del banco?')) return;
    fetch('index.php?action=banco&subaction=eliminar_banco',{method:'POST',body:new URLSearchParams({id})})
        .then(r=>r.json()).then(data=>{if(data.success){toast('Eliminada','success');cargarPreguntas();}else toast('Error','error');});
}

function toast(msg,type){
    const t=document.getElementById('toast');t.className=`toast ${type}`;
    document.getElementById('ti').innerHTML=type==='success'?'<i class="fas fa-check-circle"></i>':'<i class="fas fa-exclamation-circle"></i>';
    document.getElementById('tmsg').textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),3000);
}
function escapeHtml(t){const d=document.createElement('div');d.textContent=t;return d.innerHTML;}

document.addEventListener('DOMContentLoaded',()=>{
    cargarSecciones();
    cargarPreguntas();
});
</script>
</body>
</html>
