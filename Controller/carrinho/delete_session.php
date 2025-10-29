<?php
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
        if (isset($_SESSION['carrinho'][$id])) {
            unset($_SESSION['carrinho'][$id]);
        }
    }
}

header("Location: ../../views/carrinho/index.php");
exit;
