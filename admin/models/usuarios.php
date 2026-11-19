<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/helpers.php';

class Usuario {
    public $id;
    public $nome;
    public $email;
    public $senha;   // senha em texto puro (só vem do formulário); no banco vai o hash
    public $tipo;
    private $pdo;

    public function __construct($atrib = []) {
        global $pdo;
        $this->pdo = $pdo;

        if (!empty($atrib)) {
            $this->id    = $atrib['id'] ?? null;
            $this->nome  = $atrib['nome'] ?? null;
            $this->email = $atrib['email'] ?? null;
            $this->senha = $atrib['senha'] ?? null;
            $this->tipo  = $atrib['tipo'] ?? 'user';
        }
    }

    // Valida os dados. $exigirSenha = false na edição (senha em branco = manter a atual)
    public function validar($exigirSenha = true) {
        $erros = [];
        $this->nome  = trim((string)$this->nome);
        $this->email = trim((string)$this->email);

        if ($this->nome === '' || tamanho($this->nome) > 100) {
            $erros[] = 'O nome é obrigatório e deve ter até 100 caracteres.';
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL) || tamanho($this->email) > 150) {
            $erros[] = 'Informe um e-mail válido.';
        } elseif ($this->emailJaExiste($this->email, $this->id)) {
            $erros[] = 'Este e-mail já está cadastrado.';
        }
        if ($exigirSenha || $this->senha !== '') {
            if (tamanho((string)$this->senha) < 6) {
                $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
            }
        }
        if (!in_array($this->tipo, ['admin', 'user'], true)) {
            $erros[] = 'Tipo de usuário inválido.';
        }
        return $erros;
    }

    // Verifica se o e-mail já está em uso por OUTRO usuário
    public function emailJaExiste($email, $ignorarId = null) {
        $sth = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email AND id <> :id");
        $sth->bindValue(':email', $email, PDO::PARAM_STR);
        $sth->bindValue(':id', (int)$ignorarId, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchColumn() > 0;
    }

    // Listar todos os usuários (sem trazer a senha)
    public function listarUsuarios() {
        $sth = $this->pdo->query("SELECT id, nome, email, tipo, created_at FROM usuarios ORDER BY nome ASC");
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cadastrar usuário (senha com password_hash)
    public function cadastrarUsuario() {
        $sth = $this->pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, created_at, updated_at)
                                    VALUES (:nome, :email, :senha, :tipo, NOW(), NOW())");
        $sth->bindValue(':nome', $this->nome, PDO::PARAM_STR);
        $sth->bindValue(':email', $this->email, PDO::PARAM_STR);
        $sth->bindValue(':senha', password_hash($this->senha, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $sth->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);

        return $sth->execute();
    }

    // Editar usuário. Se a senha vier em branco, mantém a senha atual.
    public function editarUsuario() {
        if ($this->senha !== null && $this->senha !== '') {
            $sth = $this->pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, tipo = :tipo,
                                        senha = :senha, updated_at = NOW() WHERE id = :id");
            $sth->bindValue(':senha', password_hash($this->senha, PASSWORD_DEFAULT), PDO::PARAM_STR);
        } else {
            $sth = $this->pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, tipo = :tipo,
                                        updated_at = NOW() WHERE id = :id");
        }
        $sth->bindValue(':id', $this->id, PDO::PARAM_INT);
        $sth->bindValue(':nome', $this->nome, PDO::PARAM_STR);
        $sth->bindValue(':email', $this->email, PDO::PARAM_STR);
        $sth->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);

        return $sth->execute();
    }

    // Deletar usuário
    public function deletarUsuario() {
        $sth = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $sth->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $sth->execute();
    }

    // Buscar usuário por ID (sem a senha)
    public function buscarUsuarioPorId($id) {
        $sth = $this->pdo->prepare("SELECT id, nome, email, tipo, created_at FROM usuarios WHERE id = :id");
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    // Conta administradores (para não excluir/rebaixar o último)
    public function contarAdmins() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'admin'")->fetchColumn();
    }

    // Autenticação: retorna os dados do usuário se e-mail e senha estiverem corretos; senão, false.
    public function autenticar($email, $senha) {
        $sth = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $sth->bindValue(':email', $email, PDO::PARAM_STR);
        $sth->execute();
        $u = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$u) {
            return false;
        }

        $ok = false;
        if (password_verify($senha, $u['senha'])) {
            $ok = true;
        } elseif (strlen($u['senha']) === 64 && ctype_xdigit($u['senha'])
                  && hash_equals(strtolower($u['senha']), hash('sha256', $senha))) {
            // Senha antiga criada com SHA2() do SQL de teste do professor:
            // aceita uma vez e já converte para password_hash.
            $ok = true;
            $up = $this->pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
            $up->bindValue(':senha', password_hash($senha, PASSWORD_DEFAULT), PDO::PARAM_STR);
            $up->bindValue(':id', $u['id'], PDO::PARAM_INT);
            $up->execute();
        }

        if (!$ok) {
            return false;
        }
        unset($u['senha']);
        return $u;
    }
}
