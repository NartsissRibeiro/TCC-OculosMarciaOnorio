<?php
        include "../../db/conexao.php";
        
        $id_produto = $_GET['id_produto'];
        $name_produto = $_GET['nome_produto'];
        $description = $_GET['desc_produto'];
        $price_produto = $_GET['preco_produto'];
        $estoque_produto = $_GET['estoque_produto'];
        $id_cor = $_GET['id_cor'];
        $id_material = $_GET['id_material'];

    $sql = "UPDATE produto SET nome_produto='$name_produto',desc_produto='$description',preco_produto='$price_produto',estoque_produto='$estoque_produto',id_cor='$id_cor',id_material='$id_material'  WHERE id_produto=$id_produto";

    if ($conexao->query($sql) === TRUE) {
    //echo "alteração feita com sucesso";
    header('location: ../../views/produto/index.php');
    } else {
    echo "Error updating record: " . $conexao->error;
    }

    $conexao->close();
?>

