<?php
// __DIR__ garante que o caminho funcione de qualquer pasta (raiz, views, controllers)
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/helpers.php';

class Game {
    public $id;
    public $titulo;
    public $descricao;
    public $preco;
    public $estoque;
    public $categoria;
    public $imagem;
    private $pdo;

    public function __construct($atrib = []) {
        global $pdo;
        $this->pdo = $pdo;

        if (!empty($atrib)) {
            $this->id        = $atrib['id'] ?? null;
            $this->titulo    = $atrib['titulo'] ?? null;
            $this->descricao = $atrib['descricao'] ?? null;
            $this->preco     = $atrib['preco'] ?? null;
            $this->estoque   = $atrib['estoque'] ?? null;
            $this->categoria = $atrib['categoria'] ?? null;
            $this->imagem    = $atrib['imagem'] ?? null;
        }
    }

    // Valida os dados do objeto. Retorna lista de erros (vazia = tudo certo)
    public function validar() {
        $erros = [];
        $this->titulo    = trim((string)$this->titulo);
        $this->descricao = trim((string)$this->descricao);
        $this->categoria = trim((string)$this->categoria);

        if ($this->titulo === '' || tamanho($this->titulo) > 100) {
            $erros[] = 'O título é obrigatório e deve ter até 100 caracteres.';
        }
        if ($this->descricao === '') {
            $erros[] = 'A descrição é obrigatória.';
        }
        if (!is_numeric($this->preco) || $this->preco < 0 || $this->preco > 99999999.99) {
            $erros[] = 'Informe um preço válido (maior ou igual a zero).';
        }
        if (filter_var($this->estoque, FILTER_VALIDATE_INT) === false || $this->estoque < 0) {
            $erros[] = 'O estoque deve ser um número inteiro maior ou igual a zero.';
        }
        if ($this->categoria === '' || tamanho($this->categoria) > 50) {
            $erros[] = 'A categoria é obrigatória e deve ter até 50 caracteres.';
        }
        return $erros;
    }

    // Listar todos os games
    public function listarGames() {
        $sth = $this->pdo->query("SELECT * FROM games ORDER BY titulo ASC");
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar games por nome (ou parte do nome)
    public function buscarPorNome($nome) {
        $sth = $this->pdo->prepare("SELECT * FROM games WHERE titulo LIKE :nome ORDER BY titulo ASC");
        $nome = "%" . $nome . "%";
        $sth->bindValue(':nome', $nome, PDO::PARAM_STR);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cadastrar um novo game
    public function cadastrarGame() {
        $sth = $this->pdo->prepare("INSERT INTO games (titulo, descricao, preco, estoque,
            categoria, imagem, created_at, updated_at) VALUES (:titulo, :descricao, :preco,
            :estoque, :categoria, :imagem, NOW(), NOW())");
        $sth->bindValue(':titulo', $this->titulo, PDO::PARAM_STR);
        $sth->bindValue(':descricao', $this->descricao, PDO::PARAM_STR);
        $sth->bindValue(':preco', $this->preco, PDO::PARAM_STR);
        $sth->bindValue(':estoque', $this->estoque, PDO::PARAM_INT);
        $sth->bindValue(':categoria', $this->categoria, PDO::PARAM_STR);
        $sth->bindValue(':imagem', $this->imagem, PDO::PARAM_STR);

        return $sth->execute();
    }

    // Editar um game
    public function editarGame() {
        $sth = $this->pdo->prepare("UPDATE games SET titulo = :titulo, descricao = :descricao,
                                    preco = :preco, estoque = :estoque, categoria = :categoria,
                                    imagem = :imagem, updated_at = NOW()
                                    WHERE id = :id");
        $sth->bindValue(':id', $this->id, PDO::PARAM_INT);
        $sth->bindValue(':titulo', $this->titulo, PDO::PARAM_STR);
        $sth->bindValue(':descricao', $this->descricao, PDO::PARAM_STR);
        $sth->bindValue(':preco', $this->preco, PDO::PARAM_STR);
        $sth->bindValue(':estoque', $this->estoque, PDO::PARAM_INT);
        $sth->bindValue(':categoria', $this->categoria, PDO::PARAM_STR);
        $sth->bindValue(':imagem', $this->imagem, PDO::PARAM_STR);

        return $sth->execute();
    }

    // Deletar um game
    public function deletarGame() {
        $sth = $this->pdo->prepare("DELETE FROM games WHERE id = :id");
        $sth->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $sth->execute();
    }

    // Buscar um game por ID
    public function buscarGamePorId($id) {
        $sth = $this->pdo->prepare("SELECT * FROM games WHERE id = :id");
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    // Conta quantos games existem (usado no painel admin)
    public function contarGames() {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();
    }

    // Compra simulada: baixa o estoque só se houver quantidade suficiente.
    // Retorna true se conseguiu, false se não havia estoque.
    public function baixarEstoque($id, $quantidade) {
        $sth = $this->pdo->prepare("UPDATE games SET estoque = estoque - :qtd, updated_at = NOW()
                                    WHERE id = :id AND estoque >= :qtd2");
        $sth->bindValue(':qtd', $quantidade, PDO::PARAM_INT);
        $sth->bindValue(':qtd2', $quantidade, PDO::PARAM_INT);
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();

        return $sth->rowCount() > 0;
    }
}
