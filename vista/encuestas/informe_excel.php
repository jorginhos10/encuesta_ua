<html>
<head><meta charset="UTF-8"></head>
<body>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:12px;">

    <!-- Encabezado del informe -->
    <tr style="background:#1c1c1e;color:white;">
        <td colspan="5" style="font-size:15px;font-weight:bold;padding:12px;">
            INFORME DE RESULTADOS — <?php echo htmlspecialchars($encuesta['nombre']); ?>
        </td>
    </tr>
    <tr style="background:#2c2c2e;color:#ccc;">
        <td><b>Fecha inicio</b></td>
        <td colspan="4"><?php echo date('d/m/Y', strtotime($encuesta['fecha_inicio'])); ?></td>
    </tr>
    <tr style="background:#2c2c2e;color:#ccc;">
        <td><b>Fecha cierre</b></td>
        <td colspan="4"><?php echo date('d/m/Y', strtotime($encuesta['fecha_cierre'])); ?></td>
    </tr>
    <tr style="background:#2c2c2e;color:#ccc;">
        <td><b>Estado</b></td>
        <td colspan="4"><?php echo ucfirst($encuesta['estado']); ?></td>
    </tr>
    <tr style="background:#2c2c2e;color:#ccc;">
        <td><b>Participantes</b></td>
        <td colspan="4"><?php echo $totalRespuestas; ?></td>
    </tr>

    <!-- Fila vacía separadora -->
    <tr><td colspan="5"></td></tr>

    <!-- Cabecera de columnas -->
    <tr style="background:#007aff;color:white;font-weight:bold;text-align:center;">
        <td style="width:180px;">Sección</td>
        <td style="width:280px;">Pregunta</td>
        <td style="width:200px;">Opción / Tarjeta</td>
        <td style="width:80px;text-align:center;">👍 / Total</td>
        <td style="width:80px;text-align:center;">👎</td>
    </tr>

    <?php
    $tipoLabels = [
        'texto_corto'       => 'Texto corto',
        'texto_largo'       => 'Texto largo',
        'seleccion_unica'   => 'Selección única',
        'seleccion_multiple'=> 'Selección múltiple',
        'tinder'            => 'Card Slider',
        'satisfaccion'      => 'Satisfacción (1-5)',
        'matriz'            => 'Matriz importancia/satisfacción',
        'encabezado'        => 'Encabezado',
        'texto_ordinario'   => 'Informativo',
    ];
    foreach ($encabezados as $enc):
        $preguntas = $enc['preguntas'];
        if (empty($preguntas)) continue;
    ?>

    <!-- Fila de sección -->
    <tr style="background:#e8f0fe;">
        <td colspan="5" style="font-weight:bold;color:#1a56db;padding:8px 10px;">
            <?php echo htmlspecialchars($enc['titulo']); ?>
            <?php if (!empty($enc['descripcion'])): ?>
            — <span style="font-weight:normal;color:#555;"><?php echo htmlspecialchars($enc['descripcion']); ?></span>
            <?php endif; ?>
        </td>
    </tr>

    <?php foreach ($preguntas as $preg):
        $tipo  = $preg['tipo_pregunta'];
        $stats = $estadisticas[$preg['id']]['datos'] ?? [];
        $tl    = $tipoLabels[$tipo] ?? $tipo;
        $isTexto    = in_array($tipo, ['texto_corto', 'texto_largo']);
        $isTinder   = $tipo === 'tinder';
        $isSat      = $tipo === 'satisfaccion';
        $isStatic   = in_array($tipo, ['encabezado', 'texto_ordinario']);
    ?>

    <!-- Fila de pregunta -->
    <tr style="background:#f5f7ff;">
        <td style="color:#555;font-size:11px;"><?php echo htmlspecialchars($enc['titulo']); ?></td>
        <td style="font-weight:bold;">
            <?php echo htmlspecialchars($preg['pregunta']); ?>
            <span style="color:#888;font-weight:normal;font-size:10px;"> [<?php echo $tl; ?>]</span>
        </td>
        <td colspan="3" style="color:#aaa;font-size:11px;">
            <?php if ($isStatic): ?>Elemento visual<?php elseif (empty($stats)): ?>Sin respuestas<?php endif; ?>
        </td>
    </tr>

    <?php if ($isTinder && !empty($stats)): ?>
        <?php foreach ($stats as $card): ?>
        <tr>
            <td></td>
            <td style="color:#888;font-size:11px;padding-left:20px;">Tarjeta</td>
            <td><?php echo htmlspecialchars($card['titulo']); ?></td>
            <td style="text-align:center;color:#34c759;font-weight:bold;"><?php echo $card['yes']; ?></td>
            <td style="text-align:center;color:#ff3b30;font-weight:bold;"><?php echo $card['no']; ?></td>
        </tr>
        <?php endforeach; ?>

    <?php elseif (in_array($tipo, ['seleccion_unica', 'seleccion_multiple']) && !empty($stats)): ?>
        <?php foreach ($stats as $opt): ?>
        <tr>
            <td></td>
            <td style="color:#888;font-size:11px;padding-left:20px;">Opción</td>
            <td><?php echo htmlspecialchars($opt['respuesta']); ?></td>
            <td style="text-align:center;font-weight:bold;"><?php echo $opt['total']; ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>

    <?php elseif ($tipo === 'matriz' && !empty($stats)): ?>
        <?php foreach ($stats as $mi => $mf): ?>
        <tr>
            <td></td>
            <td style="color:#888;font-size:11px;padding-left:20px;">Aspecto <?php echo $mi + 1; ?></td>
            <td><?php echo htmlspecialchars($mf['aspecto']); ?></td>
            <td style="text-align:center;font-weight:bold;">Imp: <?php echo $mf['n'] ? $mf['prom_imp'] : '—'; ?></td>
            <td style="text-align:center;font-weight:bold;">Sat: <?php echo $mf['n'] ? $mf['prom_sat'] : '—'; ?></td>
        </tr>
        <?php endforeach; ?>

    <?php elseif ($isSat && !empty($stats)): ?>
        <?php
            $satEmojisXls = ['😞','😕','😐','🙂','😊'];
            $promXls = $estadisticas[$preg['id']]['promedio'] ?? 0;
            $totalXls = $estadisticas[$preg['id']]['total'] ?? 0;
        ?>
        <tr>
            <td></td>
            <td style="color:#888;font-size:11px;padding-left:20px;">Promedio</td>
            <td colspan="3" style="font-weight:bold;color:#007aff;"><?php echo $promXls; ?> / 5 &nbsp;(<?php echo $totalXls; ?> respuesta<?php echo $totalXls!=1?'s':''; ?>)</td>
        </tr>
        <?php foreach ($stats as $item): ?>
        <tr>
            <td></td>
            <td style="color:#888;font-size:11px;padding-left:20px;">Valor <?php echo $item['valor']; ?> <?php echo $satEmojisXls[$item['valor']-1]; ?></td>
            <td></td>
            <td style="text-align:center;font-weight:bold;"><?php echo $item['total']; ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>

    <?php elseif ($isTexto && !empty($stats)): ?>
        <?php foreach ($stats as $resp): ?>
        <tr>
            <td></td>
            <td style="color:#888;font-size:11px;padding-left:20px;">Respuesta</td>
            <td colspan="3" style="color:#333;"><?php echo htmlspecialchars($resp['respuesta']); ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php endforeach; ?>
    <?php endforeach; ?>

</table>
</body>
</html>
