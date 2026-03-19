<?php
/**
 * guidon.php — Membership form handler for FFGVA
 * Deploy to: derailleur.ffgva.ch/guidon.php
 */

// ---------- CONFIG ----------
$to       = 'fastandfemalegva@etik.com';
$subject  = 'Nouvelle demande d\'adhésion FFGVA';
$from     = 'noreply@ffgva.ch';
$allowed  = ['https://ffgva.ch', 'https://www.ffgva.ch'];

// Rate limit: max requests per IP within window
$rate_max    = 5;
$rate_window = 3600; // seconds
$rate_dir    = __DIR__ . '/rate';

// ---------- CORS ----------
header('Content-Type: application/json; charset=utf-8');

$origin = rtrim($_SERVER['HTTP_ORIGIN'] ?? '', '/');
if (in_array($origin, $allowed, true)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    http_response_code(403);
    exit(json_encode(['ok' => false, 'error' => 'Origine non autorisée.']));
}

header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ---------- POST only ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['ok' => false, 'error' => 'Méthode non autorisée.']));
}

// ---------- Read request body ----------
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$raw_input = file_get_contents('php://input');

if (stripos($contentType, 'application/json') !== false) {
    $data = json_decode($raw_input, true);
} else {
    $data = $_POST;
}

if (!is_array($data) || empty($data)) {
    http_response_code(400);
    exit(json_encode(['ok' => false, 'error' => 'Requête invalide.']));
}

// ---------- Honeypot ----------
if (!empty($data['website'] ?? '')) {
    // Bot filled the hidden field — pretend success
    exit(json_encode(['ok' => true]));
}

// ---------- Validate required fields ----------
$nom       = trim($data['nom']       ?? '');
$prenom    = trim($data['prenom']    ?? '');
$email     = trim($data['email']     ?? '');
$telephone = trim($data['telephone'] ?? '');
$photo_ok  = trim($data['photo_ok']  ?? '');

if ($nom === '' || $prenom === '' || $email === '' || $telephone === '' || $photo_ok === '') {
    http_response_code(422);
    exit(json_encode(['ok' => false, 'error' => 'Tous les champs obligatoires doivent être remplis.']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    exit(json_encode(['ok' => false, 'error' => 'Adresse email invalide.']));
}

// Reject email header injection attempts
if (preg_match('/[\r\n]/', $nom . $prenom . $email)) {
    http_response_code(400);
    exit(json_encode(['ok' => false, 'error' => 'Caractères invalides.']));
}

// ---------- Optional fields ----------
$type_velo  = trim($data['type_velo']  ?? '');
$sorties    = trim($data['sorties']    ?? '');
$atelier    = trim($data['atelier']    ?? '');
$instagram  = trim($data['instagram']  ?? '');
$strava     = trim($data['strava']     ?? '');
$statuts_ok    = trim($data['statuts_ok']    ?? '');
$cotisation_ok = trim($data['cotisation_ok'] ?? '');

// ---------- Rate limiting (file-based) ----------
if (!is_dir($rate_dir)) {
    @mkdir($rate_dir, 0700, true);
}

$ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$file = $rate_dir . '/' . md5($ip) . '.json';

$hits = [];
if (file_exists($file)) {
    $hits = json_decode(file_get_contents($file), true) ?: [];
}

// Prune old entries
$now  = time();
$hits = array_filter($hits, fn($t) => ($now - $t) < $rate_window);

if (count($hits) >= $rate_max) {
    http_response_code(429);
    exit(json_encode(['ok' => false, 'error' => 'Trop de demandes. Réessayez plus tard.']));
}

$hits[] = $now;
file_put_contents($file, json_encode(array_values($hits)), LOCK_EX);

// ---------- Send mail ----------
$body  = "=== Nouvelle demande d'adhésion FFGVA ===\n\n";
$body .= "Nom : $nom\n";
$body .= "Prénom : $prenom\n";
$body .= "Courriel : $email\n";
$body .= "Téléphone : $telephone\n";
$body .= "Autorisation photos/vidéos : $photo_ok\n";
$body .= "\n--- Informations complémentaires ---\n\n";
$body .= "Type de vélo : " . ($type_velo ?: '—') . "\n";
$body .= "Sorties souhaitées : " . ($sorties ?: '—') . "\n";
$body .= "Atelier souhaité : " . ($atelier ?: '—') . "\n";
$body .= "Instagram : " . ($instagram ?: '—') . "\n";
$body .= "Strava : " . ($strava ?: '—') . "\n";
$body .= "Statuts acceptés : $statuts_ok\n";
$body .= "Cotisation acceptée : $cotisation_ok\n";

$headers  = "From: $from\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: FFGVA-Inscription/1.0\r\n";

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erreur lors de l\'envoi. Réessayez plus tard.']);
}
