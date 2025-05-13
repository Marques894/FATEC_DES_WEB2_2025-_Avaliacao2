<?php

session_start();
require_once 'classes/login.php';
$login = new Login();
// Verifica se o usuário está logado
// Se o usuário não estiver logado, redireciona para a página de login

require_once 'classes/DB.php';
$db = new DB();
// Intanciar a classe DB para usae os metodos !

$mensagem = "";
// Verifica se o formulário foi enviado

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'cadastrar') {
        $nome = $_POST['nome'];
        $preco = $_POST['preco'];
        $descricao = $_POST['descricao'];
        $categoria = $_POST['categoria'];

        try {
            $db->cadastrarProduto($nome, $preco, $descricao, $categoria);
            $mensagem = "Produto cadastrado com sucesso!";
        } catch (Exception $e) {
            $mensagem = "Erro ao cadastrar: " . $e->getMessage();
        }
    }

    if ($_POST['acao'] === 'remover' && isset($_POST['id_produto'])) {
        $idProduto = $_POST['id_produto'];
        try {
            $db->removerProduto($idProduto);
            $mensagem = "Produto removido com sucesso!";
        } catch (Exception $e) {
            $mensagem = "Erro ao remover: " . $e->getMessage();
        }
    }

    // Ação de Logout
    if ($_POST['acao'] === 'logout') {
        session_start();
        session_unset(); // Limpa
        session_destroy(); // Destroi
        header('Location: login.php'); // Redireciona para a pagina de login
        exit();
    }
}

$mostrarProdutos = isset($_GET['ver']) && $_GET['ver'] === '1';
$produtos = $mostrarProdutos ? $db->listarProdutos() : [];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Produtos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form input,
        form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        form button {
            background-color: #6c63ff;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
        }

        form button:hover {
            background-color: #5149d4;
        }

        .mensagem {
            text-align: center;
            margin: 10px 0;
            color: green;
        }

        .visualizar-btn {
            margin-top: 20px;
            text-align: center;
        }

        .visualizar-btn a {
            display: inline-block;
            background-color: #10b981;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .visualizar-btn a:hover {
            background-color: #059669;
        }

        table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #6c63ff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Estilo do botão de logout */
        .logout-btn {
            display: inline-block;
            background-color: #ff4d4d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.3s ease;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST" style="text-align: center;">
            <button type="submit" name="acao" value="logout" class="logout-btn">Logout</button>
        </form>
        
        <h2>Cadastrar Novo Produto</h2>

        <?php if ($mensagem) echo "<p class='mensagem'>$mensagem</p>"; ?>
        <!-- Caso o formulario seja enviad corretamente, é para aparecer uma mensagem de sucesso! -->


        <form method="POST">
            <input type="text" name="nome" placeholder="Nome do produto" required>
            <input type="number" step="0.01" name="preco" placeholder="Preço" required>
            <textarea name="descricao" placeholder="Descrição (opcional)"></textarea>
            <input type="text" name="categoria" placeholder="Categoria" required>
            <button type="submit" name="acao" value="cadastrar">Cadastrar</button>
        </form>

        <div class="visualizar-btn">
            <?php if ($mostrarProdutos): ?>
                <a href="?ver=0">Ocultar Produtos</a>
            <?php else: ?>
                <a href="?ver=1">Visualizar Produtos</a>
            <?php endif; ?>
        </div>

        <?php if ($mostrarProdutos): ?>
            <h2>Produtos Cadastrados</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Ações</th>
                </tr>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nome_produto']) ?></td>
                        <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($p['descricao']) ?></td>
                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id_produto" value="<?= $p['id'] ?>">
                                <button type="submit" name="acao" value="remover" style="background-color: #ff4d4d; color: white; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer;">
                                    Remover
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
