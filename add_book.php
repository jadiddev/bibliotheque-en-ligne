<?php
/**
 * Ajoute un nouveau livre dans la collection
 * Utilisé par manage.html (formulaire d'ajout)
 */
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);

$titre          = trim($data['titre'] ?? '');
$auteur         = trim($data['auteur'] ?? '');
$description    = trim($data['description'] ?? '');
$maison_edition = trim($data['maison_edition'] ?? '');
$nb_exemplaire  = isset($data['nombre_exemplaire']) ? (int) $data['nombre_exemplaire'] : 0;

if ($titre === '' || $auteur === '') {
    http_response_code(400);
    echo json_encode(["error" => "Le titre et l'auteur sont obligatoires."]);
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO livres (titre, auteur, description, maison_edition, nombre_exemplaire)
     VALUES (:titre, :auteur, :description, :maison_edition, :nb)"
);
$stmt->execute([
    "titre" => $titre,
    "auteur" => $auteur,
    "description" => $description,
    "maison_edition" => $maison_edition,
    "nb" => $nb_exemplaire
]);

echo json_encode(["success" => true, "id" => $pdo->lastInsertId()]);
