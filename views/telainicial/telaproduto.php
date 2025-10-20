<?php 
include "../partials/header.php"; 
include "../partials/navbar.php"; 
include "../../db/conexao.php"; 

$id_produto = isset($_GET['id_produto']) ? intval($_GET['id_produto']) : 0;

$sql = "SELECT * FROM produto WHERE id_produto = $id_produto";
$result = $conexao->query($sql);

if ($row = $result->fetch_assoc()) {
    $imagens = explode(',', $row['imagem_produto']); 
?>
<main class="container my-5">

    <div class="row g-4 produto-container p-4 rounded-4">
       
        <div class="col-md-6">
            <div id="produtoCarrossel" class="carousel slide rounded-4" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($imagens as $index => $img) { ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="../../assets/img/<?php echo htmlspecialchars($img); ?>" class="d-block w-100 rounded-3" alt="Imagem do produto">
                        </div>
                    <?php } ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#produtoCarrossel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#produtoCarrossel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
            </div>
        </div>
        
        <div class="col-md-6">
            <h2 class="text-white"><?php echo htmlspecialchars($row['nome_produto']); ?></h2>
            <p class="text-muted">Código do produto: <?php echo htmlspecialchars($row['id_produto']); ?></p>
            <p class="h4 text-primary">R$ <?php echo number_format($row['preco_produto'], 2, ',', '.'); ?></p>
            <p class="text-white"><?php echo htmlspecialchars($row['desc_produto']); ?></p>

            <form method="POST" action="../../Controller/carrinho/new.php" class="mt-3">
                <input type="hidden" name="id_produto" value="<?php echo $row['id_produto']; ?>">
                <div class="quantity-selector mb-3">
                    <input type="number" name="quantidade" class="quantity-input" value="1" min="1" max="<?php echo $row['estoque_produto']; ?>" style="width: 80px; text-align: center;">
                </div>
                <button type="submit" class="btn-rosa btn-lg w-100">
                    <i class="bi bi-cart-fill me-2"></i>Adicionar ao Carrinho
                </button>
            </form>
        </div>
    </div>

</main>

<?php 
} else {
    echo "<p class='text-center my-5 text-white'>Produto não encontrado.</p>";
}
include "../partials/footer.php"; 
?>
