<?php
/**
 * Retourne la liste de lecture du lecteur courant (session)
 */
require_once "db.php";
require_once "session_lecteur.php";
header("Content-Type: application/json; charset=utf-8");

if (empty($_SESSION['id_lecteur'])) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT l.id, l.titre, l.auteur, ll.date_emprunt, ll.date_retour
     FROM liste_lecture ll
     JOIN livres l ON l.id = ll.id_livre
     WHERE ll.id_lecteur = :lecteur
     ORDER BY ll.date_emprunt DESC"
);
$stmt->execute(["lecteur" => $_SESSION['id_lecteur']]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
