<?php
/**
 * Supprime un livre de la collection
 * Utilisé par manage.html
 */
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Identifiant invalide."]);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM livres WHERE id = :id");
$stmt->execute(["id" => $id]);

echo json_encode(["success" => true]);
