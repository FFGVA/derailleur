# Analyse de couverture de tests — Dérailleur

**Date**: 23.03.2026
**Suite de tests**: PHPUnit 11 + Laravel 12 TestCase
**Résultat actuel**: 406 tests, 984 assertions — **100% green** (P1 mitigations implémentées le 23.03.2026)
**Durée**: ~29s contre MariaDB réelle (DatabaseTransactions)

---

## Table des matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Inventaire des tests existants](#2-inventaire-des-tests-existants)
3. [Analyse de pertinence et validité](#3-analyse-de-pertinence-et-validité)
4. [Matrice de couverture](#4-matrice-de-couverture)
5. [Composants non testés](#5-composants-non-testés)
6. [Lacunes critiques par domaine métier](#6-lacunes-critiques-par-domaine-métier)
7. [Problèmes de qualité des tests](#7-problèmes-de-qualité-des-tests)
8. [Recommandations et plan de mitigation](#8-recommandations-et-plan-de-mitigation)
9. [Stratégie Dusk / Playwright](#9-stratégie-dusk--playwright)

---

## 1. Vue d'ensemble

### Statistiques globales

| Métrique | Valeur |
|----------|--------|
| Fichiers de test | 51 |
| Tests unitaires | 25 fichiers |
| Tests fonctionnels (Feature) | 26 fichiers |
| Total assertions | 984 |
| Tests Dusk (browser) | 26 (7 suites) |
| Tests Playwright (E2E) | 0 |
| Factories | 1 (UserFactory uniquement) |

### Architecture de test

- **Trait**: `DatabaseTransactions` sur tous les tests DB (pas de RefreshDatabase)
- **Base de données**: MariaDB réelle (`agiletra_ffgva`), pas de SQLite
- **Mails**: `Mail::fake()` pour intercepter les envois
- **Auth**: `$this->actingAs($user)` pour Filament, session manuelle pour le portail
- **Livewire**: `Livewire::test()` pour les composants Filament
- **Pas de factories domaine**: les modèles sont créés directement via `Model::create()` dans chaque test

---

## 2. Inventaire des tests existants

### Tests unitaires (Unit/)

| Fichier | Lignes | Ce qui est testé |
|---------|--------|------------------|
| `Enums/EventMemberStatusTest` | 40 | Labels, couleurs, valeurs |
| `Enums/EventStatusTest` | 44 | Labels, couleurs, valeurs |
| `Enums/EventTypeTest` | 58 | Labels, couleurs des 9 types |
| `Enums/MemberStatusTest` | 52 | Labels, couleurs, 6 statuts |
| `Enums/UserRoleTest` | 30 | Labels Admin/ChefPeloton |
| `Models/EventMemberTest` | 84 | Pivot, statut, présence nullable |
| `Models/EventPricingTest` | 73 | `priceForMember()` : membre, non-membre, enfant, fallback |
| `Models/EventTest` | 154 | Création, relations, soft delete, casting |
| `Models/InvoiceTest` | 193 | Numéro facture, lignes cotisation, paiement, membership_end |
| `Models/MemberNumberTest` | 76 | High-watermark, assignation, retry sur doublon |
| `Models/MemberPhoneTest` | 90 | Formatage, label, WhatsApp, tri |
| `Models/MemberTest` | 162 | Création, relations, metadata JSON, soft delete |
| `Models/ModifiedByTest` | 94 | `modified_by_id` sur Member, Event, MemberPhone |
| `Models/UserTest` | 78 | Rôles, `canAccessPanel()`, relation member |
| `Mail/AdhesionConfirmationMailTest` | 52 | Sujet, contenu, pièce jointe PDF |
| `Mail/AdhesionWelcomeMailTest` | 55 | Sujet, lien d'activation, tutoiement |
| `Services/ICalServiceTest` | 66 | Génération iCal, filename, structure VCALENDAR |
| `Services/InvoiceServiceTest` | 81 | PDF (%PDF header), filename, numéro facture |
| `PhoneFormattingTest` | 117 | CH/FR/IT/DE/AT/PT, formats, erreurs |
| `ExampleTest` | 16 | Test de base Laravel (à supprimer) |

### Tests fonctionnels (Feature/)

| Fichier | Lignes | Ce qui est testé |
|---------|--------|------------------|
| `Api/AdhesionEndpointTest` | 278 | POST /api/adhesion : création membre, téléphone, mails, honeypot, validation |
| `Api/ContactEndpointTest` | 118 | POST /api/contact : envoi mail, honeypot, validation |
| `Api/EventRegistrationEndpointTest` | 147 | POST /api/inscription-event : inscription rapide, mails |
| `Auth/LoginTest` | 54 | Login Filament admin |
| `Auth/PasswordResetTest` | 91 | Reset mot de passe Filament |
| `Filament/CotisationsPageTest` | 218 | Page cotisations : envoi facture, marquer payé, visibilité actions |
| `Filament/DashboardWidgetsTest` | 63 | StatsOverview : montants, compteurs |
| `Filament/EventGpxUploadTest` | 114 | Upload GPX sur événement |
| `Filament/ExpiringMembershipsTest` | 74 | Widget adhésions expirantes |
| `Filament/InvoiceResourceTest` | 143 | Liste factures, action marquer payé, validation date |
| `Filament/UpcomingEventsWidgetTest` | 100 | Widget événements à venir |
| `Middleware/SetDatabaseUserIdTest` | 36 | Variable `@current_user_id` en session DB |
| `Policies/EventPolicyTest` | 117 | Admin vs ChefPeloton : CRUD, ownership event |
| `Policies/MemberPolicyTest` | 95 | Admin vs ChefPeloton : CRUD membres |
| `AdhesionActivationTest` | 169 | Confirmation email : token, facture, mails, erreurs |
| `EventRegistrationFlowTest` | 276 | Inscription événement : membre existant, nouveau, prix, facture |
| `ICalFeedTest` | 114 | Feed iCal public : filtrage par statut et date |
| `MemberMagicTokenTest` | 99 | Génération, validation, expiration, marquage used |
| `MembershipCardTest` | 135 | Carte membre : QR, validation, statuts |
| `PortalAuthTest` | 212 | Login magic link : envoi, vérification, session, logout |
| `PortalDashboardTest` | 483 | Dashboard portail : événements, inscription, annulation, factures |
| `PortalPelotonTest` | 626 | Gestion peloton : participants, présence, ajout, facturation |

---

## 3. Analyse de pertinence et validité

### Évaluation par fichier de test

| Fichier | Qualité | Commentaire |
|---------|---------|-------------|
| `AdhesionEndpointTest` | **Bon** | Flow complet, validation, honeypot, mails. Manque: CORS, rate limiting |
| `AdhesionActivationTest` | **Bon** | Tous les chemins d'erreur (token invalide, expiré, déjà confirmé) |
| `PortalAuthTest` | **Bon** | Excellente couverture auth: tous les états de token, filtrage par statut membre |
| `PortalDashboardTest` | **Bon** | Inscription/annulation, affichage conditionnel, timeout session |
| `PortalPelotonTest` | **Excellent** | Le plus complet: cycle présence 3 états, autorisations, facturation chef |
| `EventRegistrationFlowTest` | **Bon** | Deux flows (existant/nouveau), pricing membre/non-membre, URL signées |
| `CotisationsPageTest` | **Bon** | Actions conditionnelles, effets de bord (extension adhésion) |
| `EventPricingTest` | **Bon** | Logique métier ciblée: membre actif, non-membre, enfant, fallback |
| `PhoneFormattingTest` | **Bon** | Multi-pays, formats, cas d'erreur |
| `InvoiceTest` | **Correct** | Numéro facture, période cotisation. Manque: cas limites année |
| `MemberNumberTest` | **Correct** | High-watermark, retry. Manque: concurrence |
| `InvoiceResourceTest` | **Correct** | Validation date, mais portée très limitée |
| `MemberMagicTokenTest` | **Correct** | Méthodes couvertes, mais pas les cas limites temporels |
| `MembershipCardTest` | **Correct** | Fonctionnalité de base. Manque: contenu QR |
| `ICalFeedTest` | **Faible** | Structure uniquement, pas de validation du contenu événement |
| `InvoiceServiceTest` | **Faible** | Vérifie seulement le header PDF, pas le contenu |
| `EventTest` | **Faible** | Purement technique (CRUD), aucune logique métier |
| `MemberTest` | **Faible** | Purement technique, pas de validation métier |
| `ExampleTest` | **Inutile** | Test par défaut Laravel, à supprimer |

### Tests valides mais superficiels

Plusieurs tests unitaires de modèles (`EventTest`, `MemberTest`) vérifient uniquement que les données sont persistées et récupérées. Ils ne testent **aucune règle métier**:
- Pas de validation des transitions de statut
- Pas de contraintes sur les données (email unique, dates cohérentes)
- Pas de vérification des cascades soft-delete

Ces tests passent mais n'apportent qu'une valeur minimale — ils testent essentiellement Eloquent, pas l'application.

---

## 4. Matrice de couverture

### Par composant applicatif

| Composant | Total | Testé | Non testé | Couverture |
|-----------|-------|-------|-----------|------------|
| **Modèles** | 11 | 8 | 3 | 73% |
| **Enums** | 8 | 5 | 3 | 63% |
| **Services** | 4 | 3 | 1 | 75% |
| **Contrôleurs** | 8 | 6 | 2 | 75% |
| **Mail** | 11 | 9 | 2 | 82% |
| **Policies** | 2 | 2 | 0 | 100% |
| **Middleware** | 2 | 2 | 0 | 100% |
| **Filament Resources** | 3 | 3 | 0 | 100% |
| **Filament Pages** | 4 | 2 | 2 | 50% |
| **Filament Widgets** | 3 | 3 | 0 | 100% |
| **Form Requests** | 3 | 0 (indirect) | 3 | 0% |

### Par flux métier

| Flux métier | Couverture | Détail |
|-------------|------------|--------|
| Adhésion (API → activation → facture) | **Bonne** | Flow complet testé, mails vérifiés |
| Portail membre (auth, dashboard, events) | **Bonne** | Session, inscription, annulation, factures |
| Gestion peloton (chef de peloton) | **Excellente** | Présence, ajout participants, facturation |
| Facturation (création, PDF, paiement) | **Correcte** | Création testée, PDF superficiel, paiement partiel |
| Contact (formulaire) | **Correcte** | Flow API testé avec honeypot |
| Inscription événement (existant + nouveau) | **Bonne** | Deux parcours, pricing, mails |
| Administration Filament (CRUD) | **Bonne** | MemberResource, EventResource, InvoiceResource, Users, Cotisations testés |
| Gestion membres (admin) | **Bonne** | CRUD, validation, suppression avec dépendances |
| Gestion événements (admin) | **Bonne** | CRUD, validation, suppression, accès chef |
| Strava (OAuth, connexion) | **Absente** | Intégration incomplète — pas prioritaire |
| Gestion utilisateurs (admin) | **Bonne** | Create, edit, lock/unlock, accès admin-only |
| Carte de membre | **Correcte** | Affichage et validation QR testés |

---

## 5. Composants non testés

### Critique (logique métier ou sécurité)

| Composant | Risque | Justification |
|-----------|--------|---------------|
| ~~**MemberResource** (Filament CRUD)~~ | ~~Élevé~~ | ✅ Couvert par `MemberResourceTest.php` — 20 tests |
| ~~**EventResource** (Filament CRUD)~~ | ~~Élevé~~ | ✅ Couvert par `EventResourceTest.php` — 17 tests |
| ~~**Users Page** (Filament)~~ | ~~Élevé~~ | ✅ Couvert par `UsersPageTest.php` — 14 tests |
| **StravaController** | **Faible** | OAuth flow non testé, mais intégration Strava incomplète — pas prioritaire. |
| ~~**InvoiceService.generatePdf()**~~ | ~~Moyen~~ | ✅ Couvert par `InvoicePdfContentTest.php` — 25 tests (records, lines, amounts, filenames, QR) |
| **PortalAuth middleware** | **Moyen** | Testé implicitement via les feature tests, mais pas de test unitaire isolé pour le timeout et le filtrage de statut. |

### Modèles sans aucun test

| Modèle | Contenu | Impact |
|--------|---------|--------|
| `EventChef` | Relations event/member/modifiedBy | Faible (pivot simple) |
| `MemberStrava` | Relations, encryption casting | Moyen (données sensibles) |
| `PortalAuditLog` | Log model simple | Faible |

### Enums sans test dédié

| Enum | Testé indirectement ? |
|------|----------------------|
| `InvoiceStatus` | Oui, via InvoiceTest et CotisationsPageTest |
| `InvoiceType` | Oui, via InvoiceTest |
| `PhoneLabel` | Oui, via PhoneFormattingTest |

### Mails sans aucun test

| Classe Mail | Usage | Risque |
|-------------|-------|--------|
| `ExpiredMemberRegistrationMail` | Envoyé quand un membre expiré s'inscrit à un événement | Moyen — mail potentiellement jamais vérifié |
| `MemberUpdateRequestMail` | Envoyé quand un membre demande une modification | Faible — mail administratif |

---

## 6. Lacunes critiques par domaine métier

### 6.1 Facturation

**Testé**: Création de facture cotisation, numéro de facture, marquage payé, extension adhésion.

**Non testé**:
- Contenu du PDF (montant affiché, nom du membre, adresse, QR-bill IBAN)
- Facture type E (événement) : création via `InvoiceService::createEvent()`
- Facture type A (autre) : création via `InvoiceService::createAutre()`
- Facture multi-événements (pivot `invoice_event`)
- Recalcul du montant (`recalculateAmount()`) après modification de lignes
- Envoi de facture par email avec pièce jointe PDF (contenu de la pièce jointe)
- Annulation de facture et effets de bord
- Cas limite : facture pour membre sans adresse complète

### 6.2 Gestion des événements (admin)

**Testé**: Pricing, participants (via portail), upload GPX.

**Non testé**:
- Création d'événement via Filament (champs requis, validation dates)
- Modification d'événement (changement de prix avec inscriptions existantes)
- Suppression d'événement (vérification dépendances)
- ~~`isFull()` : refus d'inscription quand `max_participants` atteint~~ *(pas de règle métier pour l'instant)*
- Transitions de statut événement (N→P→T, N→X)
- Relation chef de peloton : synchro `event_chef` pivot
- Relation managers dans Filament (MembersRelationManager, PresencesRelationManager)

### 6.3 Gestion des membres (admin)

**Testé**: Modèle unitaire, policies.

**Non testé**:
- Formulaire Filament : création, modification, visualisation
- Validation email unique
- Transitions de statut membre (P→A sur paiement, A→I sur expiration)
- Suppression avec vérification des dépendances (événements, factures)
- Export vCard (action ListMembers)
- PhonesRelationManager (ajout, modification, suppression de téléphones)
- Champ metadata JSON (saisie et affichage)

### 6.4 Sécurité et autorisations

**Testé**: Policies (MemberPolicy, EventPolicy), login Filament, auth portail.

**Non testé**:
- Rate limiting sur les endpoints API (`/api/contact`, `/api/adhesion`, `/api/inscription-event`)
- CORS headers sur les endpoints API
- Honeypot : testé sur adhesion, **non testé sur contact**
- Accès admin aux pages Filament par rôle (Users page accessible uniquement aux admins ?)
- Session hijacking / fixation sur le portail
- Token d'activation : force brute, timing attacks
- Strava OAuth : validation du state parameter, token storage security

### 6.5 Inscription événement

**Testé**: Flow existant et nouveau membre, pricing, mails.

**Non testé**:
- ~~Inscription quand événement complet (`max_participants`)~~ *(pas enforced par design)*
- Inscription à un événement annulé (statut X)
- Inscription à un événement brouillon (statut N)
- Double inscription concurrent (race condition)
- Inscription avec prix null / non défini

---

## 7. Problèmes de qualité des tests

### 7.1 Assertions fragiles (brittle)

**ICalFeedTest** — Assertions par sous-chaîne trop courtes:
```php
$response->assertSee('Sortie Publi');  // Matche "Sortie Publique" mais aussi "Sortie Publication"
$response->assertSee('Sortie Termin'); // Matche "Sortie Terminée" mais aussi "Sortie Terminal"
```
**Recommandation**: Utiliser des assertions sur le contenu iCal parsé, ou des sous-chaînes complètes.

**AdhesionActivationTest** — Messages d'erreur vérifiés par mot-clé:
```php
$response->assertSee('expiré');
$response->assertSee('invalide');
```
**Recommandation**: Vérifier le message complet ou utiliser `assertSessionHasErrors()`.

### 7.2 Tests qui testent le framework, pas l'application

Les tests unitaires de modèles (`MemberTest`, `EventTest`) vérifient principalement:
- Que `Model::create()` persiste les données
- Que les casts Eloquent fonctionnent
- Que les relations retournent le bon type

Ces tests ont une valeur limitée car ils testent Laravel/Eloquent, pas la logique métier.

### 7.3 Absence de factories

Le projet n'a qu'une seule factory (`UserFactory`). Tous les autres modèles sont créés manuellement dans chaque test avec des helpers privés (`makeMember()`, `makeAdmin()`). Cela entraîne:
- **Duplication**: les mêmes helpers sont recopiés dans chaque fichier de test
- **Fragilité**: si un champ requis est ajouté, tous les helpers doivent être mis à jour
- **Incohérence**: les données de test varient d'un fichier à l'autre

### 7.4 Test inutile

`ExampleTest.php` est le test par défaut de Laravel (`assertTrue(true)`). Il devrait être supprimé.

### 7.5 Couplage test unitaire / service

`InvoiceTest` (unit) appelle `InvoiceService::computeMembershipEnd()` — cette logique devrait être testée dans `InvoiceServiceTest`, pas dans le test du modèle.

---

## 8. Recommandations et plan de mitigation

### Priorité 1 — Critique (sécurité et données)

| # | Action | Type de test | Effort |
|---|--------|-------------|--------|
| 1.1 | ~~**Tester MemberResource CRUD**~~ | Feature/Livewire | ✅ `MemberResourceTest.php` — 20 tests |
| 1.2 | ~~**Tester EventResource CRUD**~~ | Feature/Livewire | ✅ `EventResourceTest.php` — 17 tests |
| 1.3 | ~~**Tester Users Page**~~ | Feature/Livewire | ✅ `UsersPageTest.php` — 14 tests |
| ~~1.4~~ | ~~Tester `max_participants`~~ | — | — *(pas de règle métier à appliquer pour l'instant)* |
| 1.5 | ~~**Tester rate limiting**~~ | Feature | ✅ `RateLimitingTest.php` — 5 tests |
| 1.6 | ~~**Tester contenu PDF**~~ | Unit | ✅ `InvoicePdfContentTest.php` — 25 tests |

### Priorité 2 — Important (logique métier)

| # | Action | Type de test | Effort |
|---|--------|-------------|--------|
| 2.1 | **Tester InvoiceService** pour les 3 types (C, E, A) | Unit | Moyen |
| 2.2 | **Tester `recalculateAmount()`** après ajout/suppression de lignes | Unit | Petit |
| 2.3 | **Tester transitions de statut** membre (P→A, A→I) et événement (N→P→T) | Unit | Petit |
| 2.4 | **Tester suppression avec dépendances** (membre avec factures, événement avec inscrits) | Feature | Moyen |
| 2.5 | **Tester inscription événement annulé/brouillon** | Feature | Petit |
| 2.6 | **Créer factories** pour Member, Event, Invoice, MemberPhone | Infrastructure | Moyen |
| 2.7 | **Tester `ExpiredMemberRegistrationMail`** et **`MemberUpdateRequestMail`** | Unit | Petit |

### Priorité 3 — Amélioration (robustesse)

| # | Action | Type de test | Effort |
|---|--------|-------------|--------|
| ~~3.1~~ | ~~Tester StravaController~~ | — | — *(Strava pas encore implémenté complètement)* |
| 3.2 | **Tester PortalAudit::log()** | Unit | Petit |
| 3.3 | **Renforcer ICalFeedTest** : valider DTSTART, SUMMARY, UID, timezone | Unit | Petit |
| 3.4 | **Renforcer InvoiceServiceTest** : valider contenu PDF au-delà du header | Unit | Moyen |
| 3.5 | **Tester CORS headers** sur les endpoints API | Feature | Petit |
| 3.6 | **Tester MembersRelationManager** et **PresencesRelationManager** | Feature/Livewire | Moyen |
| 3.7 | **Supprimer ExampleTest.php** | Nettoyage | Trivial |
| 3.8 | **Tester EventChef** et **MemberStrava** modèles | Unit | Petit |

### Priorité 4 — Housekeeping

| # | Action | Effort |
|---|--------|--------|
| 4.1 | Extraire les helpers `makeMember()` etc. dans un trait `TestHelpers` ou créer des factories | Moyen |
| 4.2 | Corriger les assertions fragiles dans ICalFeedTest | Petit |
| 4.3 | Déplacer les tests `computeMembershipEnd()` de InvoiceTest vers InvoiceServiceTest | Petit |
| 4.4 | Ajouter les enums manquants (InvoiceStatus, InvoiceType, PhoneLabel) aux tests unitaires | Petit |

---

## 9. Stratégie Dusk / Playwright

### Outils disponibles

- **Laravel Dusk**: Tests browser headless avec ChromeDriver, intégré à Laravel
- **Playwright**: Tests E2E multi-navigateur (Chromium, Firefox, WebKit)

### Quand utiliser quel outil

| Scénario | Outil recommandé | Justification |
|----------|-----------------|---------------|
| Formulaires Filament complexes (repeaters, selects, modals) | **Dusk** | Intégration native Laravel, accès au state Livewire |
| Flow adhésion complet (homepage → API → email → activation) | **Playwright** | Multi-page, pas besoin d'accès Laravel |
| Upload de fichiers (GPX, photos) via Filament | **Dusk** | FileUpload Livewire nécessite un vrai browser |
| Responsive design / mobile-first | **Playwright** | Meilleur support multi-viewport |
| Portail membre (navigation, inscription événement) | **Playwright** | Pages Blade classiques, pas de Livewire |
| OAuth Strava (redirect externe) | **Playwright** | Gestion multi-domaine |

### Tests E2E recommandés

#### Dusk (Filament admin)

| Test | Priorité | Description |
|------|----------|-------------|
| ~~`MemberCrudTest`~~ | ~~P1~~ | ✅ 5 tests — create, view, edit, delete, list |
| ~~`EventCrudTest`~~ | ~~P1~~ | ✅ 5 tests — create, view, edit, list, participants |
| ~~`InvoiceCrudTest`~~ | ~~P1~~ | ✅ 5 tests — list, view, status badge, cotisations page |
| ~~`UserManagementTest`~~ | ~~P1~~ | ✅ 5 tests — page load, create, roles, details, lock icon |
| ~~`MemberPhoneRepeaterTest`~~ | ~~P2~~ | ✅ 2 tests — repeater add, phone display |
| ~~`EventParticipantModalTest`~~ | ~~P2~~ | ✅ 2 tests — status display, presence display |
| ~~`CotisationsBulkTest`~~ | ~~P2~~ | ✅ 2 tests — expiring members, send action |

#### Playwright (portail et flows publics)

| Test | Priorité | Description |
|------|----------|-------------|
| `AdhesionFlowTest` | **P1** | Formulaire adhésion → email → clic lien → confirmation |
| `PortalLoginFlowTest` | **P1** | Demander magic link → email → clic → dashboard |
| `EventRegistrationFlowTest` | **P1** | Inscription événement depuis le portail, vérification email |
| `MembershipCardTest` | **P2** | Affichage carte, scan QR, validation |
| `ResponsiveTest` | **P2** | Navigation mobile (top bar, formulaires) |
| `ContactFormTest` | **P3** | Soumission formulaire contact depuis la homepage |

### Estimation d'effort

| Phase | Effort | Livrable |
|-------|--------|----------|
| ~~Setup Dusk (config, ChromeDriver, base test)~~ | ~~2h~~ | ✅ `tests/Browser/` — 26 tests |
| Setup Playwright (config, fixtures) | 2h | `tests/Playwright/` fonctionnel |
| ~~Tests Dusk P1 (4 tests)~~ | ~~8h~~ | ✅ 20 tests implémentés |
| ~~Tests Dusk P2 (3 tests)~~ | ~~4h~~ | ✅ 6 tests implémentés |
| Tests Playwright P1 (3 tests) | 6h | Flows publics couverts |
| Tests Playwright P2-P3 (3 tests) | 4h | UX et responsive couverts |
| **Total** | **~26h** | Couverture E2E complète |

---

## Annexe — Couverture par fichier source

Légende: ✅ Testé | ⚠️ Partiel | ❌ Non testé

### Modèles

| Fichier | Unit | Feature | E2E |
|---------|------|---------|-----|
| `Member.php` | ✅ | ✅ | ✅ |
| `User.php` | ✅ | ✅ | ✅ |
| `Event.php` | ⚠️ | ✅ | ✅ |
| `Invoice.php` | ✅ | ✅ | ✅ |
| `InvoiceLine.php` | ⚠️ | ⚠️ | ❌ |
| `MemberPhone.php` | ✅ | ⚠️ | ✅ |
| `EventMember.php` | ✅ | ✅ | ✅ |
| `EventChef.php` | ❌ | ❌ | ❌ |
| `MemberStrava.php` | ❌ | ❌ | ❌ |
| `MemberMagicToken.php` | ✅ | ✅ | ❌ |
| `PortalAuditLog.php` | ❌ | ❌ | ❌ |

### Services

| Fichier | Unit | Feature |
|---------|------|---------|
| `InvoiceService.php` | ✅ | ✅ |
| `PhoneFormatter.php` | ✅ | ✅ |
| `ICalService.php` | ⚠️ | ✅ |
| `PortalAudit.php` | ❌ | ❌ |

### Contrôleurs

| Fichier | Feature |
|---------|---------|
| `Api/FormController.php` | ✅ |
| `Api/EventRegistrationController.php` | ✅ |
| `AdhesionActivationController.php` | ✅ |
| `EventRegistrationController.php` | ✅ |
| `PortalAuthController.php` | ✅ |
| `PortalController.php` | ✅ |
| `StravaController.php` | ❌ |

### Filament

| Fichier | Feature | E2E |
|---------|---------|-----|
| `MemberResource` (+ 4 pages) | ✅ | ✅ |
| `EventResource` (+ 4 pages) | ✅ | ✅ |
| `InvoiceResource` (+ 3 pages) | ⚠️ | ❌ |
| `Pages/Cotisations` | ✅ | ❌ |
| `Pages/Users` | ✅ | ✅ |
| `Pages/Strava` | ❌ | ❌ |
| `Pages/StravaConnect` | ❌ | ❌ |
| `Widgets/StatsOverview` | ✅ | ❌ |
| `Widgets/UpcomingEvents` | ✅ | ❌ |
| `Widgets/ExpiringMemberships` | ✅ | ❌ |

### Mail

| Classe | Unit | Feature (indirect) |
|--------|------|--------------------|
| `AdhesionWelcomeMail` | ✅ | ✅ |
| `AdhesionConfirmationMail` | ✅ | ✅ |
| `AdhesionMail` | ❌ | ✅ |
| `PortalMagicLinkMail` | ❌ | ✅ |
| `EventRegistrationExistingMail` | ❌ | ✅ |
| `EventRegistrationNewMail` | ❌ | ✅ |
| `EventConfirmationMail` | ❌ | ✅ |
| `InvoiceMail` | ❌ | ✅ |
| `ContactMail` | ❌ | ✅ |
| `ExpiredMemberRegistrationMail` | ❌ | ❌ |
| `MemberUpdateRequestMail` | ❌ | ❌ |
