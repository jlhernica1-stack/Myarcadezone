<?php
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/includes/db.php';

$db = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body     = json_decode(file_get_contents('php://input'), true);
    $juego_id = (int)($body['juego_id'] ?? 0);
    $nombre   = trim(strip_tags($body['nombre'] ?? ''));
    $texto    = trim(strip_tags($body['texto'] ?? ''));

    if (!$juego_id || strlen($nombre) < 1 || strlen($texto) < 3) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']); exit;
    }
    if (strlen($nombre) > 100) $nombre = mb_substr($nombre, 0, 100);
    if (strlen($texto)  > 500) $texto  = mb_substr($texto, 0, 500);

    $db->prepare("INSERT INTO comentarios (juego_id, nombre, texto) VALUES (?, ?, ?)")
       ->execute([$juego_id, strtoupper($nombre), $texto]);

    echo json_encode(['ok' => true]); exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);
