<?php
/**
 * chaine.php — Contact form handler for FFGVA
 * Deploy to: derailleur.ffgva.ch/chaine.php
 */

// ---------- CONFIG ----------
$to       = 'fastandfemalegva@etik.com';
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

// ---------- Detect form type ----------
$form_type = trim($data['form_type'] ?? 'contact');

// ---------- Validate fields ----------
$name    = trim($data['name']    ?? '');
$email   = trim($data['email']   ?? '');
$message = trim($data['message'] ?? '');

if ($form_type === 'adhesion') {
    $prenom = trim($data['prenom'] ?? '');
    $nom    = trim($data['nom']    ?? '');
    if ($prenom === '' || $nom === '' || $email === '') {
        http_response_code(422);
        exit(json_encode(['ok' => false, 'error' => 'Tous les champs sont requis.']));
    }
    // Build name from prenom + nom if not already set
    if ($name === '') {
        $name = "$prenom $nom";
    }
} else {
    if ($name === '' || $email === '' || $message === '') {
        http_response_code(422);
        exit(json_encode(['ok' => false, 'error' => 'Tous les champs sont requis.']));
    }
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    exit(json_encode(['ok' => false, 'error' => 'Adresse email invalide.']));
}

// Reject email header injection attempts
if (preg_match('/[\r\n]/', $name . $email)) {
    http_response_code(400);
    exit(json_encode(['ok' => false, 'error' => 'Caractères invalides.']));
}

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
    exit(json_encode(['ok' => false, 'error' => 'Trop de messages. Réessayez plus tard.']));
}

$hits[] = $now;
file_put_contents($file, json_encode(array_values($hits)), LOCK_EX);

// ---------- Send mail ----------
if ($form_type === 'adhesion') {
    $subject = 'Nouvelle demande d\'adhésion FFGVA';
    $body  = "=== Nouvelle demande d'adhésion ===\n\n";
    $body .= "Prénom : " . ($prenom ?? '') . "\n";
    $body .= "Nom : " . ($nom ?? '') . "\n";
    $body .= "Email : $email\n";
} else {
    $subject = 'Nouveau message via le site FFGVA';
    $body  = "Nom : $name\n";
    $body .= "Email : $email\n\n";
    $body .= "Message :\n$message\n";
}

$headers  = "From: $from\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: FFGVA-Contact/1.0\r\n";

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erreur lors de l\'envoi. Réessayez plus tard.']);
}
