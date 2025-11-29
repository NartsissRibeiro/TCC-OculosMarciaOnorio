<?php
include "../partials/header.php";
include "../partials/navbar.php";
include "../../db/conexao.php";
require_once '../../controller/session/session.php';

if (!SessionController::isLoggedIn()) {
    header('Location: ../usuario/index.php');
    include "../partials/footer.php";
    exit;
}

$userId = SessionController::getUserId();
$pedidoId = $_GET['idpedido'] ?? null;

if (!$pedidoId) {
    echo "<div class='alert alert-danger'>Pedido não especificado.</div>";
    include "../partials/footer.php";
    exit;
}

$stmt = $conexao->prepare("
    SELECT 
        p.id_pedido,
        l.logradouro,
        b.nome_bairro,
        b.cep,
        c.nome_cidade,
        e.nome_estado,
        p.complemento,
        l.id_logradouro,
        b.id_bairro,
        c.id_cidade,
        e.id_estado
    FROM pedido p
    JOIN bairro b ON p.id_bairro = b.id_bairro
    JOIN cidade c ON p.id_cidade = c.id_cidade
    JOIN estado e ON c.id_estado = e.id_estado
    JOIN logradouro l ON p.id_logradouro = l.id_logradouro
    WHERE p.id_pedido = ? AND p.id_user = ?
    LIMIT 1
");
$stmt->bind_param("ii", $pedidoId, $userId);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<div class='alert alert-danger'>Pedido não encontrado.</div>";
    include "../partials/footer.php";
    exit;
}

$endereco = $res->fetch_assoc();

$numero = '';
$rua = $endereco['logradouro'];
if (strpos($rua, ',') !== false) {
    list($rua, $numero) = explode(',', $rua, 2);
    $rua = trim($rua);
    $numero = trim($numero);
}

?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Editar Endereço do Pedido #<?php echo $pedidoId; ?></h2>
        </div>
        <div class="card-body">
            <div id="msgPlaceholder" class="mb-3"></div>
            <form id="editarEnderecoForm" action="../../controller/pedido/edit_endereco.php" method="POST">
                <input type="hidden" name="idpedido" value="<?php echo $pedidoId; ?>">

                <div class="mb-3">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text" id="cep" name="cep" class="form-control" required value="<?php echo htmlspecialchars($endereco['cep']); ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="rua" class="form-label">Rua</label>
                        <input type="text" id="rua" name="rua" class="form-control" required value="<?php echo htmlspecialchars($rua); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" id="numero" name="numero" class="form-control" required value="<?php echo htmlspecialchars($numero); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" id="bairro" name="bairro" class="form-control" required value="<?php echo htmlspecialchars($endereco['nome_bairro']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" id="cidade" name="cidade" class="form-control" required value="<?php echo htmlspecialchars($endereco['nome_cidade']); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" id="estado" name="estado" class="form-control" required value="<?php echo htmlspecialchars($endereco['nome_estado']); ?>">
                </div>

                <div class="mb-3">
                    <label for="complemento" class="form-label">Complemento</label>
                    <input type="text" id="complemento" name="complemento" class="form-control" value="<?php echo htmlspecialchars($endereco['complemento']); ?>">
                </div>

                <button type="submit" class="btn btn-primary">Atualizar Endereço</button>
                <a href="pedido.php" class="btn btn-secondary">Voltar</a>
            </form>
        </div>
    </div>
</div>

<?php include "../partials/footer.php"; ?>

<script>
(function() {
    const cepInput = document.getElementById('cep');
    const ruaInput = document.getElementById('rua');
    const bairroInput = document.getElementById('bairro');
    const cidadeInput = document.getElementById('cidade');
    const estadoInput = document.getElementById('estado');
    const msgPlaceholder = document.getElementById('msgPlaceholder');

    function normalizeCep(value) {
        return value.replace(/\D/g, '');
    }

    function showMessage(text, type = 'danger') {
        msgPlaceholder.innerHTML = `<div class="alert alert-${type}" role="alert">${text}</div>`;
        setTimeout(() => { if (msgPlaceholder) msgPlaceholder.innerHTML = ''; }, 5000);
    }

    async function buscarCep(cep) {
        try {
            const resp = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            if (!resp.ok) throw new Error('Erro na requisição ViaCEP');
            const data = await resp.json();
            return data;
        } catch (err) {
            console.error(err);
            throw err;
        }
    }

    async function onCepChange() {
        const raw = normalizeCep(cepInput.value);
        if (raw.length !== 8) {
            showMessage('Digite um CEP válido com 8 dígitos (somente números).', 'warning');
            return;
        }

        showMessage('Consultando CEP...', 'info');

        try {
            const data = await buscarCep(raw);
            if (data.erro) {
                showMessage('CEP não encontrado.', 'warning');
                ruaInput.value = '';
                bairroInput.value = '';
                cidadeInput.value = '';
                estadoInput.value = '';
                return;
            }

            ruaInput.value = data.logradouro || '';
            bairroInput.value = data.bairro || '';
            cidadeInput.value = data.localidade || '';
            estadoInput.value = data.uf || '';
            showMessage('Endereço preenchido com sucesso.', 'success');
        } catch (err) {
            showMessage('Erro ao consultar CEP. Verifique sua conexão.', 'danger');
        }
    }

    cepInput.addEventListener('blur', onCepChange);
    cepInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            onCepChange();
        }
    });
})();
</script>