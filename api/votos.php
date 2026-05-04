<?php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/includes/db.php';

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body      = json_decode(file_get_contents('php://input'), true);
    $juego_id  = (int)($body['juego_id'] ?? 0);
    $puntuacion = (int)($body['puntuacion'] ?? 0);

    if (!$juego_id || $puntuacion < 1 || $puntuacion > 10) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']); exit;
    }

    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

    // Una votación por IP y juego
    $existe = $db->prepare("SELECT id FROM votos WHERE juego_id = ? AND ip = ?");
    $existe->execute([$juego_id, $ip]);
    if ($existe->fetch()) {
        // Actualizar voto existente
        $db->prepare("UPDATE votos SET puntuacion = ? WHERE juego_id = ? AND ip = ?")
           ->execute([$puntuacion, $juego_id, $ip]);
    } else {
        $db->prepare("INSERT INTO votos (juego_id, puntuacion, ip) VALUES (?, ?, ?)")
           ->execute([$juego_id, $puntuacion, $ip]);
    }
    echo json_encode(['ok' => true]); exit;
}

// GET — obtener media y total
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $juego_id = (int)($_GET['juego_id'] ?? 0);
    if (!$juego_id) {
        http_response_code(400);
        echo json_encode(['error' => 'juego_id requerido']); exit;
    }
    $res = $db->prepare("SELECT AVG(puntuacion) as media, COUNT(*) as total FROM votos WHERE juego_id = ?");
    $res->execute([$juego_id]);
    $row = $res->fetch();
    echo json_encode([
        'media' => $row['total'] > 0 ? number_format((float)$row['media'], 1) : null,
        'total' => (int)$row['total'],
    ]); exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);
