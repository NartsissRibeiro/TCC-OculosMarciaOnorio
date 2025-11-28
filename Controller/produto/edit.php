<?php
include_once "../../db/conexao.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produto = (int)$_POST['id_produto'];
    $name_produto = mysqli_real_escape_string($conexao, $_POST['name_produto']);
    $desc_produto = mysqli_real_escape_string($conexao, $_POST['desc_produto']);
    $price_produto = (float)$_POST['price_produto'];
    $estoque_produto = (int)$_POST['estoque_produto'];
    $id_material = (int)$_POST['id_material'];
    $id_cor = (int)$_POST['id_cor'];

    $sql_update = "UPDATE produto SET 
        nome_produto = '$name_produto',
        preco_produto = '$price_produto',
        estoque_produto = '$estoque_produto',
        id_material = '$id_material',
        id_cor = '$id_cor'
        WHERE id_produto = $id_produto";

    if ($conexao->query($sql_update)) {
        header("Location: ../../views/produto/index.php?status=sucesso");
        exit;
    } else {
        header("Location: ../../views/produto/edit.php?id_produto=$id_produto&erro=" . urlencode($conexao->error));
        exit;
    }
}
