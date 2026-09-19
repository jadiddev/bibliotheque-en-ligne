<?php
/**
 * Connexion à la base de données MySQL (PDO)
 * Projet : Bibliothèque en Ligne
 */

$host   = "localhost";
$dbname = "bibliotheque_en_ligne";
$user   = "root";      // à adapter selon votre configuration WAMP/XAMPP
$pass   = "";           // à adapter selon votre configuration WAMP/XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "Erreur de connexion à la base de données : " . $e->getMessage()]));
}
