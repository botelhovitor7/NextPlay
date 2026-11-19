<?php
/*
 * CONEXÃO COM O BANCO DE DADOS
 * -----------------------------------------------------------
 * Se no computador da faculdade o MySQL tiver outra configuração,
 * é SOMENTE ESTE ARQUIVO que precisa ser alterado (as 4 linhas define abaixo).
 *   - servidor: normalmente 'localhost' (ou '127.0.0.1')
 *   - usuario : no XAMPP o padrão é 'root'
 *   - senha   : no XAMPP o padrão é '' (vazia)
 *   - bd      : nome do banco -> 'nextplay_db'
 * Se o MySQL estiver em outra porta (ex.: 3307), troque servidor por
 * 'localhost;port=3307'.
 */
define('servidor', 'localhost');
define('usuario',  'root');
define('senha',    '');
define('bd',       'nextplay_db');

try {
    $pdo = new PDO(
        'mysql:host=' . servidor . ';dbname=' . bd . ';charset=utf8mb4',
        usuario,
        senha,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Erro! Não foi possível conectar ao banco. Verifique se o MySQL está ligado no XAMPP '
      . 'e se o arquivo admin/config/conexao.php está correto. Detalhe: ' . $e->getMessage());
}
