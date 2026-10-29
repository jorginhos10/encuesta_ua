<?php if(!isset($_SESSION['user_id'])){ header("Location: " . BASE_URL . "login"); exit(); } ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo BASE_URL; ?>">
    <title>Constructor · Encuesta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        :root{--bg:#f2f2f7;--surface:#fff;--surface2:#f2f2f7;--border:rgba(0,0,0,.08);--border-strong:rgba(0,0,0,.12);--text:#1c1c1e;--text2:#636366;--text3:#aeaeb2;--accent:#007aff;--al:rgba(0,122,255,.1);--green:#34c759;--red:#ff3b30;--orange:#ff9500;--purple:#af52de;--tinder:#fe3c72;--sh:0 1px 3px rgba(0,0,0,.07);--shm:0 4px 16px rgba(0,0,0,.08);--shl:0 20px 60px rgba(0,0,0,.12);--r:16px;--rs:10px;--nav:64px;}
        *{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased;}
        body{font-family:'DM Sans',-apple-system,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}
        .navbar{position:sticky;top:0;z-index:200;height:var(--nav);background:rgba(255,255,255,.85);backdrop-filter:blur(24px);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 24px;}
        .navbar-left{display:flex;align-items:center;gap:12px;}
        .navbar-logo{width:34px;height:34px;background:var(--accent);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:15px;}
        .navbar h1{font-size:17px;font-weight:600;letter-spacing:-.3px;}
        .navbar-right{display:flex;align-items:center;gap:10px;}
        .nav-avatar{width:24px;height:24px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-size:10px;font-weight:600;flex-shrink:0;}
        .profile-wrap{position:relative;}
        .nav-user{display:flex;align-items:center;gap:8px;background:var(--surface2);border-radius:20px;padding:6px 12px 6px 8px;font-size:13px;color:var(--text2);cursor:pointer;border:none;font-family:inherit;transition:background .15s;}
        .nav-user:hover{background:var(--border-strong);}
        .drop-arrow{font-size:9px;margin-left:2px;transition:transform .2s;}
        .drop-menu{display:none;position:absolute;right:0;top:calc(100% + 8px);background:var(--surface);border:1px solid var(--border-strong);border-radius:14px;padding:6px;min-width:200px;box-shadow:0 8px 28px rgba(0,0,0,.13);z-index:500;}
        .drop-menu.open{display:block;animation:dropIn .14s ease;}
        @keyframes dropIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
        .drop-item{display:flex;align-items:center;gap:10px;width:100%;padding:9px 12px;border-radius:9px;font-family:inherit;font-size:13px;font-weight:500;color:var(--text);background:none;border:none;cursor:pointer;text-decoration:none;transition:background .12s;}
        .drop-item:hover{background:var(--surface2);}
        .drop-item i{width:14px;text-align:center;color:var(--text3);}
        .drop-danger{color:var(--red)!important;}
        .drop-danger i{color:var(--red)!important;}
        .drop-danger:hover{background:rgba(255,59,48,.08)!important;}
        .drop-divider{height:1px;background:var(--border);margin:5px 4px;}
        .nav-back{display:flex;align-items:center;gap:6px;background:var(--accent);color:white;padding:7px 14px;border-radius:20px;text-decoration:none;font-size:13px;font-weight:500;}
        .nav-back:hover{opacity:.85;}
        .layout{display:grid;grid-template-columns:1fr 300px;max-width:1200px;margin:0 auto;padding:24px 16px;gap:20px;align-items:start;}
        .section-card{background:var(--surface);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--border);margin-bottom:16px;overflow:hidden;transition:box-shadow .2s;}
        .section-card:hover{box-shadow:var(--shm);}
        .section-header{padding:18px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;gap:10px;background:linear-gradient(135deg,rgba(0,122,255,.04),rgba(175,82,222,.03));}
        .sec-drag-handle{color:var(--text3);cursor:grab;font-size:14px;padding:4px 2px;flex-shrink:0;transition:color .15s;}.sec-drag-handle:hover{color:var(--accent);}
        .sec-actions{display:flex;gap:4px;flex-shrink:0;}
        .section-title{font-size:15px;font-weight:600;letter-spacing:-.2px;}
        .section-subtitle{font-size:12px;color:var(--text3);margin-top:2px;}
        .section-pill{background:var(--al);color:var(--accent);font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;}
        .section-body{padding:12px 16px 16px;}
        .section-footer{padding:12px 16px;border-top:1px solid var(--border);display:flex;gap:8px;}
        .questions-list{min-height:8px;}
        .question-item{background:var(--surface2);border-radius:var(--rs);padding:12px 14px;margin-bottom:8px;display:flex;align-items:flex-start;gap:10px;transition:background .15s;border:1px solid transparent;}
        .question-item:hover{background:#e8e8ed;border-color:var(--border);}
        .question-item.dragging{opacity:.4;}
        .question-item.tinder-type{border-left:3px solid var(--tinder);}
        .drag-handle{color:var(--text3);cursor:grab;padding-top:2px;font-size:13px;flex-shrink:0;}
        .question-content{flex:1;min-width:0;}
        .question-text{font-size:14px;font-weight:500;line-height:1.4;margin-bottom:4px;}
        .question-meta{display:flex;flex-wrap:wrap;gap:5px;margin-top:5px;}
        .tag{font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;}
        .tag-type{background:var(--al);color:var(--accent);}
        .tag-tinder{background:rgba(254,60,114,.12);color:var(--tinder);}
        .tag-req{background:rgba(255,59,48,.1);color:var(--red);}
        .tag-cond{background:rgba(175,82,222,.1);color:var(--purple);}
        .tag-head{background:rgba(255,149,0,.1);color:var(--orange);}
        .tag-info{background:rgba(52,199,89,.1);color:var(--green);}
        .question-sub{font-size:11px;color:var(--text3);margin-top:4px;}
        .cond-detail{font-size:11px;color:var(--purple);margin-top:3px;}
        .tinder-preview{display:flex;gap:5px;flex-wrap:wrap;margin-top:5px;}
        .tinder-mini{background:white;border:1px solid rgba(254,60,114,.2);border-radius:7px;padding:3px 8px;font-size:10px;color:var(--tinder);}
        .question-actions{display:flex;gap:2px;flex-shrink:0;}
        .action-btn{width:30px;height:30px;border:none;border-radius:var(--rs);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--text2);transition:background .15s,color .15s;}
        .action-btn.edit:hover{background:var(--al);color:var(--accent);}
        .action-btn.del:hover{background:rgba(255,59,48,.1);color:var(--red);}
        .empty-section{text-align:center;padding:20px;color:var(--text3);font-size:13px;}
        .btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:10px;font-size:14px;font-weight:500;border:none;cursor:pointer;font-family:inherit;transition:all .15s;text-decoration:none;}
        .btn-primary{background:var(--accent);color:white;}
        .btn-primary:hover{background:#0066d6;}
        .btn-secondary{background:var(--surface2);color:var(--text);border:1px solid var(--border-strong);}
        .btn-secondary:hover{background:#e5e5ea;}
        .btn-success{background:var(--green);color:white;}
        .btn-sm{padding:7px 13px;font-size:13px;}
        .sidebar{position:sticky;top:calc(var(--nav) + 24px);}
        .sidebar-card{background:var(--surface);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--border);overflow:hidden;margin-bottom:14px;}
        .timer-card{background:linear-gradient(145deg,#1c1c1e,#2c2c2e);border-radius:var(--r);padding:20px;text-align:center;box-shadow:var(--shm);margin-bottom:14px;position:relative;overflow:hidden;}
        .timer-card::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 70% 20%,rgba(0,122,255,.25),transparent 60%);}
        .timer-label{font-size:11px;font-weight:600;letter-spacing:1px;color:rgba(255,255,255,.5);text-transform:uppercase;margin-bottom:8px;position:relative;}
        .timer-value{font-size:34px;font-weight:300;letter-spacing:-2px;color:white;line-height:1;margin-bottom:6px;font-family:'DM Serif Display',serif;position:relative;}
        .timer-sub{font-size:11px;color:rgba(255,255,255,.4);position:relative;}
        .sidebar-ttl{padding:14px 16px 10px;font-size:11px;font-weight:700;letter-spacing:.8px;color:var(--text3);text-transform:uppercase;border-bottom:1px solid var(--border);}
        .shortcut-list{padding:8px 10px;max-height:320px;overflow-y:auto;}
        .shortcut-item{display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:var(--rs);cursor:pointer;transition:background .1s;font-size:12px;color:var(--text2);}
        .shortcut-item:hover{background:var(--al);color:var(--accent);}
        .shortcut-num{width:18px;height:18px;background:var(--surface2);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--text3);flex-shrink:0;}
        .shortcut-txt{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
        .banco-fab{position:fixed;bottom:28px;right:28px;width:56px;height:56px;background:linear-gradient(145deg,#1c1c1e,#3a3a3c);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:20px;box-shadow:0 8px 24px rgba(0,0,0,.25);cursor:pointer;border:none;transition:transform .2s;z-index:100;}
        .banco-fab:hover{transform:scale(1.08);}
        .imagenes-fab{position:fixed;bottom:28px;right:96px;width:46px;height:46px;background:linear-gradient(145deg,var(--purple),#007aff);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:17px;box-shadow:0 6px 18px rgba(175,82,222,.35);cursor:pointer;border:none;transition:transform .2s;z-index:100;}
        .imagenes-fab:hover{transform:scale(1.08);}
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(6px);z-index:500;align-items:center;justify-content:center;}
        .modal-overlay.open{display:flex;animation:fadeIn .2s ease;}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        .modal-box{background:var(--surface);border-radius:20px;width:94%;max-width:580px;max-height:88vh;display:flex;flex-direction:column;box-shadow:var(--shl);animation:slideUp .25s cubic-bezier(.34,1.56,.64,1);}
        .modal-box.wide{max-width:660px;}
        @keyframes slideUp{from{transform:translateY(30px) scale(.97);opacity:0}to{transform:none;opacity:1}}
        .mh{padding:20px 22px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);flex-shrink:0;}
        .mh-title{font-size:17px;font-weight:600;letter-spacing:-.3px;}
        .mc{width:28px;height:28px;border-radius:50%;background:var(--surface2);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--text2);}
        .mb{padding:20px 22px;overflow-y:auto;flex:1;}
        .mb::-webkit-scrollbar{width:5px;}
        .mb::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:3px;}
        .mf{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;flex-shrink:0;}
        .fg{margin-bottom:18px;}
        .fl{display:block;font-size:13px;font-weight:600;color:var(--text2);margin-bottom:7px;}
        .fl i{margin-right:5px;color:var(--accent);}
        .opt{font-weight:400;color:var(--text3);font-size:11px;margin-left:4px;}
        .fi,.fs,.ft{width:100%;padding:11px 14px;border:1.5px solid var(--border-strong);border-radius:var(--rs);font-size:14px;font-family:inherit;color:var(--text);background:var(--surface);outline:none;appearance:none;transition:border-color .15s,box-shadow .15s;}
        .cond-chips{display:flex;flex-wrap:wrap;gap:6px;}
        .cond-chip{padding:5px 12px;border-radius:20px;border:1.5px solid var(--border-strong);background:var(--surface2);font-size:12px;color:var(--text2);cursor:pointer;transition:all .15s;user-select:none;}
        .cond-chip:hover{border-color:var(--accent);color:var(--accent);}
        .cond-chip.active{background:var(--accent);color:#fff;border-color:var(--accent);}
        .txt-toolbar{display:flex;gap:6px;margin-bottom:6px;}
        .txt-tool{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border:1.5px solid var(--border-strong);border-radius:8px;background:var(--surface2);font-size:12px;color:var(--text2);cursor:pointer;transition:all .15s;font-family:inherit;}
        .txt-tool:hover{border-color:var(--accent);color:var(--accent);background:var(--al);}
        .fi:focus,.fs:focus,.ft:focus{border-color:var(--accent);box-shadow:0 0 0 4px var(--al);}
        .ft{resize:vertical;min-height:80px;line-height:1.5;}
        .fs{background-image:url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 7L11 1' stroke='%23636366' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:36px;}
        .fdiv{border:none;border-top:1px solid var(--border);margin:20px 0;}
        .fst{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text3);margin-bottom:14px;display:flex;align-items:center;gap:7px;}
        .fst i{color:var(--purple);}
        .cbr{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:var(--rs);background:var(--surface2);cursor:pointer;transition:background .15s;margin-bottom:12px;}
        .cbr:hover{background:var(--al);}
        .cbr input[type="checkbox"]{width:17px;height:17px;accent-color:var(--accent);}
        .opts-box{border:1.5px solid var(--border-strong);border-radius:var(--rs);overflow:hidden;}
        .opt-chip{display:flex;align-items:center;justify-content:space-between;padding:9px 12px;border-bottom:1px solid var(--border);font-size:13px;}
        .opt-chip:last-child{border-bottom:none;}
        .opt-dot{width:7px;height:7px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-right:8px;}
        .opt-rm{width:22px;height:22px;border-radius:50%;background:rgba(255,59,48,.1);color:var(--red);border:none;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;}
        .opt-add{display:flex;gap:8px;padding:10px 12px;background:var(--surface2);border-top:1px solid var(--border);}
        .cond-box{background:linear-gradient(135deg,rgba(175,82,222,.05),rgba(0,122,255,.03));border:1.5px solid rgba(175,82,222,.2);border-radius:var(--rs);padding:16px;}
        /* TINDER BUILDER */
        .tb{background:linear-gradient(135deg,rgba(254,60,114,.04),rgba(253,125,75,.03));border:1.5px solid rgba(254,60,114,.2);border-radius:var(--rs);padding:16px;margin-bottom:16px;}
        .tb-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--tinder);margin-bottom:14px;display:flex;align-items:center;gap:6px;}
        .tc-item{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:12px;margin-bottom:10px;display:flex;gap:10px;align-items:flex-start;}
        .tc-num{width:24px;height:24px;border-radius:8px;background:linear-gradient(135deg,var(--tinder),#fd7d4b);color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;}
        .tc-fields{flex:1;display:flex;flex-direction:column;gap:8px;}
        .tc-fields input,.tc-fields textarea{padding:8px 11px;border:1.5px solid var(--border-strong);border-radius:8px;font-size:13px;font-family:inherit;color:var(--text);background:var(--surface2);outline:none;transition:border-color .15s;width:100%;}
        .tc-fields input:focus,.tc-fields textarea:focus{border-color:var(--tinder);background:white;}
        .tc-fields textarea{resize:vertical;min-height:55px;line-height:1.4;}
        .tc-fields textarea.tc-ta{min-height:36px;resize:none;overflow:hidden;line-height:1.4;}
        .tc-img-row{display:flex;align-items:center;gap:8px;}
        .tc-pick-btn{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1.5px solid var(--border-strong);background:var(--surface2);color:var(--text3);cursor:pointer;transition:all .15s;flex-shrink:0;}
        .tc-pick-btn:hover{border-color:var(--accent);color:var(--accent);background:var(--al);}
        .tc-img-prev{width:44px;height:44px;border-radius:8px;background:var(--surface2);border:1.5px dashed var(--border-strong);display:flex;align-items:center;justify-content:center;color:var(--text3);font-size:16px;overflow:hidden;flex-shrink:0;}
        .tc-img-prev img{width:100%;height:100%;object-fit:cover;}
        .tc-rm{width:26px;height:26px;border-radius:50%;background:rgba(255,59,48,.1);color:var(--red);border:none;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .add-tc{width:100%;padding:10px;border:2px dashed rgba(254,60,114,.3);border-radius:10px;background:transparent;color:var(--tinder);font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:7px;}
        .add-tc:hover{background:rgba(254,60,114,.06);border-color:var(--tinder);}
        .toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--text);color:white;padding:12px 20px;border-radius:30px;font-size:14px;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.25);display:flex;align-items:center;gap:9px;z-index:1000;transition:transform .3s cubic-bezier(.34,1.56,.64,1),opacity .3s;opacity:0;}
        .toast.show{transform:translateX(-50%) translateY(0);opacity:1;}
        .toast.success .ti{color:var(--green);}
        .toast.error .ti{color:var(--red);}
        @media(max-width:768px){.layout{grid-template-columns:1fr;}.sidebar{position:static;}}
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-left">
        <div class="navbar-logo"><i class="fas fa-pen-nib"></i></div>
        <h1>Constructor de Encuesta</h1>
    </div>
    <div class="navbar-right">
        <div class="profile-wrap" id="profileWrap">
            <button class="nav-user" onclick="toggleDrop(event)" id="profileBtn">
                <div class="nav-avatar"><?php echo strtoupper(substr($_SESSION['user_name'],0,1)); ?></div>
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                <i class="fas fa-chevron-down drop-arrow" id="dropArrow"></i>
            </button>
            <div class="drop-menu" id="dropMenu">
                <button class="drop-item" onclick="abrirAcercaDe()"><i class="fas fa-info-circle"></i> Acerca de nosotros</button>
                <div class="drop-divider"></div>
                <a href="salir" class="drop-item drop-danger"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
        <a href="encuestas" class="nav-back"><i class="fas fa-chevron-left"></i> Encuestas</a>
    </div>
