<?php
include "../../db/conexao.php";
require_once "../../controller/session/session.php";

if (!SessionController::isLoggedIn()) {
    die("Usuário não logado.");
}

$userId = SessionController::getUserId();
$idPedido = $_POST['idpedido'];
$cep = $_POST['cep'];
$rua = $_POST['rua'];
$numero = $_POST['numero'];
$bairroNome = $_POST['bairro'];
$cidadeNome = $_POST['cidade'];
$estadoNome = $_POST['estado'];
$complemento = $_POST['complemento'] ?? null;

$conexao->begin_transaction();

try {
    $stmt = $conexao->prepare("SELECT id_estado FROM estado WHERE nome_estado = ?");
    $stmt->bind_param("s", $estadoNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $estadoId = $res->fetch_assoc()['id_estado'];
    } else {
        $stmt = $conexao->prepare("INSERT INTO estado (nome_estado) VALUES (?)");
        $stmt->bind_param("s", $estadoNome);
        $stmt->execute();
        $estadoId = $conexao->insert_id;
    }

    // Atualizar cidade
    $stmt = $conexao->prepare("SELECT id_cidade FROM cidade WHERE nome_cidade = ?");
    $stmt->bind_param("s", $cidadeNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $cidadeId = $res->fetch_assoc()['id_cidade'];
    } else {
        $stmt = $conexao->prepare("INSERT INTO cidade (nome_cidade, id_estado) VALUES (?, ?)");
        $stmt->bind_param("si", $cidadeNome, $estadoId);
        $stmt->execute();
        $cidadeId = $conexao->insert_id;
    }

    // Atualizar bairro
    $stmt = $conexao->prepare("SELECT id_bairro FROM bairro WHERE nome_bairro = ?");
    $stmt->bind_param("s", $bairroNome);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $bairroId = $res->fetch_assoc()['id_bairro'];
    } else {
        $stmt = $conexao->prepare("INSERT INTO bairro (nome_bairro, cep) VALUES (?, ?)");
        $stmt->bind_param("ss", $bairroNome, $cep);
        $stmt->execute();
        $bairroId = $conexao->insert_id;
    }

    $logradouroCompleto = $rua . ', ' . $numero;
    $stmt = $conexao->prepare("INSERT INTO logradouro (logradouro, id_tipo) VALUES (?, 1)");
    $stmt->bind_param("s", $logradouroCompleto);
    $stmt->execute();
    $logradouroId = $conexao->insert_id;

    $stmt = $conexao->prepare("
        UPDATE pedido SET 
        id_bairro = ?, 
        id_cidade = ?, 
        id_logradouro = ?, 
        complemento = ?
        WHERE id_pedido = ? AND id_user = ?
    ");
    $stmt->bind_param("iiiisi", $bairroId, $cidadeId, $logradouroId, $complemento, $idPedido, $userId);
    $stmt->execute();

    $conexao->commit();
    header("Location: ../../views/usuario/pedido.php?message=Endereço atualizado com sucesso");
    exit;

} catch (Exception $e) {
    $conexao->rollback();
    echo "Erro ao atualizar endereço: " . $e->getMessage();
}
?>