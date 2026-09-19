<?php
/**
 * Modifie un livre existant
 * Utilisé par manage.html (formulaire de modification)
 */
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);

$id             = isset($data['id']) ? (int) $data['id'] : 0;
$titre          = trim($data['titre'] ?? '');
$auteur         = trim($data['auteur'] ?? '');
$description    = trim($data['description'] ?? '');
$maison_edition = trim($data['maison_edition'] ?? '');
$nb_exemplaire  = isset($data['nombre_exemplaire']) ? (int) $data['nombre_exemplaire'] : 0;

if ($id <= 0 || $titre === '' || $auteur === '') {
    http_response_code(400);
    echo json_encode(["error" => "Données invalides : id, titre et auteur sont obligatoires."]);
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE livres
     SET titre = :titre, auteur = :auteur, description = :description,
         maison_edition = :maison_edition, nombre_exemplaire = :nb
     WHERE id = :id"
);
$stmt->execute([
    "titre" => $titre,
    "auteur" => $auteur,
    "description" => $description,
    "maison_edition" => $maison_edition,
    "nb" => $nb_exemplaire,
    "id" => $id
]);

echo json_encode(["success" => true]);
