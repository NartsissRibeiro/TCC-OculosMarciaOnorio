<?php
include '../Session/Session.php';
session_start();
date_default_timezone_set('America/Sao_Paulo');
include '../../db/conexao.php';

// Verifica se o usuário está logado
if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];

    // Recebe e valida dados do formulário
    $total = isset($_POST['total']) ? floatval($_POST['total']) : 0;
    $id_pagamento= intval($_POST['id_pagamento']); // FK pagamento
    $id_bairro = intval($_POST['id_bairro']); // FK bairro
    $id_logradouro = intval($_POST['id_logradouro']); // FK logradouro
    $id_cidade = intval($_POST['id_cidade']); // FK cidade
    $complemento = !empty($_POST['complemento']) ? $_POST['complemento'] : NULL;
    $mensagem = !empty($_POST['mensagem']) ? $_POST['mensagem'] : NULL;

    // Prepara o INSERT
    $query = "INSERT INTO pedido 
                (id_user, valor_total, id_pagamento, id_bairro, complemento, mensagem, id_logradouro, id_cidade)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexao->prepare($query);
    if (!$stmt) {
        die("Erro no prepare: " . $conexao->error);
    }

    $stmt->bind_param("idii ssii",
        $id_user,
        $total,
        $id_pagamento,
        $id_bairro,
        $complemento,
        $mensagem,
        $id_logradouro,
        $id_cidade
    );

    if ($stmt->execute()) {
        $id_pedido = $stmt->insert_id;

        header("Location: finalizar_itens.php?id_pedido=" . $id_pedido);
        exit();
    } else {
        echo "Erro ao inserir pedido: " . $stmt->error;
        exit();
    }
} else {
    echo "Erro: Usuário não logado.";
    exit();
}
?>
