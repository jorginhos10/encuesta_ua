<?php
if(!isset($_SESSION['user_id'])){ header("Location: " . BASE_URL . "login"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo BASE_URL; ?>">
    <title>Informe · <?php echo htmlspecialchars($encuesta['nombre']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root{--bg:#f2f2f7;--surface:#fff;--surface2:#f2f2f7;--border:rgba(0,0,0,.08);--border-strong:rgba(0,0,0,.12);--text:#1c1c1e;--text2:#636366;--text3:#aeaeb2;--accent:#007aff;--al:rgba(0,122,255,.08);--green:#34c759;--red:#ff3b30;--orange:#ff9500;--purple:#af52de;--indigo:#5856d6;--sh:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);--shm:0 4px 16px rgba(0,0,0,.08);--r:18px;--rs:12px;}
        *{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased;}
        body{font-family:'DM Sans',-apple-system,BlinkMacSystemFont,sans-serif;background:var(--bg);color:var(--text);}
        .report-header{background:linear-gradient(145deg,#1c1c1e 0%,#2c2c2e 100%);padding:40px 32px 36px;position:relative;overflow:hidden;}
        .report-header::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 80% 0%,rgba(0,122,255,.3) 0%,transparent 55%),radial-gradient(ellipse at 20% 100%,rgba(88,86,214,.25) 0%,transparent 50%);}
        .hi{max-width:1100px;margin:0 auto;position:relative;}
        .ht{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;}
        .eyebrow{font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,.45);margin-bottom:6px;}
        .ht-title{font-family:'DM Serif Display',serif;font-size:28px;color:#fff;letter-spacing:-.5px;line-height:1.2;}
        .hd{display:flex;gap:14px;margin-top:12px;flex-wrap:wrap;}
        .dchip{display:flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,.5);background:rgba(255,255,255,.06);padding:5px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.08);}
        .dchip i{font-size:10px;}
        .btn-close{display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.15);padding:9px 16px;border-radius:30px;font-family:inherit;font-size:13px;font-weight:500;cursor:pointer;transition:all .15s;}
        .btn-close:hover{background:rgba(255,255,255,.18);color:#fff;}
        .container{max-width:1100px;margin:0 auto;padding:28px 16px;}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:28px;}
        .stat-card{background:var(--surface);border-radius:var(--r);padding:20px 22px;box-shadow:var(--sh);border:1px solid var(--border);display:flex;align-items:center;gap:16px;}
        .si{width:46px;height:46px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
        .si.blue{background:rgba(0,122,255,.1);color:var(--accent);}
        .si.green{background:rgba(52,199,89,.1);color:var(--green);}
        .si.orange{background:rgba(255,149,0,.1);color:var(--orange);}
        .si.purple{background:rgba(175,82,222,.1);color:var(--purple);}
        .sv{font-size:26px;font-weight:300;letter-spacing:-1px;font-family:'DM Serif Display',serif;color:var(--text);line-height:1;}
        .sl{font-size:12px;color:var(--text3);margin-top:3px;}
        .chart-card{background:var(--surface);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--border);margin-bottom:20px;overflow:hidden;}
        .cc-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
        .cc-tg{display:flex;align-items:center;gap:10px;}
        .cc-icon{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,var(--accent),var(--indigo));display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;}
        .cc-title{font-size:15px;font-weight:600;letter-spacing:-.2px;}
        .cc-pill{font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:var(--al);color:var(--accent);}
        .cc-body{padding:22px;}
        canvas{max-height:300px;}
        .sb{margin-bottom:28px;}
        .sh-label{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
        .sh-label h2{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text3);}
        .sdiv{flex:1;height:1px;background:var(--border);}
        .qcard{background:var(--surface);border-radius:var(--rs);box-shadow:var(--sh);border:1px solid var(--border);margin-bottom:12px;overflow:hidden;}
        .qch{padding:14px 18px 10px;border-bottom:1px solid var(--border);}
        .qct{font-size:14px;font-weight:500;color:var(--text);line-height:1.4;}
        .qtag{display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:600;padding:3px 8px;border-radius:20px;background:var(--al);color:var(--accent);margin-top:5px;}
        .qcb{padding:14px 18px;}
        .rlist{display:flex;flex-direction:column;gap:6px;max-height:260px;overflow-y:auto;}
        .rlist::-webkit-scrollbar{width:3px;}
        .rlist::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:2px;}
        .rbubble{background:var(--surface2);border-radius:10px;padding:10px 14px;font-size:13px;color:var(--text);border-left:3px solid var(--accent);line-height:1.5;}
        .nodata{text-align:center;padding:24px;color:var(--text3);font-size:13px;}
        .nodata i{font-size:24px;display:block;margin-bottom:8px;opacity:.4;}
        .ht-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
        .btn-export{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:30px;font-family:inherit;font-size:13px;font-weight:500;cursor:pointer;transition:all .15s;text-decoration:none;border:1px solid;}
        .btn-export.excel{background:rgba(52,199,89,.12);color:#34c759;border-color:rgba(52,199,89,.3);}
        .btn-export.excel:hover{background:rgba(52,199,89,.22);}
        .btn-export.pdf{background:rgba(255,59,48,.12);color:#ff3b30;border-color:rgba(255,59,48,.3);}
        .btn-export.pdf:hover{background:rgba(255,59,48,.22);}
        .chart-ctrl{display:flex;justify-content:flex-end;margin-bottom:14px;}
        @media print{
            .ht-right{display:none!important;}
            .chart-ctrl{display:none!important;}
            .qcard{break-inside:avoid;page-break-inside:avoid;}
            .rlist{max-height:none!important;overflow:visible!important;}
            .stats-grid{grid-template-columns:repeat(4,1fr)!important;}
            canvas{max-height:220px!important;}
            body{background:#fff!important;}
        }
        .chart-type-sel{padding:6px 28px 6px 10px;border:1.5px solid var(--border-strong);border-radius:8px;font-size:12px;font-weight:500;font-family:inherit;color:var(--text2);background:var(--surface2);cursor:pointer;outline:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%23636366' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;transition:border-color .15s;}
        .chart-type-sel:hover,.chart-type-sel:focus{border-color:var(--accent);color:var(--text);}
        .view-toggle-bar{margin-bottom:24px;display:flex;align-items:center;gap:12px;background:var(--surface);border-radius:var(--r);padding:14px 20px;box-shadow:var(--sh);border:1px solid var(--border);}
        .view-toggle-bar label{font-size:13px;font-weight:600;color:var(--text2);white-space:nowrap;}
        .vista-sel{flex:1;max-width:400px;padding:10px 36px 10px 14px;border:1.5px solid var(--border-strong);border-radius:12px;font-size:14px;font-weight:500;font-family:inherit;color:var(--text);background:var(--surface2);cursor:pointer;outline:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%23636366' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;transition:border-color .15s,box-shadow .15s;}
        .vista-sel:focus{border-color:var(--accent);box-shadow:0 0 0 4px var(--al);}
        .part-card{background:var(--surface);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--border);margin-bottom:14px;overflow:hidden;}
        .part-head{padding:14px 20px;background:linear-gradient(135deg,rgba(0,122,255,.05),rgba(88,86,214,.03));border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
        .part-num{width:28px;height:28px;border-radius:8px;background:var(--accent);color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;}
        .part-name{font-size:14px;font-weight:600;}
        .part-meta{font-size:12px;color:var(--text3);margin-left:auto;}
        .part-row{display:flex;gap:16px;padding:11px 20px;border-bottom:1px solid var(--border);}
        .part-row:last-child{border-bottom:none;}
        .part-q{font-size:13px;font-weight:500;color:var(--text2);flex:0 0 38%;line-height:1.4;}
        .part-a{font-size:13px;color:var(--text);flex:1;line-height:1.4;}
        .td-badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;margin:1px 2px;}
        .td-yes{background:rgba(52,199,89,.15);color:var(--green);}
        .td-no{background:rgba(255,59,48,.1);color:var(--red);}
        .btn-del-part{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:500;font-family:inherit;cursor:pointer;border:1px solid rgba(255,59,48,.3);background:rgba(255,59,48,.08);color:var(--red);transition:all .15s;margin-left:auto;flex-shrink:0;}
        .btn-del-part:hover{background:rgba(255,59,48,.18);border-color:rgba(255,59,48,.5);}
        @media print{.btn-del-part{display:none!important;}}
        .sat-toggle-bar{display:flex;gap:8px;margin-bottom:14px;}
        .sat-toggle-btn{padding:7px 16px;border-radius:20px;border:1.5px solid var(--border-strong);background:var(--surface2);font-size:12px;font-weight:600;font-family:inherit;color:var(--text2);cursor:pointer;transition:all .15s;}
        .sat-toggle-btn.active{background:var(--accent);color:#fff;border-color:var(--accent);}
        .sat-avg-wrap{text-align:center;padding:36px 24px;}
        .sat-avg-emoji{font-size:72px;line-height:1;display:block;margin-bottom:10px;}
        .sat-avg-num{font-size:56px;font-weight:300;letter-spacing:-2px;font-family:'DM Serif Display',serif;color:var(--text);}
        .sat-avg-slash{font-size:24px;color:var(--text3);margin:0 4px;}
        .sat-avg-sub{font-size:13px;color:var(--text3);margin-top:6px;}
        .sat-avg-label{font-size:16px;font-weight:600;color:var(--text);margin-top:10px;}
        .sat-ind-val{display:inline-flex;align-items:center;gap:6px;font-weight:600;}
    </style>
</head>
<body>
<div class="report-header">
    <div class="hi">
        <div class="ht">
            <div>
                <div class="eyebrow"><i class="fas fa-chart-bar"></i> Informe de resultados</div>
                <h1 class="ht-title"><?php echo htmlspecialchars($encuesta['nombre']); ?></h1>
                <div class="hd">
                    <div class="dchip"><i class="fas fa-calendar-plus"></i> Inicio: <?php echo date('d/m/Y',strtotime($encuesta['fecha_inicio'])); ?></div>
                    <div class="dchip"><i class="fas fa-calendar-times"></i> Cierre: <?php echo date('d/m/Y',strtotime($encuesta['fecha_cierre'])); ?></div>
                    <div class="dchip"><i class="fas fa-circle" style="color:<?php echo $encuesta['estado']==='activa'?'#34c759':'#ff3b30';?>"></i> <?php echo ucfirst($encuesta['estado']); ?></div>
                </div>
            </div>
            <div class="ht-right">
                <a href="encuestas/<?php echo $encuesta['id']; ?>/excel" class="btn-export excel">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <button onclick="window.print()" class="btn-export pdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button class="btn-close" onclick="window.close()"><i class="fas fa-times"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="stats-grid">
        <div class="stat-card"><div class="si blue"><i class="fas fa-users"></i></div><div><div class="sv"><?php echo $totalRespuestas; ?></div><div class="sl">Encuestas realizadas</div></div></div>
        <div class="stat-card"><div class="si green"><i class="fas fa-calendar-check"></i></div><div><div class="sv"><?php echo date('d/m',strtotime($encuesta['fecha_inicio'])); ?></div><div class="sl">Fecha inicio</div></div></div>
        <div class="stat-card"><div class="si orange"><i class="fas fa-calendar-times"></i></div><div><div class="sv"><?php echo date('d/m',strtotime($encuesta['fecha_cierre'])); ?></div><div class="sl">Fecha cierre</div></div></div>
        <div class="stat-card"><div class="si purple"><i class="fas fa-layer-group"></i></div><div><div class="sv"><?php echo count($encabezados); ?></div><div class="sl">Secciones</div></div></div>
    </div>

    <?php if(!empty($respuestasPorFecha)): ?>
    <div class="chart-card" style="margin-bottom:28px;">
        <div class="cc-header">
            <div class="cc-tg"><div class="cc-icon"><i class="fas fa-chart-line"></i></div><span class="cc-title">Participantes por fecha</span></div>
            <span class="cc-pill"><?php echo count($respuestasPorFecha); ?> días</span>
        </div>
        <div class="cc-body">
            <canvas id="graficaTiempo"></canvas>
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;margin-top:14px;padding-top:12px;border-top:1px solid var(--border);">
                <span style="font-size:12px;color:var(--text3);">Total de personas:</span>
                <span style="font-size:22px;font-weight:300;letter-spacing:-1px;font-family:'DM Serif Display',serif;color:var(--accent);"><?php echo $totalRespuestas; ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="view-toggle-bar">
        <label><i class="fas fa-eye" style="color:var(--accent);margin-right:6px;"></i>Tipo de vista:</label>
        <select class="vista-sel" onchange="toggleVista(this.value)">
            <option value="global">📊 Global &mdash; resultados agregados</option>
            <option value="individual">👤 Individual &mdash; respuestas por participante</option>
        </select>
    </div>

    <div id="view-global">
    <?php foreach($encabezados as $enc):
        $preguntas=$enc['preguntas'];
        if(empty($preguntas)) continue;
    ?>
    <div class="sb">
        <div class="sh-label">
            <h2><i class="fas fa-layer-group" style="margin-right:6px;color:var(--accent);"></i><?php echo htmlspecialchars($enc['titulo']); ?></h2>
            <div class="sdiv"></div>
        </div>
        <?php foreach($preguntas as $preg):
            $stats=$estadisticas[$preg['id']]['datos']??[];
            $isTexto=in_array($preg['tipo_pregunta'],['texto_corto','texto_largo']);
            $isTinder=$preg['tipo_pregunta']==='tinder';
            $isCheckbox=$preg['tipo_pregunta']==='checkbox';
            $tl=['texto_corto'=>'Texto corto','texto_largo'=>'Texto largo','seleccion_unica'=>'Selección única','seleccion_multiple'=>'Selección múltiple','checkbox'=>'Casilla','tinder'=>'Card Slider','satisfaccion'=>'Satisfacción','matriz'=>'Matriz importancia/satisfacción','encabezado'=>'Encabezado','texto_ordinario'=>'Informativo'][$preg['tipo_pregunta']]??$preg['tipo_pregunta'];
        ?>
        <div class="qcard">
            <div class="qch">
                <div class="qct"><?php echo textoConEnlaces($preg['pregunta']); ?></div>
                <span class="qtag"><i class="fas fa-tag"></i> <?php echo $tl; ?></span>
            </div>
            <div class="qcb">
                <?php if($isTexto): ?>
                    <?php if(!empty($stats)): ?>
                    <div class="rlist">
                        <?php foreach($stats as $resp): ?>
                        <div class="rbubble"><?php echo htmlspecialchars($resp['respuesta']); ?></div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?><div class="nodata"><i class="fas fa-inbox"></i>Sin respuestas aún</div><?php endif; ?>

                <?php elseif($isTinder): ?>
                    <?php if(!empty($stats)):
                        $cid='grafica_'.$preg['id'];
                        $tinderLabels=json_encode(array_column($stats,'titulo'));
                        $tinderYes=json_encode(array_column($stats,'yes'));
                        $tinderNo=json_encode(array_column($stats,'no'));
                        $totalYes=array_sum(array_column($stats,'yes'));
                        $totalNo=array_sum(array_column($stats,'no'));
                        $tinderOpts=json_decode($preg['opciones']??'{}',true);
                        $labelSiInf=htmlspecialchars($tinderOpts['label_si']??'Me gusta',ENT_QUOTES,'UTF-8');
                        $labelNoInf=htmlspecialchars($tinderOpts['label_no']??'No me gusta',ENT_QUOTES,'UTF-8');
                    ?>
                    <div class="chart-ctrl">
                        <select class="chart-type-sel" onchange="onChartTypeChange(this,'<?php echo $cid; ?>')">
                            <option value="bar" selected>📊 Barra</option>
                            <option value="pie">🥧 Torta</option>
                            <option value="doughnut">🍩 Anillo</option>
                        </select>
                    </div>
                    <canvas id="<?php echo $cid; ?>"></canvas>
                    <script>
                    (function(){
                        const cid='<?php echo $cid; ?>';
                        const labels=<?php echo $tinderLabels; ?>;
                        const yesData=<?php echo $tinderYes; ?>;
                        const noData=<?php echo $tinderNo; ?>;
                        const totalYes=<?php echo $totalYes; ?>;
                        const totalNo=<?php echo $totalNo; ?>;
                        const labelSi='<?php echo $labelSiInf; ?>';
                        const labelNo='<?php echo $labelNoInf; ?>';
                        const font={family:'DM Sans',size:12};
                        const top2Idx=(function(){
                            const sorted=[...yesData].map((v,i)=>({v,i})).sort((a,b)=>b.v-a.v);
                            return new Set(sorted.slice(0,2).map(x=>x.i));
                        })();
                        const barLabelPlugin={id:'barLabels',afterDatasetsDraw(chart){const{ctx}=chart;chart.data.datasets.forEach((ds,i)=>{const meta=chart.getDatasetMeta(i);if(!meta.hidden){meta.data.forEach((bar,idx)=>{const v=ds.data[idx];if(v==null)return;ctx.save();ctx.fillStyle='#000000';ctx.font='bold 12px "DM Sans",sans-serif';ctx.textAlign='center';ctx.textBaseline='bottom';ctx.fillText(v,bar.x,bar.y-4);if(i===0&&top2Idx.has(idx)){ctx.font='13px serif';ctx.fillText('👑',bar.x,bar.y-18);}ctx.restore();});}});}};
                        const pieLabelPlugin={id:'pieLabels',afterDatasetsDraw(chart){const{ctx}=chart;chart.data.datasets.forEach((ds,i)=>{const meta=chart.getDatasetMeta(i);if(!meta.hidden){meta.data.forEach((arc,idx)=>{const v=ds.data[idx];if(v==null||v===0)return;const mid=(arc.startAngle+arc.endAngle)/2;const r=(arc.outerRadius+arc.innerRadius)/2;const x=arc.x+r*Math.cos(mid);const y=arc.y+r*Math.sin(mid);ctx.save();ctx.fillStyle='#ffffff';ctx.font='bold 13px "DM Sans",sans-serif';ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillText(v,x,y);ctx.restore();});}})}};
                        window.chartInstances=window.chartInstances||{};
                        function crearGrafica(tipo){
                            if(window.chartInstances[cid]) window.chartInstances[cid].destroy();
                            const ctx=document.getElementById(cid).getContext('2d');
                            let cfg;
                            if(tipo==='bar'){
                                cfg={type:'bar',data:{labels,datasets:[
                                    {label:labelSi+' 👍',data:yesData,backgroundColor:'rgba(52,199,89,.8)',borderRadius:6,borderWidth:0},
                                    {label:labelNo+' 👎',data:noData,backgroundColor:'rgba(255,59,48,.8)',borderRadius:6,borderWidth:0}
                                ]},options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{position:'top',labels:{font,padding:14,usePointStyle:true}}},scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{font:{family:'DM Sans',size:11}}},x:{grid:{display:false},ticks:{font:{family:'DM Sans',size:11}}}}},plugins:[barLabelPlugin]};
                            } else {
                                cfg={type:tipo,data:{labels:[labelSi+' 👍',labelNo+' 👎'],datasets:[{data:[totalYes,totalNo],backgroundColor:['rgba(52,199,89,.85)','rgba(255,59,48,.85)'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{position:'bottom',labels:{font,padding:14,usePointStyle:true}}}},plugins:[pieLabelPlugin]};
                            }
                            window.chartInstances[cid]=new Chart(ctx,cfg);
                        }
                        window.chartCreators=window.chartCreators||{};
                        window.chartCreators[cid]=crearGrafica;
                        crearGrafica('bar');
                    })();
                    </script>
                    <?php else: ?><div class="nodata"><i class="fas fa-layer-group"></i>Sin respuestas aún</div><?php endif; ?>

                <?php elseif($isCheckbox): ?>
                    <?php
                        $cntSi=0;$cntNo=0;
                        foreach($stats as $r){ if($r['respuesta']==='si') $cntSi=(int)$r['total']; elseif($r['respuesta']==='no') $cntNo=(int)$r['total']; }
                        $cntTotal=$cntSi+$cntNo;
                        $pctSi=$cntTotal>0?round($cntSi/$cntTotal*100):0;
                        $pctNo=100-$pctSi;
                    ?>
                    <?php if($cntTotal>0): ?>
                    <div style="display:flex;flex-direction:column;gap:10px;padding:4px 0;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <span style="width:60px;font-size:13px;color:var(--green);font-weight:600;">Marcado</span>
                            <div style="flex:1;background:rgba(52,199,89,.12);border-radius:8px;overflow:hidden;height:22px;"><div style="width:<?php echo $pctSi; ?>%;background:var(--green);height:100%;border-radius:8px;transition:width .4s;"></div></div>
                            <span style="font-size:13px;font-weight:600;color:var(--text);min-width:60px;text-align:right;"><?php echo $cntSi; ?> (<?php echo $pctSi; ?>%)</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <span style="width:60px;font-size:13px;color:var(--text2);font-weight:600;">Sin marcar</span>
                            <div style="flex:1;background:rgba(0,0,0,.06);border-radius:8px;overflow:hidden;height:22px;"><div style="width:<?php echo $pctNo; ?>%;background:var(--text3);height:100%;border-radius:8px;transition:width .4s;"></div></div>
                            <span style="font-size:13px;font-weight:600;color:var(--text);min-width:60px;text-align:right;"><?php echo $cntNo; ?> (<?php echo $pctNo; ?>%)</span>
                        </div>
                        <div style="font-size:12px;color:var(--text3);margin-top:2px;">Total: <?php echo $cntTotal; ?> respuesta<?php echo $cntTotal!=1?'s':''; ?></div>
                    </div>
                    <?php else: ?><div class="nodata"><i class="fas fa-inbox"></i>Sin respuestas aún</div><?php endif; ?>

                <?php elseif($preg['tipo_pregunta'] === 'matriz'): ?>
                    <?php $matTotal=$estadisticas[$preg['id']]['total']??0; ?>
                    <?php if($matTotal>0): ?>
                    <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead><tr style="background:rgba(0,122,255,.08);text-align:left;">
                            <th style="padding:8px 10px;">N°</th><th style="padding:8px 10px;">Aspecto</th>
                            <th style="padding:8px 10px;text-align:center;">Importancia (prom. 1-5)</th>
                            <th style="padding:8px 10px;text-align:center;">Satisfacción (prom. 1-5)</th>
                            <th style="padding:8px 10px;text-align:center;">Respuestas</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach($stats as $mi=>$mf): ?>
                        <tr style="border-top:1px solid rgba(0,0,0,.08);">
                            <td style="padding:8px 10px;"><?php echo $mi+1; ?></td>
                            <td style="padding:8px 10px;"><strong><?php echo htmlspecialchars($mf['aspecto']); ?></strong><div style="font-size:12px;color:var(--text2);"><?php echo htmlspecialchars($mf['necesidad']); ?></div></td>
                            <td style="padding:8px 10px;text-align:center;font-weight:700;"><?php echo $mf['n']?$mf['prom_imp']:'—'; ?></td>
                            <td style="padding:8px 10px;text-align:center;font-weight:700;"><?php echo $mf['n']?$mf['prom_sat']:'—'; ?></td>
                            <td style="padding:8px 10px;text-align:center;"><?php echo $mf['n']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                    <?php else: ?><div class="nodata"><i class="fas fa-inbox"></i>Sin respuestas aún</div><?php endif; ?>

                <?php elseif($preg['tipo_pregunta'] === 'satisfaccion'): ?>
                    <?php
                        $satTotal   = $estadisticas[$preg['id']]['total']    ?? 0;
                        $satProm    = $estadisticas[$preg['id']]['promedio']  ?? 0;
                        $satDatos   = $estadisticas[$preg['id']]['datos']     ?? [];
                        $satCid     = 'grafica_'.$preg['id'];
                        $satEmojis  = ['😞','😕','😐','🙂','😊'];
                        $satEtiq    = ['Muy insatisfecho','Insatisfecho','Neutral','Satisfecho','Muy satisfecho'];
                        $satPromIdx = $satProm > 0 ? max(0, min(4, (int)round($satProm) - 1)) : 2;
                        $satEmojiAvg = $satProm > 0 ? $satEmojis[$satPromIdx] : '—';
                        $satLabelAvg = $satProm > 0 ? $satEtiq[$satPromIdx] : '';
                        $satJsLabels = json_encode(['1 '.$satEmojis[0],'2 '.$satEmojis[1],'3 '.$satEmojis[2],'4 '.$satEmojis[3],'5 '.$satEmojis[4]]);
                        $satJsData   = json_encode(array_column($satDatos,'total'));
                    ?>
                    <?php if($satTotal > 0): ?>
                    <div class="sat-toggle-bar">
                        <button class="sat-toggle-btn active" onclick="satVista('<?php echo $satCid; ?>','dist',this)"><i class="fas fa-chart-bar"></i> Distribución</button>
                        <button class="sat-toggle-btn" onclick="satVista('<?php echo $satCid; ?>','prom',this)"><i class="fas fa-star"></i> Promedio</button>
                    </div>
                    <div id="<?php echo $satCid; ?>-dist">
                        <canvas id="<?php echo $satCid; ?>"></canvas>
                        <script>
                        (function(){
                            const cid='<?php echo $satCid; ?>';
                            const labels=<?php echo $satJsLabels; ?>;
                            const data=<?php echo $satJsData; ?>;
                            const colors=['rgba(255,59,48,.85)','rgba(255,149,0,.85)','rgba(255,204,0,.85)','rgba(52,199,89,.85)','rgba(0,122,255,.85)'];
                            const barLabelPlugin={id:'barLabels',afterDatasetsDraw(chart){const{ctx}=chart;chart.data.datasets.forEach((ds,i)=>{const meta=chart.getDatasetMeta(i);if(!meta.hidden){meta.data.forEach((bar,idx)=>{const v=ds.data[idx];if(!v)return;ctx.save();ctx.fillStyle='#1c1c1e';ctx.font='bold 12px "DM Sans",sans-serif';ctx.textAlign='center';ctx.textBaseline='bottom';ctx.fillText(v,bar.x,bar.y-4);ctx.restore();});}});}};
                            window.chartInstances=window.chartInstances||{};
                            if(window.chartInstances[cid]) window.chartInstances[cid].destroy();
                            const ctx=document.getElementById(cid).getContext('2d');
                            window.chartInstances[cid]=new Chart(ctx,{type:'bar',data:{labels,datasets:[{label:'Respuestas',data,backgroundColor:colors,borderRadius:8,borderWidth:0}]},options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{font:{family:'DM Sans',size:11},stepSize:1,callback:v=>Number.isInteger(v)?v:null}},x:{grid:{display:false},ticks:{font:{family:'DM Sans',size:14}}}}},plugins:[barLabelPlugin]});
                        })();
                        </script>
                    </div>
                    <div id="<?php echo $satCid; ?>-prom" style="display:none;">
                        <div class="sat-avg-wrap">
                            <span class="sat-avg-emoji"><?php echo $satEmojiAvg; ?></span>
                            <div>
                                <span class="sat-avg-num"><?php echo $satProm; ?></span>
                                <span class="sat-avg-slash">/ 5</span>
                            </div>
                            <div class="sat-avg-label"><?php echo htmlspecialchars($satLabelAvg); ?></div>
                            <div class="sat-avg-sub"><?php echo $satTotal; ?> respuesta<?php echo $satTotal!=1?'s':''; ?></div>
                        </div>
                    </div>
                    <?php else: ?><div class="nodata"><i class="fas fa-inbox"></i>Sin respuestas aún</div><?php endif; ?>

                <?php else: ?>
                    <?php if(!empty($stats)):
                        $cid='grafica_'.$preg['id'];
                        $rLabels=json_encode(array_column($stats,'respuesta'));
                        $rData=json_encode(array_column($stats,'total'));
                        $defTipo=count($stats)<=5?'doughnut':'bar';
                        $selBar=$defTipo==='bar'?'selected':'';
                        $selDon=$defTipo==='doughnut'?'selected':'';
                    ?>
                    <div class="chart-ctrl">
                        <select class="chart-type-sel" onchange="onChartTypeChange(this,'<?php echo $cid; ?>')">
                            <option value="bar" <?php echo $selBar; ?>>📊 Barra</option>
                            <option value="pie">🥧 Torta</option>
                            <option value="doughnut" <?php echo $selDon; ?>>🍩 Anillo</option>
                        </select>
                    </div>
                    <canvas id="<?php echo $cid; ?>"></canvas>
                    <script>
                    (function(){
                        const cid='<?php echo $cid; ?>';
                        const pal=['rgba(0,122,255,.85)','rgba(88,86,214,.85)','rgba(52,199,89,.85)','rgba(255,149,0,.85)','rgba(255,59,48,.85)','rgba(175,82,222,.85)','rgba(90,200,250,.85)','rgba(255,204,0,.85)'];
                        const labels=<?php echo $rLabels; ?>;
                        const data=<?php echo $rData; ?>;
                        const font={family:'DM Sans',size:12};
                        const barLabelPlugin={id:'barLabels',afterDatasetsDraw(chart){const{ctx}=chart;chart.data.datasets.forEach((ds,i)=>{const meta=chart.getDatasetMeta(i);if(!meta.hidden){meta.data.forEach((bar,idx)=>{const v=ds.data[idx];if(v==null)return;ctx.save();ctx.fillStyle='#000000';ctx.font='bold 12px "DM Sans",sans-serif';ctx.textAlign='center';ctx.textBaseline='bottom';ctx.fillText(v,bar.x,bar.y-4);ctx.restore();});}});}};
                        const pieLabelPlugin={id:'pieLabels',afterDatasetsDraw(chart){const{ctx}=chart;chart.data.datasets.forEach((ds,i)=>{const meta=chart.getDatasetMeta(i);if(!meta.hidden){meta.data.forEach((arc,idx)=>{const v=ds.data[idx];if(v==null||v===0)return;const mid=(arc.startAngle+arc.endAngle)/2;const r=(arc.outerRadius+arc.innerRadius)/2;const x=arc.x+r*Math.cos(mid);const y=arc.y+r*Math.sin(mid);ctx.save();ctx.fillStyle='#ffffff';ctx.font='bold 13px "DM Sans",sans-serif';ctx.textAlign='center';ctx.textBaseline='middle';ctx.fillText(v,x,y);ctx.restore();});}})}};
                        window.chartInstances=window.chartInstances||{};
                        function crearGrafica(tipo){
                            if(window.chartInstances[cid]) window.chartInstances[cid].destroy();
                            const ctx=document.getElementById(cid).getContext('2d');
                            const isBar=tipo==='bar';
                            window.chartInstances[cid]=new Chart(ctx,{type:tipo,data:{labels,datasets:[{label:'Respuestas',data,backgroundColor:pal.slice(0,labels.length),borderWidth:0,borderRadius:isBar?6:0}]},options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{position:isBar?'top':'bottom',labels:{font,padding:14,usePointStyle:true}}},scales:isBar?{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{font:{family:'DM Sans',size:11}}},x:{grid:{display:false},ticks:{font:{family:'DM Sans',size:11}}}}:{}},plugins:isBar?[barLabelPlugin]:[pieLabelPlugin]});
                        }
                        window.chartCreators=window.chartCreators||{};
                        window.chartCreators[cid]=crearGrafica;
                        crearGrafica('<?php echo $defTipo; ?>');
                    })();
                    </script>
                    <?php else: ?><div class="nodata"><i class="fas fa-chart-pie"></i>Sin respuestas aún</div><?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
    </div><!-- /view-global -->

    <div id="view-individual" style="display:none;">
        <?php if(empty($respuestasIndividuales)): ?>
        <div class="nodata" style="padding:48px 24px;">
            <i class="fas fa-users"></i>Sin respuestas individuales aún
        </div>
        <?php else: ?>
        <?php foreach($respuestasIndividuales as $i => $part):
            $sk = $part['del_ip'].'||'.($part['del_usuario'] ?? '').'||'.$part['del_dt'];
        ?>
        <div class="part-card" data-sk="<?php echo htmlspecialchars($sk); ?>">
            <div class="part-head">
                <div class="part-num"><?php echo $i + 1; ?></div>
                <span class="part-name">
                    <?php echo htmlspecialchars($part['nombre'] ?? 'Anónimo'); ?>
                </span>
                <span class="part-meta">
                    <i class="fas fa-clock" style="margin-right:4px;"></i><?php echo $part['fecha']; ?>
                </span>
                <button class="btn-del-part"
                    data-eid="<?php echo intval($encuesta['id']); ?>"
                    data-ip="<?php echo htmlspecialchars($part['del_ip']); ?>"
                    data-usuario="<?php echo htmlspecialchars($part['del_usuario']); ?>"
                    data-dt="<?php echo htmlspecialchars($part['del_dt']); ?>"
                    onclick="eliminarParticipante(this)">
                    <i class="fas fa-trash"></i> Eliminar encuesta
                </button>
            </div>
            <div class="part-body">
            <?php foreach($todasPreguntas as $preg):
                $r = $part['resps'][$preg['id']] ?? null;
                if ($r === null) continue;
                if ($preg['tipo_pregunta'] === 'tinder') {
                    $decisions = json_decode($r, true);
                    $displayR = '';
                    if (is_array($decisions)) {
                        foreach ($decisions as $d) {
                            $cls  = ($d['decision'] === 'yes') ? 'td-yes' : 'td-no';
                            $icon = ($d['decision'] === 'yes') ? '👍' : '👎';
                            $displayR .= '<span class="td-badge '.$cls.'">'.$icon.' '.htmlspecialchars($d['titulo']).'</span>';
                        }
                    }
                } elseif ($preg['tipo_pregunta'] === 'matriz') {
                    $displayR = '';
                    $items = json_decode($r, true);
                    if (is_array($items)) {
                        foreach ($items as $it) {
                            $displayR .= '<div>'.htmlspecialchars($it['aspecto'] ?? '').': Importancia <strong>'.(int)($it['importancia'] ?? 0).'</strong> · Satisfacción <strong>'.(int)($it['satisfaccion'] ?? 0).'</strong></div>';
                        }
                    }
                } elseif ($preg['tipo_pregunta'] === 'satisfaccion') {
                    $satEmojisInd = ['','😞','😕','😐','🙂','😊'];
                    $satEtiqInd   = ['','Muy insatisfecho','Insatisfecho','Neutral','Satisfecho','Muy satisfecho'];
                    $sv = (int)$r;
                    if ($sv >= 1 && $sv <= 5) {
                        $displayR = '<span class="sat-ind-val">'.$satEmojisInd[$sv].' '.$sv.' &mdash; '.htmlspecialchars($satEtiqInd[$sv]).'</span>';
                    } else {
                        $displayR = htmlspecialchars($r);
                    }
                } else {
                    $displayR = nl2br(htmlspecialchars($r));
                }
            ?>
            <div class="part-row">
                <div class="part-q"><?php
                    $qt = $preg['pregunta'];
                    echo htmlspecialchars(mb_strlen($qt) > 80 ? mb_substr($qt, 0, 78).'…' : $qt);
                ?></div>
                <div class="part-a"><?php echo $displayR; ?></div>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div><!-- /view-individual -->
