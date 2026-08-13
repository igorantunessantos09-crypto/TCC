<?php
// php/csrf.php
// Proteção contra CSRF (Cross-Site Request Forgery).
// Uso:
//   1. No formulário: <?php echo csrfField();
//   2. No processamento: csrfVerify(); // encerra a requisição se inválido

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    $token = htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function csrfVerify() {
    $enviado = $_POST['csrf_token'] ?? '';
    $esperado = $_SESSION['csrf_token'] ?? '';

    if (empty($enviado) || empty($esperado) || !hash_equals($esperado, $enviado)) {
        http_response_code(403);
        die('Requisição inválida ou expirada. Volte e tente novamente.');
    }
}
?>
