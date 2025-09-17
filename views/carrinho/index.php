<?php include "../partials/header.php"; ?>
<?php include "../partials/navbar.php"; ?>
<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Carrinho de Compras</h2>
        </div>
        <div class="card-body">
        <?php
        include "../../db/conexao.php";
           if (SessionController::isLoggedIn()) {
    $userId = SessionController::getUserId();

    $stmt = $conexao->prepare("SELECT 
                                cr.id_carrinho, 
                                cr.qtd_carrinho,
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

    if (mysqli_num_rows($result) > 0) {
        $total = 0;
        echo "<table class='table table-striped'>
                <thead class='table-dark'>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Material</th>
                        <th>Cor</th>
                        <th>Quantidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            $total += $row['preco_produto'] * $row['qtd_carrinho'];

            echo "<tr>";
            //echo "<td>" . htmlspecialchars($row['imagem_produto']) . "</td>";
            echo "<td>" . ucwords(htmlspecialchars($row['nome_produto'])) . "</td>";
            echo "<td>R$ " . number_format($row['preco_produto'], 2, ',', '.') . "</td>";
            echo "<td>" . htmlspecialchars($row['nome_material'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['nome_cor'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['qtd_carrinho']) . "</td>";
            echo "<td>
                <a class='btn btn-danger' href='../../controller/carrinho/delete.php?id_carrinho=" . $row['id_carrinho'] . "'>Deletar</a>
                </td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "<h4>Total: R$ " . number_format($total, 2, ',', '.') . "</h4>";
        echo "<div class='d-flex justify-content-end mt-3'><a href='../telainicial/index.php' class='btn'>Continuar Comprando</a></div>";
        echo "<div class='mt-5'>
                            <h5 class='mb-3'>Finalize seu Pedido</h5>
                            <form id='pedidoForm' action='../../controller/carrinho/pedido.php' method='POST'>
                                <div class='input-group mb-4'>
                                    <span class='input-group-text'>
                                        <i class='bi bi-geo-alt'></i>
                                    </span>
                                    <input type='text' name='cep' class='form-control' id='cep' required placeholder='Informe seu CEP'>
                                </div>
                                <div class='input-group mb-4'>
                                    <span class='input-group-text'>
                                        <i class='bi bi-house-door'></i>
                                    </span>
                                    <input type='text' name='rua' class='form-control text-capitalize' readonly id='rua' required placeholder='Rua'>
                                </div>
                                <div class='input-group mb-4'>
                                    <span class='input-group-text'>
                                        <i class='bi bi-house-door'></i>
                                    </span>
                                    <input type='text' name='bairro' class='form-control text-capitalize' readonly id='bairro' required placeholder='Bairro'>
                                </div>
                                <div class='input-group mb-4'>
                                    <span class='input-group-text'>
                                        <i class='bi bi-hash'></i>
                                    </span>
                                    <input type='text' name='numero' class='form-control' id='numero' required placeholder='Número'>
                                </div>
                                <div class='input-group mb-4'>
                                    <span class='input-group-text'>
                                        <i class='bi bi-pencil'></i>
                                    </span>
                                    <input type='text' name='complemento' class='form-control text-capitalize' id='complemento' placeholder='Complemento (opcional)'>
                                </div>
                                <input type='hidden' name='total' value='" . $total . "'>
                                <div class='form-floating mt-3 mb-3'>
                                    <textarea class='form-control' placeholder='Leave a comment here' id='mensagem' name='mensagem' maxlenght='200'></textarea>
                                    <label for='mensagem'>Deixa Sua Mensagem Especial (Opcional)</label>
                                </div>
                                <select class='form-select' aria-label='Default select example' name= 'pagamento'>
                                    <option selected disabled>Selecione sua forma de pagamento</option>
                                    <option value='debito'>Débito</option>
                                    <option value='credito'>Crédito</option>
                                    <option value='pix'>Pix</option>
                                    <option value='dinheiro'>Dinheiro</option>
                                </select>
                                <div class='d-grid mt-3'>
                                <button type='button' class='btn btn-success btn-lg' id='finalizarPedido'>
                                    Finalizar Pedido <i class='bi bi-check-circle'></i>
                                </button>                          
                                 <div id='alertPlaceholder' class='mt-3'></div>
                            </form>
                          </div>";
    } else {
        echo "<div class='alert alert-warning' role='alert'>Sem carrinho para mostrar.</div>";
    }
                           if (isset($_SESSION['msg'])) {
                    echo "<div class='alert alert-success mt-3' role='alert'>" . htmlspecialchars($_SESSION['msg']) . "</div>";
                    unset($_SESSION['msg']);
                }

                mysqli_close($conexao);
            } else {
                echo "<div class='alert alert-warning' role='alert'>Usuário não está logado. Por favor, faça login para acessar seu carrinho de compras.</div>";
            }
            ?>
        </div>
    </div>
</div>

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
<?php include "../partials/footer.php" ?>
        //echo "<div class='d-flex justify-content-end mt-3'><a href='./pedido.php' class='btn'>Fazer Pedido</a></div>";