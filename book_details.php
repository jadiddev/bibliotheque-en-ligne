<?php
/**
 * Retourne les détails d'un livre à partir de son id
 * Utilisé par details.html
 */
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Identifiant de livre invalide."]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = :id");
$stmt->execute(["id" => $id]);
$livre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$livre) {
    http_response_code(404);
    echo json_encode(["error" => "Livre introuvable."]);
    exit;
}

echo json_encode($livre, JSON_UNESCAPED_UNICODE);
