<?php
/**
 * Gestion simplifiée du "lecteur" courant.
 *
 * Ce projet n'implémente pas de système d'authentification complet
 * (hors périmètre de l'énoncé). Pour permettre la fonctionnalité
 * "liste de lecture", on identifie le lecteur via un identifiant
 * stocké en session PHP, créé automatiquement à partir d'un email
 * saisi une seule fois par le visiteur (voir js/script.js).
 */
session_start();

function get_or_create_lecteur(PDO $pdo, string $nom, string $prenom, string $email): int
{
    $stmt = $pdo->prepare("SELECT id FROM lecteurs WHERE email = :email");
    $stmt->execute(["email" => $email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return (int) $row['id'];
    }

    $stmt = $pdo->prepare("INSERT INTO lecteurs (nom, prenom, email) VALUES (:nom, :prenom, :email)");
    $stmt->execute(["nom" => $nom, "prenom" => $prenom, "email" => $email]);
    return (int) $pdo->lastInsertId();
}
