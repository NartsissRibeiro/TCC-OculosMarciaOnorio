<?php
require_once 'session.php';

session_start();

if (SessionController::isLoggedIn()) {
    echo "Sessão ativa!<br>";
    echo "ID do usuário: " . SessionController::getUserId() . "<br>";
    echo "Nome do usuário: " . SessionController::getUsernome() . "<br>";
    echo "Tipo de usuário: " . SessionController::getUsertipo() . "<br>";
} else {
    echo "Nenhuma sessão ativa. Usuário não está logado.";
}
?>