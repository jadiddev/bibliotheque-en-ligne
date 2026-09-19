# Bibliothèque en Ligne

Site web interactif pour une bibliothèque en ligne — HTML, CSS, JavaScript, PHP, MySQL.

## Installation (WAMP / XAMPP / MAMP)

1. Copiez le dossier `bibliotheque/` dans le répertoire web de votre serveur local
   (ex. `C:\wamp64\www\` ou `htdocs/` pour XAMPP).
2. Démarrez Apache et MySQL.
3. Ouvrez **phpMyAdmin**, créez la base en important le fichier `sql/database.sql`
   (onglet "Importer" ou copier-coller le contenu dans l'onglet SQL).
4. Vérifiez les identifiants de connexion dans `php/db.php` (`$user`, `$pass`)
   selon votre configuration.
5. Ouvrez le site dans votre navigateur : `http://localhost/bibliotheque/index.html`

## Structure du projet

```
bibliotheque/
├── index.html            Page d'accueil + recherche
├── results.html           Résultats de recherche
├── details.html            Détails d'un livre
├── wishlist.html           Liste de lecture du lecteur
├── manage.html             Ajout / modification / suppression de livres
├── css/style.css          Styles (responsive, mobile-first)
├── js/script.js            Logique JS (appels AJAX vers php/)
├── php/
│   ├── db.php                Connexion PDO à MySQL
│   ├── session_lecteur.php   Identification simplifiée du lecteur
│   ├── search.php            Recherche (titre/auteur)
│   ├── book_details.php      Détails d'un livre
│   ├── list_books.php        Liste complète (page de gestion)
│   ├── add_book.php          Créer un livre
│   ├── update_book.php       Modifier un livre
│   ├── delete_book.php       Supprimer un livre
│   ├── add_to_wishlist.php   Ajouter à la liste de lecture
│   ├── remove_from_wishlist.php
│   └── get_wishlist.php
└── sql/database.sql        Script de création de la base + données de démo
```

## Fonctionnalités

- Recherche de livres par titre ou auteur.
- Affichage des résultats et des détails d'un livre.
- Ajout, modification et suppression de livres (CRUD complet, page `manage.html`).
- Liste de lecture personnelle par lecteur (identifié par email, sans mot de
  passe — hors périmètre du projet), avec ajout/retrait de livres.
- Interface responsive (mobile / tablette / desktop).

## Notes techniques

- Les échanges entre le JavaScript et le serveur se font en AJAX (`fetch`) au
  format JSON.
- Toutes les requêtes SQL utilisent des requêtes préparées (PDO) afin de
  prévenir les injections SQL.
- Le texte inséré dynamiquement dans le DOM est échappé (`escapeHtml`) afin de
  prévenir les failles XSS basiques.
