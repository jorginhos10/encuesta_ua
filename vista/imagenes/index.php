<?php
if(!isset($_SESSION['user_id'])){ header("Location: " . BASE_URL . "login"); exit(); }
$picker = isset($_GET['picker']) && $_GET['picker'] == '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo BASE_URL; ?>">
    <title>Banco de Imágenes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root{--bg:#f2f2f7;--surface:#fff;--surface2:#f2f2f7;--border:rgba(0,0,0,.08);--border-strong:rgba(0,0,0,.13);--text:#1c1c1e;--text2:#636366;--text3:#aeaeb2;--accent:#007aff;--al:rgba(0,122,255,.09);--green:#34c759;--red:#ff3b30;--orange:#ff9500;--purple:#af52de;--sh:0 1px 3px rgba(0,0,0,.06);--shm:0 4px 16px rgba(0,0,0,.08);--r:18px;--rs:12px;--nav:64px;}
        *{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased;}
        body{font-family:'DM Sans',-apple-system,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}
        /* NAVBAR */
        .navbar{position:sticky;top:0;z-index:200;height:var(--nav);background:rgba(255,255,255,.88);backdrop-filter:blur(24px);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 24px;}
        .nav-left{display:flex;align-items:center;gap:12px;}
        .nav-logo{width:34px;height:34px;background:linear-gradient(135deg,var(--purple),#007aff);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:14px;}
        .nav-title{font-size:17px;font-weight:600;letter-spacing:-.3px;}
        .nav-close{display:flex;align-items:center;gap:6px;background:rgba(0,0,0,.05);color:var(--text2);border:none;padding:7px 14px;border-radius:20px;font-family:inherit;font-size:13px;font-weight:500;cursor:pointer;transition:background .15s;}
        .nav-close:hover{background:rgba(0,0,0,.09);}
        /* HEADER */
        .page-header{background:linear-gradient(145deg,#1c1c1e,#2c2c2e);padding:28px 24px 24px;position:relative;overflow:hidden;}
        .page-header::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 85% 0%,rgba(175,82,222,.3),transparent 55%);}
        .ph-inner{max-width:1100px;margin:0 auto;position:relative;display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;}
        .ph-eyebrow{font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:6px;}
        .ph-title{font-family:'DM Serif Display',serif;font-size:24px;color:white;letter-spacing:-.5px;}
        .ph-sub{font-size:13px;color:rgba(255,255,255,.4);margin-top:4px;}
        .ph-count{font-size:13px;color:rgba(255,255,255,.45);background:rgba(255,255,255,.08);padding:5px 12px;border-radius:20px;}
        /* UPLOAD ZONE */
        .upload-wrap{max-width:1100px;margin:24px auto;padding:0 24px;}
        .drop-zone{border:2px dashed rgba(175,82,222,.4);border-radius:var(--r);background:rgba(175,82,222,.04);padding:32px 24px;text-align:center;cursor:pointer;transition:all .2s;position:relative;}
        .drop-zone.drag-over{border-color:var(--purple);background:rgba(175,82,222,.09);}
        .drop-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
        .dz-icon{font-size:32px;color:var(--purple);opacity:.6;margin-bottom:10px;}
        .dz-title{font-size:15px;font-weight:600;color:var(--text);margin-bottom:4px;}
        .dz-sub{font-size:12px;color:var(--text3);}
        .upload-progress{margin-top:12px;display:none;}
        .up-bar{height:4px;background:var(--surface2);border-radius:4px;overflow:hidden;}
        .up-fill{height:100%;background:linear-gradient(90deg,var(--purple),var(--accent));width:0%;transition:width .3s;border-radius:4px;}
        .up-label{font-size:11px;color:var(--text3);margin-top:5px;}
        /* FILTERS / TOOLBAR */
        .toolbar{max-width:1100px;margin:0 auto 16px;padding:0 24px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
        .tb-left{display:flex;align-items:center;gap:10px;}
        .search-box{display:flex;align-items:center;gap:8px;background:var(--surface);border:1.5px solid var(--border);border-radius:10px;padding:7px 12px;}
        .search-box input{border:none;outline:none;font-family:inherit;font-size:13px;color:var(--text);background:transparent;width:180px;}
        .search-box i{color:var(--text3);font-size:12px;}
        /* GRID */
        .images-grid{max-width:1100px;margin:0 auto;padding:0 24px 40px;display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;}
        .img-card{background:var(--surface);border-radius:var(--rs);overflow:hidden;box-shadow:var(--sh);border:1px solid var(--border);transition:box-shadow .18s,transform .18s;position:relative;}
        .img-card:hover{box-shadow:var(--shm);transform:translateY(-2px);}
        .img-thumb{width:100%;height:140px;object-fit:cover;display:block;background:var(--surface2);}
        .img-thumb-placeholder{width:100%;height:140px;display:flex;align-items:center;justify-content:center;background:var(--surface2);color:var(--text3);font-size:28px;}
        .img-info{padding:9px 10px 8px;}
        .img-name{font-size:11px;font-weight:500;color:var(--text2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:7px;}
        .img-actions{display:flex;gap:5px;}
        .img-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:6px 0;border-radius:8px;border:none;font-family:inherit;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;}
        .img-btn-copy{background:var(--al);color:var(--accent);}
        .img-btn-copy:hover{background:rgba(0,122,255,.16);}
        .img-btn-del{background:rgba(255,59,48,.07);color:var(--red);flex:0 0 30px;}
        .img-btn-del:hover{background:rgba(255,59,48,.14);}
        /* EMPTY */
        .empty-state{max-width:1100px;margin:0 auto;padding:0 24px 40px;}
        .empty-box{background:var(--surface);border-radius:var(--r);padding:60px 30px;text-align:center;color:var(--text3);border:1px solid var(--border);}
        .empty-box i{font-size:48px;opacity:.2;display:block;margin-bottom:12px;}
        /* TOAST */
        .toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(60px);background:#1c1c1e;color:white;padding:11px 20px;border-radius:30px;font-size:13px;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.25);display:flex;align-items:center;gap:8px;z-index:9999;transition:transform .3s cubic-bezier(.34,1.56,.64,1),opacity .3s;opacity:0;}
        .toast.show{transform:translateX(-50%) translateY(0);opacity:1;}
        .toast.success i{color:var(--green);}
        .toast.error i{color:var(--red);}
        /* LOADER */
        .loader{display:flex;align-items:center;justify-content:center;padding:60px;color:var(--text3);}
        .spinner{width:28px;height:28px;border:3px solid var(--border);border-top-color:var(--purple);border-radius:50%;animation:spin .7s linear infinite;margin-right:10px;}
        @keyframes spin{to{transform:rotate(360deg)}}
        /* PICKER MODE */
        .img-btn-pick{background:linear-gradient(135deg,var(--purple),var(--accent));color:white;}
        .img-btn-pick:hover{opacity:.88;}
        .picker-bar{background:linear-gradient(135deg,#1c1c1e,#2c2c2e);color:white;padding:10px 24px;font-size:13px;display:flex;align-items:center;gap:10px;}
        .picker-bar i{color:var(--purple);}
    </style>
</head>
<body>

<?php if($picker): ?>
<div class="picker-bar"><i class="fas fa-hand-pointer"></i> Haz clic en <strong>Seleccionar</strong> sobre la imagen que quieras usar.</div>
<?php endif; ?>
<nav class="navbar">
    <div class="nav-left">
        <div class="nav-logo"><i class="fas fa-images"></i></div>
        <span class="nav-title">Banco de Imágenes</span>
    </div>
    <button class="nav-close" onclick="window.close()"><i class="fas fa-times"></i> Cerrar</button>
</nav>

<div class="page-header">
    <div class="ph-inner">
        <div>
            <div class="ph-eyebrow"><i class="fas fa-images"></i> Biblioteca</div>
            <div class="ph-title">Banco de Imágenes</div>
            <div class="ph-sub">Sube, organiza y copia URLs de tus imágenes</div>
        </div>
        <span class="ph-count" id="ph-count">0 imágenes</span>
    </div>
</div>

<div class="upload-wrap">
    <div class="drop-zone" id="dropZone">
        <input type="file" id="fileInput" multiple accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml">
        <div class="dz-icon"><i class="fas fa-cloud-upload-alt"></i></div>
        <div class="dz-title">Arrastra imágenes aquí o haz clic para seleccionar</div>
        <div class="dz-sub">JPG, PNG, GIF, WEBP, SVG · Máx. 5 MB por imagen</div>
        <div class="upload-progress" id="uploadProgress">
            <div class="up-bar"><div class="up-fill" id="upFill"></div></div>
            <div class="up-label" id="upLabel">Subiendo...</div>
        </div>
    </div>
</div>

<div class="toolbar">
    <div class="tb-left">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar imagen..." oninput="filtrarImagenes()">
        </div>
    </div>
</div>

<div id="gridContainer"></div>

<div class="toast" id="toast"><i class="fas fa-check-circle"></i><span id="toast-msg">OK</span></div>

<script>
const BASE = '<?php echo BASE_URL; ?>';
const PICKER_MODE = <?php echo $picker ? 'true' : 'false'; ?>;
let todasImagenes = [];

async function cargarImagenes() {
    const gc = document.getElementById('gridContainer');
    gc.innerHTML = '<div class="loader"><div class="spinner"></div> Cargando...</div>';
    const r = await fetch('index.php?action=imagenes&subaction=listar');
    const d = await r.json();
    todasImagenes = d.success ? d.data : [];
    document.getElementById('ph-count').textContent = todasImagenes.length + ' imagen' + (todasImagenes.length !== 1 ? 'es' : '');
    renderGrid(todasImagenes);
}

function filtrarImagenes() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    renderGrid(q ? todasImagenes.filter(i => i.nombre.toLowerCase().includes(q)) : todasImagenes);
}

function renderGrid(imgs) {
    const gc = document.getElementById('gridContainer');
    if(!imgs.length) {
        gc.innerHTML = '<div class="empty-state"><div class="empty-box"><i class="fas fa-images"></i><p>No hay imágenes aún. ¡Sube la primera!</p></div></div>';
        return;
    }
    const grid = document.createElement('div');
    grid.className = 'images-grid';
    imgs.forEach(img => {
        const card = document.createElement('div');
        card.className = 'img-card';
        const esImagen = /\.(jpg|jpeg|png|gif|webp)$/i.test(img.nombre);
        card.innerHTML = `
            ${esImagen
                ? `<img class="img-thumb" src="${escHtml(img.url)}" alt="${escHtml(img.nombre)}" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">`
                : ''}
            <div class="img-thumb-placeholder" style="${esImagen ? 'display:none' : ''}"><i class="fas fa-file-image"></i></div>
            <div class="img-info">
                <div class="img-name" title="${escHtml(img.nombre)}">${escHtml(img.nombre)}</div>
                <div class="img-actions">
                    ${PICKER_MODE
                        ? `<button class="img-btn img-btn-pick" onclick="seleccionarImagen('${escHtml(img.url)}')"><i class="fas fa-check"></i> Seleccionar</button>`
                        : `<button class="img-btn img-btn-copy" onclick="copiarUrl('${escHtml(img.url)}')"><i class="fas fa-copy"></i> Copiar URL</button>`
                    }
                    <button class="img-btn img-btn-del" onclick="eliminarImagen('${escHtml(img.archivo)}')" title="Eliminar"><i class="fas fa-trash"></i></button>
                </div>
            </div>`;
        grid.appendChild(card);
    });
    gc.innerHTML = '';
    gc.appendChild(grid);
}

function escHtml(t) {
    const d = document.createElement('div'); d.textContent = t; return d.innerHTML;
}

function seleccionarImagen(url) {
    if(window.opener && typeof window.opener.__imagenPickerCallback === 'function') {
        window.opener.__imagenPickerCallback(url);
    }
    window.close();
}

async function copiarUrl(url) {
    try {
        await navigator.clipboard.writeText(url);
        toast('URL copiada al portapapeles', 'success');
    } catch(e) {
        // fallback
        const ta = document.createElement('textarea');
        ta.value = url; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); document.body.removeChild(ta);
        toast('URL copiada', 'success');
    }
}

async function eliminarImagen(archivo) {
    if(!confirm('¿Eliminar esta imagen?')) return;
    const r = await fetch('index.php?action=imagenes&subaction=eliminar', {
        method: 'POST',
        body: new URLSearchParams({archivo})
    });
    const d = await r.json();
    if(d.success) { toast('Imagen eliminada', 'success'); cargarImagenes(); }
    else toast(d.message || 'Error', 'error');
}

// ── Upload ──
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('drag-over'); subirArchivos(e.dataTransfer.files); });
fileInput.addEventListener('change', e => { subirArchivos(e.target.files); fileInput.value = ''; });

async function subirArchivos(files) {
    if(!files.length) return;
    const progressEl = document.getElementById('uploadProgress');
    const fillEl = document.getElementById('upFill');
    const labelEl = document.getElementById('upLabel');
    progressEl.style.display = 'block';
    let subidos = 0, errores = 0;
    for(let i = 0; i < files.length; i++) {
        const file = files[i];
        labelEl.textContent = `Subiendo ${i+1} de ${files.length}: ${file.name}`;
        fillEl.style.width = Math.round(((i) / files.length) * 100) + '%';
        const fd = new FormData();
        fd.append('imagen', file);
        try {
            const r = await fetch('index.php?action=imagenes&subaction=subir', { method: 'POST', body: fd });
            const d = await r.json();
            if(d.success) subidos++; else { errores++; toast(d.message || 'Error subiendo ' + file.name, 'error'); }
        } catch(e) { errores++; }
    }
    fillEl.style.width = '100%';
    labelEl.textContent = `${subidos} imagen${subidos !== 1 ? 'es' : ''} subida${subidos !== 1 ? 's' : ''}${errores ? ' · ' + errores + ' error(es)' : ''}`;
    setTimeout(() => { progressEl.style.display = 'none'; fillEl.style.width = '0%'; }, 2200);
    if(subidos > 0) { toast(subidos + ' imagen' + (subidos !== 1 ? 'es' : '') + ' subida' + (subidos !== 1 ? 's' : ''), 'success'); cargarImagenes(); }
}

function toast(msg, type = 'success') {
    const t = document.getElementById('toast');
    const ic = t.querySelector('i');
    ic.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    t.className = `toast ${type}`;
    document.getElementById('toast-msg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}

document.addEventListener('DOMContentLoaded', cargarImagenes);
</script>
</body>
</html>
