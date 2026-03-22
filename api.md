# Dérailleur API — Documentation d'intégration

Base URL: `https://derailleur.ffgva.ch`

CORS: les requêtes cross-origin depuis `https://ffgva.ch` et `https://www.ffgva.ch` sont autorisées.

---

## GET /api/events

Liste des événements publiés à venir (aujourd'hui et futur).

**Auth** : aucune

**Réponse** : `200 OK` — `application/json`

```json
[
  {
    "id": 1,
    "title": "Sortie Salève",
    "description": "<p>Description en HTML</p>",
    "starts_at": "2026-04-05T08:00:00+00:00",
    "ends_at": "2026-04-05T12:00:00+00:00",
    "location": "Veyrier",
    "price": "10.00",
    "price_non_member": "25.00",
    "max_participants": 20
  }
]
```

| Champ | Type | Description |
|-------|------|-------------|
| id | integer | Identifiant unique de l'événement |
| title | string | Titre |
| description | string\|null | Description en HTML (depuis l'éditeur riche) |
| starts_at | string (ISO 8601) | Date/heure de début |
| ends_at | string\|null (ISO 8601) | Date/heure de fin |
| location | string\|null | Lieu / point de rendez-vous |
| price | string | Prix membre en CHF (ex: "10.00", "0.00") |
| price_non_member | string\|null | Prix non-membre en CHF (null = même prix que membre) |
| max_participants | integer\|null | Nombre max de participantes (null = illimité) |

**Notes** :
- Les événements sont triés par date de début croissante
- Seuls les événements avec statut « Publié » sont retournés
- Les événements passés ne sont pas inclus
- `description` peut contenir du HTML (balises `<p>`, `<strong>`, `<em>`, `<ul>`, `<li>`, etc.)

---

## POST /api/inscription-event

Inscription d'une personne à un événement. Déclenche l'envoi d'un e-mail de confirmation.

**Auth** : aucune

**Rate limiting** : oui (anti-spam)

**Content-Type** : `application/json`

**Requête** :

```json
{
  "email": "participant@example.com",
  "event_id": 1,
  "website": ""
}
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-:|-------------|
| email | string | ✓ | Adresse e-mail du participant |
| event_id | integer | ✓ | ID de l'événement (provient de GET /api/events) |
| website | string | ✗ | Champ honeypot anti-spam — **doit être vide** |

**Réponse** : `200 OK`

```json
{
  "ok": true
}
```

La réponse est toujours `{"ok": true}` pour éviter l'énumération d'adresses e-mail. Aucune information sur le résultat n'est renvoyée.

**Comportement** :

| Situation | Action |
|-----------|--------|
| Email connu (membre existante) | E-mail avec lien de confirmation (expire 1h). Le clic inscrit automatiquement et connecte au portail. |
| Email inconnu | E-mail avec lien vers un formulaire d'inscription (expire 24h). Après soumission : création du compte, inscription, connexion au portail. |
| Événement inexistant ou non publié | Aucun e-mail envoyé |
| Événement complet | Aucun e-mail envoyé |
| Champ `website` rempli (bot) | Aucun e-mail envoyé |

**Tarification** :
- Membres actives → `price`
- Non-membres → `price_non_member` (ou `price` si `price_non_member` est null)

---

## POST /api/contact

Formulaire de contact.

**Auth** : aucune

**Rate limiting** : oui

**Requête** :

```json
{
  "name": "Marie Dupont",
  "email": "marie@example.com",
  "message": "Bonjour, je souhaite...",
  "website": ""
}
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-:|-------------|
| name | string | ✓ | Nom |
| email | string | ✓ | Adresse e-mail |
| message | string | ✓ | Message |
| website | string | ✗ | Honeypot — doit être vide |

**Réponse** : `{"ok": true}`

---

## POST /api/adhesion

Formulaire d'adhésion au club.

**Auth** : aucune

**Rate limiting** : oui

**Requête** :

```json
{
  "prenom": "Marie",
  "nom": "Dupont",
  "email": "marie@example.com",
  "telephone": "079 123 45 67",
  "type_velo": "Route",
  "sorties": "Weekend",
  "atelier": "Mécanique",
  "instagram": "@marie",
  "strava": "marie_dupont",
  "photo_ok": "oui",
  "statuts_ok": "oui",
  "cotisation_ok": "oui",
  "website": ""
}
```

| Champ | Type | Obligatoire | Description |
|-------|------|:-:|-------------|
| prenom | string | ✓ | Prénom |
| nom | string | ✓ | Nom |
| email | string | ✓ | Adresse e-mail |
| telephone | string | ✓ | Numéro de téléphone |
| type_velo | string | ✗ | Type de vélo |
| sorties | string | ✗ | Préférence de sorties |
| atelier | string | ✗ | Intérêt pour les ateliers |
| instagram | string | ✗ | Compte Instagram |
| strava | string | ✗ | Compte Strava |
| photo_ok | string | ✗ | Consentement photo ("oui"/"non") |
| statuts_ok | string | ✗ | Acceptation des statuts |
| cotisation_ok | string | ✗ | Acceptation de la cotisation |
| website | string | ✗ | Honeypot — doit être vide |

**Réponse** : `{"ok": true}`

**Flux** : un e-mail de bienvenue avec lien d'activation est envoyé. Après confirmation, une facture de cotisation est générée et envoyée.

---

## GET /events/ical

Flux iCal (calendrier) de tous les événements publiés et terminés.

**Auth** : aucune

**Réponse** : `text/calendar; charset=UTF-8`

**Contenu** : tous les événements avec statut Publié ou Terminé, à partir de 1 an en arrière. Format iCalendar standard (RFC 5545).

**URL d'abonnement** : `https://derailleur.ffgva.ch/events/ical`

Compatible Google Calendar, Apple Calendar, Outlook.

---

---

## Flux : Inscription à un événement

Intégration côté Hugo pour permettre à un visiteur de s'inscrire à un événement.

### Étape 1 — Afficher les événements

```javascript
fetch('https://derailleur.ffgva.ch/api/events')
  .then(r => r.json())
  .then(events => {
    // events = tableau d'événements publiés à venir
    // Afficher chaque événement avec titre, date, lieu, prix
  });
```

### Étape 2 — Formulaire d'inscription

Pour chaque événement, proposer un formulaire avec un champ e-mail + l'`id` de l'événement :

```html
<form id="inscription-form">
  <input type="email" name="email" required placeholder="Ton e-mail">
  <input type="hidden" name="event_id" value="EVENT_ID">
  <input type="text" name="website" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off">
  <button type="submit">Je m'inscris</button>
</form>
```

### Étape 3 — Envoyer la requête

```javascript
fetch('https://derailleur.ffgva.ch/api/inscription-event', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: form.email.value,
    event_id: form.event_id.value,
    website: form.website.value
  })
})
.then(() => {
  // Toujours afficher un message de succès :
  // "Un e-mail de confirmation t'a été envoyé."
});
```

### Étape 4 — L'utilisateur reçoit un e-mail

- **Membre connue** : e-mail avec bouton « Confirmer mon inscription ». Le clic inscrit automatiquement et connecte au portail membre (`derailleur.ffgva.ch/portail`).
- **Nouvelle personne** : e-mail avec bouton « M'inscrire ». Le clic ouvre un formulaire (prénom, nom, e-mail, téléphone, WhatsApp). Après soumission → compte créé, inscription confirmée, connexion au portail.

Si l'événement est payant, une facture avec QR de paiement est envoyée par e-mail.

---

## Flux : Adhésion au club

### Étape 1 — Formulaire d'adhésion

Soumettre `POST /api/adhesion` avec les champs requis (prénom, nom, email, téléphone) + les champs optionnels.

### Étape 2 — E-mail de bienvenue

La candidate reçoit un e-mail avec un bouton d'activation.

### Étape 3 — Activation

Le clic sur le lien d'activation :
1. Vérifie l'adresse e-mail
2. Génère une facture de cotisation annuelle (PDF avec QR de paiement)
3. Envoie la facture par e-mail

### Étape 4 — Paiement

L'administratrice valide manuellement le paiement. Le statut passe de « En attente » à « Active » et un numéro de membre est attribué.

---

## Erreurs

Les endpoints POST renvoient `422 Unprocessable Entity` en cas d'erreur de validation :

```json
{
  "message": "The email field is required.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

---

## Anti-spam

Tous les endpoints POST utilisent :
- **Honeypot** : champ `website` invisible dans le formulaire. Si rempli (par un bot), la requête est ignorée silencieusement.
- **Rate limiting** : nombre de requêtes limité par IP.

**Implémentation côté Hugo** :

```html
<!-- Champ honeypot — masquer en CSS, ne pas utiliser display:none (les bots le détectent) -->
<input type="text" name="website" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off">
```