</nav>

<!-- Modal Acerca de nosotros -->
<div id="mAcerca" onclick="if(event.target===this)this.style.display='none'" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:22px;padding:36px;max-width:480px;width:100%;position:relative;box-shadow:0 24px 60px rgba(0,0,0,.2);">
        <button onclick="document.getElementById('mAcerca').style.display='none'" style="position:absolute;top:14px;right:14px;background:rgba(0,0,0,.06);border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:16px;color:#636366;display:flex;align-items:center;justify-content:center;line-height:1;">×</button>
        <div style="width:52px;height:52px;background:linear-gradient(135deg,#007aff,#5856d6);border-radius:15px;display:flex;align-items:center;justify-content:center;color:white;font-size:22px;margin-bottom:18px;box-shadow:0 8px 20px rgba(0,122,255,.25);"><i class="fas fa-cloud"></i></div>
        <h2 style="font-size:19px;font-weight:700;margin-bottom:10px;letter-spacing:-.3px;color:#1c1c1e;">Acerca de nosotros</h2>
        <p style="font-size:14px;line-height:1.75;color:#636366;margin-bottom:14px;">
            Este software fue diseñado y desarrollado por <strong style="color:#1c1c1e;">Cloud Control Technology</strong>, por <strong style="color:#1c1c1e;">Jorge Albeiro Valencia Bolívar</strong>, quien otorga uso, adquisición y entrega total de propiedad intelectual a la <strong style="color:#1c1c1e;">Universidad del Atlántico</strong> y todas sus asociaciones, dependencias y organismos afiliados. La Universidad del Atlántico es titular y propietaria de todos los derechos sobre esta plataforma y sus desarrollos derivados.
        </p>
        <p style="font-size:13px;line-height:1.7;color:#8e8e93;margin-bottom:20px;background:#f9f9fb;border-radius:10px;padding:12px 14px;border-left:3px solid rgba(0,122,255,.25);">
            Agradecimiento especial a <strong style="color:#1c1c1e;">Gabriel Romero</strong>, <strong style="color:#1c1c1e;">Shirlene Buelvas</strong> y <strong style="color:#1c1c1e;">Graciela Angulo</strong> por ser fuente de apoyo e inspiración para las funciones y experiencia de usuario en los desarrollos que se han venido realizando en la Oficina de Planeación de la Universidad del Atlántico sede Norte.
        </p>
        <div style="border-top:1px solid rgba(0,0,0,.07);padding-top:14px;display:flex;align-items:center;gap:10px;">
            <div style="width:30px;height:30px;background:linear-gradient(135deg,#1c1c1e,#3a3a3c);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-university" style="color:white;font-size:11px;"></i></div>
            <span style="font-size:12px;color:#aeaeb2;">© <?php echo date('Y'); ?> Cloud Control Technology · Universidad del Atlántico</span>
        </div>
    </div>
