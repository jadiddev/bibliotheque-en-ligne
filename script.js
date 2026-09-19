/* =====================================================================
   Bibliothèque en Ligne — script.js
   Gère : recherche, affichage résultats, détails, liste de lecture,
          et la gestion (ajout/modification/suppression) des livres.
   ===================================================================== */

const API = "php/";

/* --------------------------- Utilitaires --------------------------- */
function qs(param) {
  return new URLSearchParams(window.location.search).get(param);
}

function el(html) {
  const tpl = document.createElement("template");
  tpl.innerHTML = html.trim();
  return tpl.content.firstElementChild;
}

async function apiGet(endpoint) {
  const res = await fetch(API + endpoint);
  return res.json();
}

async function apiPost(endpoint, data) {
  const res = await fetch(API + endpoint, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  });
  return res.json();
}

/* --------------------------- index.html --------------------------- */
function initHomeSearch() {
  const form = document.getElementById("search-form");
  if (!form) return;
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const q = document.getElementById("search-input").value.trim();
    window.location.href = "results.html?q=" + encodeURIComponent(q);
  });
}

/* ------------------------- results.html ---------------------------- */
async function initResultsPage() {
  const grid = document.getElementById("results-grid");
  if (!grid) return;

  const q = qs("q") || "";
  document.getElementById("search-input").value = q;
  document.getElementById("query-label").textContent = q
    ? `Résultats pour « ${q} »`
    : "Tous les livres";

  const form = document.getElementById("search-form");
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const newQ = document.getElementById("search-input").value.trim();
    window.location.href = "results.html?q=" + encodeURIComponent(newQ);
  });

  const livres = await apiGet("search.php?q=" + encodeURIComponent(q));
  grid.innerHTML = "";

  if (!Array.isArray(livres) || livres.length === 0) {
    grid.appendChild(el(`<div class="empty-state">Aucun livre ne correspond à votre recherche.</div>`));
    return;
  }

  livres.forEach((livre) => {
    grid.appendChild(el(`
      <div class="book-card">
        <h3>${escapeHtml(livre.titre)}</h3>
        <div class="author">${escapeHtml(livre.auteur)}</div>
        <div class="stock">${livre.nombre_exemplaire} exemplaire(s) disponible(s)</div>
        <div class="actions">
          <a class="btn btn-primary" href="details.html?id=${livre.id}">Voir les détails</a>
        </div>
      </div>
    `));
  });
}

/* ------------------------- details.html ------------------------------ */
async function initDetailsPage() {
  const container = document.getElementById("details-container");
  if (!container) return;

  const id = qs("id");
  if (!id) {
    container.appendChild(el(`<div class="alert alert-error">Aucun livre spécifié.</div>`));
    return;
  }

  const livre = await apiGet("book_details.php?id=" + encodeURIComponent(id));
  if (livre.error) {
    container.appendChild(el(`<div class="alert alert-error">${escapeHtml(livre.error)}</div>`));
    return;
  }

  container.appendChild(el(`
    <div class="book-details">
      <h2>${escapeHtml(livre.titre)}</h2>
      <dl>
        <dt>Auteur</dt><dd>${escapeHtml(livre.auteur)}</dd>
        <dt>Maison d'édition</dt><dd>${escapeHtml(livre.maison_edition || "—")}</dd>
        <dt>Exemplaires disponibles</dt><dd>${livre.nombre_exemplaire}</dd>
        <dt>Description</dt><dd>${escapeHtml(livre.description || "Aucune description disponible.")}</dd>
      </dl>
      <button id="add-wishlist-btn" class="btn btn-primary">Ajouter à ma liste de lecture</button>
      <div id="wishlist-feedback"></div>
    </div>
  `));

  document.getElementById("add-wishlist-btn").addEventListener("click", () => {
    openReaderModal(livre.id);
  });
}

/* Simple modal-less prompt to capture reader identity the first time */
function openReaderModal(idLivre) {
  let email = localStorage.getItem("lecteur_email");
  let nom = localStorage.getItem("lecteur_nom");
  let prenom = localStorage.getItem("lecteur_prenom");

  if (!email) {
    prenom = prompt("Votre prénom :", prenom || "");
    nom = prompt("Votre nom :", nom || "");
    email = prompt("Votre email (sert d'identifiant) :", "");
    if (!email) return;
    localStorage.setItem("lecteur_email", email);
    localStorage.setItem("lecteur_nom", nom || "");
    localStorage.setItem("lecteur_prenom", prenom || "");
  }

  apiPost("add_to_wishlist.php", { id_livre: idLivre, nom, prenom, email }).then((res) => {
    const feedback = document.getElementById("wishlist-feedback");
    if (res.success) {
      feedback.innerHTML = `<div class="alert alert-success">Livre ajouté à votre liste de lecture !</div>`;
    } else {
      feedback.innerHTML = `<div class="alert alert-error">${escapeHtml(res.error || "Erreur inconnue")}</div>`;
    }
  });
}

