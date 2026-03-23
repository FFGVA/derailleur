# Strava API — Analyse d'intégration pour Dérailleur

**Date**: 23.03.2026
**Statut**: Analyse de faisabilité

## 1. Modèle d'authentification

Strava utilise **OAuth 2.0** avec consentement par athlète. Chaque membre souhaitant l'intégration doit :
1. Se connecter à Strava et autoriser l'application Dérailleur
2. On stocke leur `refresh_token` (longue durée) et `access_token` (expire toutes les 6 heures)

**Conséquence** : pas d'accès en masse. Chaque fonctionnalité ci-dessous ne fonctionne que pour les membres ayant lié leur compte Strava. Il faut un bouton "Connecter Strava" dans le profil membre.

**Limites de requêtes** : 200 requêtes / 15 min, 2'000 / jour par application. Avec ~50 membres actifs, c'est gérable.

## 2. Fonctionnalités retenues

### A. Synchronisation des événements ★★★

Strava dispose d'une API pour les événements de groupe des clubs :

| Endpoint | Fonction |
|---|---|
| `GET /clubs/{id}/group_events` | Lister tous les événements du club |
| `GET /group_events/{id}` | Détails d'un événement |
| `POST /group_events/{id}/rsvps` | S'inscrire (RSVP) |
| `DELETE /group_events/{id}/rsvps` | Se désinscrire |
| `DELETE /group_events/{id}` | Supprimer un événement |
| `GET /group_events/{id}/athletes` | Lister les inscrit·e·s |

**Ce qu'on peut faire :**
- **Sync inscriptions Strava → Dérailleur** (pull) : interroger `GET /group_events/{id}/athletes` pour voir qui s'est inscrit sur Strava, et auto-inscrire dans la table `event_member`.
- **Événements récurrents** supportés (hebdomadaire/mensuel), ce qui correspond aux sorties régulières.

**Limitation majeure** : il n'y a **pas d'endpoint de création d'événement** dans l'API publique — uniquement lister, rejoindre, quitter, supprimer. Les événements doivent être créés manuellement sur Strava.

**Approche réaliste** : créer les événements dans Dérailleur (source de vérité), les dupliquer manuellement sur Strava, puis lier les deux et synchroniser les inscriptions.

#### Identification des événements Strava dans Dérailleur

Pour lier un événement Dérailleur à son équivalent Strava, il faut stocker l'identifiant Strava de l'événement. Deux approches :

**Approche 1 — Liaison manuelle (recommandée pour commencer)**

Ajouter un champ `strava_event_id` (BIGINT, nullable) sur la table `events`. Quand l'admin crée un événement dans Dérailleur puis le crée aussi sur Strava, il saisit l'ID Strava dans le formulaire d'édition de l'événement (ou colle l'URL Strava, dont on extrait l'ID).

Workflow :
1. Admin crée l'événement dans Dérailleur
2. Admin crée l'événement manuellement sur Strava
3. Admin copie l'URL Strava (ex: `https://www.strava.com/clubs/ffgva/group_events/12345`) dans le champ "Événement Strava" du formulaire Dérailleur
4. Dérailleur extrait l'ID `12345` et le stocke dans `events.strava_event_id`
5. Un job cron interroge périodiquement `GET /group_events/{strava_event_id}/athletes` pour synchroniser les inscriptions

**Approche 2 — Matching automatique par titre + date**

Un job cron interroge `GET /clubs/{club_id}/group_events` et tente de matcher avec les événements Dérailleur par :
- Proximité de date/heure de début (±30 min)
- Similarité du titre (fuzzy matching)

Quand un match est trouvé avec un score de confiance suffisant, le `strava_event_id` est stocké automatiquement. Les cas ambigus sont signalés à l'admin pour validation manuelle.

**Recommandation** : commencer par l'approche 1 (simple, fiable) et évaluer si l'approche 2 apporte une valeur ajoutée suffisante.

#### Champs de réponse — Représentation résumée

