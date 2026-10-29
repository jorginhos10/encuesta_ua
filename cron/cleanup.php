<?php
/**
 * Limpieza automática de duplicados — encuestas
 * Se ejecuta vía Windows Task Scheduler cada 30 minutos.
 */
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

$log = [];
$ts  = date('Y-m-d H:i:s');

// ── 1. DUPLICADOS DE FILAS (mismo sesión + misma pregunta, múltiples rows) ──
try {
    $stmt = $db->query("SELECT DISTINCT encuesta_id FROM respuestas_encuesta");
    $encuestas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $totalFilas = 0;
    foreach ($encuestas as $eid) {
        $del = $db->prepare("
            DELETE r FROM respuestas_encuesta r
            INNER JOIN respuestas_encuesta r2
              ON  r.encuesta_id  = r2.encuesta_id
              AND r.pregunta_id  = r2.pregunta_id
              AND r.ip_address   = r2.ip_address
              AND COALESCE(r.usuario_respondio,'') = COALESCE(r2.usuario_respondio,'')
              AND DATE_FORMAT(r.created_at,'%Y-%m-%d %H:%i:%s')
                = DATE_FORMAT(r2.created_at,'%Y-%m-%d %H:%i:%s')
              AND r.id > r2.id
            WHERE r.encuesta_id = :eid
        ");
        $del->execute([':eid' => $eid]);
        $totalFilas += $del->rowCount();
    }
    $log[] = "[OK] Filas duplicadas eliminadas: $totalFilas";
} catch (Exception $e) {
    $log[] = "[ERR] Filas: " . $e->getMessage();
}

// ── 2. DUPLICADOS DENTRO DEL JSON TINDER (mismo título, varias entradas) ──
try {
    $stmt = $db->query("SELECT id, respuesta FROM respuestas_encuesta WHERE respuesta LIKE '%titulo%'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updatedJson = 0;
    $removedEntries = 0;

    foreach ($rows as $row) {
        $parsed = json_decode($row['respuesta'], true);
        if (!is_array($parsed)) continue;

        $seen    = [];
        $deduped = [];
        foreach ($parsed as $item) {
            if (!isset($item['titulo'])) { $deduped[] = $item; continue; }
            $key = mb_strtolower(trim($item['titulo']), 'UTF-8');
            if (isset($seen[$key])) { $removedEntries++; continue; }
            $seen[$key] = true;
            $deduped[]  = $item;
        }

        if (count($deduped) !== count($parsed)) {
            $upd = $db->prepare("UPDATE respuestas_encuesta SET respuesta = :r WHERE id = :id");
            $upd->execute([':r' => json_encode($deduped, JSON_UNESCAPED_UNICODE), ':id' => $row['id']]);
            $updatedJson++;
        }
    }
    $log[] = "[OK] Respuestas JSON corregidas: $updatedJson (entradas quitadas: $removedEntries)";
} catch (Exception $e) {
    $log[] = "[ERR] JSON: " . $e->getMessage();
}

// ── LOG ──
$logLine = "[$ts] " . implode(' | ', $log) . PHP_EOL;
$logFile = __DIR__ . '/cleanup.log';

// Mantener solo las últimas 500 líneas
if (file_exists($logFile)) {
    $lines = file($logFile);
    if (count($lines) > 500) {
        file_put_contents($logFile, implode('', array_slice($lines, -499)));
    }
}
file_put_contents($logFile, $logLine, FILE_APPEND);
echo $logLine;