</div>
<div class="layout">
    <div class="content">
        <div id="enc-container"></div>
        <button class="btn btn-primary" onclick="abrirModalSeccion()" style="margin-top:8px;"><i class="fas fa-plus"></i> Agregar Sección</button>
    </div>
    <div class="sidebar">
        <div class="timer-card">
            <div class="timer-label"><i class="fas fa-clock"></i> Tiempo restante</div>
            <div class="timer-value" id="t-rest">—</div>
            <div class="timer-sub" id="t-estado">Calculando...</div>
        </div>
        <div class="sidebar-card">
            <div class="sidebar-ttl">Navegación</div>
            <div class="shortcut-list" id="shortcuts"><div style="text-align:center;padding:16px;color:var(--text3);font-size:12px;">Sin preguntas aún</div></div>
        </div>
    </div>
</div>
<button class="imagenes-fab" onclick="window.open('imagenes','_blank','width=1100,height=750')" title="Banco de imágenes"><i class="fas fa-images"></i></button>
<button class="banco-fab" onclick="abrirBanco()" title="Banco de preguntas"><i class="fas fa-database"></i></button>

<!-- Modal Sección -->
<div id="mSeccion" class="modal-overlay">
    <div class="modal-box">
        <div class="mh"><span class="mh-title" id="mSeccion-title"><i class="fas fa-layer-group" style="color:var(--accent);margin-right:8px;"></i>Nueva Sección</span><button class="mc" onclick="cerrar('mSeccion')"><i class="fas fa-times"></i></button></div>
        <div class="mb">
            <input type="hidden" id="s-id">
            <div class="fg"><label class="fl"><i class="fas fa-heading"></i> Título</label><input class="fi" type="text" id="s-titulo" placeholder="Ej: Información Personal"></div>
            <div class="fg"><label class="fl"><i class="fas fa-align-left"></i> Descripción <span class="opt">(opcional)</span></label><textarea class="ft" id="s-desc" rows="3"></textarea></div>
        </div>
        <div class="mf"><button class="btn btn-secondary" onclick="cerrar('mSeccion')">Cancelar</button><button class="btn btn-success" id="s-btn-guardar" onclick="guardarSeccion()"><i class="fas fa-check"></i> Crear</button></div>
    </div>
</div>

<!-- Modal Pregunta Crear -->
<div id="mPregunta" class="modal-overlay">
    <div class="modal-box wide">
        <div class="mh"><span class="mh-title"><i class="fas fa-plus-circle" style="color:var(--accent);margin-right:8px;"></i>Nuevo Elemento</span><button class="mc" onclick="cerrar('mPregunta')"><i class="fas fa-times"></i></button></div>
        <div class="mb">
            <input type="hidden" id="p-enc-id">
            <div class="fg"><label class="fl"><i class="fas fa-question-circle"></i> Contenido / Título</label><div class="txt-toolbar"><button type="button" class="txt-tool" onclick="insertarEnlace('p-texto')" title="Insertar hipervínculo"><i class="fas fa-link"></i> Enlace</button></div><textarea class="ft" id="p-texto" rows="3" placeholder="Escribe aquí..."></textarea></div>
            <div class="fg"><label class="fl"><i class="fas fa-sliders-h"></i> Tipo</label>
                <select class="fs" id="p-tipo" onchange="onTipoChange()">
                    <option value="texto_corto">📝 Texto Corto</option>
                    <option value="texto_largo">📄 Texto Largo</option>
                    <option value="seleccion_unica">🔘 Selección Única</option>
                    <option value="seleccion_multiple">☑️ Selección Múltiple</option>
                    <option value="checkbox">✅ Casilla de verificación</option>
                    <option value="tinder">🃏 Card Slider (tarjetas deslizables)</option>
                    <option value="satisfaccion">😊 Satisfacción (1-5)</option>
                    <option value="matriz">📊 Matriz Importancia / Satisfacción</option>
                    <option value="encabezado">📌 Encabezado</option>
                    <option value="texto_ordinario">📝 Texto Informativo</option>
                </select>
            </div>
            <div id="p-opts-box" style="display:none;">
                <div class="fg"><label class="fl"><i class="fas fa-list-ul"></i> Opciones</label>
                    <div class="opts-box"><div id="p-opts-list"></div><div class="opt-add"><input class="fi" type="text" id="p-nueva-opt" placeholder="Nueva opción..." style="padding:8px 12px;font-size:13px;"><button class="btn btn-primary btn-sm" onclick="addOpt()"><i class="fas fa-plus"></i></button></div></div>
                </div>
            </div>
            <div id="p-tinder-box" style="display:none;">
                <div class="tb"><div class="tb-title"><i class="fas fa-layer-group"></i> Tarjetas Tinder</div><div style="display:flex;gap:10px;margin-bottom:14px;"><div style="flex:1"><label class="fl" style="font-size:11px;"><i class="fas fa-thumbs-up" style="color:var(--green)"></i> Botón positivo</label><input class="fi" type="text" id="p-label-si" placeholder="Me gusta" style="margin-top:4px;"></div><div style="flex:1"><label class="fl" style="font-size:11px;"><i class="fas fa-thumbs-down" style="color:var(--red)"></i> Botón negativo</label><input class="fi" type="text" id="p-label-no" placeholder="No me gusta" style="margin-top:4px;"></div></div><div id="p-tinder-list"></div><button class="add-tc" onclick="addTinderCard()"><i class="fas fa-plus"></i> Agregar tarjeta</button></div>
            </div>
            <div id="p-mat-box" style="display:none;">
                <div class="tb"><div class="tb-title"><i class="fas fa-table"></i> Matriz Importancia / Satisfacción</div>
                    <div style="display:flex;gap:10px;margin-bottom:14px;"><div style="flex:1"><label class="fl" style="font-size:11px;">Título columna Importancia</label><input class="fi" type="text" id="p-mat-ci" placeholder="¿Qué tan IMPORTANTE es para usted?" style="margin-top:4px;"></div><div style="flex:1"><label class="fl" style="font-size:11px;">Título columna Satisfacción</label><input class="fi" type="text" id="p-mat-cs" placeholder="¿Qué tan SATISFECHO está?" style="margin-top:4px;"></div></div>
                    <div id="p-mat-list"></div><button class="add-tc" onclick="addMatFila()"><i class="fas fa-plus"></i> Agregar fila</button></div>
            </div>
            <div id="p-req-box"><label class="cbr"><input type="checkbox" id="p-req" checked><span><i class="fas fa-asterisk" style="color:var(--red);margin-right:5px;"></i> Respuesta requerida</span></label></div>
            <div id="p-cond-box">
                <hr class="fdiv">
                <div class="fst"><i class="fas fa-code-branch"></i> Lógica Condicional</div>
                <div class="cond-box">
                    <div class="fg" style="margin-bottom:12px;"><label class="fl">Mostrar solo si depende de:</label><select class="fs" id="p-padre" onchange="onPadreChange()"><option value="">Siempre visible</option></select></div>
                    <div id="p-op-box" style="display:none;"><div class="fg" style="margin-bottom:12px;"><label class="fl">Operador:</label><select class="fs" id="p-op"><option value="igual">Es igual a</option><option value="diferente">Es diferente de</option><option value="contiene">Contiene</option><option value="no_contiene">No contiene</option></select></div></div>
                    <div id="p-val-box" style="display:none;"><div class="fg" style="margin-bottom:0;"><label class="fl">Valor:</label><input class="fi" type="text" id="p-val" placeholder="Ej: Sí..."><div id="p-chips-box" style="display:none;margin-top:8px;"></div></div></div>
                </div>
            </div>
            <hr class="fdiv">
            <label class="cbr"><input type="checkbox" id="p-banco"><span><i class="fas fa-database" style="color:var(--accent);margin-right:5px;"></i> Guardar en banco</span></label>
        </div>
        <div class="mf"><button class="btn btn-secondary" onclick="cerrar('mPregunta')">Cancelar</button><button class="btn btn-success" onclick="guardarPregunta()"><i class="fas fa-check"></i> Guardar</button></div>
    </div>
