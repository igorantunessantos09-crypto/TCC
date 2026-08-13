<?php
// php/env.php
// Loader simples de variáveis de ambiente, sem depender do Composer.
// Lê o arquivo .env na raiz do projeto e injeta em getenv()/$_ENV.

function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }

    $linhas = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($linhas as $linha) {
        $linha = trim($linha);

        // Ignora comentários
        if ($linha === '' || strpos($linha, '#') === 0) {
            continue;
        }

        if (strpos($linha, '=') === false) {
            continue;
        }

        list($chave, $valor) = explode('=', $linha, 2);
        $chave = trim($chave);
        $valor = trim($valor);

        // Remove aspas se existirem
        $valor = trim($valor, "\"'");

        if (!array_key_exists($chave, $_ENV)) {
            putenv("$chave=$valor");
            $_ENV[$chave] = $valor;
        }
    }
}

function env($chave, $padrao = null) {
    $valor = getenv($chave);
    return $valor !== false ? $valor : $padrao;
}
?>
