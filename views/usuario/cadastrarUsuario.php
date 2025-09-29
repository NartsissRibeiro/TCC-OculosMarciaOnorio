<?php
include "../../db/conexao.php";
include "../../controller/session/session.php";
session_start();

$msgErro = '';
$msgSucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $senha = trim($_POST['senha']);
    $tipo = 'cliente'; 

    if (empty($name) || empty($email) || empty($telefone) || empty($senha)) {
        $msgErro = "Todos os campos são obrigatórios!";
        header("Location: ../../views/usuario/new.php?msgErro=" . urlencode($msgErro));
        exit();
    }

    //if (!preg_match("/^\([0-9]{2}\)[0-9]{4,5}-[0-9]{4}$/", $telefone)) {
        //$msgErro = "Telefone inválido! Formato esperado: (xx)xxxxx-xxxx";
        //header("Location: ../../views/usuario/new.php?msgErro=" . urlencode($msgErro));
        //exit();
    //}

    if (strlen($senha) < 3) {
        $msgErro = "A senha deve ter no mínimo 3 caracteres!";
        header("Location: ../../views/usuario/new.php?msgErro=" . urlencode($msgErro));
        exit();
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $verificaEmail = $conexao->prepare("SELECT id_user FROM usuarios WHERE email_user = ?");
    $verificaEmail->bind_param("s", $email);
    $verificaEmail->execute();
    $verificaEmail->store_result();

    if ($verificaEmail->num_rows > 0) {
        $msgErro = 'Já existe um usuário com este e-mail!';
        $verificaEmail->close();
        $conexao->close();
        header("Location: ../../views/usuario/new.php?msgErro=" . urlencode($msgErro));
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
        $iduser = $stmt->insert_id;
        $verificaEmail->close();
        $stmt->close();
        $conexao->close();

        SessionController::login($iduser, $name, $email, $tipo);
        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['flash_msg'] = "Usuário cadastrado com sucesso!";
        header("Location: ../../views/telainicial/index.php");
        exit();

    } else {
        $msgErro = "Erro ao inserir os dados: " . $stmt->error;
        $verificaEmail->close();
        $stmt->close();
        $conexao->close();
        header("Location: ../../views/usuario/new.php?msgErro=" . urlencode($msgErro));
        exit();
    }
}
?>
