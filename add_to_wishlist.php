<?php
/**
 * Ajoute un livre à la liste de lecture d'un lecteur
 */
require_once "db.php";
require_once "session_lecteur.php";
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);

$id_livre = isset($data['id_livre']) ? (int) $data['id_livre'] : 0;
$nom      = trim($data['nom'] ?? 'Invité');
$prenom   = trim($data['prenom'] ?? 'Invité');
$email    = trim($data['email'] ?? '');

if ($id_livre <= 0 || $email === '') {
    http_response_code(400);
    echo json_encode(["error" => "Livre et email du lecteur requis."]);
    exit;
}

$id_lecteur = get_or_create_lecteur($pdo, $nom, $prenom, $email);
$_SESSION['id_lecteur'] = $id_lecteur;
$_SESSION['email']      = $email;

$stmt = $pdo->prepare(
    "INSERT IGNORE INTO liste_lecture (id_livre, id_lecteur, date_emprunt)
     VALUES (:livre, :lecteur, CURDATE())"
);
$stmt->execute(["livre" => $id_livre, "lecteur" => $id_lecteur]);

echo json_encode(["success" => true]);
