<?php
class SessionController {

    // Inicia a sessão se ainda não estiver ativa
    private static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Login do usuário
    public static function login($iduser, $name, $email, $tipo) {
        self::startSession();
        $_SESSION['id_user'] = $iduser;
        $_SESSION['nome_user'] = $name;
        $_SESSION['email_user'] = $email;
        $_SESSION['tipo_user'] = $tipo;

        // Se houver itens na sessão antes do login, migra para o banco
        self::migrarCarrinhoParaBanco($iduser);
    }

    // Logout do usuário
    public static function logout() {
        self::startSession();
        session_unset();
        session_destroy();
    }

    // Verifica se o usuário está logado
    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['id_user']);
    }

    // Retorna o ID do usuário logado
    public static function getUserId() {
        self::startSession();
        return $_SESSION['id_user'] ?? null;
    }

    // Retorna o nome do usuário logado
    public static function getUserName() {
        self::startSession();
        return $_SESSION['nome_user'] ?? null;
    }

    // Retorna o tipo do usuário logado (ex: admin, cliente, deslogado)
    public static function getUserTipo() {
        self::startSession();
        return $_SESSION['tipo_user'] ?? 'deslogado';
    }

    // Migra o carrinho da sessão para o banco quando o usuário faz login
    private static function migrarCarrinhoParaBanco($iduser) {
        include '../../db/conexao.php';
        self::startSession();

        if (empty($_SESSION['carrinho'])) return;

        foreach ($_SESSION['carrinho'] as $id_produto => $produto) {
            $quantidade = $produto['quantidade'];

            // Verifica se já existe no banco
            $sqlCheck = "SELECT qtd_carrinho FROM carrinho WHERE id_user = ? AND id_produto = ?";
            $stmtCheck = $conexao->prepare($sqlCheck);
            $stmtCheck->bind_param("ii", $iduser, $id_produto);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();

            if ($result->num_rows > 0) {
                // Atualiza quantidade
                $row = $result->fetch_assoc();
                $novaQtd = $row['qtd_carrinho'] + $quantidade;

                $sqlUpdate = "UPDATE carrinho SET qtd_carrinho = ? WHERE id_user = ? AND id_produto = ?";
                $stmtUpdate = $conexao->prepare($sqlUpdate);
                $stmtUpdate->bind_param("iii", $novaQtd, $iduser, $id_produto);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            } else {
                // Insere novo item
                $sqlInsert = "INSERT INTO carrinho (id_user, id_produto, qtd_carrinho) VALUES (?, ?, ?)";
                $stmtInsert = $conexao->prepare($sqlInsert);
                $stmtInsert->bind_param("iii", $iduser, $id_produto, $quantidade);
                $stmtInsert->execute();
                $stmtInsert->close();
            }

            $stmtCheck->close();
        }

        // Limpa o carrinho da sessão após migrar
        unset($_SESSION['carrinho']);
    }
}
