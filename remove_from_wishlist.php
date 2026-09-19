<?php
/**
 * Retire un livre de la liste de lecture d'un lecteur
 */
require_once "db.php";
require_once "session_lecteur.php";
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);
$id_livre = isset($data['id_livre']) ? (int) $data['id_livre'] : 0;

if (empty($_SESSION['id_lecteur']) || $id_livre <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Requête invalide."]);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM liste_lecture WHERE id_livre = :livre AND id_lecteur = :lecteur");
$stmt->execute(["livre" => $id_livre, "lecteur" => $_SESSION['id_lecteur']]);

echo json_encode(["success" => true]);
