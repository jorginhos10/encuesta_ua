<?php if(!isset($_SESSION['user_id'])){ header("Location: " . BASE_URL . "login"); exit(); } ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo BASE_URL; ?>">
    <title>Mis Encuestas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root{
            --bg:#f2f2f7;--surface:#fff;--surface2:#f2f2f7;
            --border:rgba(0,0,0,.08);--border-strong:rgba(0,0,0,.13);
            --text:#1c1c1e;--text2:#636366;--text3:#aeaeb2;
            --accent:#007aff;--al:rgba(0,122,255,.09);
            --green:#34c759;--red:#ff3b30;--orange:#ff9500;--purple:#af52de;
            --sh:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
            --shm:0 4px 16px rgba(0,0,0,.08);
            --r:18px;--rs:12px;--nav:64px;
        }
        *{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased;}
        body{font-family:'DM Sans',-apple-system,BlinkMacSystemFont,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}

        /* ─── NAVBAR ─── */
        .navbar{
            position:sticky;top:0;z-index:200;height:var(--nav);
            background:rgba(255,255,255,.85);
            backdrop-filter:blur(24px) saturate(180%);
            -webkit-backdrop-filter:blur(24px) saturate(180%);
            border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;padding:0 24px;
        }
        .nav-left{display:flex;align-items:center;gap:12px;}
        .nav-logo{width:34px;height:34px;background:linear-gradient(135deg,var(--accent),#5856d6);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:15px;}
        .nav-title{font-size:17px;font-weight:600;letter-spacing:-.3px;}
        .nav-right{display:flex;align-items:center;gap:10px;}
        .nav-avatar{width:24px;height:24px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:white;font-size:10px;font-weight:700;flex-shrink:0;}
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

        /* ─── HERO ─── */
        .hero{
            background:linear-gradient(145deg,#1c1c1e 0%,#2c2c2e 100%);
            padding:36px 24px 32px;position:relative;overflow:hidden;
        }
        .hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 85% 0%,rgba(0,122,255,.28) 0%,transparent 55%),radial-gradient(ellipse at 15% 100%,rgba(88,86,214,.2) 0%,transparent 50%);}
        .hero-inner{max-width:1100px;margin:0 auto;position:relative;display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;}
        .hero-text{}
        .hero-eyebrow{font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-bottom:6px;}
        .hero-title{font-family:'DM Serif Display',serif;font-size:28px;color:white;letter-spacing:-.5px;line-height:1.2;}
        .hero-sub{font-size:14px;color:rgba(255,255,255,.45);margin-top:6px;}
        .btn-new{display:flex;align-items:center;gap:8px;background:var(--accent);color:white;border:none;padding:12px 20px;border-radius:14px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;box-shadow:0 4px 16px rgba(0,122,255,.4);transition:transform .15s,box-shadow .15s;white-space:nowrap;}
        .btn-new:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(0,122,255,.45);}

        /* ─── STATS BAR ─── */
        .stats-bar{background:var(--surface);border-bottom:1px solid var(--border);}
        .stats-inner{max-width:1100px;margin:0 auto;display:flex;padding:0 24px;}
        .stat-item{flex:1;padding:16px 12px;text-align:center;border-right:1px solid var(--border);}
        .stat-item:last-child{border-right:none;}
        .stat-num{font-size:22px;font-weight:300;letter-spacing:-1px;font-family:'DM Serif Display',serif;color:var(--text);}
        .stat-lbl{font-size:11px;color:var(--text3);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;}

        /* ─── CONTENT ─── */
        .content{max-width:1100px;margin:0 auto;padding:24px 16px;}

        /* Filtros */
        .filters{display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
        .filter-btn{padding:7px 16px;border-radius:20px;border:1.5px solid var(--border-strong);background:var(--surface);font-family:inherit;font-size:13px;font-weight:500;color:var(--text2);cursor:pointer;transition:all .15s;}
        .filter-btn.active{background:var(--accent);color:white;border-color:var(--accent);}
        .filter-btn:hover:not(.active){background:var(--surface2);}
        .search-box{display:flex;align-items:center;gap:8px;background:var(--surface);border:1.5px solid var(--border-strong);border-radius:12px;padding:7px 14px;flex:1;max-width:280px;}
        .search-box i{color:var(--text3);font-size:13px;}
        .search-box input{border:none;outline:none;font-family:inherit;font-size:14px;color:var(--text);background:transparent;width:100%;}
        .search-box input::placeholder{color:var(--text3);}

        /* Grid */
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:14px;}

        /* Encuesta Card */
        .enc-card{
            background:var(--surface);border-radius:var(--r);
            box-shadow:var(--sh);border:1px solid var(--border);
            overflow:hidden;transition:box-shadow .2s,transform .2s;
            display:flex;flex-direction:column;
        }
        .enc-card:hover{box-shadow:var(--shm);transform:translateY(-2px);}
        .enc-card-top{padding:20px 20px 14px;flex:1;}
        .enc-card-meta{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
        .status-pill{display:flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;letter-spacing:.3px;}
        .status-pill.activa{background:rgba(52,199,89,.12);color:var(--green);}
        .status-pill.pendiente{background:rgba(255,149,0,.12);color:var(--orange);}
        .status-pill.cerrada{background:rgba(255,59,48,.1);color:var(--red);}
        .status-dot{width:6px;height:6px;border-radius:50%;background:currentColor;}
        .enc-menu-btn{width:28px;height:28px;border-radius:8px;background:transparent;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text3);font-size:14px;transition:background .15s;}
        .enc-menu-btn:hover{background:var(--surface2);color:var(--text);}
        .enc-name{font-size:16px;font-weight:600;color:var(--text);letter-spacing:-.2px;line-height:1.3;margin-bottom:8px;}
        .enc-dates{display:flex;gap:12px;flex-wrap:wrap;}
        .enc-date{font-size:11px;color:var(--text3);display:flex;align-items:center;gap:4px;}
        .enc-date i{color:var(--text3);font-size:10px;}

        /* Progress bar en la card */
        .enc-progress{padding:0 20px 14px;}
        .enc-prog-bar{height:4px;background:var(--surface2);border-radius:2px;overflow:hidden;}
        .enc-prog-fill{height:100%;border-radius:2px;background:linear-gradient(90deg,var(--accent),#5856d6);transition:width .4s;}

        /* Acciones */
        .enc-actions{padding:12px 16px;border-top:1px solid var(--border);display:flex;gap:6px;background:rgba(0,0,0,.01);}
        .enc-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 8px;border-radius:10px;font-family:inherit;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:all .15s;text-decoration:none;}
        .enc-btn-build{background:var(--al);color:var(--accent);}
        .enc-btn-build:hover{background:rgba(0,122,255,.16);}
        .enc-btn-report{background:rgba(52,199,89,.1);color:var(--green);}
        .enc-btn-report:hover{background:rgba(52,199,89,.18);}
        .enc-btn-link{background:var(--surface2);color:var(--text2);}
        .enc-btn-link:hover{background:#e5e5ea;}
        .enc-btn-edit{background:rgba(255,149,0,.1);color:var(--orange);}
        .enc-btn-edit:hover{background:rgba(255,149,0,.18);}
        .enc-btn-del{background:rgba(255,59,48,.08);color:var(--red);}
        .enc-btn-del:hover{background:rgba(255,59,48,.15);}
        .enc-btn-reset{background:rgba(255,149,0,.08);color:var(--orange);flex:0 0 auto;width:38px;padding:9px 0;}
        .enc-btn-reset:hover{background:rgba(255,149,0,.18);}
        #reset-input{width:100%;padding:10px 13px;border:2px solid var(--border-strong);border-radius:10px;font-size:14px;font-family:inherit;outline:none;margin-top:12px;transition:border-color .15s;}
        #reset-input:focus{border-color:var(--orange);}
        #reset-input.error{border-color:var(--red);}
        .reset-hint{font-size:12px;color:var(--text3);margin-top:6px;}

        /* Empty */
        .empty{text-align:center;padding:60px 20px;color:var(--text3);}
        .empty i{font-size:48px;margin-bottom:16px;opacity:.25;display:block;}
        .empty p{font-size:15px;margin-bottom:20px;}

        /* ─── MODAL ─── */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(6px);z-index:500;align-items:center;justify-content:center;}
        .modal-overlay.open{display:flex;animation:fadeIn .2s ease;}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        .modal-box{background:var(--surface);border-radius:20px;width:94%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.15);animation:slideUp .25s cubic-bezier(.34,1.56,.64,1);max-height:92vh;overflow-y:auto;}
        @keyframes slideUp{from{transform:translateY(30px) scale(.97);opacity:0}to{transform:none;opacity:1}}
        .mh{padding:20px 22px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);position:sticky;top:0;background:var(--surface);z-index:2;}
        .mh-title{font-size:17px;font-weight:600;letter-spacing:-.3px;}
        .mc{width:28px;height:28px;border-radius:50%;background:var(--surface2);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--text2);}
        .mb{padding:20px 22px;}
        .mf{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;position:sticky;bottom:0;background:var(--surface);}
        .form-group{margin-bottom:16px;}
        .form-label{display:block;font-size:12px;font-weight:600;color:var(--text2);letter-spacing:.2px;margin-bottom:7px;text-transform:uppercase;}
        .form-input,.form-row-input,.form-select{width:100%;padding:11px 14px;border:1.5px solid var(--border-strong);border-radius:12px;font-size:14px;font-family:inherit;color:var(--text);background:var(--surface);outline:none;transition:border-color .15s,box-shadow .15s;}
        .form-input:focus,.form-row-input:focus,.form-select:focus{border-color:var(--accent);box-shadow:0 0 0 4px var(--al);}
        .form-select{appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23636366' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 12px center;background-size:16px;padding-right:38px;cursor:pointer;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
        .form-hint{font-size:11px;color:var(--text3);margin-top:6px;display:flex;align-items:flex-start;gap:5px;line-height:1.4;}
        .form-hint i{color:var(--accent);margin-top:2px;}
        .pwd-wrap{position:relative;}
        .pwd-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text3);cursor:pointer;font-size:14px;padding:4px;}
        .pwd-toggle:hover{color:var(--accent);}
        .security-info{background:var(--surface2);border-radius:10px;padding:10px 12px;font-size:12px;color:var(--text2);margin-top:8px;line-height:1.5;border-left:3px solid var(--accent);}
        .security-info i{color:var(--accent);margin-right:4px;}
        .btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:10px;font-size:14px;font-weight:500;border:none;cursor:pointer;font-family:inherit;transition:all .15s;}
        .btn-primary{background:var(--accent);color:white;}
        .btn-primary:hover{background:#0066d6;}
        .btn-secondary{background:var(--surface2);color:var(--text);border:1px solid var(--border-strong);}
        .btn-secondary:hover{background:#e5e5ea;}
        .btn-danger-solid{background:var(--red);color:white;}
        .btn-danger-solid:hover{background:#e0342b;}

        /* Toast */
        .toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--text);color:white;padding:12px 20px;border-radius:30px;font-size:14px;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.25);display:flex;align-items:center;gap:9px;z-index:1000;transition:transform .3s cubic-bezier(.34,1.56,.64,1),opacity .3s;opacity:0;}
        .toast.show{transform:translateX(-50%) translateY(0);opacity:1;}
        .toast.success .ti{color:var(--green);}
        .toast.error .ti{color:var(--red);}

        /* Link modal */
        .link-box{display:flex;gap:8px;align-items:center;background:var(--surface2);border-radius:12px;padding:12px 14px;border:1.5px solid var(--border-strong);}
        .link-url{font-size:13px;color:var(--text2);flex:1;word-break:break-all;font-family:'SF Mono',monospace;}
        .copy-btn{background:var(--accent);color:white;border:none;padding:7px 13px;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;font-family:inherit;}
        .qr-wrap{display:flex;flex-direction:column;align-items:center;gap:8px;padding:20px;background:var(--surface2);border-radius:16px;border:1.5px solid var(--border);margin-bottom:16px;}
        .qr-wrap canvas,.qr-wrap img{border-radius:8px;display:block;}
        .qr-label{font-size:11px;font-weight:600;letter-spacing:.6px;text-transform:uppercase;color:var(--text3);}
        .qr-dl{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:500;color:var(--accent);background:var(--al);border:none;padding:5px 12px;border-radius:20px;cursor:pointer;font-family:inherit;text-decoration:none;margin-top:4px;}
        .qr-dl:hover{background:rgba(0,122,255,.15);}
        .btn-share{display:none;align-items:center;gap:7px;background:linear-gradient(135deg,#25d366,#128c7e);color:white;border:none;padding:9px 18px;border-radius:20px;font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;transition:opacity .15s;}
        .btn-share:hover{opacity:.88;}
        @media(max-width:768px){.btn-share{display:flex;margin-right:auto;}}

        @media(max-width:600px){.grid{grid-template-columns:1fr;}.hero-inner{flex-direction:column;align-items:flex-start;}.stats-inner{flex-wrap:wrap;}.stat-item{min-width:50%;}.form-row{grid-template-columns:1fr;}}

        /* ─── ACORDEÓN OPCIONES AVANZADAS ─── */
        .accordion-toggle{width:100%;display:flex;align-items:center;gap:8px;background:var(--surface2);border:1.5px solid var(--border-strong);border-radius:12px;padding:11px 14px;font-family:inherit;font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;text-align:left;transition:background .15s,border-color .15s;}
        .accordion-toggle:hover{background:#e8e8ed;border-color:rgba(0,0,0,.18);}
        .accordion-toggle.open{border-color:var(--accent);color:var(--accent);background:var(--al);}
        .accordion-toggle i:first-child{font-size:12px;}
        .accordion-arrow{margin-left:auto;font-size:11px;transition:transform .25s;}
        .accordion-toggle.open .accordion-arrow{transform:rotate(180deg);}
        .accordion-body{overflow:hidden;max-height:0;transition:max-height .3s ease,opacity .3s ease;opacity:0;}
        .accordion-body.open{max-height:400px;opacity:1;}
        .accordion-inner{padding:14px 2px 0;}
        .banner-row{display:grid;grid-template-columns:1fr auto auto;gap:8px;align-items:end;}
        .banner-tipo-wrap{width:140px;}
        .btn-pick-img{display:inline-flex;align-items:center;gap:5px;padding:0 12px;height:42px;border-radius:12px;border:1.5px solid var(--border-strong);background:var(--surface2);font-family:inherit;font-size:12px;font-weight:600;color:var(--text2);cursor:pointer;white-space:nowrap;transition:all .15s;}
        .btn-pick-img:hover{border-color:var(--accent);color:var(--accent);background:var(--al);}
        .btn-pick-img i{font-size:13px;}
        .input-pick-wrap{display:flex;gap:8px;align-items:center;}
        .op-range{width:100%;accent-color:var(--accent);height:5px;cursor:pointer;border-radius:4px;}
        .op-label-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;}
        .op-val{font-size:12px;font-weight:700;color:var(--accent);background:var(--al);padding:2px 9px;border-radius:20px;}
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-left">
        <div class="nav-logo"><i class="fas fa-chart-bar"></i></div>
        <span class="nav-title">Mis Encuestas</span>
    </div>
    <div class="nav-right">
        <div class="profile-wrap" id="profileWrap">
            <button class="nav-user" onclick="toggleDrop(event)" id="profileBtn">
                <div class="nav-avatar"><?php echo strtoupper(substr($_SESSION['user_name'],0,1)); ?></div>
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                <i class="fas fa-chevron-down drop-arrow" id="dropArrow"></i>
            </button>
            <div class="drop-menu" id="dropMenu">
                <button class="drop-item" onclick="window.open('imagenes','_blank','width=1100,height=750')"><i class="fas fa-images" style="color:var(--purple)"></i> Banco de imágenes</button>
                <button class="drop-item" onclick="abrirAcercaDe()"><i class="fas fa-info-circle"></i> Acerca de nosotros</button>
                <div class="drop-divider"></div>
                <a href="salir" class="drop-item drop-danger"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
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

<div class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-eyebrow"><i class="fas fa-clipboard-list"></i> Panel principal</div>
            <h1 class="hero-title">Tus Encuestas</h1>
            <p class="hero-sub">Crea, gestiona y analiza tus encuestas</p>
        </div>
        <button class="btn-new" onclick="abrirModalCrear()">
            <i class="fas fa-plus"></i> Nueva Encuesta
        </button>
    </div>
</div>

<div class="stats-bar">
    <div class="stats-inner" id="stats-bar">
        <div class="stat-item"><div class="stat-num" id="stat-total">—</div><div class="stat-lbl">Total</div></div>
        <div class="stat-item"><div class="stat-num" id="stat-activas">—</div><div class="stat-lbl">Activas</div></div>
        <div class="stat-item"><div class="stat-num" id="stat-pendientes">—</div><div class="stat-lbl">Pendientes</div></div>
        <div class="stat-item"><div class="stat-num" id="stat-cerradas">—</div><div class="stat-lbl">Cerradas</div></div>
    </div>
</div>

<div class="content">
    <div class="filters">
        <button class="filter-btn active" data-filter="todas" onclick="filtrar(this)">Todas</button>
        <button class="filter-btn" data-filter="activa" onclick="filtrar(this)">Activas</button>
        <button class="filter-btn" data-filter="pendiente" onclick="filtrar(this)">Pendientes</button>
        <button class="filter-btn" data-filter="cerrada" onclick="filtrar(this)">Cerradas</button>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar encuesta..." oninput="buscar()">
        </div>
    </div>
    <div class="grid" id="grid-encuestas">
        <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text3);">
            <i class="fas fa-spinner fa-spin" style="font-size:24px;"></i>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div id="modalCrear" class="modal-overlay">
    <div class="modal-box">
        <div class="mh">
            <span class="mh-title"><i class="fas fa-plus-circle" style="color:var(--accent);margin-right:8px;"></i>Nueva Encuesta</span>
            <button class="mc" onclick="cerrarModalCrear()"><i class="fas fa-times"></i></button>
        </div>
        <div class="mb">
            <div class="form-group">
                <label class="form-label">Nombre de la encuesta</label>
                <input class="form-input" type="text" id="nueva_nombre" placeholder="Ej: Satisfacción del cliente 2025">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Fecha inicio</label>
                    <input class="form-row-input" type="datetime-local" id="nueva_inicio">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha cierre</label>
                    <input class="form-row-input" type="datetime-local" id="nueva_cierre">
                </div>
            </div>

            <!-- ── SEGURIDAD DE ENCUESTA ── -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-shield-alt" style="color:var(--accent);margin-right:4px;"></i> Seguridad de encuesta</label>
                <select class="form-select" id="nueva_seguridad" onchange="toggleNuevaPassword()">
                    <option value="publica">🌐 Pública (cualquiera puede responder)</option>
                    <option value="password">🔒 Protegida con contraseña</option>
                    <option value="gmail_cualquiera">📧 Correo electrónico</option>
                    <option value="gmail_uniatlantico">🎓 Solo @mail.uniatlantico.edu.co</option>
                </select>
                <div class="security-info" id="nueva_seg_info">
                    <i class="fas fa-info-circle"></i> Cualquier persona con el enlace podrá responder esta encuesta.
                </div>
            </div>
            <div class="form-group" id="nueva_password_wrap" style="display:none;">
                <label class="form-label"><i class="fas fa-key" style="color:var(--accent);margin-right:4px;"></i> Contraseña</label>
                <div class="pwd-wrap">
                    <input class="form-input" type="password" id="nueva_password" placeholder="Ingresa una contraseña segura" maxlength="100" style="padding-right:40px;">
                    <button type="button" class="pwd-toggle" onclick="togglePwdVisibility('nueva_password','nueva_pwd_icon')"><i class="fas fa-eye" id="nueva_pwd_icon"></i></button>
                </div>
                <div class="form-hint"><i class="fas fa-info-circle"></i> Los participantes necesitarán esta contraseña para responder.</div>
            </div>
        </div>
        <div class="mf">
            <button class="btn btn-secondary" onclick="cerrarModalCrear()">Cancelar</button>
            <button class="btn btn-primary" onclick="crearEncuesta()"><i class="fas fa-check"></i> Crear</button>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div id="modalEditar" class="modal-overlay">
    <div class="modal-box">
        <div class="mh">
            <span class="mh-title"><i class="fas fa-pen" style="color:var(--accent);margin-right:8px;"></i>Editar Encuesta</span>
            <button class="mc" onclick="cerrarModalEditar()"><i class="fas fa-times"></i></button>
        </div>
        <div class="mb">
            <input type="hidden" id="editar_id">
            <input type="hidden" id="editar_seguridad_actual">
            <div class="form-group">
                <label class="form-label">Nombre</label>
                <input class="form-input" type="text" id="editar_nombre">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Fecha inicio</label>
                    <input class="form-row-input" type="datetime-local" id="editar_inicio">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha cierre</label>
                    <input class="form-row-input" type="datetime-local" id="editar_cierre">
                </div>
            </div>

            <!-- ── SEGURIDAD DE ENCUESTA ── -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-shield-alt" style="color:var(--accent);margin-right:4px;"></i> Seguridad de encuesta</label>
                <select class="form-select" id="editar_seguridad" onchange="toggleEditarPassword()">
                    <option value="publica">🌐 Pública (cualquiera puede responder)</option>
                    <option value="password">🔒 Protegida con contraseña</option>
                    <option value="gmail_cualquiera">📧 Correo electrónico</option>
                    <option value="gmail_uniatlantico">🎓 Solo @mail.uniatlantico.edu.co</option>
                </select>
                <div class="security-info" id="editar_seg_info">
                    <i class="fas fa-info-circle"></i> Cualquier persona con el enlace podrá responder esta encuesta.
                </div>
            </div>
            <div class="form-group" id="editar_password_wrap" style="display:none;">
                <label class="form-label"><i class="fas fa-key" style="color:var(--accent);margin-right:4px;"></i> Contraseña</label>
                <div class="pwd-wrap">
                    <input class="form-input" type="password" id="editar_password" placeholder="Deja vacío para mantener la actual" maxlength="100" style="padding-right:40px;">
                    <button type="button" class="pwd-toggle" onclick="togglePwdVisibility('editar_password','editar_pwd_icon')"><i class="fas fa-eye" id="editar_pwd_icon"></i></button>
                </div>
                <div class="form-hint"><i class="fas fa-info-circle"></i> Si la encuesta ya tenía contraseña, déjala vacía para no cambiarla.</div>
            </div>

            <!-- ── OPCIONES AVANZADAS ── -->
            <div class="form-group" style="margin-bottom:0;">
                <button type="button" class="accordion-toggle" id="avanzadoToggle" onclick="toggleAvanzado()">
                    <i class="fas fa-sliders-h"></i> Opciones avanzadas
                    <i class="fas fa-chevron-down accordion-arrow" id="avanzadoArrow"></i>
                </button>
                <div class="accordion-body" id="avanzadoBody">
                    <div class="accordion-inner">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-image" style="color:var(--accent);margin-right:4px;"></i> URL del banner</label>
                            <div class="banner-row">
                                <input class="form-input" type="url" id="editar_banner_url" placeholder="https://ejemplo.com/imagen.jpg">
                                <button type="button" class="btn-pick-img" onclick="abrirPickerImagenes('editar_banner_url')"><i class="fas fa-images"></i></button>
                                <div class="banner-tipo-wrap">
                                    <select class="form-select" id="editar_banner_modo" style="padding-left:10px;" onchange="actualizarHintBanner()">
                                        <option value="enunciado">Enunciado</option>
                                        <option value="ambos">Ambos</option>
                                        <option value="banner">Banner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-hint"><i class="fas fa-info-circle"></i> <span id="banner_modo_hint">El banner no se mostrará; solo el enunciado de la encuesta.</span></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-fill-drip" style="color:var(--accent);margin-right:4px;"></i> URL del background</label>
                            <div class="input-pick-wrap">
                                <input class="form-input" type="url" id="editar_background_url" placeholder="https://ejemplo.com/fondo.jpg">
                                <button type="button" class="btn-pick-img" onclick="abrirPickerImagenes('editar_background_url')"><i class="fas fa-images"></i></button>
                            </div>
                            <div class="form-hint"><i class="fas fa-info-circle"></i> Imagen de fondo para toda la página de la encuesta.</div>
                        </div>

                        <div style="border-top:1px solid var(--border);margin:4px 0 14px;"></div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.4px;color:var(--text3);text-transform:uppercase;margin-bottom:12px;"><i class="fas fa-circle-half-stroke" style="margin-right:5px;"></i>Opacidad</div>

                        <div class="form-group">
                            <div class="op-label-row">
                                <label class="form-label" style="margin-bottom:0;"><i class="fas fa-wpforms" style="color:var(--accent);margin-right:4px;"></i> Formulario</label>
                                <span class="op-val" id="op_formulario_val">100%</span>
                            </div>
                            <input type="range" class="op-range" id="editar_opacidad_formulario" min="10" max="100" step="5" value="100" oninput="document.getElementById('op_formulario_val').textContent=this.value+'%'">
                        </div>
                        <div class="form-group">
                            <div class="op-label-row">
                                <label class="form-label" style="margin-bottom:0;"><i class="fas fa-image" style="color:var(--accent);margin-right:4px;"></i> Banner</label>
                                <span class="op-val" id="op_banner_val">100%</span>
                            </div>
                            <input type="range" class="op-range" id="editar_opacidad_banner" min="10" max="100" step="5" value="100" oninput="document.getElementById('op_banner_val').textContent=this.value+'%'">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <div class="op-label-row">
                                <label class="form-label" style="margin-bottom:0;"><i class="fas fa-clock" style="color:var(--accent);margin-right:4px;"></i> Reloj y navegador</label>
                                <span class="op-val" id="op_sidebar_val">100%</span>
                            </div>
                            <input type="range" class="op-range" id="editar_opacidad_sidebar" min="10" max="100" step="5" value="100" oninput="document.getElementById('op_sidebar_val').textContent=this.value+'%'">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mf">
            <button class="btn btn-secondary" onclick="cerrarModalEditar()">Cancelar</button>
            <button class="btn btn-primary" onclick="actualizarEncuesta()"><i class="fas fa-check"></i> Guardar</button>
        </div>
    </div>
</div>

<!-- Modal Link -->
<div id="modalLink" class="modal-overlay">
    <div class="modal-box">
        <div class="mh">
            <span class="mh-title"><i class="fas fa-link" style="color:var(--accent);margin-right:8px;"></i>Enlace público</span>
            <button class="mc" onclick="cerrarModalLink()"><i class="fas fa-times"></i></button>
        </div>
        <div class="mb">
            <div class="qr-wrap">
                <span class="qr-label"><i class="fas fa-qrcode" style="margin-right:5px;"></i>Código QR</span>
                <div id="qr-canvas"></div>
                <a id="qr-download" class="qr-dl" download="enlace-encuesta.png"><i class="fas fa-download"></i> Descargar QR</a>
            </div>
            <p style="font-size:14px;color:var(--text2);margin-bottom:10px;">Comparte este enlace para que los participantes respondan la encuesta:</p>
            <div class="link-box">
                <span class="link-url" id="link-url"></span>
                <button class="copy-btn" onclick="copiarLink()"><i class="fas fa-copy"></i> Copiar</button>
            </div>
        </div>
        <div class="mf" style="display:flex;justify-content:flex-end;align-items:center;gap:10px;">
            <button class="btn-share" id="btnCompartir" onclick="compartirLink()">
                <i class="fas fa-share-alt"></i> Compartir
            </button>
            <button class="btn btn-secondary" onclick="cerrarModalLink()">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal Reset Respuestas -->
<div id="modalReset" class="modal-overlay">
    <div class="modal-box">
        <div class="mh">
            <span class="mh-title" style="color:var(--orange);"><i class="fas fa-eraser" style="margin-right:8px;"></i>Resetear respuestas</span>
            <button class="mc" onclick="cerrarModalReset()"><i class="fas fa-times"></i></button>
        </div>
        <div class="mb">
            <p style="font-size:14px;color:var(--text2);line-height:1.6;">Se eliminarán <strong>todas las respuestas</strong> de <strong id="reset_nombre" style="color:var(--text);"></strong>. Esta acción no se puede deshacer.</p>
            <p style="font-size:13px;color:var(--text2);margin-top:10px;">Para confirmar, escribe <strong style="color:var(--orange);">reset</strong> en el campo:</p>
            <input type="text" id="reset-input" placeholder="Escribe reset aquí..." autocomplete="off">
            <p class="reset-hint" id="reset-error" style="display:none;color:var(--red);">Debes escribir exactamente "reset"</p>
        </div>
        <div class="mf">
            <input type="hidden" id="reset_id">
            <button class="btn btn-secondary" onclick="cerrarModalReset()">Cancelar</button>
            <button class="btn" style="background:var(--orange);color:white;" onclick="confirmarReset()"><i class="fas fa-eraser"></i> Resetear</button>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div id="modalEliminar" class="modal-overlay">
    <div class="modal-box">
        <div class="mh">
            <span class="mh-title" style="color:var(--red);"><i class="fas fa-trash" style="margin-right:8px;"></i>Eliminar encuesta</span>
            <button class="mc" onclick="cerrarModalEliminar()"><i class="fas fa-times"></i></button>
        </div>
        <div class="mb">
            <p style="font-size:14px;color:var(--text2);line-height:1.6;">¿Estás seguro que deseas eliminar <strong id="eliminar_nombre" style="color:var(--text);"></strong>? Esta acción no se puede deshacer.</p>
        </div>
        <div class="mf">
            <input type="hidden" id="eliminar_id">
            <button class="btn btn-secondary" onclick="cerrarModalEliminar()">Cancelar</button>
            <button class="btn btn-danger-solid" onclick="confirmarEliminar()"><i class="fas fa-trash"></i> Eliminar</button>
        </div>
    </div>
</div>

<div id="toast" class="toast"><span class="ti" id="toast-icon"></span><span id="toast-msg"></span></div>

<script>
    let todasEncuestas = [];
    let filtroActual = 'todas';

    // ── Mensajes informativos por tipo de seguridad ──
    const SEG_INFO = {
        'publica': '<i class="fas fa-globe"></i> Cualquier persona con el enlace podrá responder esta encuesta.',
        'password': '<i class="fas fa-lock"></i> Los participantes deberán ingresar la contraseña para responder.',
        'gmail_cualquiera': '<i class="fas fa-envelope"></i> Cualquier correo electrónico válido podrá responder.',
        'gmail_uniatlantico': '<i class="fas fa-graduation-cap"></i> Solo correos <strong>@mail.uniatlantico.edu.co</strong> podrán responder.'
    };

    function cargarEncuestas() {
        fetch('index.php?action=encuestas&subaction=listar')
            .then(r=>r.json()).then(data=>{
                if(data.success){ todasEncuestas=data.data; renderEncuestas(todasEncuestas); actualizarStats(data.data); }
            });
    }

    function actualizarStats(enc) {
        document.getElementById('stat-total').textContent = enc.length;
        document.getElementById('stat-activas').textContent = enc.filter(e=>e.estado==='activa').length;
        document.getElementById('stat-pendientes').textContent = enc.filter(e=>e.estado==='pendiente').length;
        document.getElementById('stat-cerradas').textContent = enc.filter(e=>e.estado==='cerrada').length;
    }

    function filtrar(btn) {
        document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        filtroActual = btn.getAttribute('data-filter');
        aplicarFiltros();
    }

    function buscar() { aplicarFiltros(); }

    function aplicarFiltros() {
        const term = document.getElementById('searchInput').value.toLowerCase();
        let resultado = todasEncuestas;
        if(filtroActual !== 'todas') resultado = resultado.filter(e=>e.estado===filtroActual);
        if(term) resultado = resultado.filter(e=>e.nombre.toLowerCase().includes(term));
        renderEncuestas(resultado);
    }

    function renderEncuestas(encuestas) {
        const grid = document.getElementById('grid-encuestas');
        if(!encuestas.length) {
            grid.innerHTML = '<div class="empty" style="grid-column:1/-1"><i class="fas fa-clipboard"></i><p>No hay encuestas que mostrar</p><button class="btn btn-primary" onclick="abrirModalCrear()"><i class="fas fa-plus"></i> Crear primera encuesta</button></div>';
            return;
        }
        grid.innerHTML = '';
        encuestas.forEach(enc => {
            const statusMap = {activa:{label:'Activa',cls:'activa'},pendiente:{label:'Pendiente',cls:'pendiente'},cerrada:{label:'Cerrada',cls:'cerrada'}};
            const st = statusMap[enc.estado] || {label:enc.estado,cls:'cerrada'};
            const fi = new Date(enc.fecha_inicio), fc = new Date(enc.fecha_cierre), now = new Date();
            let prog = 0;
            if(enc.estado==='activa') { const total=fc-fi; const trans=now-fi; prog=Math.min(100,Math.max(0,Math.round((trans/total)*100))); }
            else if(enc.estado==='cerrada') prog=100;
            const card = document.createElement('div');
            card.className='enc-card';
            card.innerHTML=`
                <div class="enc-card-top">
                    <div class="enc-card-meta">
                        <span class="status-pill ${st.cls}"><span class="status-dot"></span>${st.label}</span>
                        <button class="enc-menu-btn" onclick="abrirEditar(${enc.id})" title="Editar"><i class="fas fa-pen"></i></button>
                    </div>
                    <div class="enc-name">${escapeHtml(enc.nombre)}</div>
                    <div class="enc-dates">
                        <span class="enc-date"><i class="fas fa-calendar-plus"></i>${formatDate(enc.fecha_inicio)}</span>
                        <span class="enc-date"><i class="fas fa-calendar-times"></i>${formatDate(enc.fecha_cierre)}</span>
                    </div>
                </div>
                ${enc.estado==='activa'?`<div class="enc-progress"><div class="enc-prog-bar"><div class="enc-prog-fill" style="width:${prog}%"></div></div></div>`:''}
                <div class="enc-actions">
                    <button class="enc-btn enc-btn-build" onclick="construir(${enc.id})" title="Construir"><i class="fas fa-pen-nib"></i> Construir</button>
                    <button class="enc-btn enc-btn-report" onclick="verInforme(${enc.id})" title="Informe"><i class="fas fa-chart-bar"></i></button>
                    <button class="enc-btn enc-btn-link" onclick="verLink(${enc.id})" title="Enlace"><i class="fas fa-link"></i></button>
                    <button class="enc-btn enc-btn-reset" onclick="abrirReset(${enc.id},'${escapeHtml(enc.nombre)}')" title="Resetear respuestas"><i class="fas fa-eraser"></i></button>
                    <button class="enc-btn enc-btn-del" onclick="abrirEliminar(${enc.id},'${escapeHtml(enc.nombre)}')" title="Eliminar"><i class="fas fa-trash"></i></button>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    function formatDate(d) {
        const dt=new Date(d);
        return dt.toLocaleDateString('es-ES',{day:'2-digit',month:'short',year:'numeric'});
    }

    function construir(id) { window.location.href=`encuestas/${id}`; }
    function verInforme(id) { window.open(`encuestas/${id}/informe`,'_blank'); }
    function verLink(id) {
        const url=`${window.location.origin}<?php echo BASE_URL; ?>responder/${id}`;
        document.getElementById('link-url').textContent=url;

        // Generar QR
        const container=document.getElementById('qr-canvas');
        container.innerHTML='';
        const qr=new QRCode(container,{text:url,width:192,height:192,colorDark:'#1c1c1e',colorLight:'#f9f9fb',correctLevel:QRCode.CorrectLevel.M});

        // Conectar botón de descarga (el canvas lo genera qrcode.js de forma asíncrona)
        setTimeout(()=>{
            const canvas=container.querySelector('canvas');
            if(canvas){
                document.getElementById('qr-download').href=canvas.toDataURL('image/png');
            }
        },120);

        document.getElementById('modalLink').classList.add('open');
    }
    function cerrarModalLink() { document.getElementById('modalLink').classList.remove('open'); }
    function copiarLink() {
        navigator.clipboard.writeText(document.getElementById('link-url').textContent);
        toast('Enlace copiado','success');
    }
    async function compartirLink() {
        const url = document.getElementById('link-url').textContent;
        const canvas = document.querySelector('#qr-canvas canvas');
        const texto = `¡Responde esta encuesta!\n${url}`;

        // Intentar compartir con imagen QR + enlace
        if (canvas && navigator.canShare) {
            try {
                await new Promise(resolve => setTimeout(resolve, 50)); // asegurar canvas listo
                const blob = await new Promise(res => canvas.toBlob(res, 'image/png'));
                const file = new File([blob], 'codigo-qr-encuesta.png', { type: 'image/png' });
                const datos = { title: 'Responde la encuesta', text: texto, files: [file] };
                if (navigator.canShare(datos)) {
                    await navigator.share(datos);
                    return;
                }
            } catch(e) { if (e.name === 'AbortError') return; }
        }
        // Fallback: compartir solo URL
        if (navigator.share) {
            try {
                await navigator.share({ title: 'Responde la encuesta', text: texto, url });
            } catch(e) { if (e.name !== 'AbortError') { navigator.clipboard.writeText(url); toast('Enlace copiado','success'); } }
        }
    }
    // Ocultar botón compartir si el API no está disponible en este dispositivo
    if (!navigator.share) { const b = document.getElementById('btnCompartir'); if(b) b.style.display='none'; }

    // ── Mostrar/ocultar contraseña visualmente ──
    function togglePwdVisibility(inputId, iconId) {
        const inp = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (inp.type === 'password') { inp.type = 'text'; icon.className = 'fas fa-eye-slash'; }
        else { inp.type = 'password'; icon.className = 'fas fa-eye'; }
    }

    // ── Mostrar/ocultar input de contraseña según select de seguridad ──
    function toggleNuevaPassword() {
        const sel = document.getElementById('nueva_seguridad').value;
        document.getElementById('nueva_password_wrap').style.display = (sel === 'password') ? 'block' : 'none';
        document.getElementById('nueva_seg_info').innerHTML = SEG_INFO[sel] || SEG_INFO['publica'];
        if (sel !== 'password') document.getElementById('nueva_password').value = '';
    }
    function toggleEditarPassword() {
        const sel = document.getElementById('editar_seguridad').value;
        document.getElementById('editar_password_wrap').style.display = (sel === 'password') ? 'block' : 'none';
        document.getElementById('editar_seg_info').innerHTML = SEG_INFO[sel] || SEG_INFO['publica'];
    }

    // ── Crear ──
    function abrirModalCrear() {
        document.getElementById('nueva_nombre').value='';
        document.getElementById('nueva_inicio').value='';
        document.getElementById('nueva_cierre').value='';
        document.getElementById('nueva_seguridad').value='publica';
        document.getElementById('nueva_password').value='';
        document.getElementById('nueva_password_wrap').style.display='none';
        document.getElementById('nueva_seg_info').innerHTML = SEG_INFO['publica'];
        document.getElementById('modalCrear').classList.add('open');
    }
    function cerrarModalCrear() { document.getElementById('modalCrear').classList.remove('open'); }
    function crearEncuesta() {
        const nombre=document.getElementById('nueva_nombre').value.trim();
        const inicio=document.getElementById('nueva_inicio').value;
        const cierre=document.getElementById('nueva_cierre').value;
        const tipo_seguridad=document.getElementById('nueva_seguridad').value;
        const password=document.getElementById('nueva_password').value;

        if(!nombre||!inicio||!cierre){toast('Completa todos los campos','error');return;}
        if(tipo_seguridad==='password' && !password.trim()){
            toast('Debes ingresar una contraseña','error');return;
        }

        const params = new URLSearchParams({nombre,fecha_inicio:inicio,fecha_cierre:cierre,tipo_seguridad});
        if(tipo_seguridad==='password') params.append('password', password);

        fetch('index.php?action=encuestas&subaction=crear',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:params
        })
        .then(r=>r.json())
        .then(d=>{
            if(d.success){toast('Encuesta creada','success');cerrarModalCrear();cargarEncuestas();}
            else toast(d.message||'Error','error');
        });
    }

    // ── Picker banco de imágenes ──
    let _pickerPopup = null;
    function abrirPickerImagenes(inputId, callback) {
        window.__imagenPickerCallback = function(url) {
            if(typeof callback === 'function') {
                callback(url);
            } else {
                const el = document.getElementById(inputId);
                if(el){ el.value = url; el.dispatchEvent(new Event('input')); }
            }
        };
        const w = 920, h = 660;
        const left = Math.max(0, (screen.width - w) / 2);
        const top  = Math.max(0, (screen.height - h) / 2);
        _pickerPopup = window.open(`index.php?action=imagenes&picker=1`, 'bancoPicker',
            `width=${w},height=${h},left=${left},top=${top},resizable=yes,scrollbars=yes`);
        if(_pickerPopup) _pickerPopup.focus();
    }

    // ── Acordeón opciones avanzadas ──
    const bannerHints = {
        enunciado: 'El banner no se mostrará; solo el enunciado de la encuesta.',
        ambos:     'Se mostrarán el banner y el enunciado de la encuesta.',
        banner:    'Solo se mostrará el banner, sin el enunciado.'
    };
    function toggleAvanzado() {
        const btn  = document.getElementById('avanzadoToggle');
        const body = document.getElementById('avanzadoBody');
        const open = body.classList.toggle('open');
        btn.classList.toggle('open', open);
    }
    function actualizarHintBanner() {
        const modo = document.getElementById('editar_banner_modo').value;
        document.getElementById('banner_modo_hint').textContent = bannerHints[modo] || '';
    }

    // ── Editar ──
    function abrirEditar(id) {
        fetch(`index.php?action=encuestas&subaction=obtener&id=${id}`).then(r=>r.json()).then(data=>{
            if(!data.success) return;
            const e=data.data;
            document.getElementById('editar_id').value=e.id;
            document.getElementById('editar_nombre').value=e.nombre;
            document.getElementById('editar_inicio').value=e.fecha_inicio.replace(' ','T').substring(0,16);
            document.getElementById('editar_cierre').value=e.fecha_cierre.replace(' ','T').substring(0,16);
            const seg = e.tipo_seguridad || 'publica';
            document.getElementById('editar_seguridad').value = seg;
            document.getElementById('editar_seguridad_actual').value = seg;
            document.getElementById('editar_password').value='';
            // Banner y background
            document.getElementById('editar_banner_url').value = e.banner_url || '';
            document.getElementById('editar_banner_modo').value = e.banner_modo || 'enunciado';
            document.getElementById('editar_background_url').value = e.background_url || '';
            // Opacidades
            const opF = e.opacidad_formulario != null ? e.opacidad_formulario : 100;
            const opB = e.opacidad_banner     != null ? e.opacidad_banner     : 100;
            const opS = e.opacidad_sidebar    != null ? e.opacidad_sidebar    : 100;
            document.getElementById('editar_opacidad_formulario').value = opF;
            document.getElementById('editar_opacidad_banner').value     = opB;
            document.getElementById('editar_opacidad_sidebar').value    = opS;
            document.getElementById('op_formulario_val').textContent    = opF + '%';
            document.getElementById('op_banner_val').textContent        = opB + '%';
            document.getElementById('op_sidebar_val').textContent       = opS + '%';
            actualizarHintBanner();
            // Cerrar acordeón al abrir modal
            document.getElementById('avanzadoBody').classList.remove('open');
            document.getElementById('avanzadoToggle').classList.remove('open');
            toggleEditarPassword();
            document.getElementById('modalEditar').classList.add('open');
        });
    }
    function cerrarModalEditar() { document.getElementById('modalEditar').classList.remove('open'); }
    function actualizarEncuesta() {
        const id=document.getElementById('editar_id').value;
        const nombre=document.getElementById('editar_nombre').value.trim();
        const inicio=document.getElementById('editar_inicio').value;
        const cierre=document.getElementById('editar_cierre').value;
        const tipo_seguridad=document.getElementById('editar_seguridad').value;
        const seg_actual=document.getElementById('editar_seguridad_actual').value;
        const password=document.getElementById('editar_password').value;
        const banner_url=document.getElementById('editar_banner_url').value.trim();
        const banner_modo=document.getElementById('editar_banner_modo').value;
        const background_url=document.getElementById('editar_background_url').value.trim();
        const opacidad_formulario=document.getElementById('editar_opacidad_formulario').value;
        const opacidad_banner=document.getElementById('editar_opacidad_banner').value;
        const opacidad_sidebar=document.getElementById('editar_opacidad_sidebar').value;

        if(!nombre||!inicio||!cierre){toast('Completa todos los campos','error');return;}

        // Si cambia a 'password' desde otro tipo y no escribe contraseña → error
        if(tipo_seguridad==='password' && seg_actual!=='password' && !password.trim()){
            toast('Debes ingresar una contraseña para activar el modo privado','error');return;
        }

        const params = new URLSearchParams({id,nombre,fecha_inicio:inicio,fecha_cierre:cierre,tipo_seguridad,banner_url,banner_modo,background_url,opacidad_formulario,opacidad_banner,opacidad_sidebar});

        // Si el usuario escribió una contraseña nueva → mandar bandera para cambiarla
        if(tipo_seguridad==='password' && password.trim() !== ''){
            params.append('password', password);
            params.append('cambiar_password','1');
        }

        fetch('index.php?action=encuestas&subaction=actualizar',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:params
        })
        .then(r=>r.json())
        .then(d=>{
            if(d.success){toast('Encuesta actualizada','success');cerrarModalEditar();cargarEncuestas();}
            else toast(d.message||'Error','error');
        });
    }

    // ── Reset Respuestas ──
    function abrirReset(id, nombre) {
        document.getElementById('reset_id').value = id;
        document.getElementById('reset_nombre').textContent = nombre;
        document.getElementById('reset-input').value = '';
        document.getElementById('reset-input').classList.remove('error');
        document.getElementById('reset-error').style.display = 'none';
        document.getElementById('modalReset').classList.add('open');
        setTimeout(()=>document.getElementById('reset-input').focus(), 120);
    }
    function cerrarModalReset() {
        document.getElementById('modalReset').classList.remove('open');
    }
    function confirmarReset() {
        const val = document.getElementById('reset-input').value.trim().toLowerCase();
        if(val !== 'reset') {
            document.getElementById('reset-input').classList.add('error');
            document.getElementById('reset-error').style.display = 'block';
            document.getElementById('reset-input').focus();
            return;
        }
        const id = document.getElementById('reset_id').value;
        fetch('index.php?action=encuestas&subaction=resetear', {method:'POST', body:new URLSearchParams({id})})
            .then(r=>r.json())
            .then(d=>{
                if(d.success){ toast('Respuestas eliminadas','success'); cerrarModalReset(); }
                else toast(d.message||'Error al resetear','error');
            });
    }
    document.getElementById('reset-input').addEventListener('keydown', e=>{ if(e.key==='Enter') confirmarReset(); });

    // ── Eliminar ──
    function abrirEliminar(id,nombre) {
        document.getElementById('eliminar_id').value=id;
        document.getElementById('eliminar_nombre').textContent=nombre;
        document.getElementById('modalEliminar').classList.add('open');
    }
    function cerrarModalEliminar() { document.getElementById('modalEliminar').classList.remove('open'); }
    function confirmarEliminar() {
        const id=document.getElementById('eliminar_id').value;
        fetch('index.php?action=encuestas&subaction=eliminar',{method:'POST',body:new URLSearchParams({id})})
            .then(r=>r.json()).then(d=>{if(d.success){toast('Encuesta eliminada','success');cerrarModalEliminar();cargarEncuestas();}else toast('Error','error');});
    }

    function toast(msg,type) {
        const t=document.getElementById('toast');
        const ic=document.getElementById('toast-icon');
        t.className=`toast ${type}`;
        ic.innerHTML=type==='success'?'<i class="fas fa-check-circle"></i>':'<i class="fas fa-exclamation-circle"></i>';
        document.getElementById('toast-msg').textContent=msg;
        t.classList.add('show');
        setTimeout(()=>t.classList.remove('show'),2800);
    }

    function escapeHtml(t){const d=document.createElement('div');d.textContent=t;return d.innerHTML;}

    window.onclick=e=>{
        ['modalCrear','modalEditar','modalLink','modalReset','modalEliminar'].forEach(id=>{
            if(e.target===document.getElementById(id)) document.getElementById(id).classList.remove('open');
        });
    };

    document.addEventListener('DOMContentLoaded', cargarEncuestas);

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
        const m=document.getElementById('mAcerca');
        m.style.display='flex';
    }
</script>
</body>
</html>
