<?php
require_once '../../db/conexao.php';
require_once '../Session/session.php';
session_start();

if (!SessionController::isLoggedIn()) {
    header('Location: ../../views/usuario/perfil.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeUser = trim($_POST['nome']);
    $emailUser = trim($_POST['email']);
    $userId = SessionController::getUserId();

    if (empty($nomeUser) || empty($emailUser)) {
        header('Location: ../../views/usuario/editar_perfil.php?error=empty_fields');
        exit();
    }

    $stmt = $conexao->prepare("UPDATE usuarios SET nome_user = ?, email_user = ? WHERE id_user = ?");
    $stmt->bind_param('ssi', $nomeUser, $emailUser, $userId);

    if ($stmt->execute()) {
        $_SESSION['nome_user'] = $nomeUser;
        $_SESSION['email_user'] = $emailUser;

        header('Location: ../../views/usuario/perfil.php?success=profile_updated');
        exit();
    } else {
        header('Location: ../../views/usuario/editar_perfil.php?error=update_failed');
        exit();
    }
    $stmt->close();
} else {
    header('Location: ../../views/usuario/perfil.php');
    exit();
}
?>