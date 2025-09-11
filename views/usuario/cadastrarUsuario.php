<?php
include "../../db/conexao.php"; 

$msgErro = '';
$msgSucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $senha = trim($_POST['senha']);
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $tipo = 'cliente'; 

    $verificaEmail = $conexao->prepare("SELECT id_user FROM usuarios WHERE email_user = ?");
    $verificaEmail->bind_param("s", $email);
    $verificaEmail->execute();
    $verificaEmail->store_result();

    if ($verificaEmail->num_rows > 0) {
        $msgErro = 'Já existe um usuário com este e-mail!';
        $verificaEmail->close();
        $conexao->close();
        header("Location: ../../views/usuario/index.php?msgErro=" . urlencode($msgErro));
        exit();
    }

    $sql = "INSERT INTO usuarios (nome_user, email_user, telefone_user, senha_user, tipo_user) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        $msgErro = "Erro ao preparar a consulta: " . $conexao->error;
        $verificaEmail->close();
        $conexao->close();
        header("Location: ./index.php?msgErro=" . urlencode($msgErro));
        exit();
    }

    $stmt->bind_param("sssss", $name, $email, $telefone, $senhaHash, $tipo);

    if ($stmt->execute()) {
        $verificaEmail->close();
        $stmt->close();
        $conexao->close();
        $msgSucesso = "Usuário cadastrado com sucesso!";
        header("Location: ../../views/usuario/index.php?msgSucesso=" . urlencode($msgSucesso));
        exit();
    } else {
        $msgErro = "Erro ao inserir os dados: " . $stmt->error;
        $verificaEmail->close();
        $stmt->close();
        $conexao->close();
        header("Location: ../../views/usuario/index.php?msgErro=" . urlencode($msgErro));
        exit();
    }
}
?>