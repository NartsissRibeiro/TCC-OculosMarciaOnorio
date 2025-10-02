<?php include "../partials/header.php";
      include "../partials/navbar.php";
?>
      <?php
include "../../db/conexao.php";

// Pega o ID do produto via GET
$id_produto = isset($_GET['id_produto']) ? intval($_GET['id_produto']) : 0;

$sql = "SELECT * FROM produto WHERE id_produto = $id_produto";
$result = $conexao->query($sql);

if ($row = $result->fetch_assoc()) {
    // Imagens separadas por vírgula, por exemplo: "img1.jpg,img2.jpg,img3.jpg"
    $imagens = explode(',', $row['imagem_produto']); 
?>
<main class="container">
    <div class="detalhes-produto">
        <div class="imagens-produtos">
            <img id="imagem-principal" src="../../assets/img/<?php echo htmlspecialchars($imagens[0]); ?>" width="400" height="350">
            <div class="imagens-pequenas">
                <?php foreach ($imagens as $img) { ?>
                    <img class="img-pequena" src="../../assets/img/<?php echo htmlspecialchars($img); ?>" width="65" height="60">
                <?php } ?>
            </div>
        </div>
        <div class="informacoes-produto">
            <h2><?php echo htmlspecialchars($row['nome_produto']); ?></h2>
            <p class="id-produto">Código do produto: <?php echo htmlspecialchars($row['id_produto']); ?></p>
            <p class="preco">R$ <?php echo number_format($row['preco_produto'], 2, ',', '.'); ?></p>
            <p class="descricao-produto"><?php echo htmlspecialchars($row['desc_produto']); ?></p>
            
            <div class="icons-site">
                <img width="25px" height="25px" src="https://img.icons8.com/ios-glyphs/30/ffffff/shopping-cart--v1.png" alt="Carrinho">
            </div>

            <a href="finalizar pedido/index.html"><button>Comprar</button></a>
            <br>
        </div>
    </div>
</main>

<script>
// Troca a imagem principal ao clicar na miniatura
const imagemPrincipal = document.getElementById('imagem-principal');
const imagensPequenas = document.querySelectorAll('.img-pequena');

imagensPequenas.forEach(img => {
    img.addEventListener('click', () => {
        imagemPrincipal.src = img.src;
    });
});
</script>

<?php
} else {
    echo "<p>Produto não encontrado.</p>";
}
?>
<?php
    include "../partials/footer.php";
?>