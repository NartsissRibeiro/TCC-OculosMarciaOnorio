<?php
header('Content-Type: application/json');
include '../../db/conexao.php';

if (isset($_GET['cep']) && preg_match('/^\d{8}$/', $_GET['cep'])) {
    $cep = $_GET['cep'];
    
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data && !isset($data['erro'])) {
        $bairro = $data['bairro'] ?? '';
        $cidade = $data['localidade'] ?? '';
        $uf = $data['uf'] ?? '';

        $sql_cidade = "SELECT id_cidade, nome_cidade FROM cidade WHERE nome_cidade = ? AND uf = ?";
        $stmt_cidade = mysqli_prepare($conexao, $sql_cidade);
        mysqli_stmt_bind_param($stmt_cidade, 'ss', $cidade, $uf);
        mysqli_stmt_execute($stmt_cidade);
        $result_cidade = mysqli_stmt_get_result($stmt_cidade);
        $cidade_data = mysqli_fetch_assoc($result_cidade);
        mysqli_stmt_close($stmt_cidade);

        $id_bairro = null;
        $nome_bairro = null;
        if ($bairro && $cidade_data) {
            $sql_bairro = "SELECT id_bairro, nome_bairro FROM bairro WHERE nome_bairro = ? AND id_cidade = ?";
            $stmt_bairro = mysqli_prepare($conexao, $sql_bairro);
            mysqli_stmt_bind_param($stmt_bairro, 'si', $bairro, $cidade_data['id_cidade']);
            mysqli_stmt_execute($stmt_bairro);
            $result_bairro = mysqli_stmt_get_result($stmt_bairro);
            $bairro_data = mysqli_fetch_assoc($result_bairro);
            mysqli_stmt_close($stmt_bairro);

            if ($bairro_data) {
                $id_bairro = $bairro_data['id_bairro'];
                $nome_bairro = $bairro_data['nome_bairro'];
            }
        }

        echo json_encode([
            'id_cidade' => $cidade_data ? $cidade_data['id_cidade'] : null,
            'nome_cidade' => $cidade_data ? $cidade_data['nome_cidade'] : null,
            'id_bairro' => $id_bairro,
            'nome_bairro' => $nome_bairro
        ]);
    } else {
        echo json_encode(['error' => 'CEP não encontrado']);
    }
} else {
    echo json_encode(['error' => 'CEP inválido']);
}

mysqli_close($conexao);
exit();
?>