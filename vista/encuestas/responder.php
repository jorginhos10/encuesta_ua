<?php
// ════════════════════════════════════════════════════════════
// 🔍 BLOQUE DE DEBUG TEMPORAL — quitar cuando el problema esté resuelto
// Para activar: agregar ?debug=1 al final de la URL de la encuesta
// ════════════════════════════════════════════════════════════
if(isset($_GET['debug']) && $_GET['debug'] == '1'){
    echo '<div style="background:#1c1c1e;color:#0f0;padding:20px;font-family:monospace;font-size:12px;white-space:pre-wrap;border:3px solid #ff9500;margin:10px;">';
    echo "🔍 DEBUG — Estructura de datos de la encuesta\n";
    echo "═══════════════════════════════════════════════\n\n";
    echo "ENCUESTA ID: " . htmlspecialchars($encuesta_id) . "\n\n";
    foreach($encabezados as $enc){
        echo "📂 SECCIÓN [id={$enc['id']}]: " . htmlspecialchars($enc['titulo']) . "\n";
        echo "   Total preguntas: " . count($enc['preguntas']) . "\n\n";
        foreach($enc['preguntas'] as $idx => $p){
            echo "   ┌─ Pregunta #" . ($idx+1) . " [id={$p['id']}]\n";
            echo "   │  pregunta:           " . htmlspecialchars(substr($p['pregunta']??'',0,60)) . "\n";
            echo "   │  tipo_pregunta:      '" . htmlspecialchars($p['tipo_pregunta']??'(NULL)') . "'\n";
            echo "   │  tipo (raw bytes):   " . bin2hex($p['tipo_pregunta']??'') . "\n";
            echo "   │  opciones (primeros 200 chars): " . htmlspecialchars(substr($p['opciones']??'(NULL)',0,200)) . "\n";
            echo "   │  pregunta_padre_id:  " . var_export($p['pregunta_padre_id']??null,true) . "\n";
            echo "   │  requerido:          " . var_export($p['requerido']??null,true) . "\n";
            echo "   └─\n\n";
        }
    }
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo BASE_URL; ?>">
    <title><?php echo htmlspecialchars($encuesta['nombre']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ========== ESTILOS (Mismos que tenías, no cambio) ========== */
        :root{--bg:#f2f2f7;--surface:#fff;--surface2:#f2f2f7;--border:rgba(0,0,0,.08);--border-strong:rgba(0,0,0,.13);--text:#1c1c1e;--text2:#636366;--text3:#aeaeb2;--accent:#007aff;--al:rgba(0,122,255,.08);--green:#34c759;--red:#ff3b30;--orange:#ff9500;--tinder:#fe3c72;--sh:0 1px 3px rgba(0,0,0,.06);--shm:0 4px 16px rgba(0,0,0,.08);--shl:0 24px 80px rgba(0,0,0,.22);--r:18px;--rs:12px;}
        *{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased;}
        body{font-family:'DM Sans',-apple-system,sans-serif;background:var(--bg);color:var(--text);}
        body.has-bg{background-image:var(--survey-bg);background-size:cover;background-attachment:fixed;background-position:center;}
        .progress-track{position:fixed;top:0;left:0;right:0;height:3px;background:rgba(0,0,0,.06);z-index:1000;}
        .progress-fill{height:100%;background:linear-gradient(90deg,var(--accent),#5856d6);width:0%;transition:width .4s ease;border-radius:0 2px 2px 0;}
        .survey-banner-wrap{max-width:1060px;margin:0 auto;padding:12px 16px 0;}
        .survey-banner{width:100%;max-height:260px;object-fit:cover;display:block;border-radius:16px;opacity:var(--op-banner,1);}
        .survey-header{background:rgba(255,255,255,.88);backdrop-filter:blur(20px) saturate(180%);border-bottom:1px solid var(--border);padding:20px 24px 18px;position:sticky;top:0;z-index:100;}
        .survey-header.banner-only{padding:12px 24px;border-bottom:none;background:transparent;backdrop-filter:none;position:relative;}
        .hi{max-width:760px;margin:0 auto;}
        .h-eye{font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--accent);margin-bottom:4px;}
        .h-title{font-family:'DM Serif Display',serif;font-size:22px;color:var(--text);letter-spacing:-.5px;line-height:1.25;}
        .h-meta{display:flex;align-items:center;gap:14px;margin-top:10px;flex-wrap:wrap;}
        .h-chip{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text2);background:var(--surface2);padding:4px 10px;border-radius:20px;}
        .h-chip i{color:var(--accent);font-size:10px;}
        .layout{display:grid;grid-template-columns:1fr 260px;max-width:1060px;margin:0 auto;padding:24px 16px;gap:20px;align-items:start;}
        .section-card{background:var(--surface);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--border);margin-bottom:16px;overflow:hidden;}
        .sec-label{padding:16px 22px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;}
        .sec-icon{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,var(--accent),#5856d6);display:flex;align-items:center;justify-content:center;color:white;font-size:13px;flex-shrink:0;}
        .sec-name{font-size:15px;font-weight:600;letter-spacing:-.2px;}
        .sec-desc{font-size:12px;color:var(--text2);margin-top:2px;}
        .sec-body{padding:8px 0;}
        .q-row{padding:16px 22px;border-bottom:1px solid var(--border);transition:background .15s;}
        .q-row:last-child{border-bottom:none;}
        .q-row:focus-within{background:rgba(0,122,255,.02);}
        .q-row.condicional.oculta{display:none!important;}
        .q-label{font-size:15px;font-weight:500;color:var(--text);margin-bottom:12px;line-height:1.4;display:flex;align-items:baseline;gap:4px;flex-wrap:wrap;}
        .q-req{color:var(--red);font-size:13px;}
        .q-badge{font-size:10px;font-weight:600;padding:2px 7px;border-radius:20px;background:rgba(88,86,214,.1);color:#5856d6;margin-left:6px;}
        .q-head{background:linear-gradient(135deg,rgba(0,122,255,.06),rgba(88,86,214,.04));border-radius:var(--rs);padding:14px 16px;border-left:3px solid var(--accent);margin:8px 22px;}
        .q-head h3{font-size:15px;font-weight:600;}
        .q-info{background:var(--surface2);border-radius:var(--rs);padding:12px 16px;margin:8px 22px;font-size:13px;color:var(--text2);line-height:1.5;border-left:3px solid var(--orange);}
        .q-input,.q-textarea{width:100%;padding:12px 14px;border:1.5px solid var(--border-strong);border-radius:var(--rs);font-size:14px;font-family:inherit;color:var(--text);background:var(--surface);outline:none;transition:border-color .15s,box-shadow .15s;}
        .q-input:focus,.q-textarea:focus{border-color:var(--accent);box-shadow:0 0 0 4px var(--al);}
        .q-textarea{resize:vertical;min-height:90px;line-height:1.5;}
        .opts-list{display:flex;flex-direction:column;gap:8px;}
        .opt-item{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:var(--rs);border:1.5px solid var(--border-strong);cursor:pointer;transition:all .15s;background:var(--surface);}
        .opt-item:hover{border-color:var(--accent);background:var(--al);}
        .opt-item input[type="radio"],.opt-item input[type="checkbox"]{width:18px;height:18px;accent-color:var(--accent);cursor:pointer;flex-shrink:0;}
        .opt-item label{font-size:14px;cursor:pointer;flex:1;}
        .opt-item:has(input:checked){border-color:var(--accent);background:var(--al);}
        .checkbox-wrap{display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border-radius:var(--rs);border:1.5px solid var(--border-strong);background:var(--surface);cursor:pointer;transition:all .15s;}
        .checkbox-wrap:hover{border-color:var(--accent);background:var(--al);}
        .checkbox-wrap:has(input[type="checkbox"]:checked){border-color:var(--accent);background:var(--al);}
        .q-checkbox{width:20px;height:20px;accent-color:var(--accent);cursor:pointer;flex-shrink:0;margin-top:1px;}
        .q-checkbox-label{font-size:15px;cursor:pointer;line-height:1.45;color:var(--text);}
        .tinder-section{padding:16px 22px;}
        .tinder-q-label{font-size:15px;font-weight:600;margin-bottom:4px;}
        .tinder-q-sub{font-size:12px;color:var(--text3);margin-bottom:20px;}
        /* === LAYOUT DESKTOP: botones a los lados de la tarjeta === */
        .tinder-arena{display:flex;align-items:center;justify-content:center;gap:28px;margin-bottom:14px;}
        .tinder-stage{position:relative;width:340px;height:460px;flex-shrink:0;background:rgba(0,0,0,.02);border-radius:22px;}
        .tcard{position:absolute;top:0;left:0;width:100%;height:100%;border-radius:22px;overflow:hidden;box-shadow:var(--shl);background:var(--surface);cursor:grab;user-select:none;will-change:transform;display:flex;flex-direction:column;z-index:1;}
        /* Mostrar la primera tarjeta por defecto aunque el JS no se ejecute */
        .tcard:first-child{z-index:10;}
        .tcard:not(:first-child){display:none;}
        .tcard:active{cursor:grabbing;}
        .tcard.gone{display:none;}
        .tcard-img{width:100%;height:240px;object-fit:cover;display:block;flex-shrink:0;}
        .tcard-body{padding:18px 20px 16px;flex:1;overflow:hidden;}
        .tcard-title{font-size:18px;font-weight:600;letter-spacing:-.3px;margin-bottom:6px;color:var(--text);}
        .tcard-text{font-size:13px;color:var(--text2);line-height:1.55;}
        .tcard-no-img .tcard-body{padding:30px 22px 22px;}
        .tcard-body-centered{display:flex;align-items:center;justify-content:center;text-align:center;}
        .tcard-body-centered .tcard-title{margin-bottom:0;}
        .tcard-no-text .tcard-img{flex:1;min-height:200px;height:auto;}
        .tcard-no-text .tcard-body{flex:none;padding:14px 20px 16px;}
        .tcard-no-text .tcard-title{margin-bottom:0;text-align:center;}
        .tcard-info-btn{position:absolute;top:12px;right:12px;width:34px;height:34px;border-radius:50%;background:rgba(0,0,0,.42);backdrop-filter:blur(4px);border:none;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;z-index:10;transition:background .15s;}
        .tcard-info-btn:hover{background:rgba(0,0,0,.62);}
        .tcard-no-img .tcard-info-btn{background:rgba(0,122,255,.15);color:var(--accent);}
        .tcard-like-stamp{position:absolute;top:30px;left:24px;background:rgba(52,199,89,.92);color:white;padding:8px 18px;border-radius:8px;font-weight:700;font-size:18px;opacity:0;transform:rotate(-14deg);pointer-events:none;transition:opacity .1s;border:3px solid white;z-index:5;letter-spacing:1px;}
        .tcard-nope-stamp{position:absolute;top:30px;right:24px;background:rgba(255,59,48,.92);color:white;padding:8px 18px;border-radius:8px;font-weight:700;font-size:18px;opacity:0;transform:rotate(14deg);pointer-events:none;transition:opacity .1s;border:3px solid white;z-index:5;letter-spacing:1px;}
        /* === Botones Like / Dislike === */
        .tinder-btn{padding:13px 28px;border-radius:50px;border:2px solid;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;font-size:15px;font-weight:600;font-family:inherit;letter-spacing:.2px;transition:transform .15s,box-shadow .15s,background .15s;background:white;flex-shrink:0;box-shadow:0 4px 14px rgba(0,0,0,.1);}
        .tinder-btn:disabled{opacity:.35;cursor:not-allowed;}
        .tinder-btn-no{border-color:var(--red);color:var(--red);}
        .tinder-btn-no:hover:not(:disabled){background:rgba(255,59,48,.08);transform:translateY(-1px);box-shadow:0 6px 18px rgba(255,59,48,.25);}
        .tinder-btn-yes{border-color:var(--green);color:var(--green);}
        .tinder-btn-yes:hover:not(:disabled){background:rgba(52,199,89,.08);transform:translateY(-1px);box-shadow:0 6px 18px rgba(52,199,89,.25);}
        .tinder-mobile-buttons{display:none;}
        .tinder-counter{font-size:12px;color:var(--text3);text-align:center;margin-top:6px;}
        .tinder-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;width:100%;height:100%;background:linear-gradient(135deg,rgba(0,122,255,.05),rgba(88,86,214,.04));border-radius:22px;color:var(--text3);text-align:center;padding:20px;}
        .tinder-empty i{font-size:42px;opacity:.35;margin-bottom:10px;}
        .tinder-results{margin-top:16px;display:flex;flex-direction:column;gap:8px;}
        .tinder-result-item{display:flex;align-items:center;justify-content:space-between;background:var(--surface2);border-radius:var(--rs);padding:10px 14px;}
        .tinder-result-title{font-size:13px;font-weight:500;}
        .tinder-result-badge{font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;}
        .badge-yes{background:rgba(52,199,89,.15);color:var(--green);}
        .badge-no{background:rgba(255,59,48,.1);color:var(--red);}
        .tinder-done{text-align:center;padding:30px 20px;color:var(--text2);}
        /* === MOBILE: botones debajo, tarjeta más grande, swipe activo === */
        @media(max-width:600px){
            .tinder-arena{flex-direction:column;gap:16px;}
            .tinder-stage{width:100%;max-width:340px;height:480px;}
            .tinder-arena>.tinder-btn{display:none;}
            .tinder-mobile-buttons{display:flex;align-items:center;justify-content:center;gap:16px;width:100%;}
            .tinder-mobile-buttons .tinder-btn{padding:12px 22px;font-size:14px;}
        }
        .popup-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:900;align-items:flex-end;justify-content:center;}
        .popup-overlay.open{display:flex;animation:fadeIn .2s ease;}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        .popup-box{background:var(--surface);border-radius:22px 22px 0 0;width:100%;max-width:500px;padding:28px 24px 36px;box-shadow:0 -8px 40px rgba(0,0,0,.15);animation:slideUp .3s cubic-bezier(.34,1.2,.64,1);}
        @keyframes slideUp{from{transform:translateY(100%)}to{transform:none}}
        .popup-handle{width:36px;height:4px;border-radius:2px;background:var(--border-strong);margin:0 auto 16px;}
        .popup-title{font-size:18px;font-weight:600;margin-bottom:8px;letter-spacing:-.3px;}
        .popup-text{font-size:14px;color:var(--text2);line-height:1.6;}
        .popup-close{margin-top:20px;width:100%;padding:13px;background:var(--surface2);border:none;border-radius:12px;font-family:inherit;font-size:14px;font-weight:500;color:var(--text);cursor:pointer;}
        .sidebar{position:sticky;top:calc(80px + 20px);}
        .sidebar-card{background:var(--surface);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--border);overflow:hidden;margin-bottom:14px;}
        .timer-card{background:linear-gradient(145deg,#1c1c1e,#2c2c2e);border-radius:var(--r);padding:22px;text-align:center;box-shadow:var(--shm);margin-bottom:14px;position:relative;overflow:hidden;}
        .timer-card::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 60% 20%,rgba(0,122,255,.2),transparent 60%);}
        .t-label{font-size:11px;font-weight:700;letter-spacing:1px;color:rgba(255,255,255,.45);text-transform:uppercase;margin-bottom:10px;position:relative;}
        .t-value{font-size:36px;font-weight:300;letter-spacing:-2px;color:white;line-height:1;font-family:'DM Serif Display',serif;position:relative;}
        .t-sub{font-size:11px;color:rgba(255,255,255,.35);margin-top:6px;position:relative;}
        .s-ttl{padding:14px 16px 10px;font-size:11px;font-weight:700;letter-spacing:.8px;color:var(--text3);text-transform:uppercase;border-bottom:1px solid var(--border);}
        .prog-card{padding:16px;}
        .prog-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;}
        .prog-lbl{font-size:12px;font-weight:600;color:var(--text2);}
        .prog-pct{font-size:13px;font-weight:700;color:var(--accent);}
        .prog-bar{height:6px;background:var(--surface2);border-radius:3px;overflow:hidden;}
        .prog-fill{height:100%;background:linear-gradient(90deg,var(--accent),#5856d6);border-radius:3px;transition:width .4s ease;}
        .nav-list{padding:8px 10px;max-height:340px;overflow-y:auto;}
        .nav-item{display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:9px;cursor:pointer;transition:background .1s;font-size:12px;color:var(--text2);}
        .nav-item:hover{background:var(--al);color:var(--accent);}
        .nav-num{width:20px;height:20px;border-radius:6px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--text3);flex-shrink:0;}
        .nav-answered .nav-num{background:rgba(52,199,89,.15);color:var(--green);}
        .submit-sec{padding:24px 0 8px;}
        .btn-submit{width:100%;padding:16px;border-radius:var(--r);background:linear-gradient(135deg,var(--accent),#5856d6);color:white;border:none;cursor:pointer;font-family:inherit;font-size:16px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:10px;box-shadow:0 8px 24px rgba(0,122,255,.3);transition:transform .15s,box-shadow .15s,opacity .15s;}
        .btn-submit:hover{transform:translateY(-1px);box-shadow:0 12px 32px rgba(0,122,255,.35);}
        .btn-submit:disabled{opacity:.5;cursor:not-allowed;transform:none;}
        .submit-note{text-align:center;font-size:12px;color:var(--text3);margin-top:10px;}
        .sat-items{display:flex;gap:8px;flex-wrap:wrap;}
        .sat-item{display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 10px;border-radius:var(--rs);border:1.5px solid var(--border-strong);cursor:pointer;transition:all .2s;background:var(--surface);min-width:60px;flex:1;max-width:120px;user-select:none;}
        .sat-item:hover{border-color:var(--accent);background:var(--al);}
        .sat-item.sat-selected{border-color:var(--accent);background:var(--al);box-shadow:0 0 0 3px rgba(0,122,255,.15);}
        .sat-face{font-size:32px;line-height:1;}
        .sat-label{font-size:10px;font-weight:600;color:var(--text2);text-align:center;line-height:1.2;}
        .sat-num{font-size:14px;font-weight:700;color:var(--text2);}
        .sat-item.sat-selected .sat-num{color:var(--accent);}
        .sat-item.sat-selected .sat-label{color:var(--accent);}
        .mat-title{background:linear-gradient(135deg,#1f3864,#2e5597);color:#fff;font-weight:800;font-size:15px;letter-spacing:.2px;padding:14px 18px;border-radius:14px 14px 0 0;margin-bottom:0!important;}
        .mat-title .q-req{color:#ffb4ae;}
        .mat-wrap{overflow-x:auto;border:1px solid #d5dce6;border-top:0;border-radius:0 0 14px 14px;background:#fff;box-shadow:0 2px 10px rgba(31,56,100,.06);}
        .mat-table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px;min-width:760px;}
        .mat-table th{padding:12px 14px;text-align:center;font-weight:700;font-size:12.5px;color:#1f2d3d;background:#f1f4f8;border-bottom:1px solid #d5dce6;border-right:1px solid #e3e8ef;vertical-align:middle;}
        .mat-table th:last-child,.mat-table td:last-child{border-right:0;}
        .mat-table th.mat-nec{background:#2e75b6;color:#fff;line-height:1.35;letter-spacing:.3px;}
        .mat-table th.mat-nec small{display:block;font-weight:600;font-size:11.5px;opacity:.9;}
        .mat-table th.mat-sel{background:#dce8f5;}
        .mat-table tr.mat-ej td{font-style:italic;color:#8a8f98;font-size:12px;padding:9px 14px;background:#fafbfc;border-bottom:1px solid #e3e8ef;border-right:1px solid #e3e8ef;text-align:left;}
        .mat-table tr.mat-ej td.mat-n,.mat-table tr.mat-ej td.mat-sel{text-align:center;background:#fafbfc;}
        .mat-table td{padding:12px 14px;border-bottom:1px solid #e3e8ef;border-right:1px solid #e3e8ef;vertical-align:middle;}
        .mat-table tbody tr:last-child td{border-bottom:0;}
        .mat-table td.mat-n{text-align:center;font-weight:700;width:48px;color:#5b6b82;}
        .mat-table td.mat-asp{font-weight:700;color:#1f3864;width:20%;line-height:1.35;}
        .mat-table td.mat-nec{color:#3a3a3c;background:#f7f9fc;line-height:1.45;}
        .mat-table td.mat-sel{background:#fff4dc;width:19%;}
        .mat-table tbody tr:not(.mat-ej):hover td.mat-n,.mat-table tbody tr:not(.mat-ej):hover td.mat-asp{background:#f1f6fd;}
        .mat-select{width:100%;padding:9px 32px 9px 12px;border:1.5px solid #e0c98f;border-radius:10px;font-size:13px;font-family:inherit;background:#fff url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'><path d='M1 1l5 5 5-5' fill='none' stroke='%238a6d1f' stroke-width='2' stroke-linecap='round'/></svg>") no-repeat right 12px center;color:var(--text);appearance:none;-webkit-appearance:none;cursor:pointer;transition:border-color .15s,box-shadow .15s;text-overflow:ellipsis;}
        .mat-select:hover{border-color:#d4b25a;}
        .mat-select:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--al);}
        .mat-select.mat-filled{border-color:#34c759;background-color:#f2fbf5;}
        @media(max-width:900px){.mat-table{min-width:640px;}.mat-table td.mat-asp{width:18%;}}
        @media(max-width:700px){
            .mat-wrap{overflow:visible;background:transparent;border:0;box-shadow:none;}
            .mat-title{border-radius:14px;}
            .mat-table,.mat-table tbody{display:block;min-width:0;width:100%;}
            .mat-table thead,.mat-table tr.mat-ej{display:none;}
            .mat-table tbody tr{display:grid;grid-template-columns:1fr 1fr;gap:8px 10px;background:#fff;border:1px solid #d5dce6;border-radius:14px;padding:14px;margin-top:12px;box-shadow:0 2px 8px rgba(31,56,100,.06);}
            .mat-table td{display:block;border:0!important;padding:0;width:auto!important;background:transparent!important;}
            .mat-table td.mat-n{display:none;}
            .mat-table td.mat-asp{grid-column:1/-1;font-size:14.5px;}
            .mat-table td.mat-nec{grid-column:1/-1;font-size:12.5px;color:#636366;padding-bottom:4px;}
            .mat-table td.mat-sel::before{content:attr(data-label);display:block;font-size:11px;font-weight:700;color:#5b6b82;margin-bottom:5px;line-height:1.25;}
            .mat-select{padding:11px 30px 11px 12px;font-size:14px;}
        }
        @media(max-width:480px){.mat-table tbody tr{grid-template-columns:1fr;}}
        .mobile-timer-strip{display:none;align-items:center;gap:10px;margin-top:12px;padding:10px 16px;background:linear-gradient(135deg,#1c1c1e,#2c2c2e);border-radius:12px;opacity:var(--op-sidebar,1);box-shadow:0 2px 10px rgba(0,0,0,.25);}
        .mobile-timer-val{font-size:15px;font-weight:700;letter-spacing:-.3px;color:#fff;}
        .mobile-timer-sub{font-size:11px;color:rgba(255,255,255,.45);}
        .mobile-nav-section{display:none;}
        .mobile-nav-section .nav-list{max-height:none;}
        .main-form .section-card{background:rgb(255 255 255 / var(--op-formulario,1));}
        .main-form .q-input,.main-form .q-textarea{background:rgb(255 255 255 / var(--op-formulario,1));}
        .main-form .opt-item,.main-form .checkbox-wrap{background:rgb(255 255 255 / var(--op-formulario,1));}
        .sidebar{opacity:var(--op-sidebar,1);}
        @media(max-width:768px){.layout{grid-template-columns:1fr;}.sidebar{display:none;}.mobile-timer-strip{display:flex;}.mobile-nav-section{display:block;}}
    </style>
</head>
<?php
    $bannerUrl        = trim($encuesta['banner_url']          ?? '');
    $bannerModo       = trim($encuesta['banner_modo']         ?? '') ?: 'enunciado';
    $backgroundUrl    = trim($encuesta['background_url']      ?? '');
    $opFormulario     = max(10, min(100, (int)($encuesta['opacidad_formulario'] ?? 100)));
    $opBanner         = max(10, min(100, (int)($encuesta['opacidad_banner']     ?? 100)));
    $opSidebar        = max(10, min(100, (int)($encuesta['opacidad_sidebar']    ?? 100)));
    $showBanner       = $bannerUrl !== '' && in_array($bannerModo, ['ambos','banner']);
    $showEnunciado    = in_array($bannerModo, ['enunciado','ambos']) || $bannerUrl === '';
    $bodyStyle = '--op-formulario:'.($opFormulario/100).';--op-banner:'.($opBanner/100).';--op-sidebar:'.($opSidebar/100).';';
    if($backgroundUrl !== '') $bodyStyle .= "--survey-bg:url('".htmlspecialchars($backgroundUrl)."');";
?>
<body<?php if($backgroundUrl !== ''): ?> class="has-bg"<?php endif; ?> style="<?php echo $bodyStyle; ?>">
<div class="progress-track"><div class="progress-fill" id="prog-fill"></div></div>
<?php if($showBanner): ?>
<div class="survey-banner-wrap">
    <img src="<?php echo htmlspecialchars($bannerUrl); ?>" alt="Banner de encuesta" class="survey-banner">
</div>
<?php endif; ?>
<header class="survey-header<?php echo (!$showEnunciado) ? ' banner-only' : ''; ?>">
<?php if($showEnunciado): ?>
    <div class="hi">
        <div class="h-eye"><i class="fas fa-clipboard-list"></i> Encuesta</div>
        <h1 class="h-title"><?php echo htmlspecialchars($encuesta['nombre']); ?></h1>
        <div class="h-meta">
            <div class="h-chip"><i class="fas fa-calendar"></i> Cierra: <?php echo date('d/m/Y',strtotime($encuesta['fecha_cierre'])); ?></div>
            <div class="h-chip"><i class="fas fa-asterisk"></i> * campos obligatorios</div>
        </div>
    </div>
<?php endif; ?>
    <div class="mobile-timer-strip">
        <i class="fas fa-hourglass-half" style="color:rgba(255,255,255,.6);font-size:14px;"></i>
        <div>
            <div class="mobile-timer-val" id="t-rest-mobile">—</div>
            <div class="mobile-timer-sub" id="t-sub-mobile">Calculando...</div>
        </div>
    </div>
</header>

<div class="layout">
    <div class="main-form">
        <form method="POST" action="index.php?action=guardar_respuesta" id="encuestaForm">
            <input type="hidden" name="encuesta_id" value="<?php echo $encuesta_id; ?>">
            <input type="hidden" name="submit_token" value="<?php echo htmlspecialchars($submit_token); ?>">

            <?php foreach($encabezados as $enc): ?>
            <div class="section-card" data-seccion="<?php echo $enc['id']; ?>">
                <div class="sec-label">
                    <div class="sec-icon"><i class="fas fa-layer-group"></i></div>
                    <div>
                        <div class="sec-name"><?php echo htmlspecialchars($enc['titulo']); ?></div>
                        <?php if($enc['descripcion']): ?><div class="sec-desc"><?php echo nl2br(htmlspecialchars($enc['descripcion'])); ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="sec-body">
                    <?php foreach($enc['preguntas'] as $preg): 
                        // ── NORMALIZAR TIPO: evitar problemas con espacios/mayúsculas/null ──
                        $tipoRaw = $preg['tipo_pregunta'] ?? '';
                        $preg['tipo_pregunta'] = strtolower(trim($tipoRaw));
                        // Detección flexible para Tinder (cualquier variante)
                        if(strpos($preg['tipo_pregunta'], 'tinder') !== false){
                            $preg['tipo_pregunta'] = 'tinder';
                        }
                        // ── DETECCIÓN INTELIGENTE POR CONTENIDO ──
                        // Si el tipo está vacío o desconocido pero hay opciones JSON,
                        // intentamos inferir el tipo a partir del contenido.
                        $tiposConocidos = ['texto_corto','texto_largo','seleccion_unica','seleccion_multiple','checkbox','tinder','satisfaccion','matriz','encabezado','texto_ordinario'];
                        if(!in_array($preg['tipo_pregunta'], $tiposConocidos) && !empty($preg['opciones'])){
                            $testOpciones = json_decode($preg['opciones'], true);
                            // Soporte para doble-codificación JSON
                            if(is_string($testOpciones)){
                                $testOpciones = json_decode($testOpciones, true);
                            }
                            if(is_array($testOpciones) && count($testOpciones) > 0){
                                // Tomar el primer elemento (independiente de la indexación)
                                $keys = array_keys($testOpciones);
                                $primero = $testOpciones[$keys[0]];
                                if(is_array($primero)){
                                    // Si tiene cualquiera de estos campos, es Tinder
                                    if(array_key_exists('titulo', $primero) || 
                                       array_key_exists('imagen', $primero) || 
                                       array_key_exists('texto_corto', $primero) ||
                                       array_key_exists('descripcion', $primero)){
                                        $preg['tipo_pregunta'] = 'tinder';
                                    }
                                } elseif(is_string($primero)){
                                    $preg['tipo_pregunta'] = 'seleccion_unica';
                                }
                            }
                        }
                        // Fallback final: si seguimos sin tipo válido, lo mostramos como diagnóstico
                        $tipoDesconocido = !in_array($preg['tipo_pregunta'], $tiposConocidos);
                    ?>
                        <?php if($tipoDesconocido): ?>
                            <div class="q-row" style="padding:16px 22px;">
                                <div style="background:#fff5e6;border:1.5px solid #ff9500;border-radius:12px;padding:14px;color:#b65f00;font-size:13px;">
                                    <strong><i class="fas fa-exclamation-triangle"></i> Pregunta con tipo no reconocido</strong><br>
                                    <small style="opacity:.8;">ID: <?php echo $preg['id']; ?> · Tipo guardado: "<?php echo htmlspecialchars($tipoRaw); ?>"</small><br>
                                    <div style="margin-top:8px;font-size:14px;color:var(--text);"><?php echo textoConEnlaces($preg['pregunta']); ?></div>
                                </div>
                            </div>
                        <?php elseif($preg['tipo_pregunta'] === 'encabezado'): ?>
                            <div class="q-head"><h3><?php echo textoConEnlaces($preg['pregunta']); ?></h3></div>
                        <?php elseif($preg['tipo_pregunta'] === 'texto_ordinario'): ?>
                            <div class="q-info"><?php echo nl2br(textoConEnlaces($preg['pregunta'])); ?></div>
                        <?php else: 
                            $esCond = !empty($preg['pregunta_padre_id']) && $preg['pregunta_padre_id'] > 0;
                            $dataCond = $esCond ? 'data-padre="'.$preg['pregunta_padre_id'].'" data-operador="'.htmlspecialchars($preg['condicion_operador']).'" data-valor="'.htmlspecialchars($preg['condicion_valor']).'"' : '';
                        ?>
                        <div class="q-row condicional <?php echo $esCond ? 'oculta' : ''; ?>" id="pregunta-<?php echo $preg['id']; ?>" <?php echo $dataCond; ?> <?php echo ($preg['tipo_pregunta'] === 'tinder') ? 'data-tinder="1"' : ''; ?>>
                            <?php if(isset($_GET['debug']) && $_GET['debug']=='1'): ?>
                            <div style="background:#ffe6e6;border:2px solid red;padding:8px;font-family:monospace;font-size:11px;color:#900;margin-bottom:8px;">
                                🔧 RENDER DEBUG · ID=<?php echo $preg['id']; ?> · tipo='<?php echo htmlspecialchars($preg['tipo_pregunta']); ?>' · esCond=<?php echo $esCond?'SI':'NO'; ?> · padre=<?php echo var_export($preg['pregunta_padre_id'],true); ?> · oculta=<?php echo $esCond?'SI':'NO'; ?>
                            </div>
                            <?php endif; ?>
                            <?php if($preg['tipo_pregunta'] === 'tinder'): ?>
                                <?php
                                    $opcionesRaw = json_decode($preg['opciones'] ?? '[]', true);
                                    if(is_string($opcionesRaw)) $opcionesRaw = json_decode($opcionesRaw, true);
                                    // Nuevo formato: {label_si, label_no, tarjetas:[...]}
                                    if(is_array($opcionesRaw) && array_key_exists('tarjetas', $opcionesRaw)){
                                        $labelSi = htmlspecialchars($opcionesRaw['label_si'] ?? 'Me gusta', ENT_QUOTES, 'UTF-8');
                                        $labelNo = htmlspecialchars($opcionesRaw['label_no'] ?? 'No me gusta', ENT_QUOTES, 'UTF-8');
                                        $cardsRaw = $opcionesRaw['tarjetas'] ?? [];
                                    } else {
                                        $labelSi = 'Me gusta';
                                        $labelNo = 'No me gusta';
                                        $cardsRaw = is_array($opcionesRaw) ? $opcionesRaw : [];
                                    }
                                    $cards = [];
                                    foreach($cardsRaw as $c){
                                        if(is_array($c) && !empty($c['titulo'])){
                                            $cards[] = [
                                                'titulo' => $c['titulo'],
                                                'texto_corto' => $c['texto_corto'] ?? '',
                                                'descripcion' => $c['descripcion'] ?? '',
                                                'imagen' => $c['imagen'] ?? ''
                                            ];
                                        }
                                    }
                                ?>
                                <input type="hidden" name="pregunta_<?php echo $preg['id']; ?>" id="tinder-resp-<?php echo $preg['id']; ?>" value="">
                                <div class="tinder-section" data-label-si="<?php echo $labelSi; ?>" data-label-no="<?php echo $labelNo; ?>">
                                    <div class="tinder-q-label"><?php echo nl2br(textoConEnlaces($preg['pregunta'])); ?><?php if($preg['requerido']): ?> <span style="color:var(--red);">*</span><?php endif; ?></div>
                                    <?php if(empty($cards)): ?>
                                        <div style="background:#fff5e6;border:1.5px solid #ff9500;border-radius:12px;padding:16px;color:#b65f00;font-size:13px;text-align:center;">
                                            <i class="fas fa-exclamation-triangle"></i> Esta pregunta no tiene tarjetas configuradas. Contacta al creador de la encuesta.
                                        </div>
                                    <?php else: ?>
                                    <div class="tinder-q-sub"><i class="fas fa-hand-pointer"></i> Desliza la tarjeta o usa los botones · <span style="color:var(--green);">✓ <?php echo $labelSi; ?></span> · <span style="color:var(--red);">✗ <?php echo $labelNo; ?></span></div>
                                    <div class="tinder-arena">
                                        <button type="button" class="tinder-btn tinder-btn-no" onclick="tinderSwipe(<?php echo $preg['id']; ?>,'no')"><i class="fas fa-thumbs-down"></i> <?php echo $labelNo; ?></button>
                                        <div class="tinder-stage" id="tinder-stage-<?php echo $preg['id']; ?>">
                                            <?php foreach($cards as $idx => $card): ?>
                                            <div class="tcard<?php echo empty($card['imagen']) ? ' tcard-no-img' : ''; ?><?php echo (!empty($card['imagen']) && empty($card['texto_corto'])) ? ' tcard-no-text' : ''; ?>"
                                                 data-titulo="<?php echo htmlspecialchars($card['titulo'] ?? ''); ?>"
                                                 data-tinder-id="<?php echo $preg['id']; ?>"
                                                 data-card-idx="<?php echo $idx; ?>">
                                                <?php if(!empty($card['imagen'])): ?>
                                                <img class="tcard-img" src="<?php echo htmlspecialchars($card['imagen']); ?>" alt="<?php echo htmlspecialchars($card['titulo']??''); ?>" onerror="this.style.display='none';this.parentElement.classList.add('tcard-no-img')">
                                                <?php endif; ?>
                                                <?php if(!empty(trim($card['descripcion'] ?? ''))): ?>
                                                <button type="button" class="tcard-info-btn" onclick="abrirPopup(<?php echo htmlspecialchars(json_encode($card['titulo']??''), ENT_QUOTES); ?>,<?php echo htmlspecialchars(json_encode($card['descripcion']), ENT_QUOTES); ?>)"><i class="fas fa-info"></i></button>
                                                <?php endif; ?>
                                                <div class="tcard-like-stamp"><?php echo strtoupper($labelSi); ?></div>
                                                <div class="tcard-nope-stamp"><?php echo strtoupper($labelNo); ?></div>
                                                <div class="tcard-body<?php echo empty($card['texto_corto']) ? ' tcard-body-centered' : ''; ?>">
                                                    <div class="tcard-title"><?php echo nl2br(htmlspecialchars($card['titulo'] ?? 'Sin título')); ?></div>
                                                    <?php if(!empty($card['texto_corto'])): ?>
                                                    <div class="tcard-text"><?php echo nl2br(htmlspecialchars($card['texto_corto'])); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="tinder-btn tinder-btn-yes" onclick="tinderSwipe(<?php echo $preg['id']; ?>,'yes')"><i class="fas fa-thumbs-up"></i> <?php echo $labelSi; ?></button>
                                    </div>
                                    <div class="tinder-mobile-buttons">
                                        <button type="button" class="tinder-btn tinder-btn-no" onclick="tinderSwipe(<?php echo $preg['id']; ?>,'no')"><i class="fas fa-thumbs-down"></i> <?php echo $labelNo; ?></button>
                                        <button type="button" class="tinder-btn tinder-btn-yes" onclick="tinderSwipe(<?php echo $preg['id']; ?>,'yes')"><i class="fas fa-thumbs-up"></i> <?php echo $labelSi; ?></button>
                                    </div>
                                    <div class="tinder-counter" id="tinder-counter-<?php echo $preg['id']; ?>"><?php echo count($cards); ?> tarjeta<?php echo count($cards)!=1?'s':''; ?></div>
                                    <div class="tinder-results" id="tinder-results-<?php echo $preg['id']; ?>"></div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php if($preg['tipo_pregunta'] === 'checkbox'): ?>
                                <!-- Casilla de verificación: checkbox + enunciado en línea -->
                                <div class="checkbox-wrap">
                                    <input type="hidden" name="pregunta_<?php echo $preg['id']; ?>" value="no" <?php echo $esCond ? 'disabled' : ''; ?>>
                                    <input type="checkbox" name="pregunta_<?php echo $preg['id']; ?>" value="si" id="cb_<?php echo $preg['id']; ?>" class="q-checkbox" <?php echo $esCond ? 'disabled' : ''; ?> data-required="<?php echo $preg['requerido']?1:0; ?>">
                                    <label for="cb_<?php echo $preg['id']; ?>" class="q-checkbox-label">
                                        <?php echo nl2br(textoConEnlaces($preg['pregunta'])); ?>
                                        <?php if($preg['requerido']): ?><span class="q-req">*</span><?php endif; ?>
                                        <?php if($esCond): ?><span class="q-badge"><i class="fas fa-code-branch"></i> Condicional</span><?php endif; ?>
                                    </label>
                                </div>
                                <?php else: ?>
                                <!-- Preguntas normales -->
                                <div class="q-label<?php echo $preg['tipo_pregunta']==='matriz' ? ' mat-title' : ''; ?>">
                                    <?php echo nl2br(textoConEnlaces($preg['pregunta'])); ?>
                                    <?php if($preg['requerido']): ?><span class="q-req">*</span><?php endif; ?>
                                    <?php if($esCond): ?><span class="q-badge"><i class="fas fa-code-branch"></i> Condicional</span><?php endif; ?>
                                </div>

                                <?php if($preg['tipo_pregunta'] === 'texto_corto'): ?>
                                    <input class="q-input" type="text" name="pregunta_<?php echo $preg['id']; ?>" <?php echo (!$esCond && $preg['requerido']) ? 'required' : ''; ?> <?php echo $esCond ? 'disabled' : ''; ?> data-required="<?php echo $preg['requerido']?1:0; ?>" placeholder="Escribe tu respuesta...">
                                <?php elseif($preg['tipo_pregunta'] === 'texto_largo'): ?>
                                    <textarea class="q-textarea" name="pregunta_<?php echo $preg['id']; ?>" <?php echo (!$esCond && $preg['requerido']) ? 'required' : ''; ?> <?php echo $esCond ? 'disabled' : ''; ?> data-required="<?php echo $preg['requerido']?1:0; ?>" placeholder="Escribe tu respuesta detallada..."></textarea>
                                <?php elseif($preg['tipo_pregunta'] === 'seleccion_unica'): 
                                    $opciones = json_decode($preg['opciones'] ?? '[]', true);
                                    if(!is_array($opciones)) $opciones = [];
                                ?>
                                    <div class="opts-list">
                                        <?php foreach($opciones as $op): ?>
                                        <div class="opt-item">
                                            <input type="radio" name="pregunta_<?php echo $preg['id']; ?>" value="<?php echo htmlspecialchars($op); ?>" id="r_<?php echo $preg['id'].'_'.md5($op); ?>" <?php echo (!$esCond && $preg['requerido']) ? 'required' : ''; ?> <?php echo $esCond ? 'disabled' : ''; ?> data-required="<?php echo $preg['requerido']?1:0; ?>">
                                            <label for="r_<?php echo $preg['id'].'_'.md5($op); ?>"><?php echo htmlspecialchars($op); ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif($preg['tipo_pregunta'] === 'seleccion_multiple'): 
                                    $opciones = json_decode($preg['opciones'] ?? '[]', true);
                                    if(!is_array($opciones)) $opciones = [];
                                ?>
                                    <div class="opts-list">
                                        <?php foreach($opciones as $op): ?>
                                        <div class="opt-item">
                                            <input type="checkbox" name="pregunta_<?php echo $preg['id']; ?>[]" value="<?php echo htmlspecialchars($op); ?>" id="c_<?php echo $preg['id'].'_'.md5($op); ?>" <?php echo $esCond ? 'disabled' : ''; ?> data-required="<?php echo $preg['requerido']?1:0; ?>">
                                            <label for="c_<?php echo $preg['id'].'_'.md5($op); ?>"><?php echo htmlspecialchars($op); ?></label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif($preg['tipo_pregunta'] === 'satisfaccion'): ?>
                                    <div class="sat-items" data-pregid="<?php echo $preg['id']; ?>">
                                        <input type="hidden" name="pregunta_<?php echo $preg['id']; ?>" id="sat-<?php echo $preg['id']; ?>" value="" data-required="<?php echo $preg['requerido']?1:0; ?>" data-satisfaccion="1" <?php echo $esCond ? 'disabled' : ''; ?>>
                                        <?php
                                        $satEmojis  = ['😞','😕','😐','🙂','😊'];
                                        $satLabels  = ['Pésima','Mala','Buena','Muy buena','Excelente'];
                                        for($sv=1;$sv<=5;$sv++): ?>
                                        <div class="sat-item" data-val="<?php echo $sv; ?>" onclick="selSat(<?php echo $preg['id']; ?>,<?php echo $sv; ?>)">
                                            <div class="sat-face"><?php echo $satEmojis[$sv-1]; ?></div>
                                            <div class="sat-label"><?php echo $satLabels[$sv-1]; ?></div>
                                            <div class="sat-num"><?php echo $sv; ?></div>
                                        </div>
                                        <?php endfor; ?>
                                    </div>
                                <?php elseif($preg['tipo_pregunta'] === 'matriz'):
                                    $matCfg = json_decode($preg['opciones'] ?? '{}', true);
                                    if(!is_array($matCfg)) $matCfg = [];
                                    $matFilas = is_array($matCfg['filas'] ?? null) ? $matCfg['filas'] : [];
                                    $matColImp = $matCfg['col_importancia'] ?? '¿Qué tan IMPORTANTE es para usted?';
                                    $matColSat = $matCfg['col_satisfaccion'] ?? '¿Qué tan SATISFECHO está?';
                                    $matEscImp = ['1'=>'Nada importante','2'=>'Poco importante','3'=>'Medianamente importante','4'=>'Importante','5'=>'Muy importante'];
                                    $matEscSat = ['1'=>'Muy insatisfecho','2'=>'Insatisfecho','3'=>'Ni satisfecho ni insatisfecho','4'=>'Satisfecho','5'=>'Muy satisfecho'];
                                    $matReq = (!$esCond && $preg['requerido']) ? 'required' : '';
                                ?>
                                    <div class="mat-wrap">
                                    <table class="mat-table">
                                        <thead>
                                            <tr>
                                                <th class="mat-n">N°</th>
                                                <th class="mat-asp">Aspecto</th>
                                                <th class="mat-nec">NECESIDAD<small>Lo que usted REQUIERE</small></th>
                                                <th class="mat-sel"><?php echo htmlspecialchars($matColImp); ?></th>
                                                <th class="mat-sel"><?php echo htmlspecialchars($matColSat); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="mat-ej">
                                                <td class="mat-n">Ej.</td>
                                                <td class="mat-asp" style="font-weight:400;font-style:italic;color:#8a8f98;">Cafetería</td>
                                                <td class="mat-nec" style="background:#fff;">Alimentación a precio justo en el campus.</td>
                                                <td class="mat-sel">5 Muy importante</td>
                                                <td class="mat-sel">3 Ni satisfecho ni insatisfecho</td>
                                            </tr>
                                        <?php foreach(array_values($matFilas) as $mi => $mf): ?>
                                            <tr>
                                                <td class="mat-n"><?php echo $mi + 1; ?></td>
                                                <td class="mat-asp"><?php echo htmlspecialchars($mf['aspecto'] ?? ''); ?></td>
                                                <td class="mat-nec"><?php echo htmlspecialchars($mf['necesidad'] ?? ''); ?></td>
                                                <td class="mat-sel" data-label="<?php echo htmlspecialchars($matColImp); ?>">
                                                    <select class="mat-select" name="pregunta_<?php echo $preg['id']; ?>[<?php echo $mi; ?>][imp]" <?php echo $matReq; ?> <?php echo $esCond ? 'disabled' : ''; ?> data-required="<?php echo $preg['requerido']?1:0; ?>">
                                                        <option value="">Seleccione…</option>
                                                        <?php foreach($matEscImp as $v => $l): ?><option value="<?php echo $v; ?>"><?php echo $v.' '.$l; ?></option><?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td class="mat-sel" data-label="<?php echo htmlspecialchars($matColSat); ?>">
                                                    <select class="mat-select" name="pregunta_<?php echo $preg['id']; ?>[<?php echo $mi; ?>][sat]" <?php echo $matReq; ?> <?php echo $esCond ? 'disabled' : ''; ?> data-required="<?php echo $preg['requerido']?1:0; ?>">
                                                        <option value="">Seleccione…</option>
                                                        <?php foreach($matEscSat as $v => $l): ?><option value="<?php echo $v; ?>"><?php echo $v.' '.$l; ?></option><?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    </div>
                                <?php endif; ?>
                                <?php endif; // fin else (no checkbox) ?>
                            <?php endif; // fin if(tinder)/else ?>
                        </div>
                        <?php endif; // fin else (no encabezado) ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="mobile-nav-section">
                <div class="sidebar-card">
                    <div class="s-ttl"><i class="fas fa-list" style="margin-right:5px;color:var(--accent);"></i>Preguntas</div>
                    <div class="nav-list" id="mobile-nav-list"></div>
                </div>
            </div>

            <div class="submit-sec">
                <button type="submit" class="btn-submit" id="btn-submit">
                    <i class="fas fa-paper-plane"></i> Enviar Respuestas
                </button>
                <p class="submit-note"><i class="fas fa-lock" style="margin-right:4px;"></i> Tu respuesta es anónima y segura</p>
            </div>
        </form>
    </div>

    <div class="sidebar">
        <div class="timer-card">
            <div class="t-label"><i class="fas fa-hourglass-half"></i> Tiempo</div>
            <div class="t-value" id="t-rest">—</div>
            <div class="t-sub" id="t-sub">Calculando...</div>
        </div>
        <div class="sidebar-card">
            <div class="prog-card">
                <div class="prog-row"><span class="prog-lbl">Completado</span><span class="prog-pct" id="pct-lbl">0%</span></div>
                <div class="prog-bar"><div class="prog-fill" id="bar-fill" style="width:0%"></div></div>
            </div>
        </div>
        <div class="sidebar-card">
            <div class="s-ttl">Preguntas</div>
            <div class="nav-list" id="nav-list"></div>
        </div>
    </div>
</div>

<div id="popup-overlay" class="popup-overlay" onclick="if(event.target===this)cerrarPopup()">
    <div class="popup-box">
        <div class="popup-handle"></div>
        <div class="popup-title" id="popup-title"></div>
        <div class="popup-text" id="popup-text"></div>
        <button class="popup-close" onclick="cerrarPopup()">Cerrar</button>
    </div>
</div>

<script>
<?php if(isset($_GET['debug']) && $_GET['debug']=='1'): ?>
// ── DEBUG: capturar errores JS y mostrarlos en pantalla ──
window.addEventListener('error', function(e){
    const errBox = document.createElement('div');
    errBox.style.cssText = 'position:fixed;bottom:0;left:0;right:0;background:#ff3b30;color:white;padding:14px 20px;font-family:monospace;font-size:12px;z-index:9999;max-height:200px;overflow:auto;';
    errBox.innerHTML = '🔧 ERROR JS: <strong>' + (e.message||'desconocido') + '</strong><br>'+
        'Archivo: ' + (e.filename||'') + ' línea ' + (e.lineno||'?') + '<br>'+
        (e.error && e.error.stack ? '<pre style="margin:6px 0 0;white-space:pre-wrap;">'+e.error.stack+'</pre>' : '');
    document.body.appendChild(errBox);
});
console.log('🔧 DEBUG MODE activo');
<?php endif; ?>
const fechaInicio = new Date("<?php echo $encuesta['fecha_inicio']; ?>").getTime();
const fechaCierre = new Date("<?php echo $encuesta['fecha_cierre']; ?>").getTime();
let tinderState = {};
let dragListeners = new Map(); // para evitar múltiples binds

// ========== CONDICIONALES ==========
function actualizarCondicionales() {
    document.querySelectorAll('.q-row.condicional').forEach(row => {
        const padreId = row.getAttribute('data-padre');
        const operador = row.getAttribute('data-operador');
        const condValor = row.getAttribute('data-valor');
        if (!padreId) return;

        // Obtener valor de la pregunta padre
        let valorPadre = '';
        const radio = document.querySelector(`input[name="pregunta_${padreId}"]:checked`);
        if (radio) valorPadre = radio.value;
        const checks = document.querySelectorAll(`input[name="pregunta_${padreId}[]"]:checked`);
        if (checks.length) valorPadre = Array.from(checks).map(c => c.value).join(', ');
        const textInput = document.querySelector(`input[name="pregunta_${padreId}"][type="text"], textarea[name="pregunta_${padreId}"]`);
        if (textInput && textInput.value.trim()) valorPadre = textInput.value.trim();

        let mostrar = false;
        switch (operador) {
            case 'igual': mostrar = (valorPadre == condValor); break;
            case 'diferente': mostrar = (valorPadre != condValor && valorPadre !== ''); break;
            case 'contiene': {
                const vls = (condValor||'').split(',').map(v=>v.trim().toLowerCase()).filter(v=>v);
                mostrar = vls.length ? vls.some(v=>valorPadre.toLowerCase().includes(v)) : valorPadre !== '';
                break;
            }
            case 'no_contiene': {
                const vls = (condValor||'').split(',').map(v=>v.trim().toLowerCase()).filter(v=>v);
                mostrar = valorPadre !== '' && (vls.length ? !vls.some(v=>valorPadre.toLowerCase().includes(v)) : true);
                break;
            }
            default: mostrar = (valorPadre == condValor);
        }

        if (mostrar) {
            row.classList.remove('oculta');
            row.querySelectorAll('input,textarea,select').forEach(el => {
                el.removeAttribute('disabled');
                if (el.getAttribute('data-required') === '1' && el.type !== 'checkbox' && el.type !== 'radio') {
                    el.setAttribute('required', 'required');
                } else if ((el.type === 'radio' || el.type === 'checkbox') && el.getAttribute('data-required') === '1') {
                    // No se puede usar required directo en grupo, se maneja por JS
                }
            });
        } else {
            row.classList.add('oculta');
            row.querySelectorAll('input,textarea,select').forEach(el => {
                el.removeAttribute('required');
                el.setAttribute('disabled', 'disabled');
                if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
                else if (el.type !== 'hidden') el.value = '';
            });
            // Si es Tinder, limpiar estado
            if (row.hasAttribute('data-tinder')) {
                const pregId = row.id.replace('pregunta-', '');
                if (tinderState[pregId]) {
                    tinderState[pregId] = [];
                    actualizarRespuestaTinder(pregId);
                }
                const hidden = document.getElementById(`tinder-resp-${pregId}`);
                if (hidden) hidden.value = '';
                // Reiniciar las cartas (opcional: recargar la stage)
                const stage = document.getElementById(`tinder-stage-${pregId}`);
                if (stage) reiniciarTinderStage(pregId, stage);
            }
        }
    });
    actualizarProgreso();
}

// ========== TINDER CARD LOGIC (rewrite simple y robusto) ==========
// tinderState[pregId] = array de {titulo, decision}
// currentIndex[pregId] = índice de la tarjeta visible (0-based sobre cards originales)

let currentIndex = {};

function initTinderStage(stage, pregId) {
    const cards = Array.from(stage.querySelectorAll('.tcard'));
    if (!cards.length) {
        mostrarEmptyState(stage);
        return;
    }
    // Ocultar todas, mostrar solo la del currentIndex
    if (currentIndex[pregId] === undefined) currentIndex[pregId] = 0;
    cards.forEach((card, idx) => {
        const cardIdx = parseInt(card.getAttribute('data-card-idx'));
        if (cardIdx === currentIndex[pregId]) {
            card.style.display = 'flex';
            card.style.transform = 'translateX(0) rotate(0deg)';
            card.style.opacity = '1';
            card.style.transition = 'transform .3s ease, opacity .3s ease';
            card.style.zIndex = '10';
            const ls = card.querySelector('.tcard-like-stamp'); if (ls) ls.style.opacity = 0;
            const ns = card.querySelector('.tcard-nope-stamp'); if (ns) ns.style.opacity = 0;
            setupDrag(card, pregId);
        } else {
            card.style.display = 'none';
        }
    });
    actualizarCounter(pregId);
}

function mostrarEmptyState(stage) {
    if (stage.querySelector('.tinder-empty')) return;
    const div = document.createElement('div');
    div.className = 'tinder-empty';
    div.innerHTML = '<i class="fas fa-check-circle" style="color:var(--green);"></i><div style="font-weight:600;color:var(--text);">¡Listo!</div><div style="font-size:12px;margin-top:4px;">Todas las tarjetas evaluadas</div>';
    stage.appendChild(div);
}

function initAllTinderStages() {
    document.querySelectorAll('.tinder-stage').forEach(stage => {
        const pregId = stage.id.replace('tinder-stage-', '');
        if (!tinderState[pregId]) tinderState[pregId] = [];
        if (currentIndex[pregId] === undefined) currentIndex[pregId] = 0;
        initTinderStage(stage, pregId);
    });
}

function setupDrag(card, pregId) {
    // Limpiar listeners previos
    if (dragListeners.has(card)) {
        const old = dragListeners.get(card);
        card.removeEventListener('mousedown', old.start);
        document.removeEventListener('mousemove', old.move);
        document.removeEventListener('mouseup', old.end);
        card.removeEventListener('touchstart', old.startTouch);
        document.removeEventListener('touchmove', old.moveTouch);
        document.removeEventListener('touchend', old.endTouch);
    }

    let startX = 0, currX = 0, isDragging = false;
    let likeStamp = card.querySelector('.tcard-like-stamp');
    let nopeStamp = card.querySelector('.tcard-nope-stamp');

    function onStart(e) {
        // Evitar drag si el target es el botón de info
        if (e.target.closest('.tcard-info-btn')) return;
        isDragging = true;
        const clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
        startX = clientX;
        currX = 0;
        card.style.transition = 'none';
    }
    function onMove(e) {
        if (!isDragging) return;
        const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
        currX = clientX - startX;
        // En mobile, si se está deslizando horizontalmente, prevenir el scroll vertical
        if (e.type === 'touchmove' && Math.abs(currX) > 6 && e.cancelable) {
            e.preventDefault();
        }
        const rot = currX * 0.08;
        card.style.transform = `translateX(${currX}px) rotate(${rot}deg)`;
        if (likeStamp) likeStamp.style.opacity = Math.max(0, Math.min(1, currX / 100));
        if (nopeStamp) nopeStamp.style.opacity = Math.max(0, Math.min(1, -currX / 100));
    }
    function onEnd() {
        if (!isDragging) return;
        isDragging = false;
        card.style.transition = 'transform .3s ease, opacity .3s ease';
        const threshold = 90;
        if (currX > threshold) {
            swipeCard(card, pregId, 'yes');
        } else if (currX < -threshold) {
            swipeCard(card, pregId, 'no');
        } else {
            card.style.transform = 'translateX(0) rotate(0deg)';
            if (likeStamp) likeStamp.style.opacity = 0;
            if (nopeStamp) nopeStamp.style.opacity = 0;
        }
        currX = 0;
    }

    card.addEventListener('mousedown', onStart);
    document.addEventListener('mousemove', onMove);
    document.addEventListener('mouseup', onEnd);
    card.addEventListener('touchstart', onStart, { passive: true });
    document.addEventListener('touchmove', onMove, { passive: false });
    document.addEventListener('touchend', onEnd);

    dragListeners.set(card, {
        start: onStart, move: onMove, end: onEnd,
        startTouch: onStart, moveTouch: onMove, endTouch: onEnd
    });
}

function swipeCard(card, pregId, decision) {
    const titulo = card.getAttribute('data-titulo');
    if (!tinderState[pregId]) tinderState[pregId] = [];
    tinderState[pregId].push({ titulo, decision });

    const dir = decision === 'yes' ? window.innerWidth : -window.innerWidth;
    card.style.transition = 'transform .4s ease, opacity .4s ease';
    card.style.transform = `translateX(${dir}px) rotate(${dir * 0.04}deg)`;
    card.style.opacity = '0';

    setTimeout(() => {
        card.style.display = 'none';
        currentIndex[pregId]++;
        actualizarSiguienteCard(pregId);
        actualizarRespuestaTinder(pregId);
        actualizarProgreso();
    }, 380);
}

function tinderSwipe(pregId, decision) {
    const stage = document.getElementById(`tinder-stage-${pregId}`);
    if (!stage) return;
    // Buscar carta visible
    const cards = Array.from(stage.querySelectorAll('.tcard'));
    const idx = currentIndex[pregId] || 0;
    const card = cards.find(c => parseInt(c.getAttribute('data-card-idx')) === idx);
    if (!card || card.style.display === 'none') return;
    swipeCard(card, pregId, decision);
}

function actualizarSiguienteCard(pregId) {
    const stage = document.getElementById(`tinder-stage-${pregId}`);
    if (!stage) return;
    const cards = Array.from(stage.querySelectorAll('.tcard'));
    const totalCards = cards.length;
    const idx = currentIndex[pregId];

    if (idx >= totalCards) {
        // No quedan más cartas
        mostrarEmptyState(stage);
        actualizarBotonesTinder(pregId, true);
        actualizarCounter(pregId);
        return;
    }
    const next = cards.find(c => parseInt(c.getAttribute('data-card-idx')) === idx);
    if (next) {
        next.style.display = 'flex';
        next.style.transform = 'translateX(0) rotate(0deg)';
        next.style.opacity = '1';
        next.style.transition = 'transform .3s ease, opacity .3s ease';
        next.style.zIndex = '10';
        const ls = next.querySelector('.tcard-like-stamp'); if (ls) ls.style.opacity = 0;
        const ns = next.querySelector('.tcard-nope-stamp'); if (ns) ns.style.opacity = 0;
        setupDrag(next, pregId);
    }
    actualizarCounter(pregId);
}

function actualizarBotonesTinder(pregId, disable) {
    // Localizar todos los botones de esta pregunta y deshabilitarlos
    const stage = document.getElementById(`tinder-stage-${pregId}`);
    if (!stage) return;
    const section = stage.closest('.tinder-section');
    if (!section) return;
    section.querySelectorAll('.tinder-btn').forEach(btn => {
        btn.disabled = !!disable;
    });
}

function actualizarCounter(pregId) {
    const stage = document.getElementById(`tinder-stage-${pregId}`);
    if (!stage) return;
    const totalCards = stage.querySelectorAll('.tcard').length;
    const idx = currentIndex[pregId] || 0;
    const remaining = Math.max(0, totalCards - idx);
    const counter = document.getElementById(`tinder-counter-${pregId}`);
    if (counter) {
        if (remaining > 0) {
            counter.textContent = `${remaining} de ${totalCards} tarjeta${totalCards !== 1 ? 's' : ''} restante${remaining !== 1 ? 's' : ''}`;
        } else {
            counter.textContent = '✓ Todas las tarjetas evaluadas';
        }
    }
}

function reiniciarTinderStage(pregId, stage) {
    // Reset completo: limpiar empty state, restablecer indice y estado
    currentIndex[pregId] = 0;
    tinderState[pregId] = [];
    const empty = stage.querySelector('.tinder-empty');
    if (empty) empty.remove();
    actualizarBotonesTinder(pregId, false);
    initTinderStage(stage, pregId);
}

function actualizarRespuestaTinder(pregId) {
    const hidden = document.getElementById(`tinder-resp-${pregId}`);
    if (hidden) hidden.value = JSON.stringify(tinderState[pregId] || []);
    const resultsDiv = document.getElementById(`tinder-results-${pregId}`);
    if (resultsDiv) {
        const stage = document.getElementById(`tinder-stage-${pregId}`);
        const section = stage ? stage.closest('.tinder-section') : null;
        const labelSi = section ? (section.dataset.labelSi || 'Me gusta') : 'Me gusta';
        const labelNo = section ? (section.dataset.labelNo || 'No me gusta') : 'No me gusta';
        const state = tinderState[pregId] || [];
        resultsDiv.innerHTML = state.map(r => `<div class="tinder-result-item"><span class="tinder-result-title">${escHtml(r.titulo)}</span><span class="tinder-result-badge ${r.decision === 'yes' ? 'badge-yes' : 'badge-no'}">${r.decision === 'yes' ? '✓ ' + escHtml(labelSi) : '✗ ' + escHtml(labelNo)}</span></div>`).join('');
    }
}

function abrirPopup(titulo, desc) {
    document.getElementById('popup-title').textContent = titulo || '';
    // Preservar saltos de línea en la descripción del popup
    const el = document.getElementById('popup-text');
    el.innerHTML = '';
    (desc||'').split('\n').forEach((line,i,arr) => {
        el.appendChild(document.createTextNode(line));
        if(i < arr.length-1) el.appendChild(document.createElement('br'));
    });
    document.getElementById('popup-overlay').classList.add('open');
}
function cerrarPopup() {
    document.getElementById('popup-overlay').classList.remove('open');
}
function escHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function selSat(pregId, val) {
    const hidden = document.getElementById('sat-' + pregId);
    if (hidden && hidden.disabled) return;
    if (hidden) hidden.value = val;
    document.querySelectorAll(`.sat-items[data-pregid="${pregId}"] .sat-item`).forEach(item => {
        item.classList.toggle('sat-selected', parseInt(item.dataset.val) === val);
    });
    actualizarCondicionales();
    actualizarProgreso();
}

// ========== PROGRESO Y NAVEGACIÓN ==========
function actualizarProgreso() {
    const rows = document.querySelectorAll('.q-row');
    let total = 0, respondidas = 0;
    rows.forEach(row => {
        if (row.classList.contains('oculta')) return;
        if (row.querySelector('.q-head, .q-info')) return;
        total++;
        if (row.hasAttribute('data-tinder')) {
            const pregId = row.id.replace('pregunta-', '');
            if (tinderState[pregId] && tinderState[pregId].length > 0) respondidas++;
            return;
        }
        const radios = row.querySelectorAll('input[type="radio"]:checked');
        const checks = row.querySelectorAll('input[type="checkbox"]:checked');
        const texts = Array.from(row.querySelectorAll('input[type="text"], textarea')).filter(i => i.value.trim() !== '');
        const sats = Array.from(row.querySelectorAll('input[data-satisfaccion]')).filter(i => i.value !== '');
        const mats = Array.from(row.querySelectorAll('select.mat-select'));
        mats.forEach(s => s.classList.toggle('mat-filled', s.value !== ''));
        const matOk = mats.length > 0 && mats.every(s => s.value !== '');
        if (radios.length || checks.length || texts.length || sats.length || matOk) respondidas++;
    });
    const pct = total > 0 ? Math.round((respondidas / total) * 100) : 0;
    document.getElementById('prog-fill').style.width = pct + '%';
    document.getElementById('bar-fill').style.width = pct + '%';
    document.getElementById('pct-lbl').textContent = pct + '%';
    actualizarNav();
}

function construirNav() {
    ['nav-list', 'mobile-nav-list'].forEach(listId => {
        const nav = document.getElementById(listId);
        if (!nav) return;
        nav.innerHTML = '';
        let idx = 1;
        const isMobile = listId === 'mobile-nav-list';
        document.querySelectorAll('.q-row').forEach(row => {
            if (row.querySelector('.q-head, .q-info')) return;
            const label = row.querySelector('.q-label, .tinder-q-label');
            if (!label) return;
            let texto = label.innerText.trim().substring(0, 40);
            if (!texto) texto = 'Pregunta';
            const item = document.createElement('div');
            item.className = 'nav-item';
            item.id = `${isMobile ? 'm' : ''}nav-${row.id}`;
            item.innerHTML = `<span class="nav-num">${idx}</span><span>${texto.substring(0, 36)}${texto.length > 36 ? '…' : ''}</span>`;
            item.onclick = () => {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                row.style.outline = '2px solid var(--accent)';
                setTimeout(() => row.style.outline = '', 1500);
            };
            nav.appendChild(item);
            idx++;
        });
    });
}

function actualizarNav() {
    document.querySelectorAll('.q-row').forEach(row => {
        ['', 'm'].forEach(prefix => {
            const navItem = document.getElementById(`${prefix}nav-${row.id}`);
            if (!navItem) return;
            if (row.classList.contains('oculta')) { navItem.style.display = 'none'; return; }
            navItem.style.display = 'flex';
            let respondida = false;
            if (row.hasAttribute('data-tinder')) {
                const pregId = row.id.replace('pregunta-', '');
                respondida = tinderState[pregId] && tinderState[pregId].length > 0;
            } else {
                const checks = row.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked');
                const texts = Array.from(row.querySelectorAll('input[type="text"], textarea')).filter(i => i.value.trim() !== '');
                const sats = Array.from(row.querySelectorAll('input[data-satisfaccion]')).filter(i => i.value !== '');
                const mats = Array.from(row.querySelectorAll('select.mat-select'));
                const matOk = mats.length > 0 && mats.every(s => s.value !== '');
                respondida = checks.length > 0 || texts.length > 0 || sats.length > 0 || matOk;
            }
            if (respondida) navItem.classList.add('nav-answered');
            else navItem.classList.remove('nav-answered');
        });
    });
}

function actualizarTiempo() {
    const ahora = Date.now();
    const diff = fechaCierre - ahora;
    if (ahora < fechaInicio) {
        const dias = Math.floor((fechaInicio - ahora) / 86400000);
        const horas = Math.floor(((fechaInicio - ahora) % 86400000) / 3600000);
        document.getElementById('t-rest').textContent = `${dias}d ${horas}h`;
        document.getElementById('t-sub').textContent = 'Próximamente';
    } else if (diff > 0) {
        const dias = Math.floor(diff / 86400000);
        const horas = Math.floor((diff % 86400000) / 3600000);
        const minutos = Math.floor((diff % 3600000) / 60000);
        document.getElementById('t-rest').textContent = `${dias}d ${horas}h ${minutos}m`;
        document.getElementById('t-sub').textContent = 'Para el cierre';
    } else {
        document.getElementById('t-rest').textContent = 'Cerrada';
        document.getElementById('t-sub').textContent = 'No acepta más respuestas';
        document.getElementById('btn-submit').disabled = true;
        document.querySelectorAll('#encuestaForm input, #encuestaForm textarea, #encuestaForm select').forEach(el => el.disabled = true);
    }
    const mR = document.getElementById('t-rest-mobile');
    const mS = document.getElementById('t-sub-mobile');
    if (mR) mR.textContent = document.getElementById('t-rest').textContent;
    if (mS) mS.textContent = document.getElementById('t-sub').textContent;
}

// ========== EVENTOS ==========
document.addEventListener('DOMContentLoaded', () => {
    actualizarTiempo();
    setInterval(actualizarTiempo, 60000);
    initAllTinderStages();
    actualizarCondicionales();
    construirNav();
    actualizarProgreso();

    // Eventos para cambios en inputs
    document.querySelectorAll('input, textarea, select').forEach(el => {
        el.addEventListener('change', () => {
            actualizarCondicionales();
            actualizarProgreso();
        });
        el.addEventListener('input', () => {
            actualizarCondicionales();
            actualizarProgreso();
        });
    });
});
</script>
</body>
</html>