</div>

<?php if(!empty($respuestasPorFecha)): ?>
<script>
(function(){
    const ctx=document.getElementById('graficaTiempo').getContext('2d');
    const pointLabels={id:'pointLabels',afterDatasetsDraw(chart){
        const{ctx:c}=chart;
        chart.data.datasets.forEach((ds,i)=>{
            const meta=chart.getDatasetMeta(i);
            meta.data.forEach((pt,idx)=>{
                const v=ds.data[idx];
                if(v==null) return;
                c.save();
                c.fillStyle='rgba(0,122,255,1)';
                c.font='bold 11px "DM Sans",sans-serif';
                c.textAlign='center';
                c.textBaseline='bottom';
                c.fillText(v,pt.x,pt.y-6);
                c.restore();
            });
        });
    }};
    new Chart(ctx,{type:'line',data:{labels:<?php echo json_encode(array_column($respuestasPorFecha,'fecha')); ?>,datasets:[{label:'Personas por día',data:<?php echo json_encode(array_column($respuestasPorFecha,'total')); ?>,borderColor:'rgba(0,122,255,1)',backgroundColor:'rgba(0,122,255,.08)',borderWidth:2,tension:.4,fill:true,pointBackgroundColor:'rgba(0,122,255,1)',pointRadius:4,pointHoverRadius:6}]},options:{responsive:true,maintainAspectRatio:true,layout:{padding:{top:20}},plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return ' '+c.parsed.y+' persona'+(c.parsed.y!==1?'s':'')}}}},scales:{y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{font:{family:'DM Sans',size:11},stepSize:1,callback:function(v){return Number.isInteger(v)?v:null}}},x:{grid:{display:false},ticks:{font:{family:'DM Sans',size:11}}}}},plugins:[pointLabels]});
})();
</script>
<?php endif; ?>
<script>
function onChartTypeChange(sel, cid) {
    if (window.chartCreators && window.chartCreators[cid]) {
        window.chartCreators[cid](sel.value);
    }
}
function satVista(cid, mode, btn) {
    document.getElementById(cid + '-dist').style.display = mode === 'dist' ? '' : 'none';
    document.getElementById(cid + '-prom').style.display = mode === 'prom' ? '' : 'none';
    btn.closest('.sat-toggle-bar').querySelectorAll('.sat-toggle-btn').forEach(b => b.classList.toggle('active', b === btn));
}
function toggleVista(val) {
    document.getElementById('view-global').style.display     = val === 'global'     ? '' : 'none';
    document.getElementById('view-individual').style.display = val === 'individual' ? '' : 'none';
}
function eliminarParticipante(btn) {
    if (!confirm('¿Eliminar esta respuesta? Esta acción no se puede deshacer.')) return;
    const eid     = btn.dataset.eid;
    const ip      = btn.dataset.ip;
    const usuario = btn.dataset.usuario;
    const dt      = btn.dataset.dt;
    btn.disabled = true;
    const fd = new FormData();
    fd.append('encuesta_id', eid);
    fd.append('ip', ip);
    fd.append('usuario', usuario);
    fd.append('dt', dt);
    fetch('index.php?action=eliminar_participante', {method:'POST', body:fd})
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                const card = btn.closest('.part-card');
                card.style.transition = 'opacity .3s';
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 300);
            } else {
                alert(d.message || 'Error al eliminar');
                btn.disabled = false;
            }
        })
        .catch(() => { alert('Error de red'); btn.disabled = false; });
}