</div>

<!-- Modal Editar -->
<div id="mEditar" class="modal-overlay">
    <div class="modal-box wide">
        <div class="mh"><span class="mh-title"><i class="fas fa-pen" style="color:var(--accent);margin-right:8px;"></i>Editar Elemento</span><button class="mc" onclick="cerrar('mEditar')"><i class="fas fa-times"></i></button></div>
        <div class="mb">
            <input type="hidden" id="e-id">
            <div class="fg"><label class="fl"><i class="fas fa-question-circle"></i> Contenido</label><div class="txt-toolbar"><button type="button" class="txt-tool" onclick="insertarEnlace('e-texto')" title="Insertar hipervínculo"><i class="fas fa-link"></i> Enlace</button></div><textarea class="ft" id="e-texto" rows="3"></textarea></div>
            <div class="fg"><label class="fl"><i class="fas fa-sliders-h"></i> Tipo</label>
                <select class="fs" id="e-tipo" onchange="onTipoChangeE()">
                    <option value="texto_corto">📝 Texto Corto</option><option value="texto_largo">📄 Texto Largo</option><option value="seleccion_unica">🔘 Selección Única</option><option value="seleccion_multiple">☑️ Selección Múltiple</option><option value="checkbox">✅ Casilla de verificación</option><option value="tinder">🃏 Card Slider</option><option value="satisfaccion">😊 Satisfacción (1-5)</option><option value="matriz">📊 Matriz Importancia / Satisfacción</option><option value="encabezado">📌 Encabezado</option><option value="texto_ordinario">📝 Texto Informativo</option>
                </select>
            </div>
            <div id="e-opts-box" style="display:none;">
                <div class="fg"><label class="fl"><i class="fas fa-list-ul"></i> Opciones</label>
                    <div class="opts-box"><div id="e-opts-list"></div><div class="opt-add"><input class="fi" type="text" id="e-nueva-opt" placeholder="Nueva opción..." style="padding:8px 12px;font-size:13px;"><button class="btn btn-primary btn-sm" onclick="addOptE()"><i class="fas fa-plus"></i></button></div></div>
                </div>
            </div>
            <div id="e-tinder-box" style="display:none;">
                <div class="tb"><div class="tb-title"><i class="fas fa-layer-group"></i> Tarjetas Tinder</div><div style="display:flex;gap:10px;margin-bottom:14px;"><div style="flex:1"><label class="fl" style="font-size:11px;"><i class="fas fa-thumbs-up" style="color:var(--green)"></i> Botón positivo</label><input class="fi" type="text" id="e-label-si" placeholder="Me gusta" style="margin-top:4px;"></div><div style="flex:1"><label class="fl" style="font-size:11px;"><i class="fas fa-thumbs-down" style="color:var(--red)"></i> Botón negativo</label><input class="fi" type="text" id="e-label-no" placeholder="No me gusta" style="margin-top:4px;"></div></div><div id="e-tinder-list"></div><button class="add-tc" onclick="addTinderCardE()"><i class="fas fa-plus"></i> Agregar tarjeta</button></div>
            </div>
            <div id="e-mat-box" style="display:none;">
                <div class="tb"><div class="tb-title"><i class="fas fa-table"></i> Matriz Importancia / Satisfacción</div>
                    <div style="display:flex;gap:10px;margin-bottom:14px;"><div style="flex:1"><label class="fl" style="font-size:11px;">Título columna Importancia</label><input class="fi" type="text" id="e-mat-ci" placeholder="¿Qué tan IMPORTANTE es para usted?" style="margin-top:4px;"></div><div style="flex:1"><label class="fl" style="font-size:11px;">Título columna Satisfacción</label><input class="fi" type="text" id="e-mat-cs" placeholder="¿Qué tan SATISFECHO está?" style="margin-top:4px;"></div></div>
                    <div id="e-mat-list"></div><button class="add-tc" onclick="addMatFilaE()"><i class="fas fa-plus"></i> Agregar fila</button></div>
            </div>
            <div id="e-req-box"><label class="cbr"><input type="checkbox" id="e-req"><span><i class="fas fa-asterisk" style="color:var(--red);margin-right:5px;"></i> Requerida</span></label></div>
            <div id="e-cond-box">
                <hr class="fdiv">
                <div class="fst"><i class="fas fa-code-branch"></i> Lógica Condicional</div>
                <div class="cond-box">
                    <div class="fg" style="margin-bottom:12px;"><label class="fl">Depende de:</label><select class="fs" id="e-padre" onchange="onPadreChangeE()"><option value="">Siempre visible</option></select></div>
                    <div id="e-op-box" style="display:none;"><div class="fg" style="margin-bottom:12px;"><label class="fl">Operador:</label><select class="fs" id="e-op"><option value="igual">Es igual a</option><option value="diferente">Es diferente de</option><option value="contiene">Contiene</option><option value="no_contiene">No contiene</option></select></div></div>
                    <div id="e-val-box" style="display:none;"><div class="fg" style="margin-bottom:0;"><label class="fl">Valor:</label><input class="fi" type="text" id="e-val" placeholder="Ej: Sí..."><div id="e-chips-box" style="display:none;margin-top:8px;"></div></div></div>
                </div>
            </div>
        </div>
        <div class="mf"><button class="btn btn-secondary" onclick="cerrar('mEditar')">Cancelar</button><button class="btn btn-primary" onclick="actualizarPregunta()"><i class="fas fa-check"></i> Actualizar</button></div>
    </div>
</div>

<div id="toast" class="toast"><span class="ti" id="ti"></span><span id="tmsg"></span></div>
<script>
const EID=<?php echo intval($encuesta_id); ?>;
let sortables={},optsC=[],optsE=[],tinderC=[],tinderE=[],matC=[],matE=[];
let _seccionesMap={};

// helpers
function g(id){return document.getElementById(id);}
function cerrar(id){g(id).classList.remove('open');}
function abrir(id){g(id).classList.add('open');}
function escHtml(t){const d=document.createElement('div');d.textContent=t;return d.innerHTML;}
function stripTags(t){const d=document.createElement('div');d.innerHTML=t;return d.textContent||d.innerText||'';}
function insertarEnlace(taId){
    const ta=g(taId);
    const url=prompt('URL del enlace (ej: https://ejemplo.com):','https://');
    if(!url||!url.startsWith('http')) return;
    const sel=ta.value.substring(ta.selectionStart,ta.selectionEnd);
    const txt=sel||prompt('Texto del enlace:','Ver más')||url;
    const tag=`<a href="${url}">${txt}</a>`;
    const s=ta.selectionStart,e=ta.selectionEnd;
    ta.value=ta.value.substring(0,s)+tag+ta.value.substring(e);
    ta.focus();ta.selectionStart=ta.selectionEnd=s+tag.length;
}
function toast(msg,type){
    const t=g('toast');t.className=`toast ${type}`;
    g('ti').innerHTML=type==='success'?'<i class="fas fa-check-circle"></i>':'<i class="fas fa-exclamation-circle"></i>';
    g('tmsg').textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2800);
}