| Champ | Type | Description |
|---|---|---|
| `id` | integer | Identifiant de l'événement |
| `title` | string | Nom de l'événement |
| `description` | string | Description |
| `club` | object | Résumé du club |
| `organizing_athlete` | object | Résumé de l'athlète organisateur ; nullable |
| `activity_type` | string | Type d'activité (ride, run, swim, etc.) |
| `created_at` | string | Timestamp de création |
| `route` | object | Représentation du parcours ; nullable |
| `start_latlng` | [lat, lng] | Coordonnées de départ ; nullable |
| `woman_only` | bool | Réservé aux femmes |
| `private` | bool | Réservé aux membres du club |
| `skill_levels` | integer | 1=casual, 2=tempo, 4=hammerfest |
| `terrain` | integer | 0=plat, 1=vallonné, 2=grimpées |
| `upcoming_occurrences` | array | Jusqu'à 5 timestamps UTC |
| `zone` | string | Fuseau horaire |
| `address` | string | Adresse ; nullable |
| `joined` | bool | Statut de participation de l'athlète authentifié |

#### Champs supplémentaires — Représentation détaillée

| Champ | Type | Description |
|---|---|---|
| `viewer_permissions` | object | `{"edit": bool}` |
| `start_datetime` | string | Format local : `yyyy-mm-ddThh:mm` |
| `frequency` | string | `no_repeat`, `weekly`, `monthly` |
| `day_of_week` | string | Pour événements mensuels uniquement |
| `week_of_month` | integer | Pour mensuels : -1, 1, 2, 3, 4 |
| `days_of_week` | array | Pour hebdomadaires : noms des jours |
| `weekly_interval` | integer | Pour hebdomadaires : toutes les x semaines (1-4) |

### B. Synchronisation des membres du club ★★

| Endpoint | Fonction |
|---|---|
| `GET /clubs/{id}/members` | Lister les membres du club Strava |
| `GET /clubs/{id}/admins` | Lister les admins du club Strava |

**Ce qu'on peut faire :**
- **Croiser** les membres Strava avec les membres Dérailleur pour identifier les comptes liés
- **Identifier les membres Strava pas encore dans Dérailleur** (aide au recrutement / relance d'adhésion)
- **Afficher le lien profil / avatar Strava** sur les pages membres dans Dérailleur
- **Détecter les départs** : un membre qui quitte le club Strava peut être signalé à l'admin

Le matching se fait via la table `member_strava` (voir section 4) qui lie `member_id` à `strava_athlete_id`. Pour les membres non encore liés, un matching par prénom + nom peut être proposé à l'admin pour validation.

### C. Messagerie aux participants d'un événement ★★★

**Mauvaise nouvelle** : l'API Strava ne propose **aucun endpoint de messagerie**. Il n'est pas possible d'envoyer des messages, des annonces ou des notifications aux membres via l'API.

**Alternatives possibles :**

1. **Lien profond vers la discussion Strava de l'événement** : chaque group event Strava a un fil de discussion. On peut générer un lien vers l'événement Strava (`https://www.strava.com/clubs/{slug}/group_events/{id}`) depuis Dérailleur, permettant à l'admin d'y poster manuellement un message visible par tous les inscrits.

2. **Notifications via l'inscription Strava** : quand un événement est modifié sur Strava (changement d'heure, annulation), Strava notifie automatiquement les inscrits. En supprimant/recréant l'événement ou en modifiant ses détails, on peut indirectement notifier les participants.

3. **Canal complémentaire** : utiliser le mail (déjà en place dans Dérailleur) ou WhatsApp (numéros déjà stockés) pour la communication directe, et Strava uniquement pour la sync des inscriptions.

**Recommandation** : ne pas compter sur Strava pour la messagerie. Conserver les emails et WhatsApp comme canaux de communication, et utiliser Strava exclusivement pour la gestion des inscriptions et la visibilité des événements.

### D. Parcours (Routes) ★★

L'API Strava permet de **lire** les parcours mais pas d'en créer.

#### Endpoints disponibles

| Endpoint | Fonction |
|---|---|
| `GET /routes/{id}` | Détails d'un parcours |
| `GET /athletes/{id}/routes` | Lister les parcours d'un athlète |
| `GET /routes/{id}/export/gpx` | Export GPX |
| `GET /routes/{id}/export/tcx` | Export TCX |
| `GET /routes/{id}/streams` | Données GPS du parcours |

#### Champs d'un objet Route

