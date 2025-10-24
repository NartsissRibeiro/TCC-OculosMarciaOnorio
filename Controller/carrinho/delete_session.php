<?php
session_start();

// Verifica se o ID foi passado pela URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Garante que é um número

    // Verifica se o carrinho existe e é um array
    if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
        // Se o produto existe no carrinho, remove
        if (isset($_SESSION['carrinho'][$id])) {
            unset($_SESSION['carrinho'][$id]);
        }
    }
}

// Redireciona de volta para a página do carrinho
header("Location: ../../views/carrinho/index.php");
exit;