// condicionales helpers


// ── FIX BUG #5: Promise.all para evitar race condition en todasPreguntas ──
function cargarEncuesta(){
    fetch(`index.php?action=banco&subaction=listar_encabezados&encuesta_id=${EID}`)
        .then(r=>r.json()).then(d=>{if(d.success){renderSecciones(d.data);cargarTimer();}});
}
function cargarTimer(){
    fetch(`index.php?action=encuestas&subaction=obtener&id=${EID}`).then(r=>r.json()).then(d=>{
        if(!d.success) return;
        const fc=new Date(d.data.fecha_cierre).getTime();
        function upd(){const diff=fc-Date.now();if(diff>0){const dd=Math.floor(diff/86400000),h=Math.floor((diff%86400000)/3600000),m=Math.floor((diff%3600000)/60000);g('t-rest').textContent=`${dd}d ${h}h ${m}m`;g('t-estado').textContent='Para el cierre';}else{g('t-rest').textContent='Cerrada';g('t-estado').textContent='Expirada';}}
        upd();setInterval(upd,60000);
    });
}
let sortableSecciones=null;
function renderSecciones(secciones){
    const c=g('enc-container');c.innerHTML='';
    _seccionesMap={};
    if(!secciones.length){c.innerHTML='<div style="background:white;border-radius:16px;padding:60px 30px;text-align:center;color:var(--text3);border:1px solid var(--border);"><i class="fas fa-layer-group" style="font-size:48px;opacity:.2;display:block;margin-bottom:12px;"></i><p>Agrega tu primera sección</p></div>';return;}
    const promises=secciones.map(s=>{
        _seccionesMap[s.id]=s;
        const div=document.createElement('div');div.className='section-card';div.id=`sec-${s.id}`;div.setAttribute('data-sid',s.id);
        div.innerHTML=`<div class="section-header"><i class="fas fa-grip-vertical sec-drag-handle" title="Arrastrar para reordenar"></i><div style="flex:1;min-width:0;"><div class="section-title"><i class="fas fa-layer-group" style="color:var(--accent);margin-right:8px;"></i>${escHtml(s.titulo)}</div>${s.descripcion?`<div class="section-subtitle">${escHtml(s.descripcion)}</div>`:''}</div><div class="sec-actions"><button class="action-btn edit" onclick="editarSeccion(${s.id})" title="Editar sección"><i class="fas fa-pen"></i></button><button class="action-btn del" onclick="eliminarSeccion(${s.id})" title="Eliminar sección"><i class="fas fa-trash"></i></button></div><span class="section-pill">Sección</span></div><div class="section-body"><div id="q-list-${s.id}" class="questions-list"></div></div><div class="section-footer"><button class="btn btn-primary btn-sm" onclick="abrirModalPregunta(${s.id})"><i class="fas fa-plus"></i> Agregar</button></div>`;
        c.appendChild(div);
        return fetch(`index.php?action=banco&subaction=listar_preguntas&encabezado_id=${s.id}`).then(r=>r.json()).then(d=>({sid:s.id,preguntas:d.success?d.data:[]}));
    });
    Promise.all(promises).then(res=>{
        const todas=[];
        res.forEach(({sid,preguntas})=>{todas.push(...preguntas);renderPreguntas(sid,preguntas);});
        buildAtajos(todas);
    });
    if(sortableSecciones) sortableSecciones.destroy();
    sortableSecciones=new Sortable(c,{animation:200,handle:'.sec-drag-handle',ghostClass:'dragging',onEnd:guardarOrdenSecciones});
}
const TIPO_MAP={texto_corto:'Texto corto',texto_largo:'Texto largo',seleccion_unica:'Única',seleccion_multiple:'Múltiple',checkbox:'Casilla',tinder:'Card Slider',satisfaccion:'Satisfacción',matriz:'Matriz',encabezado:'Encabezado',texto_ordinario:'Texto info'};
function renderPreguntas(sid,preguntas){
    const c=g(`q-list-${sid}`);if(!c) return;c.innerHTML='';
    if(!preguntas.length){c.innerHTML='<div class="empty-section"><i class="fas fa-inbox" style="margin-right:5px;"></i>Sin elementos</div>';return;}
    preguntas.forEach(p=>{
        const div=document.createElement('div');div.className=`question-item${p.tipo_pregunta==='tinder'?' tinder-type':''}`;div.id=`p-${p.id}`;div.setAttribute('data-id',p.id);
        const tc=p.tipo_pregunta==='tinder'?'tag-tinder':p.tipo_pregunta==='encabezado'?'tag-head':p.tipo_pregunta==='texto_ordinario'?'tag-info':'tag-type';
        let tags=`<span class="tag ${tc}">${TIPO_MAP[p.tipo_pregunta]||p.tipo_pregunta}</span>`;
        if(p.requerido&&p.tipo_pregunta!=='encabezado'&&p.tipo_pregunta!=='texto_ordinario') tags+='<span class="tag tag-req">Req.</span>';
        if(p.pregunta_padre_id>0) tags+='<span class="tag tag-cond"><i class="fas fa-code-branch"></i> Cond.</span>';
        let extra='';
        if(p.tipo_pregunta==='tinder'&&p.opciones){try{const cs=JSON.parse(p.opciones);if(cs.length) extra=`<div class="tinder-preview">${cs.slice(0,4).map(c=>`<div class="tinder-mini">🃏 ${escHtml((c.titulo||'').substring(0,16))}</div>`).join('')}${cs.length>4?`<div class="tinder-mini">+${cs.length-4}</div>`:''}</div>`;}catch(e){}}
        if((p.tipo_pregunta==='seleccion_unica'||p.tipo_pregunta==='seleccion_multiple')&&p.opciones){try{const ops=JSON.parse(p.opciones);if(ops.length) extra=`<div class="question-sub">${ops.map(o=>'· '+escHtml(o)).join('  ')}</div>`;}catch(e){}}
        if(p.tipo_pregunta==='checkbox') extra=`<div class="question-sub"><span style="display:inline-flex;align-items:center;gap:6px;"><input type="checkbox" disabled> <span style="font-size:12px;color:var(--text2);">${escHtml(p.pregunta.substring(0,50))}</span></span></div>`;
        const cond=p.pregunta_padre_id>0?`<div class="cond-detail"><i class="fas fa-arrow-right"></i> Si ${escHtml(p.condicion_operador||'')} "${escHtml(p.condicion_valor||'')}"</div>`:'';
        div.innerHTML=`<i class="fas fa-grip-vertical drag-handle"></i><div class="question-content"><div class="question-text">${escHtml(stripTags(p.pregunta))}</div><div class="question-meta">${tags}</div>${extra}${cond}</div><div class="question-actions"><button class="action-btn edit" onclick="abrirEditar(${p.id})"><i class="fas fa-pen"></i></button><button class="action-btn del" onclick="eliminarP(${p.id})"><i class="fas fa-trash"></i></button></div>`;
        c.appendChild(div);
    });
    if(sortables[sid]) sortables[sid].destroy();
    sortables[sid]=new Sortable(c,{animation:200,handle:'.drag-handle',ghostClass:'dragging',onEnd:()=>guardarOrden(sid)});
}
function guardarOrden(sid){
    const items=g(`q-list-${sid}`).querySelectorAll('.question-item');
    const ordenes=Array.from(items).map((el,i)=>({id:el.getAttribute('data-id'),orden:i}));
    fetch('index.php?action=banco&subaction=guardar_orden_preguntas',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({ordenes})}).then(r=>r.json()).then(d=>{if(d.success) toast('Orden guardado','success');});
}
function buildAtajos(todas){
    const c=g('shortcuts');c.innerHTML='';let n=1;
    todas.forEach(p=>{
        if(p.tipo_pregunta==='encabezado'||p.tipo_pregunta==='texto_ordinario') return;
        const item=document.createElement('div');item.className='shortcut-item';
        item.innerHTML=`<span class="shortcut-num">${n}</span><span class="shortcut-txt">${escHtml(p.pregunta.substring(0,40))}${p.pregunta.length>40?'…':''}</span>`;
        item.onclick=()=>{const el=g(`p-${p.id}`);if(el){el.scrollIntoView({behavior:'smooth',block:'center'});el.style.boxShadow='0 0 0 3px rgba(0,122,255,.3)';setTimeout(()=>el.style.boxShadow='',1800);}};
        c.appendChild(item);n++;
    });
    if(!c.children.length) c.innerHTML='<div style="text-align:center;padding:16px;color:var(--text3);font-size:12px;">Sin preguntas aún</div>';
}
function cargarCondOpts(eid,excluir,preId,editId){
    let url=`index.php?action=banco&subaction=listar_preguntas_condicion&encabezado_id=${eid}`;
    if(excluir) url+=`&excluir=${excluir}`;
    fetch(url).then(r=>r.json()).then(d=>{
        if(!d.success) return;
        [preId,editId].forEach(id=>{
            const s=g(id);if(!s) return;
            s.innerHTML='<option value="">Siempre visible</option>';
            d.data.forEach(p=>{
                const opt=document.createElement('option');
                opt.value=p.id;
                opt.textContent=p.pregunta.substring(0,50);
                opt.dataset.tipo=p.tipo_pregunta||'';
                opt.dataset.opciones=p.opciones||'[]';
                s.appendChild(opt);
            });
        });
    });
}
function renderChips(chipsBoxId, valInputId, opciones, currentVal){
    const box=g(chipsBoxId);if(!box) return;
    let opts=[];
    try{opts=JSON.parse(opciones);if(!Array.isArray(opts)) opts=[];}catch(e){opts=[];}
    if(!opts.length){box.style.display='none';g(valInputId).style.display='';return;}
    const selected=new Set((currentVal||'').split(',').map(v=>v.trim()).filter(v=>v));
    box.style.display='block';
    g(valInputId).style.display='none';
    box.innerHTML='<div class="cond-chips">'+opts.map(op=>{
        const active=selected.has(op)?'active':'';
        return `<span class="cond-chip ${active}" data-val="${escAttr(op)}" onclick="toggleChip('${chipsBoxId}','${valInputId}',this)">${escHtml(op)}</span>`;
    }).join('')+'</div>';
}
function toggleChip(chipsBoxId,valInputId,chip){
    chip.classList.toggle('active');
    const sel=Array.from(g(chipsBoxId).querySelectorAll('.cond-chip.active')).map(c=>c.dataset.val);
    g(valInputId).value=sel.join(',');
}
function onPadreChange(){
    const s=g('p-padre');const v=s.value;
    g('p-op-box').style.display=v?'block':'none';
    g('p-val-box').style.display=v?'block':'none';
    if(!v){g('p-op').value='igual';g('p-val').value='';g('p-chips-box').style.display='none';g('p-val').style.display='';return;}
    const opt=s.options[s.selectedIndex];
    if(opt && opt.dataset.tipo==='seleccion_multiple'){
        renderChips('p-chips-box','p-val',opt.dataset.opciones,g('p-val').value);
    } else {
        g('p-chips-box').style.display='none';g('p-val').style.display='';
    }
}
function onPadreChangeE(){
    const s=g('e-padre');const v=s.value;
    g('e-op-box').style.display=v?'block':'none';
    g('e-val-box').style.display=v?'block':'none';
    if(!v){g('e-op').value='igual';g('e-val').value='';g('e-chips-box').style.display='none';g('e-val').style.display='';return;}
    const opt=s.options[s.selectedIndex];
    if(opt && opt.dataset.tipo==='seleccion_multiple'){
        renderChips('e-chips-box','e-val',opt.dataset.opciones,g('e-val').value);
    } else {
        g('e-chips-box').style.display='none';g('e-val').style.display='';
    }
}

