<?php
session_start();
include "../../db/conexao.php";
include_once "../../Controller/Session/Session.php";
require_once "../../Controller/MailController.php"; 

if (!SessionController::isLoggedIn()) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = $_POST['id_pedido'];
    $tipo_pagamento = $_POST['tipo_pagamento'];
    $valor = str_replace(',', '.', str_replace('.', '', $_POST['valor']));

    $stmt = $conexao->prepare("
        UPDATE pedido 
        SET tipo_pagamento = ?, id_status = (SELECT id_status FROM status WHERE tipo_status='pago')
        WHERE id_pedido = ?
    ");
    $stmt->bind_param("si", $tipo_pagamento, $id_pedido);

    if ($stmt->execute()) {

        $stmtItems = $conexao->prepare("
            SELECT id_produto, quantidade 
            FROM pedido_item 
            WHERE id_pedido = ?
        ");
        $stmtItems->bind_param("i", $id_pedido);
        $stmtItems->execute();
        $resultItems = $stmtItems->get_result();

        while ($item = $resultItems->fetch_assoc()) {
            $stmtUpdate = $conexao->prepare("
                UPDATE produto 
                SET estoque_produto = estoque_produto - ? 
                WHERE id_produto = ? AND estoque_produto >= ?
            ");
            $stmtUpdate->bind_param("iii", $item['quantidade'], $item['id_produto'], $item['quantidade']);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }

        $stmtItems->close();

        $userId = SessionController::getUserId();
        $stmtUser = $conexao->prepare("SELECT email_user, nome_user FROM usuarios WHERE id_user = ?");
        $stmtUser->bind_param("i", $userId);
        $stmtUser->execute();
        $userData = $stmtUser->get_result()->fetch_assoc();
        $stmtUser->close();

        $email = $userData['email_user'];
        $nome = $userData['nome_user'];

        $body = "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #f4f4f9;
                    color: #333;
                    padding: 30px;
                }
                .container {
                    background: #fff;
                    max-width: 600px;
                    margin: 0 auto;
                    border-radius: 12px;
                    padding: 25px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                h2 {
                    color: #2d89ef;
                    text-align: center;
                }
                .status {
                    background: #28a745;
                    color: #fff;
                    padding: 10px 15px;
                    border-radius: 8px;
                    text-align: center;
                    margin: 15px 0;
                    font-weight: bold;
                }
                .footer {
                    text-align: center;
                    color: #666;
                    font-size: 14px;
                    margin-top: 30px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Pagamento Confirmado!</h2>
                <p>Olá, <strong>$nome</strong> 👋</p>
                <p>Seu pagamento do pedido <strong>#$id_pedido</strong> foi confirmado com sucesso.</p>

                <div class='status'>Status: Pago ✅</div>

                <p>Forma de pagamento: <strong>$tipo_pagamento</strong></p>
                <p>Valor total: <strong>R$ " . number_format($valor, 2, ',', '.') . "</strong></p>

                <p>Agradecemos por sua compra! Em breve seu pedido será processado e enviado.</p>

                <div class='footer'>
                    © " . date('Y') . " - Sistema de Pedidos<br>
                    Esta é uma mensagem automática. Por favor, não responda.
                </div>
            </div>
        </body>
        </html>
        ";

        MailController::sendMail($email, "Pagamento Confirmado - Pedido #$id_pedido", $body);

        echo "<div class='alert alert-success text-center mt-5'>
                Pagamento registrado, estoque atualizado e e-mail enviado com sucesso!
              </div>";
        echo "<div class='text-center mt-3'>
                <a href='../telainicial/index.php' class='btn btn-primary'>Voltar ao Início</a>
              </div>";

    } else {
        echo "<div class='alert alert-danger text-center mt-5'>
                Erro ao processar pagamento.
              </div>";
    }

    $stmt->close();
}

$conexao->close();
?>