| Champ | Type | Description |
|---|---|---|
| `id` | int64 | Identifiant unique du parcours |
| `name` | string | Nom du parcours |
| `description` | string | Description |
| `distance` | float | Distance en mètres |
| `elevation_gain` | float | Dénivelé positif en mètres |
| `type` | integer | 1 = vélo, 2 = course |
| `sub_type` | integer | 1=route, 2=VTT, 3=cross, 4=trail, 5=mixte |
| `map` | PolylineMap | Contient `polyline` (encodage Google) et `summary_polyline` |
| `segments` | array | Segments traversés par le parcours |
| `waypoints` | array | Points d'intérêt personnalisés |
| `estimated_moving_time` | integer | Temps estimé en secondes |
| `private` | boolean | Parcours privé ou public |
| `starred` | boolean | Marqué comme favori |
| `athlete` | object | Créateur du parcours |
| `created_at` | date-time | Date de création |
| `updated_at` | date-time | Dernière modification |

#### Lien entre événements Strava et parcours

Les événements de groupe Strava ont un champ `route` (nullable) dans leur réponse. Quand un organisateur attache un parcours à un événement sur Strava, ce champ contient la référence au parcours. On peut donc :
- Récupérer le `route.id` depuis l'événement Strava lié
- Appeler `GET /routes/{id}` pour obtenir les détails (distance, dénivelé, polyline)
- Exporter le GPX via `GET /routes/{id}/export/gpx`

#### Ce qu'on peut faire : Strava → Dérailleur (lecture)

1. **Importer le parcours d'un événement Strava** : quand un événement Dérailleur est lié à un événement Strava (via `strava_event_id`), on récupère automatiquement le parcours associé et on stocke les infos clés dans Dérailleur.
2. **Afficher la carte du parcours** sur la page événement en décodant la `polyline` (bibliothèque JS type Leaflet + OpenStreetMap).
3. **Afficher distance et dénivelé** sur la page événement.
4. **Proposer le téléchargement GPX** pour les membres qui veulent charger le parcours sur leur compteur.

#### Ce qui N'EST PAS possible : Dérailleur → Strava (écriture)

- **Pas de création de parcours via API** — les parcours doivent être créés dans Strava Route Builder.
- **Pas de modification de parcours existants**.
- **Pas d'attachement de parcours à un événement via API** — cela se fait uniquement dans l'interface Strava lors de la création de l'événement.

#### Stockage dans Dérailleur

Ajouter un champ sur la table `events` pour cacher les données du parcours Strava :

```sql
ALTER TABLE events ADD COLUMN strava_route_id BIGINT NULL DEFAULT NULL AFTER strava_event_id;
```

Les données détaillées du parcours (polyline, distance, dénivelé) peuvent être stockées dans le champ `metadata` JSON existant ou dans une table dédiée si le volume le justifie. Un job cron met à jour ces données périodiquement.

#### Workflow recommandé

1. Cheffe de peloton crée le parcours dans **Strava Route Builder**
2. Cheffe de peloton crée l'événement sur **Strava** et y attache le parcours
3. Admin lie l'événement Dérailleur à l'événement Strava (URL ou ID)
4. **Sync automatique** : Dérailleur récupère le parcours via l'événement Strava → stocke distance, dénivelé, polyline
5. La page événement Dérailleur affiche la carte, les stats, et un lien de téléchargement GPX

## 3. Ce qui N'EST PAS possible via l'API

| Fonctionnalité | Statut |
|---|---|
| **Créer des événements via API** | Pas d'endpoint public — création uniquement via l'interface Strava |
| **Créer ou modifier des parcours via API** | Lecture seule — création uniquement via Strava Route Builder |
| **Attacher un parcours à un événement via API** | Uniquement via l'interface Strava à la création de l'événement |
| **Envoyer des messages / annonces** | Aucune API de messagerie |
| **Notifications push aux membres** | Non disponible |
| **Accéder aux membres non consentants** | Impossible — OAuth par athlète |
| **Posts / discussions du club** | Pas d'accès API |

## 4. Exigences techniques

### Table `member_strava`

