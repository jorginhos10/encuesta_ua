<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo BASE_URL; ?>">
    <title>Acceso · <?php echo htmlspecialchars($encuesta['nombre']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root{--bg:#f2f2f7;--surface:#fff;--surface2:#f2f2f7;--border:rgba(0,0,0,.08);--border-strong:rgba(0,0,0,.13);--text:#1c1c1e;--text2:#636366;--text3:#aeaeb2;--accent:#007aff;--al:rgba(0,122,255,.08);--green:#34c759;--red:#ff3b30;--orange:#ff9500;--purple:#af52de;--sh:0 1px 3px rgba(0,0,0,.06);--shm:0 4px 16px rgba(0,0,0,.08);--shl:0 24px 80px rgba(0,0,0,.18);--r:18px;--rs:12px;}
        *{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased;}
        body{font-family:'DM Sans',-apple-system,sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
        <?php if(!empty($encuesta['background_url'])): ?>
        body{background:url('<?php echo htmlspecialchars($encuesta['background_url'],ENT_QUOTES); ?>') center center / cover no-repeat fixed!important;}
        <?php endif; ?>
        .access-card{background:var(--surface);border-radius:24px;max-width:460px;width:100%;box-shadow:var(--shl);overflow:hidden;}
        .access-header{padding:34px 32px 24px;text-align:center;border-bottom:1px solid var(--border);background:linear-gradient(135deg,rgba(0,122,255,.04),rgba(88,86,214,.04));}
        .lock-icon{width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,var(--accent),#5856d6);display:flex;align-items:center;justify-content:center;color:white;font-size:26px;margin:0 auto 18px;box-shadow:0 8px 20px rgba(0,122,255,.3);}
        .lock-icon.logo-mode{background:transparent;box-shadow:none;width:auto;height:auto;max-height:80px;}
        .lock-icon.logo-mode img{max-height:80px;max-width:220px;object-fit:contain;}
        .access-eyebrow{font-size:11px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;color:var(--accent);margin-bottom:6px;}
        .access-title{font-family:'DM Serif Display',serif;font-size:24px;letter-spacing:-.5px;line-height:1.25;color:var(--text);margin-bottom:6px;}
        .access-survey-name{font-size:14px;color:var(--text2);margin-top:8px;font-style:italic;}
        .access-survey-name.prominent{font-size:17px;font-weight:600;font-style:normal;color:var(--text);margin-top:14px;line-height:1.35;}
        .access-body{padding:28px 32px 24px;}
        .access-intro{font-size:13px;color:var(--text2);line-height:1.6;margin-bottom:22px;text-align:center;background:var(--surface2);padding:12px 16px;border-radius:var(--rs);}
        .access-intro i{color:var(--accent);margin-right:6px;}
        .form-group{margin-bottom:16px;}
        .form-label{display:block;font-size:13px;font-weight:600;color:var(--text2);margin-bottom:7px;}
        .form-label i{color:var(--accent);margin-right:5px;}
        .form-input{width:100%;padding:13px 14px;border:1.5px solid var(--border-strong);border-radius:var(--rs);font-size:14px;font-family:inherit;color:var(--text);background:var(--surface);outline:none;transition:border-color .15s,box-shadow .15s;}
        .form-input:focus{border-color:var(--accent);box-shadow:0 0 0 4px var(--al);}
        .form-hint{font-size:11px;color:var(--text3);margin-top:5px;}
        .pwd-wrap{position:relative;}
        .pwd-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text3);cursor:pointer;font-size:14px;}
        .pwd-toggle:hover{color:var(--accent);}
        .error-msg{display:none;background:rgba(255,59,48,.08);border:1px solid rgba(255,59,48,.2);color:var(--red);padding:11px 14px;border-radius:var(--rs);font-size:13px;margin-bottom:14px;}
        .error-msg.show{display:block;animation:shake .35s;}
        @keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
        .btn-access{width:100%;padding:14px;border-radius:var(--rs);background:linear-gradient(135deg,var(--accent),#5856d6);color:white;border:none;cursor:pointer;font-family:inherit;font-size:15px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 6px 16px rgba(0,122,255,.25);transition:transform .15s,box-shadow .15s;}
        .btn-access:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(0,122,255,.32);}
        .btn-access:disabled{opacity:.6;cursor:not-allowed;transform:none;}
        .access-footer{padding:14px 32px 24px;text-align:center;font-size:11px;color:var(--text3);}
        .access-footer i{color:var(--green);margin-right:4px;}
        .badge-tipo{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:4px 11px;border-radius:20px;margin-top:10px;}
        .badge-password{background:rgba(255,149,0,.1);color:var(--orange);}
        .badge-gmail{background:rgba(255,59,48,.08);color:var(--red);}
    </style>
</head>
<body>
    <div class="access-card">
        <div class="access-header">
            <div class="lock-icon<?php echo ($encuesta['tipo_seguridad'] === 'gmail_cualquiera') ? ' logo-mode' : ''; ?>">
                <?php if ($encuesta['tipo_seguridad'] === 'password'): ?>
                    <i class="fas fa-lock"></i>
                <?php elseif ($encuesta['tipo_seguridad'] === 'gmail_uniatlantico'): ?>
                    <i class="fas fa-graduation-cap"></i>
                <?php else: ?>
                    <img src="<?php echo BASE_URL; ?>src/logo.png" alt="Logo">
                <?php endif; ?>
            </div>
            <?php if ($encuesta['tipo_seguridad'] === 'password'): ?>
            <div class="access-eyebrow"><i class="fas fa-shield-alt"></i> Acceso restringido</div>
            <?php endif; ?>
            <?php if ($encuesta['tipo_seguridad'] !== 'gmail_cualquiera'): ?>
            <h1 class="access-title">
                <?php if ($encuesta['tipo_seguridad'] === 'password'): ?>
                    Encuesta privada
                <?php else: ?>
                    Encuesta institucional
                <?php endif; ?>
            </h1>
            <?php endif; ?>
            <div class="access-survey-name<?php echo ($encuesta['tipo_seguridad'] === 'gmail_cualquiera') ? ' prominent' : ''; ?>">
                <?php echo htmlspecialchars($encuesta['nombre']); ?>
            </div>
            <?php if ($encuesta['tipo_seguridad'] === 'password'): ?>
                <span class="badge-tipo badge-password"><i class="fas fa-key"></i> Requiere contraseña</span>
            <?php elseif ($encuesta['tipo_seguridad'] === 'gmail_uniatlantico'): ?>
                <span class="badge-tipo badge-gmail"><i class="fas fa-at"></i> Solo @mail.uniatlantico.edu.co</span>
            <?php endif; ?>
        </div>

        <form id="accessForm" class="access-body">
            <input type="hidden" name="encuesta_id" value="<?php echo $encuesta['id']; ?>">

            <div class="access-intro">
                <?php if ($encuesta['tipo_seguridad'] === 'password'): ?>
                    <i class="fas fa-info-circle"></i> Esta encuesta requiere una contraseña proporcionada por el creador.
                <?php elseif ($encuesta['tipo_seguridad'] === 'gmail_uniatlantico'): ?>
                    <i class="fas fa-info-circle"></i> Solo correos institucionales <strong>@mail.uniatlantico.edu.co</strong> pueden responder.
                <?php else: ?>
                    <i class="fas fa-info-circle"></i> Ingresa tu nombre y correo electrónico para responder esta encuesta.
                <?php endif; ?>
            </div>

            <div class="error-msg" id="errorMsg"></div>

            <?php if ($encuesta['tipo_seguridad'] === 'password'): ?>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-key"></i> Contraseña</label>
                    <div class="pwd-wrap">
                        <input type="password" class="form-input" name="password" id="passwordInput" required placeholder="Ingresa la contraseña" autofocus>
                        <button type="button" class="pwd-toggle" onclick="togglePwd()"><i class="fas fa-eye" id="pwdIcon"></i></button>
                    </div>
                    <div class="form-hint">Solicítala al creador de la encuesta.</div>
                </div>
            <?php else: ?>
                <!-- Tipo Gmail (cualquiera o uniatlantico) -->
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Nombre completo</label>
                    <input type="text" class="form-input" name="nombre" id="nombreInput" required placeholder="Ej: Juan Pérez García" maxlength="150" autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-envelope"></i> Correo electrónico</label>
                    <input type="email" class="form-input" name="email" id="emailInput" required placeholder="<?php echo $encuesta['tipo_seguridad'] === 'gmail_uniatlantico' ? 'tunombre@mail.uniatlantico.edu.co' : 'tucorreo@ejemplo.com'; ?>" maxlength="150">
                    <?php if ($encuesta['tipo_seguridad'] === 'gmail_uniatlantico'): ?>
                    <div class="form-hint">Debe terminar en <strong>@mail.uniatlantico.edu.co</strong></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-access" id="btnAccess">
                <i class="fas fa-unlock"></i>
                <span>Acceder a la encuesta</span>
            </button>
        </form>

        <div class="access-footer">
            <i class="fas fa-lock"></i> Tus datos son confidenciales y serán utilizados únicamente para dar respuesta a tus preguntas e intervenciones.
        </div>
    </div>

<script>
function togglePwd(){
    const inp = document.getElementById('passwordInput');
    const icon = document.getElementById('pwdIcon');
    if (inp.type === 'password') { inp.type = 'text'; icon.className = 'fas fa-eye-slash'; }
    else { inp.type = 'password'; icon.className = 'fas fa-eye'; }
}

function mostrarError(msg){
    const el = document.getElementById('errorMsg');
    el.textContent = msg;
    el.classList.add('show');
    el.scrollIntoView({behavior:'smooth',block:'center'});
}

function ocultarError(){
    document.getElementById('errorMsg').classList.remove('show');
}

document.getElementById('accessForm').addEventListener('submit', async function(e){
    e.preventDefault();
    ocultarError();

    const btn = document.getElementById('btnAccess');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Validando...</span>';

    const fd = new FormData(this);
    try {
        const resp = await fetch('index.php?action=validar_acceso', {method:'POST', body: fd});
        const data = await resp.json();
        if (data.success) {
            window.location.href = 'responder/<?php echo $encuesta['id']; ?>';
        } else {
            mostrarError(data.message || 'Error de validación');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-unlock"></i> <span>Acceder a la encuesta</span>';
        }
    } catch (err) {
        mostrarError('Error de conexión. Intenta de nuevo.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-unlock"></i> <span>Acceder a la encuesta</span>';
    }
});
</script>
</body>
</html>
