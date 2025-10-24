<?php
include '../../db/conexao.php';
require_once '../session/session.php';
//SessionController::startSession(); // Garante que a sessão está ativa

// Verifica se os dados foram enviados
if (isset($_POST['id_produto'], $_POST['nome_produto'], $_POST['preco_produto'], $_POST['quantidade'])) {

    $idproduto = (int)$_POST['id_produto'];
    $nomeproduto = $_POST['nome_produto'];
    $precoproduto = (float)$_POST['preco_produto'];
    $quantidade = (int)$_POST['quantidade'];

    // --- Usuário logado → salva no banco ---
    if (SessionController::isLoggedIn()) {
        $iduser = SessionController::getUserId();

        // Verifica se o produto já está no carrinho do usuário
        $sqlCheck = "SELECT qtd_carrinho FROM carrinho WHERE id_user = ? AND id_produto = ?";
        $stmtCheck = $conexao->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $iduser, $idproduto);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $novaQtd = $row['qtd_carrinho'] + $quantidade;

            $sqlUpdate = "UPDATE carrinho SET qtd_carrinho = ? WHERE id_user = ? AND id_produto = ?";
            $stmtUpdate = $conexao->prepare($sqlUpdate);
            $stmtUpdate->bind_param("iii", $novaQtd, $iduser, $idproduto);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        } else {
            $sqlInsert = "INSERT INTO carrinho (id_user, id_produto, qtd_carrinho) VALUES (?, ?, ?)";
            $stmtInsert = $conexao->prepare($sqlInsert);
            $stmtInsert->bind_param("iii", $iduser, $idproduto, $quantidade);
            $stmtInsert->execute();
            $stmtInsert->close();
        }

        $stmtCheck->close();
        $conexao->close();
        header('Location: ../../views/carrinho/index.php');
        exit();

    } else {
       // --- Usuário NÃO logado → salva na sessão ---
if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Se o produto já existir no carrinho como array, soma a quantidade
if (isset($_SESSION['carrinho'][$idproduto]) && is_array($_SESSION['carrinho'][$idproduto])) {
    $_SESSION['carrinho'][$idproduto]['quantidade'] += $quantidade;
} else {
    // Caso seja scalar ou não exista, substitui por array completo
    $_SESSION['carrinho'][$idproduto] = [
        'nome' => $nomeproduto,
        'preco' => $precoproduto,
        'quantidade' => $quantidade
    ];
}


        $conexao->close();
        //$message = "Produto adicionado ao carrinho!";
        header('Location: ../../views/carrinho/index.php');
        exit();
    }

} else {
    echo "<div class='alert alert-warning'>Dados de produto ou quantidade não recebidos.</div>";
}
?>