```sql
CREATE TABLE member_strava (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id       INT UNSIGNED NOT NULL,
    strava_athlete_id BIGINT NOT NULL,
    access_token    VARCHAR(255) NOT NULL,
    refresh_token   VARCHAR(255) NOT NULL,
    token_expires_at DATETIME NOT NULL,
    scopes          VARCHAR(255) NOT NULL,
    updated_at      DATETIME DEFAULT NULL,
    UNIQUE KEY uq_member_strava (member_id),
    UNIQUE KEY uq_strava_athlete (strava_athlete_id),
    CONSTRAINT fk_member_strava_member FOREIGN KEY (member_id) REFERENCES members(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Champ sur la table `events`

```sql
ALTER TABLE events ADD COLUMN strava_event_id BIGINT NULL DEFAULT NULL AFTER statuscode;
```

### Autres composants

| Composant | Détails |
|---|---|
| **Enregistrement app Strava** | S'inscrire sur strava.com/settings/api |
| **Flux OAuth** | Redirection vers Strava → callback sur `derailleur.ffgva.ch/strava/callback` |
| **Scopes nécessaires** | `profile:read_all`, `read` (pour les données club et événements) |
| **Tâche cron** | Rafraîchissement des tokens (toutes les 6h), sync périodique des inscriptions et membres |
| **Config** | `STRAVA_CLIENT_ID`, `STRAVA_CLIENT_SECRET`, `STRAVA_CLUB_ID` dans `.env` |

## 5. Phases d'intégration recommandées

### Phase 1 — Strava Connect + Liaison des comptes
- Bouton "Connecter mon compte Strava" dans le profil membre (Filament)
- Flux OAuth complet avec stockage des tokens dans `member_strava`
- Affichage du statut de connexion Strava sur la page membre

### Phase 2 — Synchronisation des événements
- Champ `strava_event_id` sur la table `events`
- Champ URL Strava dans le formulaire d'édition d'événement
- Job cron : sync des inscriptions Strava → `event_member` (statut N = inscrit)
- Indicateur visuel sur la liste des participants : "inscrit via Strava"

### Phase 3 — Parcours Strava sur les événements
- Récupérer le parcours lié à l'événement Strava (champ `route` de la réponse group event)
- Stocker `strava_route_id`, distance, dénivelé, polyline
- Afficher carte du parcours (Leaflet + OpenStreetMap) sur la page événement
- Lien de téléchargement GPX

### Phase 4 — Synchronisation des membres du club
- Job cron : interroge `GET /clubs/{id}/members`
- Dashboard admin : liste des membres Strava non liés à un compte Dérailleur
- Proposition de matching par nom pour l'admin

## 6. Verdict

| Aspect | Évaluation |
|---|---|
| **Sync événements / inscriptions** | Bonne valeur — réduit la double saisie des inscriptions |
| **Parcours (Strava → Dérailleur)** | Bonne valeur — carte, stats, GPX automatiques sur les événements |
| **Parcours (Dérailleur → Strava)** | Impossible — API en lecture seule, création via Strava Route Builder uniquement |
| **Sync membres du club** | Utile pour le recrutement et la cohérence des données |
| **Messagerie** | Impossible via API — conserver email + WhatsApp |
| **Création d'événements** | Impossible via API — duplication manuelle nécessaire |

**Conclusion** : l'intégration Strava la plus réaliste est la **synchronisation des inscriptions aux événements** et le **croisement des membres**. La création d'événements et la messagerie ne sont pas possibles via l'API. Le workflow reste : créer l'événement dans Dérailleur (source de vérité), le dupliquer manuellement sur Strava, lier les deux via l'URL, puis laisser la sync automatique gérer les inscriptions. Pour la communication avec les participantes, conserver les canaux existants (email, WhatsApp).

## Sources

- [Strava API Getting Started](https://developers.strava.com/docs/getting-started/)
- [Strava API Reference](https://developers.strava.com/docs/reference/)
- [Strava Club Group Events V3 API](https://strava.github.io/api/v3/club_group_events/)
- [Strava Webhooks Documentation](https://developers.strava.com/docs/webhooks/)
- [Strava API Changelog](https://developers.strava.com/docs/changelog/)
- [Club Events API Discussion (Community)](https://communityhub.strava.com/developers-api-7/is-there-an-option-to-get-club-events-via-strava-api-1595)