// TINDER CARD BUILDERS
function escAttr(t){return String(t==null?'':t).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function autoResize(el){el.style.height='auto';el.style.height=el.scrollHeight+'px';}
function tinderCardHTML(c,i,prefix){
    return `<div class="tc-item" id="${prefix}-tc-${i}"><div class="tc-num">${i+1}</div><div class="tc-fields"><textarea class="tc-ta" placeholder="Título *" rows="1" oninput="${prefix}[${i}].titulo=this.value;autoResize(this)">${escHtml(c.titulo||'')}</textarea><textarea class="tc-ta" placeholder="Texto corto (opcional)" rows="1" oninput="${prefix}[${i}].texto_corto=this.value;autoResize(this)">${escHtml(c.texto_corto||'')}</textarea><textarea placeholder="Descripción larga (opcional - popup ℹ)" oninput="${prefix}[${i}].descripcion=this.value">${escHtml(c.descripcion||'')}</textarea><div class="tc-img-row"><div class="tc-img-prev" id="${prefix}-tprev-${i}">${c.imagen?`<img src="${escAttr(c.imagen)}">`:'<i class="fas fa-image"></i>'}</div><input type="url" placeholder="URL imagen (opcional)" value="${escAttr(c.imagen||'')}" oninput="${prefix}[${i}].imagen=this.value;updImg('${prefix}-tprev-${i}',this.value)" style="flex:1;"><button type="button" class="tc-pick-btn" title="Banco de imágenes" onclick="abrirPickerTinder('${prefix}',${i})"><i class="fas fa-images"></i></button></div></div><button class="tc-rm" onclick="${prefix}.splice(${i},1);renderTC('${prefix}')"><i class="fas fa-times"></i></button></div>`;
}
function updImg(pid,url){const p=g(pid);if(!p) return;p.innerHTML=url?`<img src="${escHtml(url)}" onerror="this.parentElement.innerHTML='<i class=\\'fas fa-image\\'></i>'">`:'<i class="fas fa-image"></i>';}
function abrirPickerTinder(prefix,i){
    window.__imagenPickerCallback=function(url){
        window[prefix][i].imagen=url;
        updImg(`${prefix}-tprev-${i}`,url);
        const inp=document.querySelector(`#${prefix}-tc-${i} input[type=url]`);
        if(inp){inp.value=url;}
    };
    const w=920,h=660,left=Math.max(0,(screen.width-w)/2),top=Math.max(0,(screen.height-h)/2);
    const p=window.open(`index.php?action=imagenes&picker=1`,'bancoPicker',`width=${w},height=${h},left=${left},top=${top},resizable=yes,scrollbars=yes`);
    if(p) p.focus();
}
function renderTC(prefix){
    const arr=prefix==='tinderC'?tinderC:tinderE;
    const listId=prefix==='tinderC'?'p-tinder-list':'e-tinder-list';
    g(listId).innerHTML=arr.map((c,i)=>tinderCardHTML(c,i,prefix)).join('');
    g(listId).querySelectorAll('textarea.tc-ta').forEach(ta=>autoResize(ta));
}
function addTinderCard(){tinderC.push({titulo:'',texto_corto:'',descripcion:'',imagen:''});renderTC('tinderC');}
function addTinderCardE(){tinderE.push({titulo:'',texto_corto:'',descripcion:'',imagen:''});renderTC('tinderE');}

// Matriz Importancia / Satisfacción
function matArr(pre){return pre==='p'?matC:matE;}
function renderMat(pre){
    g(pre+'-mat-list').innerHTML=matArr(pre).map((f,i)=>`<div class="tc-item"><div class="tc-num">${i+1}</div><div class="tc-fields"><textarea class="tc-ta" placeholder="Aspecto *" rows="1" oninput="matArr('${pre}')[${i}].aspecto=this.value;autoResize(this)">${escHtml(f.aspecto||'')}</textarea><textarea class="tc-ta" placeholder="Necesidad (lo que usted requiere)" rows="1" oninput="matArr('${pre}')[${i}].necesidad=this.value;autoResize(this)">${escHtml(f.necesidad||'')}</textarea></div><button class="tc-rm" onclick="matArr('${pre}').splice(${i},1);renderMat('${pre}')"><i class="fas fa-times"></i></button></div>`).join('');
    g(pre+'-mat-list').querySelectorAll('textarea.tc-ta').forEach(ta=>autoResize(ta));
}
function addMatFila(){matC.push({aspecto:'',necesidad:''});renderMat('p');}
function addMatFilaE(){matE.push({aspecto:'',necesidad:''});renderMat('e');}
function matOpts(pre){
    return JSON.stringify({col_importancia:g(pre+'-mat-ci').value.trim()||'¿Qué tan IMPORTANTE es para usted?',col_satisfaccion:g(pre+'-mat-cs').value.trim()||'¿Qué tan SATISFECHO está?',filas:matArr(pre).filter(f=>f.aspecto.trim())});
}

// Modal Sección
function abrirModalSeccion(){
    g('s-id').value='';g('s-titulo').value='';g('s-desc').value='';
    g('mSeccion-title').innerHTML='<i class="fas fa-layer-group" style="color:var(--accent);margin-right:8px;"></i>Nueva Sección';
    g('s-btn-guardar').innerHTML='<i class="fas fa-check"></i> Crear';
    abrir('mSeccion');
}
function editarSeccion(id){
    const s=_seccionesMap[id];if(!s) return;
    g('s-id').value=id;g('s-titulo').value=s.titulo||'';g('s-desc').value=s.descripcion||'';
    g('mSeccion-title').innerHTML='<i class="fas fa-pen" style="color:var(--accent);margin-right:8px;"></i>Editar Sección';
    g('s-btn-guardar').innerHTML='<i class="fas fa-check"></i> Guardar';
    abrir('mSeccion');
}
function guardarSeccion(){
    const titulo=g('s-titulo').value.trim();if(!titulo){toast('Título requerido','error');return;}
    const id=g('s-id').value;
    if(id){
        fetch('index.php?action=banco&subaction=actualizar_encabezado',{method:'POST',body:new URLSearchParams({id,titulo,descripcion:g('s-desc').value})}).then(r=>r.json()).then(d=>{if(d.success){toast('Sección actualizada','success');cerrar('mSeccion');cargarEncuesta();}else toast('Error','error');});
    } else {
        fetch('index.php?action=banco&subaction=agregar_encabezado',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({encuesta_id:EID,titulo,descripcion:g('s-desc').value})}).then(r=>r.json()).then(d=>{if(d.success){toast('Sección creada','success');cerrar('mSeccion');cargarEncuesta();}else toast('Error','error');});
    }
}
function eliminarSeccion(id){
    if(!confirm('¿Eliminar esta sección y todas sus preguntas?')) return;
    fetch('index.php?action=banco&subaction=eliminar_encabezado',{method:'POST',body:new URLSearchParams({id})}).then(r=>r.json()).then(d=>{if(d.success){toast('Sección eliminada','success');cargarEncuesta();}else toast('Error','error');});
}
function guardarOrdenSecciones(){
    const items=g('enc-container').querySelectorAll('.section-card');
    const ordenes=Array.from(items).map((el,i)=>({id:el.getAttribute('data-sid'),orden:i}));
    fetch('index.php?action=banco&subaction=guardar_orden_encabezados',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({ordenes})}).then(r=>r.json()).then(d=>{if(d.success) toast('Orden guardado','success');});
}