// ── PARTICIPANTES SIN RESPUESTA EN PREGUNTAS 20 Y 21 ─────────
(function(){
    const participantes = <?php
        $sinRespuesta = [];
        foreach ($respuestasIndividuales as $i => $part) {
            $falta = [];
            if (!isset($part['resps'][20])) $falta[] = 'P20 – He leído la Política de tratamiento de Datos Personales';
            if (!isset($part['resps'][21])) $falta[] = 'P21 – Acepto la Política de tratamiento de Datos Personales';
            if (!empty($falta)) {
                $sinRespuesta[] = [
                    'num'    => $i + 1,
                    'nombre' => $part['nombre'] ?? 'Anónimo',
                    'fecha'  => $part['fecha'],
                    'falta'  => $falta,
                ];
            }
        }
        echo json_encode($sinRespuesta);
    ?>;

    if (!participantes.length) {
        console.log('%c✅ Todos respondieron las preguntas 20 y 21.', 'color:#34c759;font-weight:bold;');
        return;
    }
    console.group('%c⚠️ ' + participantes.length + ' participante(s) sin responder P20 o P21', 'color:#ff9500;font-weight:bold;font-size:13px;');
    console.table(participantes.map(p => ({
        '#':      p.num,
        Nombre:   p.nombre,
        Fecha:    p.fecha,
        'Faltan': p.falta.join(' | ')
    })));
    console.groupEnd();
})();
// ── DETECCIÓN DE DUPLICADOS ──────────────────────────────────
(function(){
    const duplicados = <?php echo json_encode($duplicados ?? []); ?>;
    if (!duplicados.length) return;

    // Cambiar a vista individual para que las cards estén en el DOM
    const sel = document.querySelector('.vista-sel');
    if (sel) { sel.value = 'individual'; toggleVista('individual'); }

    window.__irDup = [];

    console.warn('%c⚠️ DUPLICADOS — ' + duplicados.length + ' caso(s) detectado(s)', 'background:#ff9500;color:#fff;font-size:13px;font-weight:bold;padding:3px 8px;border-radius:4px;');

    duplicados.forEach((d, i) => {
        const resps = d.respuestas.split(' ||| ');
        const card  = document.querySelector(`.part-card[data-sk="${CSS.escape(d.sesion_key)}"]`);

        function resaltarCard(el) {
            if (!el) return;
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.style.transition  = 'background .3s';
            el.style.background  = '#fffbe6';
            el.style.boxShadow   = '0 0 0 3px #ffcc00';
            setTimeout(() => { el.style.background = ''; el.style.boxShadow = ''; }, 2500);
        }

        window.__irDup[i] = () => resaltarCard(card);

        if (card) {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function onClick() {
                resaltarCard(card);
            });
        }

        console.groupCollapsed(
            `%c#${i+1} %c${d.pregunta_texto}  %c→ __irDup[${i}]()`,
            'color:#ff3b30;font-weight:bold;',
            'color:#1c1c1e;font-weight:600;',
            'color:#007aff;font-style:italic;'
        );
        console.log('❓ Pregunta:         ', d.pregunta_texto);
        console.log('📋 Respuesta repetida:', resps[0]);
        console.log('📋 Copias guardadas: ', resps);
        console.log('👤 Usuario:          ', d.usuario || 'Anónimo');
        console.log('📅 Fecha:            ', d.fecha);
        if (card) console.log('🔗 Elemento en página:', card);
        console.groupEnd();
    });

    console.info('%cPara ir a un duplicado en la página ejecuta: __irDup[N]()', 'color:#007aff;');
})();
</script>
</body>
</html>
