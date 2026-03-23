# Strava API — Analyse d'intégration pour Dérailleur

**Date**: 23.03.2026
**Statut**: Analyse de faisabilité

## 1. Modèle d'authentification

Strava utilise **OAuth 2.0** avec consentement par athlète. Chaque membre souhaitant l'intégration doit :
1. Se connecter à Strava et autoriser l'application Dérailleur
2. On stocke leur `refresh_token` (longue durée) et `access_token` (expire toutes les 6 heures)

**Conséquence** : pas d'accès en masse. Chaque fonctionnalité ci-dessous ne fonctionne que pour les membres ayant lié leur compte Strava. Il faut un bouton "Connecter Strava" dans le profil membre.

**Limites de requêtes** : 200 requêtes / 15 min, 2'000 / jour par application. Avec ~50 membres actifs, c'est gérable.

## 2. Ce qui EST possible (et utile)

### A. Club Group Events — Sync bidirectionnelle ★★★

Strava dispose d'une API complète pour les événements de groupe :

| Endpoint | Fonction |
|---|---|
| `GET /clubs/{id}/group_events` | Lister tous les événements du club |
| `GET /group_events/{id}` | Détails d'un événement |
| `POST /group_events/{id}/rsvps` | S'inscrire (RSVP) |
| `DELETE /group_events/{id}/rsvps` | Se désinscrire |
| `DELETE /group_events/{id}` | Supprimer un événement |
| `GET /group_events/{id}/athletes` | Lister les inscrit·e·s |

**Ce qu'on pourrait faire :**
- **Sync inscriptions Strava → Dérailleur** (pull) : interroger `GET /group_events/{id}/athletes` pour voir qui s'est inscrit sur Strava, et auto-inscrire dans la table `event_member`.
- **Événements récurrents** supportés (hebdomadaire/mensuel), ce qui correspond aux sorties régulières.

**Limitation majeure** : il n'y a **pas d'endpoint de création d'événement** dans l'API publique — uniquement lister, rejoindre, quitter, supprimer. Les événements doivent être créés manuellement sur Strava.

**Approche réaliste** : créer les événements dans Dérailleur (source de vérité), les dupliquer manuellement sur Strava, puis synchroniser les inscriptions.

### B. Suivi d'activités — Stats post-sortie ★★★

| Endpoint | Fonction |
|---|---|
| `GET /athlete/activities` | Lister les activités d'un membre |
| `GET /activities/{id}` | Détails complets (distance, dénivelé, temps, carte) |
| `GET /activities/{id}/streams` | Données GPS, fréquence cardiaque, puissance |

**Ce qu'on pourrait faire :**
- Après un événement, **associer automatiquement les activités Strava aux événements Dérailleur** par proximité date/heure
- Afficher les **stats de la sortie sur la page événement** : km total, dénivelé, vitesse moyenne par participante
- Construire un **tableau de bord club** avec stats agrégées (km total roulés cette saison, etc.)
- Afficher une **fiche membre** avec ses stats Strava

### C. Webhooks — Mises à jour en temps réel ★★★

Strava supporte les notifications push via webhooks :

| Événement | Objet | Type |
|---|---|---|
| Nouvelle activité uploadée | activity | create |
| Activité modifiée | activity | update |
| Activité supprimée | activity | delete |
| Membre désautorise l'app | athlete | delete |

**Ce qu'on pourrait faire :**
- **Matching instantané** : quand un membre uploade une sortie, le webhook se déclenche → on vérifie si ça correspond à un événement → on marque la présence (`present = true`)
- **Suivi de présence automatique** : si l'activité Strava d'un membre chevauche la fenêtre temporelle d'un événement, marquer `present = true` automatiquement — remplace le suivi manuel
- Détecter la **désautorisation** (obligatoire selon les conditions Strava)

**Contrainte technique** : nécessite une URL callback publique sur derailleur.ffgva.ch. Un seul abonnement par app, couvre tous les athlètes autorisés.

**Payload webhook :**

| Champ | Type | Description |
|---|---|---|
| `object_type` | string | "activity" ou "athlete" |
| `object_id` | long | ID activité ou athlète |
| `aspect_type` | string | "create", "update" ou "delete" |
| `updates` | hash | Champs modifiés (title, type, private) |
| `owner_id` | long | ID athlète |
| `subscription_id` | integer | ID abonnement push |
| `event_time` | long | Timestamp Unix |

L'endpoint callback doit répondre 200 OK en moins de 2 secondes. Strava retente jusqu'à 3 fois en cas d'échec.

### D. Membres du club ★★

| Endpoint | Fonction |
|---|---|
| `GET /clubs/{id}/members` | Lister les membres du club |
| `GET /clubs/{id}/admins` | Lister les admins |

**Ce qu'on pourrait faire :**
- **Croiser** les membres Strava avec les membres Dérailleur (match par nom)
- Identifier les membres Strava pas encore dans Dérailleur (recrutement)
- Afficher le lien profil / avatar Strava sur les pages membres

### E. Parcours (Routes) ★★

| Endpoint | Fonction |
|---|---|
| `GET /athletes/{id}/routes` | Lister les parcours d'un athlète |
| `GET /routes/{id}` | Détails du parcours |
| `GET /routes/{id}/export/gpx` | Export GPX |
| `GET /routes/{id}/export/tcx` | Export TCX |

**Ce qu'on pourrait faire :**
- Permettre aux cheffes de peloton d'**attacher un parcours Strava** à un événement
- Afficher la carte / profil du parcours sur la page événement
- Télécharger le GPX pour les membres qui veulent le charger sur leur compteur

