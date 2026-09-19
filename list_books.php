<?php
/**
 * Retourne la liste complète des livres (tous les champs)
 * Utilisé par manage.html
 */
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$stmt = $pdo->query("SELECT * FROM livres ORDER BY titre");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
