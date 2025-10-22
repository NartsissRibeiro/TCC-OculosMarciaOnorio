<?php include "../partials/header.php"; ?>
<?php include "../partials/navbar.php"; ?>
<?php
include '../../db/conexao.php';

$produtos = null;

if (isset($_GET['q']) && $_GET['q'] !== '') {
    $q = trim($_GET['q']);
    $sql = "SELECT * FROM produto WHERE (nome_produto LIKE ? OR id_produto = ?) AND estoque_produto > 0";
    $stmt = $conexao->prepare($sql);
    $likeQuery = "%$q%";
    $idQuery = is_numeric($q) ? (int)$q : 0;
    $stmt->bind_param("si", $likeQuery, $idQuery);
    $stmt->execute();
    $produtos = $stmt->get_result();
} else {
    $sql = "SELECT * FROM produto WHERE estoque_produto > 0";
    $produtos = $conexao->query($sql);
}
?>

<section class="menu" id="menu" style="padding-top: 100px;">
    <div class="box-container">
        <?php if ($produtos && $produtos->num_rows > 0): ?>
            <?php while ($row = $produtos->fetch_assoc()): ?>
                <div class="box">
                    <form method="POST" action="../../Controller/carrinho/new.php">
                        <input type="hidden" name="id_produto" value="<?= $row['id_produto'] ?>">
                        <div class="box-content">
                            <img src="../../assets/img/<?= htmlspecialchars($row['imagem_produto']) ?>" 
                                 alt="<?= htmlspecialchars($row['nome_produto']) ?>" width="350" height="250">
                            <h3><?= htmlspecialchars($row['nome_produto']) ?></h3>
                            <div class="price">R$<?= number_format($row['preco_produto'], 2, ',', '.') ?></div>
                            <div class="quantity-selector">
                                <input type="number" name="quantidade" class="quantity-input" 
                                       value="0" min="1" max="<?= htmlspecialchars($row['estoque_produto']) ?>" 
                                       style="width: 60px; text-align: center;">
                            </div>
                            <button type="submit" class="btn">Adicionar ao Carrinho</button>
                        </div>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-products-message" style="text-align:center; color:white; width:100%;">
                <h3>Nenhum produto encontrado</h3>
                <p>Tente pesquisar outro nome ou ID.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
