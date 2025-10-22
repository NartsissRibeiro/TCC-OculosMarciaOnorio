<?php
include "../partials/header.php";
include "../partials/navbar.php";
include "../../db/conexao.php";

if (!SessionController::isLoggedIn()) {
    echo "<div class='alert alert-danger'>Você precisa fazer login para finalizar o pedido. <a href='../usuario/index.php' class='btn btn-sm btn-warning ms-2'>Fazer Login</a></div>";
    include "../partials/footer.php";
    exit;
}
$userId = SessionController::getUserId();
$stmt = $conexao->prepare("SELECT 
                            cr.id_carrinho, 
                            cr.qtd_carrinho,
                            p.id_produto,
                            p.imagem_produto, 
                            p.nome_produto, 
                            p.preco_produto, 
                            m.nome_material, 
                            c.nome_cor
                        FROM carrinho cr
                        INNER JOIN produto p ON cr.id_produto = p.id_produto
                        LEFT JOIN material m ON p.id_material = m.id_material
                        LEFT JOIN cor c ON p.id_cor = c.id_cor
                        WHERE cr.id_user = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if (mysqli_num_rows($result) === 0) {
    echo "<div class='alert alert-warning'>Seu carrinho está vazio.</div>";
    include "../partials/footer.php";
    exit;
}

$total = 0;
$produtosCarrinho = [];
while ($row = mysqli_fetch_assoc($result)) {
    $total += $row['preco_produto'] * $row['qtd_carrinho'];
    $produtosCarrinho[] = $row;
}

mysqli_close($conexao);
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Finalizar Pedido</h2>
        </div>
        <div class="card-body">

            <h5>Produtos no Pedido:</h5>
            <ul class="list-group mb-3">
                <?php foreach ($produtosCarrinho as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo ucwords(htmlspecialchars($item['nome_produto'])); ?> 
                        x <?php echo $item['qtd_carrinho']; ?>
                        <span>R$ <?php echo number_format($item['preco_produto'] * $item['qtd_carrinho'], 2, ',', '.'); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h4>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></h4>

            <!-- Formulário de entrega -->
            <form id="finalizarForm" action="../../controller/carrinho/pedido.php" method="POST" novalidate>
                <input type="hidden" name="total" value="<?php echo number_format($total, 2, '.', ''); ?>">

                <?php 
                // Envia também os IDs e quantidades dos produtos
                foreach ($produtosCarrinho as $item): ?>
                    <input type="hidden" name="produtos[<?php echo $item['id_produto']; ?>]" value="<?php echo $item['qtd_carrinho']; ?>">
                <?php endforeach; ?>

               <form id="finalizarForm" action="../../controller/carrinho/pedido.php" method="POST" novalidate>
<<<<<<< HEAD
                <div class="text-center mb-3">
        
                    <div class="text-start mb-4">
=======
                <div class="mt-5">
        
                    <div class="mb-4">
>>>>>>> 7fbf063b0314e4ef7e3f8e62e5945154d518c096
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" id="cep" name="cep" class="form-control mx-auto"placeholder="Ex: 01001-000 ou 01001000" required>
                    </div>

<<<<<<< HEAD
                    <div class="text-start mb-4">
=======
                    <div class="mb-4">
>>>>>>> 7fbf063b0314e4ef7e3f8e62e5945154d518c096
                        <label for="rua" class="form-label">Rua</label>
                        <input type="text" id="rua" name="rua" class="form-control mx-auto"  readonly required>
                    </div>

<<<<<<< HEAD
                    <div class="text-start mb-4">
=======
                    <div class="mb-4">
>>>>>>> 7fbf063b0314e4ef7e3f8e62e5945154d518c096
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" id="bairro" name="bairro" class="form-control mx-auto"  readonly required>
                    </div>

                    
<<<<<<< HEAD
                    <div class="text-start mb-4">
=======
                    <div class="mb-4">
>>>>>>> 7fbf063b0314e4ef7e3f8e62e5945154d518c096
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" id="cidade" name="cidade"class="form-control mx-auto"   readonly required>
                    </div>

<<<<<<< HEAD
                    <div class="text-start mb-4">
=======
                    <div class="mb-4">
>>>>>>> 7fbf063b0314e4ef7e3f8e62e5945154d518c096
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" id="estado" name="estado" class="form-control mx-auto"  readonly required>
                    </div>

<<<<<<< HEAD
                    <div class="text-start mb-4">
=======
                    <div class="mb-4">
>>>>>>> 7fbf063b0314e4ef7e3f8e62e5945154d518c096
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" id="numero" name="numero" class="form-control mx-auto"  required>
                    </div>

<<<<<<< HEAD
                    <div class="text-start mb-4">
=======
                    <div class="mb-4">
>>>>>>> 7fbf063b0314e4ef7e3f8e62e5945154d518c096
                        <label for="complemento" class="form-label">Complemento</label>
                        <input type="text" id="complemento" name="complemento" class="form-control mx-auto"  placeholder="Opcional">
                    </div>

<<<<<<< HEAD
                    <div class="text-start mb-4">
                        <label for="mensagem" class="form-label">Mensagem (opcional)</label>
                        <textarea id="mensagem" name="mensagem" class="form-control" maxlength="250" rows="2" placeholder="Deixe uma mensagem para o pedido"></textarea>
                    </div>

                    <div class="text-start mb-4">
=======
                    <div class="mb-4">
                        <label for="mensagem" class="form-label">Mensagem (opcional)</label>
                        <textarea id="mensagem" name="mensagem" class="form-control" maxlength="250" rows="2" placeholder="Deixe uma mensagem para o pedido"></textarea>
                    </div>
                    
                    <!--<div class="col-md-6">
>>>>>>> 7fbf063b0314e4ef7e3f8e62e5945154d518c096
                        <label for="pagamento" class="form-label">Forma de Pagamento</label>
                        <select id="pagamento" name="pagamento" class="form-select" required>
                            <option value="" selected disabled>Selecione</option>
                            <option value="debito">Débito</option>
                            <option value="credito">Crédito</option>
                            <option value="pix">Pix</option>
                            <option value="dinheiro">Dinheiro</option>
                        </select>
                    </div>-->

                    <input type="hidden" name="total" value="<?php echo number_format($total, 2, '.', ''); ?>">

                    <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                        <a href="../carrinho/index.php" class="btn btn-secondary">Voltar ao Carrinho</a>
                        <button type="submit" name="acao" class="btn btn-success" value="nao_pago" id="btnEnviar">fazer pedido (R$ <?php echo number_format($total, 2, ',', '.'); ?>)</button>
                        <!--<button type="submit" name="acao" value="nao_pago" class="btn btn-danger">
                            Finalizar como Não Pago
                        </button>-->
                    </div>
                </div>
            </form>

            <div id="msgPlaceholder" class="mt-3"></div>
        </div>
    </div>
</div>

<?php include "../partials/footer.php"; ?>

<!-- JavaScript: consulta ViaCEP e preenche os campos -->
<script>
(function () {
    const cepInput = document.getElementById('cep');
    const ruaInput = document.getElementById('rua');
    const bairroInput = document.getElementById('bairro');
    const cidadeInput = document.getElementById('cidade');
    const estadoInput = document.getElementById('estado');
    const form = document.getElementById('finalizarForm');
    const msgPlaceholder = document.getElementById('msgPlaceholder');

    // Normaliza CEP (remove não-dígitos)
    function normalizeCep(value) {
        return value.replace(/\D/g, '');
    }

    // Mostra mensagem temporária
    function showMessage(text, type = 'danger') {
        msgPlaceholder.innerHTML = `<div class="alert alert-${type}" role="alert">${text}</div>`;
        // desaparece depois de 5s
        setTimeout(() => { if (msgPlaceholder) msgPlaceholder.innerHTML = ''; }, 5000);
    }

    // Busca ViaCEP
    async function buscarCep(cep) {
        try {
            const url = `https://viacep.com.br/ws/${cep}/json/`;
            const resp = await fetch(url);
            if (!resp.ok) throw new Error('Erro na requisição ViaCEP');
            const data = await resp.json();
            console.log('ViaCEP:', data);
            return data;
        } catch (err) {
            console.error(err);
            throw err;
        }
    }

    // Evento: ao sair do campo ou ao digitar 8 dígitos e pressionar Enter
    cepInput.addEventListener('blur', onCepChange);
    cepInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            onCepChange();
        }
    });

    async function onCepChange() {
        const raw = normalizeCep(cepInput.value);
        if (raw.length !== 8) {
            // Não limpa automaticamente (apenas alerta)
            showMessage('Digite um CEP válido com 8 dígitos (somente números).', 'warning');
            return;
        }

        // opcional: mostra carregando
        showMessage('Consultando CEP...', 'info');

        try {
            const data = await buscarCep(raw);
            if (data.erro) {
                showMessage('CEP não encontrado.', 'warning');
                // limpa os campos auto preenchíveis
                ruaInput.value = '';
                bairroInput.value = '';
                cidadeInput.value = '';
                estadoInput.value = '';
                return;
            }
            // Preenche os campos (uso de fallback para string vazia)
            ruaInput.value = data.logradouro || '';
            bairroInput.value = data.bairro || '';
            cidadeInput.value = data.localidade || '';
            estadoInput.value = data.uf || '';
            showMessage('Endereço preenchido com sucesso.', 'success');
        } catch (err) {
            showMessage('Erro ao consultar CEP. Verifique sua conexão.', 'danger');
        }
    }

    // Validação simples no submit: garante campos obrigatórios preenchidos
    form.addEventListener('submit', function (e) {
        // Se quiser prevenir e validar manualmente, use e.preventDefault() aqui.
        const requiredFields = [
            { el: cepInput, name: 'CEP' },
            { el: ruaInput, name: 'Rua' },
            { el: bairroInput, name: 'Bairro' },
            { el: cidadeInput, name: 'Cidade' },
            { el: estadoInput, name: 'Estado' },
            { el: document.getElementById('numero'), name: 'Número' },
            //{ el: document.getElementById('pagamento'), name: 'Pagamento' }
        ];

        for (let f of requiredFields) {
            if (!f.el || !f.el.value || f.el.value.trim() === '') {
                e.preventDefault();
                showMessage(`Preencha o campo obrigatório: ${f.name}`, 'warning');
                f.el.focus && f.el.focus();
                return false;
            }
        }

        // deixa o envio seguir para o seu controller PHP
        return true;
    });

})();
</script>

            </form>
        </div>
    </div>
</div>

<?php include "../partials/footer.php"; ?>
