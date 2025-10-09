<?php
include "../partials/header.php";
include "../partials/navbar.php";
include "../../db/conexao.php";
include_once "../../Controller/Session/Session.php";

if (!SessionController::isLoggedIn()) {
    header("Location: ../usuario/index.php?message=" . urlencode("Faça login para pagar"));
    exit;
}

$userId = SessionController::getUserId();
$pedidoId = intval($_GET['id_pedido'] ?? 0);
if (!$pedidoId) {
    die("Pedido inválido.");
}

// Carrega pedido e total (valide que o pedido pertence ao usuário)
$stmt = $conexao->prepare("SELECT id_pedido, valor_total, id_status FROM pedido WHERE id_pedido = ? AND id_user = ?");
$stmt->bind_param("ii", $pedidoId, $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Pedido não encontrado.");
}
$pedido = $res->fetch_assoc();
$total = number_format($pedido['valor_total'], 2, ',', '.');
?>

<div class="container mt-5">
  <div class="card">
    <div class="card-header text-center">
      <h2>Pagar Pedido #<?php echo $pedidoId; ?></h2>
      <small>Total: R$ <?php echo $total; ?></small>
    </div>
    <div class="card-body">
      <form id="simPaymentForm" method="POST" action="../../controller/pagamento/process.php">
        <input type="hidden" name="id_pedido" value="<?php echo $pedidoId; ?>">
        <input type="hidden" name="valor" value="<?php echo number_format($pedido['valor_total'], 2, '.', ''); ?>">

        <div class="mb-3">
          <label class="form-label">Método de Pagamento</label>
          <select id="metodoPagamento" name="metodo_pagamento" class="form-select" required>
            <option value="pix">Pix (simulado)</option>
            <option value="cartao">Cartão de Crédito (simulado)</option>
            <option value="boleto">Boleto (simulado)</option>
            <option value="dinheiro">Dinheiro (na entrega)</option>
          </select>
        </div>

        <!-- Campos de cartão (aparecem só se método = cartao) -->
        <div id="cartaoFields" style="display:none;">
          <div class="mb-3">
            <label class="form-label">Nome no Cartão</label>
            <input type="text" name="nome_cartao" class="form-control" maxlength="100">
          </div>
          <div class="mb-3 row">
            <div class="col">
              <label class="form-label">Número do Cartão</label>
              <input type="text" name="numero_cartao" class="form-control" maxlength="19" placeholder="1111 2222 3333 4444">
            </div>
            <div class="col-4">
              <label class="form-label">Validade</label>
              <input type="text" name="validade_cartao" class="form-control" maxlength="5" placeholder="MM/AA">
            </div>
            <div class="col-2">
              <label class="form-label">CVV</label>
              <input type="text" name="cvv_cartao" class="form-control" maxlength="4" placeholder="123">
            </div>
          </div>
        </div>

        <!-- Mensagem / resultado -->
        <div id="paymentMsg" class="mt-3"></div>

        <div class="d-flex justify-content-between mt-4">
          <a href="../pedido/new.php?id_pedido=<?php echo $pedidoId; ?>" class="btn btn-warning">Voltar ao Pedido</a>
          <button type="submit" class="btn btn-primary" id="btnPagar">Simular Pagamento (R$ <?php echo $total; ?>)</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include "../partials/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const metodo = document.getElementById('metodoPagamento');
  const cartaoFields = document.getElementById('cartaoFields');
  const form = document.getElementById('simPaymentForm');
  const msg = document.getElementById('paymentMsg');
  const btn = document.getElementById('btnPagar');

  metodo.addEventListener('change', () => {
    cartaoFields.style.display = metodo.value === 'cartao' ? 'block' : 'none';
  });

  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    // validações básicas
    const metodoVal = metodo.value;
    if (!metodoVal) return;

    // Simulação: desabilita botão e mostra carregando
    btn.disabled = true;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processando...';

    // simula "tempo de processamento"
    await new Promise(r => setTimeout(r, 1200));

    // simulador simples: 85% chance de sucesso, 15% falha (ajuste se quiser)
    const sucesso = Math.random() < 0.85;

    if (!sucesso) {
      // mostra erro sem enviar ao servidor (simulação de falha)
      msg.innerHTML = '<div class="alert alert-danger">Pagamento recusado (simulação). Tente novamente.</div>';
      btn.disabled = false;
      btn.innerHTML = originalText;
      return;
    }

    // Em caso de sucesso, enviamos o POST real para o backend que atualiza o pedido
    // Criamos um FormData e fazemos fetch POST
    const formData = new FormData(form);
    try {
      const resp = await fetch(form.action, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });
      const text = await resp.text();
      // O backend deve redirecionar; se receber HTML, mostramos ou redirecionamos manualmente
      if (resp.redirected) {
        window.location = resp.url;
      } else {
        // Espera que server retorne JSON com {success: true, redirect: 'url'}
        try {
          const json = JSON.parse(text);
          if (json.success && json.redirect) {
            window.location = json.redirect;
          } else {
            msg.innerHTML = '<div class="alert alert-success">Pagamento simulado com sucesso, mas o redirecionamento falhou.</div>';
            btn.disabled = false;
            btn.innerHTML = originalText;
          }
        } catch (err) {
          // fallback: mostra retorno do servidor
          msg.innerHTML = '<div class="alert alert-success">Pagamento simulado com sucesso.</div>' + text;
          btn.disabled = false;
          btn.innerHTML = originalText;
        }
      }
    } catch (err) {
      msg.innerHTML = '<div class="alert alert-danger">Erro na comunicação com o servidor.</div>';
      btn.disabled = false;
      btn.innerHTML = originalText;
    }
  });
});
</script>
