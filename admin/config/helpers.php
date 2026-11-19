<?php
/* Funções auxiliares simples, sem dependência de extensões opcionais do PHP (como mbstring). */

// Escapa texto para exibir em HTML (evita XSS)
function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// Formata preço: 299.99 -> R$ 299,99
function moeda($valor) {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

// Quantidade de caracteres (conta corretamente acentos em UTF-8)
function tamanho($texto) {
    return preg_match_all('/./us', (string)$texto);
}

// Corta o texto em $max caracteres, acrescentando "..." se foi cortado
function cortar($texto, $max) {
    $texto = (string)$texto;
    if (tamanho($texto) <= $max) {
        return $texto;
    }
    preg_match('/^.{0,' . (int)$max . '}/us', $texto, $m);
    return $m[0] . '...';
}