// Modal Pregunta
function abrirModalPregunta(eid){
    optsC=[];tinderC=[];matC=[];g('p-mat-ci').value='';g('p-mat-cs').value='';
    g('p-enc-id').value=eid;g('p-texto').value='';g('p-tipo').value='texto_corto';
    g('p-req').checked=true;g('p-banco').checked=false;
    g('p-label-si').value='Me gusta';g('p-label-no').value='No me gusta';
    g('p-opts-list').innerHTML='';g('p-tinder-list').innerHTML='';
    g('p-op-box').style.display='none';g('p-val-box').style.display='none';g('p-val').value='';
    cargarCondOpts(eid,null,'p-padre',null);
    abrir('mPregunta');onTipoChange();
}
function onTipoChange(){
    const t=g('p-tipo').value,isOpt=t==='seleccion_unica'||t==='seleccion_multiple',isTinder=t==='tinder',isStatic=t==='encabezado'||t==='texto_ordinario';
    g('p-opts-box').style.display=isOpt?'block':'none';
    g('p-tinder-box').style.display=isTinder?'block':'none';
    g('p-mat-box').style.display=t==='matriz'?'block':'none';
    if(t==='matriz'&&!matC.length){matC.push({aspecto:'',necesidad:''});renderMat('p');}
    g('p-req-box').style.display=isStatic?'none':'block';
    g('p-cond-box').style.display=isStatic?'none':'block';
    g('p-banco').disabled=isStatic;
}
function addOpt(){const v=g('p-nueva-opt').value.trim();if(!v){toast('Escribe una opción','error');return;}optsC.push(v);renderOpts('p-opts-list',optsC,'optsC');g('p-nueva-opt').value='';}
function renderOpts(listId,arr,arrName){
    const l=g(listId);l.innerHTML='';
    arr.forEach((item,i)=>{
        const div=document.createElement('div');
        div.className='opt-chip';
        div.style.cssText='align-items:center;';
        div.innerHTML=`
            <div style="display:flex;align-items:center;flex:1;min-width:0;gap:8px;">
                <div class="opt-dot" style="flex-shrink:0;"></div>
                <input type="text" value="${escAttr(item)}"
                    style="border:1px solid transparent;outline:none;background:transparent;font-size:13px;color:var(--text);font-family:inherit;flex:1;min-width:0;padding:4px 8px;border-radius:6px;transition:border-color .15s,background .15s;cursor:text;"
                    oninput="${arrName}[${i}]=this.value"
                    onfocus="this.style.borderColor='var(--accent)';this.style.background='white';"
                    onblur="this.style.borderColor='transparent';this.style.background='transparent';"
                    placeholder="Editar opción...">
            </div>
            <span title="Editar" style="color:var(--text3);font-size:11px;margin-right:4px;pointer-events:none;"><i class="fas fa-pen"></i></span>
            <button class="opt-rm" onclick="${arrName}.splice(${i},1);renderOpts('${listId}',${arrName},'${arrName}')"><i class="fas fa-times"></i></button>`;
        l.appendChild(div);
    });
}
function guardarPregunta(){
    const pregunta=g('p-texto').value.trim(),tipo=g('p-tipo').value;
    if(!pregunta){toast('Contenido requerido','error');return;}
    if((tipo==='seleccion_unica'||tipo==='seleccion_multiple')&&optsC.length<2){toast('Min. 2 opciones','error');return;}
    if(tipo==='tinder'&&tinderC.length<1){toast('Agrega al menos 1 tarjeta','error');return;}
    if(tipo==='tinder'&&tinderC.some(c=>!c.titulo.trim())){toast('Todas las tarjetas necesitan título','error');return;}
    if(tipo==='matriz'&&!matC.some(f=>f.aspecto.trim())){toast('Agrega al menos 1 fila con aspecto','error');return;}
    let opts='null';
    if(tipo==='matriz') opts=matOpts('p');
    if(tipo==='seleccion_unica'||tipo==='seleccion_multiple') opts=JSON.stringify(optsC);
    if(tipo==='tinder') opts=JSON.stringify({label_si:g('p-label-si').value.trim()||'Me gusta',label_no:g('p-label-no').value.trim()||'No me gusta',tarjetas:tinderC});
    const fd=new URLSearchParams();
    fd.append('encabezado_id',g('p-enc-id').value);fd.append('pregunta',pregunta);fd.append('tipo_pregunta',tipo);fd.append('opciones',opts);
    fd.append('requerido',(tipo==='encabezado'||tipo==='texto_ordinario')?0:(g('p-req').checked?1:0));
    fd.append('guardar_banco',(tipo==='encabezado'||tipo==='texto_ordinario')?0:(g('p-banco').checked?1:0));
    fd.append('orden',0);fd.append('pregunta_padre_id',g('p-padre').value||'');fd.append('condicion_operador',g('p-op').value);fd.append('condicion_valor',g('p-val').value);
    fetch('index.php?action=banco&subaction=guardar_pregunta',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{if(d.success){toast('Guardado','success');cerrar('mPregunta');cargarEncuesta();}else toast(d.message||'Error','error');});
}