/* ------------------------- wishlist.html ------------------------------ */
async function initWishlistPage() {
  const list = document.getElementById("wishlist-list");
  if (!list) return;

  const livres = await apiGet("get_wishlist.php");
  list.innerHTML = "";

  if (!Array.isArray(livres) || livres.length === 0) {
    list.appendChild(el(`<div class="empty-state">Votre liste de lecture est vide pour le moment.<br>
      Ajoutez des livres depuis leur page de détails.</div>`));
    return;
  }

  livres.forEach((livre) => {
    const card = el(`
      <div class="book-card">
        <h3>${escapeHtml(livre.titre)}</h3>
        <div class="author">${escapeHtml(livre.auteur)}</div>
        <div class="stock">Emprunté le ${livre.date_emprunt}${livre.date_retour ? " · Retourné le " + livre.date_retour : ""}</div>
        <div class="actions">
          <a class="btn btn-secondary" href="details.html?id=${livre.id}">Voir les détails</a>
          <button class="btn btn-danger remove-btn" data-id="${livre.id}">Retirer</button>
        </div>
      </div>
    `);
    card.querySelector(".remove-btn").addEventListener("click", async () => {
      await apiPost("remove_from_wishlist.php", { id_livre: livre.id });
      initWishlistPage();
    });
    list.appendChild(card);
  });
}

/* ------------------------- manage.html (CRUD) ------------------------- */
async function initManagePage() {
  const table = document.getElementById("manage-table-body");
  if (!table) return;

  const form = document.getElementById("book-form");
  const formTitle = document.getElementById("form-title");
  const cancelBtn = document.getElementById("cancel-edit");

  async function loadBooks() {
    const livres = await apiGet("list_books.php");
    table.innerHTML = "";
    livres.forEach((livre) => {
      const row = el(`
        <tr>
          <td>${escapeHtml(livre.titre)}</td>
          <td>${escapeHtml(livre.auteur)}</td>
          <td>${livre.nombre_exemplaire}</td>
          <td>
            <button class="btn btn-secondary edit-btn">Modifier</button>
            <button class="btn btn-danger delete-btn">Supprimer</button>
          </td>
        </tr>
      `);
      row.querySelector(".edit-btn").addEventListener("click", () => fillForm(livre));
      row.querySelector(".delete-btn").addEventListener("click", async () => {
        if (confirm(`Supprimer « ${livre.titre} » ?`)) {
          await apiPost("delete_book.php", { id: livre.id });
          loadBooks();
        }
      });
      table.appendChild(row);
    });
  }

  function fillForm(livre) {
    form.id.value = livre.id;
    form.titre.value = livre.titre;
    form.auteur.value = livre.auteur;
    form.description.value = livre.description || "";
    form.maison_edition.value = livre.maison_edition || "";
    form.nombre_exemplaire.value = livre.nombre_exemplaire;
    formTitle.textContent = "Modifier le livre";
    cancelBtn.style.display = "inline-block";
  }

  function resetForm() {
    form.reset();
    form.id.value = "";
    formTitle.textContent = "Ajouter un livre";
    cancelBtn.style.display = "none";
  }

  cancelBtn.addEventListener("click", resetForm);

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const payload = {
      id: form.id.value,
      titre: form.titre.value.trim(),
      auteur: form.auteur.value.trim(),
      description: form.description.value.trim(),
      maison_edition: form.maison_edition.value.trim(),
      nombre_exemplaire: form.nombre_exemplaire.value || 0
    };

    const endpoint = payload.id ? "update_book.php" : "add_book.php";
    const res = await apiPost(endpoint, payload);

    if (res.success) {
      resetForm();
      loadBooks();
    } else {
      alert(res.error || "Une erreur est survenue.");
    }
  });

  loadBooks();
}

/* --------------------------- Helpers --------------------------- */
function escapeHtml(str) {
  if (str === null || str === undefined) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

/* --------------------------- Init --------------------------- */
document.addEventListener("DOMContentLoaded", () => {
  initHomeSearch();
  initResultsPage();
  initDetailsPage();
  initWishlistPage();
  initManagePage();
});
