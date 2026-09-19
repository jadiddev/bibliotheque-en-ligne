<?php
/**
 * Recherche de livres par titre ou auteur
 * Utilisé par results.html (appel AJAX ou lien direct)
 */
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '') {
    // Aucune recherche : retourne tous les livres
    $stmt = $pdo->query("SELECT id, titre, auteur, nombre_exemplaire FROM livres ORDER BY titre");
} else {
    $stmt = $pdo->prepare(
        "SELECT id, titre, auteur, nombre_exemplaire
         FROM livres
         WHERE titre LIKE :q OR auteur LIKE :q
         ORDER BY titre"
    );
    $stmt->execute(["q" => "%$q%"]);
}

$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($livres, JSON_UNESCAPED_UNICODE);
