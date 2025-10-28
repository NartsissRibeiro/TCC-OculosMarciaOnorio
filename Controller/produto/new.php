<?php
include "../../db/conexao.php";

$name_produto = $_POST['name_produto'];
$desc_produto = $_POST['desc_produto'];
$price_produto = $_POST['price_produto'];


$target_dir = "../../assets/img/"; 
$image_name = "";

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($image_ext, $allowed_ext)) {
        die("Formato de arquivo inválido. Permitido: JPG, JPEG, PNG, GIF.");
    }

    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        die("Arquivo muito grande. O tamanho máximo permitido é 2MB.");
    }

    $image_name = uniqid() . "." . $image_ext;
    $target_file = $target_dir . $image_name;

    if (!move_uploaded_file($image_tmp, $target_file)) {
        die("Erro ao salvar a imagem.");
    }
}
$estoque = $_POST['estoque'];
$id_material = $_POST['id_material'];
$id_cor = $_POST['id_cor'];

$comando_sql = "INSERT INTO produto (nome_produto, desc_produto, preco_produto, imagem_produto, estoque_produto, id_material, id_cor) 
                VALUES ('$name_produto', '$desc_produto', '$price_produto', '$image_name', '$estoque', '$id_material', '$id_cor')";

if (mysqli_query($conexao, $comando_sql)) {
    header('Location: ../../views/produto/index.php');
} else {
    echo "Error: " . $comando_sql . "<br>" . mysqli_error($conexao);
}
?>
