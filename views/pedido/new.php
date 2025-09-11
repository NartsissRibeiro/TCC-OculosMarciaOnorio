<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Márcia Onorio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/style.css">
  </head>
  <body>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirme seu Pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Total:</strong> <span id="modalTotal"></span></p>
        <p><strong>CEP:</strong> <span id="modalCEP"></span></p>
        <p><strong>Número:</strong> <span id="modalNumero"></span></p>
        <p class="text-capitalize"><strong>Forma de Pagamento:</strong> <span id="modalPagamento"></span></p>
        <p><strong>Mensagem Especial:</strong> <span id="modalMensagem"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success" form="pedidoForm">Confirmar Pedido</button>
      </div>
    </div>
  </div>
</div>
<!-- Script Modal -->
<script>
document.getElementById('finalizarPedido').addEventListener('click', function () {
    const total = parseFloat(document.querySelector("input[name='total']").value).toFixed(2);
    let cep = document.querySelector("input[name='cep']").value.trim();
    const numero = document.querySelector("input[name='numero']").value.trim();
    const pagamento = document.querySelector("select[name='pagamento']").value.trim();
    let mensagem = document.querySelector("textarea[name='mensagem']").value.trim();

    // Validação: CEP, número e forma de pagamento devem estar preenchidos
    if (!cep || !numero || !pagamento) {
        alert("Por favor, preencha todos os campos obrigatórios: CEP, Número e Forma de Pagamento.");
        return; // Não abre o modal
    }

    // Formatação do CEP (xxxxx-xxx)
    if (!cep.includes('-') && cep.length === 8) {
        cep = cep.slice(0, 5) + '-' + cep.slice(5);
    }

    // Capitalização da mensagem
    mensagem = mensagem.charAt(0).toUpperCase() + mensagem.slice(1) || 'Nenhuma mensagem.';

    // Atualiza os valores no modal
    document.getElementById('modalTotal').innerText = `R$ ${total.replace('.', ',')}`;
    document.getElementById('modalCEP').innerText = cep;
    document.getElementById('modalNumero').innerText = numero;
    document.getElementById('modalPagamento').innerText = pagamento;
    document.getElementById('modalMensagem').innerText = mensagem;

    // Abre o modal via JavaScript
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
});
</script>
?>



<!-- Script API -->

<script>
document.getElementById('cep').addEventListener('blur', function() {
    var cep = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos

    if (cep !== "") {
        // Expressão regular para validar o CEP
        var validacep = /^[0-9]{8}$/;
        if (validacep.test(cep)) {
            // Faz a requisição AJAX para a API ViaCEP
            var url = `https://viacep.com.br/ws/${cep}/json/`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        alert("CEP não encontrado.");
                    } else {
                        // Preenche os campos do formulário com os dados retornados
                        document.getElementById('rua').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                    }
                })
                .catch(error => {
                    console.error("Erro ao buscar o CEP:", error);
                    alert("Erro ao buscar o CEP.");
                });
        } else {
            alert("CEP inválido.");
        }
    }
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  </body>
</html>