// Modal Editar
function abrirEditar(id){
    fetch(`index.php?action=banco&subaction=obtener_pregunta&id=${id}`).then(r=>r.json()).then(d=>{
        if(!d.success){toast('No se pudo cargar la pregunta','error');return;}
        const p=d.data;

        // ── NORMALIZAR TIPO ──
        // El tipo puede venir con espacios, mayúsculas o vacío. Intentamos hacerlo coincidir.
        const tiposValidos=['texto_corto','texto_largo','seleccion_unica','seleccion_multiple','checkbox','tinder','satisfaccion','matriz','encabezado','texto_ordinario'];
        let tipoRaw=(p.tipo_pregunta||'').toString().trim().toLowerCase();
        let tipoFinal=tiposValidos.indexOf(tipoRaw)>=0 ? tipoRaw : null;

        // Fallback: inferir por contenido de opciones
        if(!tipoFinal && p.opciones){
            try{
                const test=JSON.parse(p.opciones);
                if(Array.isArray(test) && test.length){
                    // Si las opciones son objetos con 'titulo', es tinder (formato viejo)
                    if(typeof test[0]==='object' && test[0]!==null && ('titulo' in test[0] || 'imagen' in test[0] || 'texto_corto' in test[0])){
                        tipoFinal='tinder';
                    } else if(typeof test[0]==='string'){
                        // String → asumimos selección única por defecto (más común)
                        tipoFinal='seleccion_unica';
                    }
                } else if(test && !Array.isArray(test) && test.tarjetas && Array.isArray(test.tarjetas)){
                    // Nuevo formato con etiquetas: {label_si, label_no, tarjetas:[...]}
                    tipoFinal='tinder';
                }
            }catch(e){}
        }
        if(!tipoFinal) tipoFinal='texto_corto';

        // Si tipoRaw no estaba en la lista, avisamos en consola para diagnóstico
        if(tipoRaw && tiposValidos.indexOf(tipoRaw)<0){
            console.warn(`Tipo de pregunta no reconocido: "${p.tipo_pregunta}" — usando "${tipoFinal}" como fallback.`);
        }

        // ── ASIGNAR CAMPOS ──
        g('e-id').value=p.id;
        g('e-texto').value=p.pregunta||'';
        g('e-tipo').value=tipoFinal;
        // Verificar que el select realmente tomó el valor (si no, lo forzamos)
        if(g('e-tipo').value!==tipoFinal){
            // Buscar option por value y seleccionar a mano
            const opt=Array.from(g('e-tipo').options).find(o=>o.value===tipoFinal);
            if(opt) opt.selected=true;
        }
        g('e-req').checked=p.requerido==1||p.requerido==='1';

        // ── CARGAR OPCIONES / TARJETAS ──
        optsE=[];tinderE=[];matE=[];g('e-mat-ci').value='';g('e-mat-cs').value='';
        if(tipoFinal==='matriz'&&p.opciones){
            try{const m=JSON.parse(p.opciones);matE=Array.isArray(m.filas)?m.filas.map(f=>({aspecto:f.aspecto||'',necesidad:f.necesidad||''})):[];g('e-mat-ci').value=m.col_importancia||'';g('e-mat-cs').value=m.col_satisfaccion||'';}catch(e){}
        }
        if(tipoFinal==='tinder' && p.opciones){
            try{
                let parsed=JSON.parse(p.opciones);
                if(typeof parsed==='string'){try{parsed=JSON.parse(parsed);}catch(e){}}
                const normalizarTarjeta=c=>{
                    if(typeof c==='string') return {titulo:c,texto_corto:'',descripcion:'',imagen:''};
                    return {titulo:(c&&c.titulo)||'',texto_corto:(c&&c.texto_corto)||'',descripcion:(c&&c.descripcion)||'',imagen:(c&&c.imagen)||''};
                };
                if(Array.isArray(parsed)){
                    // Formato viejo: array directo de tarjetas
                    tinderE=parsed.map(normalizarTarjeta);
                    g('e-label-si').value='Me gusta';
                    g('e-label-no').value='No me gusta';
                } else if(parsed && parsed.tarjetas && Array.isArray(parsed.tarjetas)){
                    // Nuevo formato: {label_si, label_no, tarjetas:[...]}
                    tinderE=parsed.tarjetas.map(normalizarTarjeta);
                    g('e-label-si').value=parsed.label_si||'Me gusta';
                    g('e-label-no').value=parsed.label_no||'No me gusta';
                }
            }catch(e){console.error('Error parseando opciones tinder:',e);}
        }
        if((tipoFinal==='seleccion_unica'||tipoFinal==='seleccion_multiple') && p.opciones){
            try{
                const parsed=JSON.parse(p.opciones);
                if(Array.isArray(parsed)) optsE=parsed;
            }catch(e){}
        }

        // ── MOSTRAR CONTENEDORES Y RENDERIZAR ──
        onTipoChangeE();
        renderOpts('e-opts-list',optsE,'optsE');
        renderTC('tinderE');
        renderMat('e');
        cargarCondOpts(p.encabezado_id,p.id,null,'e-padre');
        setTimeout(()=>{
            if(p.pregunta_padre_id>0){
                g('e-padre').value=p.pregunta_padre_id;
                g('e-op').value=p.condicion_operador||'igual';
                g('e-val').value=p.condicion_valor||'';
                g('e-op-box').style.display='block';
                g('e-val-box').style.display='block';
                onPadreChangeE(); // muestra chips si el padre es seleccion_multiple
            }
            else{g('e-padre').value='';g('e-op-box').style.display='none';g('e-val-box').style.display='none';}
        },120);
        abrir('mEditar');
        // Re-render defensivo después de abrir el modal
        if(tipoFinal==='tinder'){
            setTimeout(()=>{
                // Volver a forzar el tipo en el select (por si algo lo limpió)
                if(g('e-tipo').value!=='tinder'){g('e-tipo').value='tinder';onTipoChangeE();}
                renderTC('tinderE');
            },80);
        }
    }).catch(err=>{console.error('Error al abrir editar:',err);toast('Error al cargar','error');});
}
function onTipoChangeE(){
    const t=g('e-tipo').value,isOpt=t==='seleccion_unica'||t==='seleccion_multiple',isTinder=t==='tinder',isStatic=t==='encabezado'||t==='texto_ordinario';
    g('e-opts-box').style.display=isOpt?'block':'none';
    g('e-tinder-box').style.display=isTinder?'block':'none';
    g('e-mat-box').style.display=t==='matriz'?'block':'none';
    g('e-req-box').style.display=isStatic?'none':'block';
    g('e-cond-box').style.display=isStatic?'none':'block';
}
function addOptE(){const v=g('e-nueva-opt').value.trim();if(!v){toast('Escribe una opción','error');return;}optsE.push(v);renderOpts('e-opts-list',optsE,'optsE');g('e-nueva-opt').value='';}
function actualizarPregunta(){
    const id=g('e-id').value,pregunta=g('e-texto').value.trim(),tipo=g('e-tipo').value;
    if(!pregunta){toast('Contenido requerido','error');return;}
    if((tipo==='seleccion_unica'||tipo==='seleccion_multiple')&&optsE.length<2){toast('Min. 2 opciones','error');return;}
    if(tipo==='tinder'&&tinderE.length<1){toast('Agrega al menos 1 tarjeta','error');return;}
    if(tipo==='matriz'&&!matE.some(f=>f.aspecto.trim())){toast('Agrega al menos 1 fila con aspecto','error');return;}
    let opts='null';
    if(tipo==='matriz') opts=matOpts('e');
    if(tipo==='seleccion_unica'||tipo==='seleccion_multiple') opts=JSON.stringify(optsE);
    if(tipo==='tinder') opts=JSON.stringify({label_si:g('e-label-si').value.trim()||'Me gusta',label_no:g('e-label-no').value.trim()||'No me gusta',tarjetas:tinderE});
    const fd=new URLSearchParams();fd.append('id',id);fd.append('pregunta',pregunta);fd.append('tipo_pregunta',tipo);fd.append('opciones',opts);fd.append('requerido',g('e-req').checked?1:0);fd.append('pregunta_padre_id',g('e-padre').value||'');fd.append('condicion_operador',g('e-op').value);fd.append('condicion_valor',g('e-val').value);
    fetch('index.php?action=banco&subaction=actualizar_pregunta',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{if(d.success){toast('Actualizado','success');cerrar('mEditar');cargarEncuesta();}else toast('Error','error');});
}
function eliminarP(id){if(!confirm('¿Eliminar este elemento?'))return;fetch('index.php?action=banco&subaction=eliminar_pregunta',{method:'POST',body:new URLSearchParams({id})}).then(r=>r.json()).then(d=>{if(d.success){toast('Eliminado','success');cargarEncuesta();}});}
// FIX BUG #4: pasar encuesta_id al banco para que cargue las secciones disponibles
function abrirBanco(){window.open(`banco/${EID}`,'_blank','width=960,height=720');}
document.addEventListener('DOMContentLoaded',cargarEncuesta);
window.onclick=e=>{['mSeccion','mPregunta','mEditar'].forEach(id=>{if(e.target===g(id))cerrar(id);});};

// ── Dropdown perfil ──
function toggleDrop(e){
    e.stopPropagation();
    const m=document.getElementById('dropMenu');
    const a=document.getElementById('dropArrow');
    const open=m.classList.toggle('open');
    a.style.transform=open?'rotate(180deg)':'';
}
document.addEventListener('click',function(e){
    const w=document.getElementById('profileWrap');
    if(w&&!w.contains(e.target)){
        document.getElementById('dropMenu').classList.remove('open');
        document.getElementById('dropArrow').style.transform='';
    }
});
function abrirAcercaDe(){
    document.getElementById('dropMenu').classList.remove('open');
    document.getElementById('dropArrow').style.transform='';
    document.getElementById('mAcerca').style.display='flex';
}
</script>
</body>
</html>