### F. Flux d'activités du club ★

| Endpoint | Fonction |
|---|---|
| `GET /clubs/{id}/activities` | Activités récentes des membres (max 200) |

**Ce qu'on pourrait faire :**
- Afficher un flux "sorties récentes" sur le tableau de bord
- **Limitation** : max 200 activités, pas de filtrage par date, pas d'ID d'activité dans la réponse

## 3. Ce qui N'EST PAS possible

| Fonctionnalité | Statut |
|---|---|
| **Créer des événements via API** | Pas d'endpoint public — création uniquement via l'interface Strava |
| **Envoyer des messages / annonces** | Aucune API de messagerie |
| **Notifications push aux membres** | Non disponible |
| **Accéder aux membres non consentants** | Impossible — OAuth par athlète |
| **Posts / discussions du club** | Pas d'accès API |
| **Paiement / formulaires d'inscription** | Pas un concept Strava |
| **Export de données en masse** | Bloqué par les limites de requêtes et le consentement par athlète |

## 4. Phases d'intégration recommandées

### Phase 1 — Strava Connect + Sync d'activités (haute valeur, effort modéré)
- Bouton "Connecter mon compte Strava" dans le profil membre
- Stocker les tokens OAuth dans une nouvelle table `member_strava`
- Abonnement webhook pour les événements d'activité
- Associer automatiquement les activités aux événements par date/heure
- Afficher les stats (distance, dénivelé) sur la page événement

### Phase 2 — Automatisation de la présence (haute valeur, s'appuie sur Phase 1)
- Quand le webhook se déclenche pour une nouvelle activité, vérifier si elle tombe dans ±2h d'un événement
- Auto-marquer `present = true` dans `event_member`
- Widget tableau de bord : "X membres ont roulé Y km ce mois"

### Phase 3 — Intégration des parcours (nice-to-have)
- Attacher un parcours Strava à un événement
- Afficher la carte du parcours sur la page événement
- Lien de téléchargement GPX

### Phase 4 — Sync des inscriptions (si événements créés manuellement sur Strava)
- Interroger les group events Strava pour les RSVPs
- Synchroniser vers les inscriptions `event_member`
- Nécessite que quelqu'un crée aussi l'événement sur Strava (pas d'API pour la création)

## 5. Exigences techniques

| Composant | Détails |
|---|---|
| **Enregistrement app Strava** | S'inscrire sur strava.com/settings/api |
| **Nouvelle table DB** | `member_strava` : member_id, strava_athlete_id, access_token, refresh_token, token_expires_at, scopes |
| **Flux OAuth** | Redirection vers Strava → callback sur `derailleur.ffgva.ch/strava/callback` |
| **Endpoint webhook** | `POST /api/strava/webhook` (public, valide l'abonnement) |
| **Scopes nécessaires** | `activity:read_all`, `profile:read_all`, `read` (pour les données club) |
| **Tâche cron** | Rafraîchissement des tokens (toutes les 6h), sync périodique des RSVPs |
| **SQL de production** | Nouvelle table + FK vers members |

## 6. Détails API Group Events

### Champs de réponse — Représentation résumée

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

### Champs supplémentaires — Représentation détaillée

| Champ | Type | Description |
|---|---|---|
| `viewer_permissions` | object | `{"edit": bool}` |
| `start_datetime` | string | Format local : `yyyy-mm-ddThh:mm` |
| `frequency` | string | `no_repeat`, `weekly`, `monthly` |
| `day_of_week` | string | Pour événements mensuels uniquement |
| `week_of_month` | integer | Pour mensuels : -1, 1, 2, 3, 4 |
| `days_of_week` | array | Pour hebdomadaires : noms des jours |
| `weekly_interval` | integer | Pour hebdomadaires : toutes les x semaines (1-4) |

## 7. Verdict

| Aspect | Évaluation |
|---|---|
| **Suivi d'activités & stats** | Excellent — c'est le point fort de Strava |
| **Auto-présence via webhook** | Très prometteur — pourrait éliminer le suivi manuel |
| **Sync événements (Strava → Dérailleur)** | Bon pour les RSVPs, mais pas de création d'événement via API |
| **Création d'événements (Dérailleur → Strava)** | Impossible via API |
| **Messagerie / notifications** | Impossible |
| **Parcours sur les événements** | Bonus appréciable |

**Conclusion** : l'intégration à plus haute valeur est le **suivi de présence automatique via webhooks** — les membres roulent, uploadent sur Strava, et Dérailleur sait automatiquement qui était présent. Combiné avec les stats post-sortie sur les pages événements, cela réduirait significativement le travail admin et ajouterait une couche de données fun. L'impossibilité de créer des événements ou d'envoyer des messages via l'API limite le côté "push", mais le côté "pull" (activités, présence, stats) est solide.

## Sources

- [Strava API Getting Started](https://developers.strava.com/docs/getting-started/)
- [Strava API Reference](https://developers.strava.com/docs/reference/)
- [Strava Club Group Events V3 API](https://strava.github.io/api/v3/club_group_events/)
- [Strava Webhooks Documentation](https://developers.strava.com/docs/webhooks/)
- [Strava API Changelog](https://developers.strava.com/docs/changelog/)
- [Club Activities Limitations (Community)](https://communityhub.strava.com/developers-api-7/club-activities-limited-to-the-last-200-and-no-dates-or-activity-ids-3027)
- [Club Events API Discussion (Community)](https://communityhub.strava.com/developers-api-7/is-there-an-option-to-get-club-events-via-strava-api-1595)
