 <?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/navbar.php'; ?>
<body>
<?php if(isset($_SESSION['flash_msg'])): ?>
<div id="flash-message" class="flash-message">
    <?= htmlspecialchars($_SESSION['flash_msg']); ?>
</div>
<script>
    setTimeout(() => {
        const msg = document.getElementById('flash-message');
        if(msg){
            msg.style.transition = "opacity 0.5s ease";
            msg.style.opacity = 0;
            setTimeout(() => msg.remove(), 500);
        }
    }, 3000);
</script>
<?php unset($_SESSION['flash_msg']); endif; ?>

    <?php include '../partials/navbar.php' ?>
    <div class="home-container">
        <section>
            <div class="content">
                <h3>VEJA O MUNDO COM ESTILO DESCUBRA OS ÓCULOS ESCUROS <b>MÁRCIA ONÓRIO</b></h3>
                <p>Márcia Onório: Enxergando o futuro com estilo e elegância, refletindo sua essência em cada par de óculos.</p>
                <a href="#menu" class="btn">Escolha o seu Agora</a>
            </div>
        </section>
    </div>

<section class="menu" id="menu">
    <h2 class="title">Nossos <span>Produtos</span></h2>
<?php
if (isset($_GET['message']) && !empty($_GET['message'])) {
    echo '<div class="alert alert-warning text-center" style="margin-bottom: 20px;">' 
         . htmlspecialchars($_GET['message']) . 
         '</div>';
}
?>
    <div class="box-container">
        <?php
        include '../../db/conexao.php';
        $sql = "SELECT * FROM produto WHERE estoque_produto > 0";
        $stmt = $conexao->query($sql);

        while ($row = $stmt->fetch_assoc()) {
            ?>
            <div class="box">
    <form method="POST" action="../../Controller/carrinho/new.php">
        <input type="hidden" name="id_produto" value="<?php echo $row['id_produto']; ?>">
        <div class="box-content">
            <img src="../../assets/img/<?php echo htmlspecialchars($row['imagem_produto']); ?>" 
                 alt="<?php echo htmlspecialchars($row['nome_produto']); ?>" width="250" height="250">
            <h3><?php echo htmlspecialchars($row['nome_produto']); ?></h3>
            <div class="price">R$<?php echo number_format($row['preco_produto'], 2, ',', '.'); ?></div>
            <div class="quantity-selector">
                <input type="number" name="quantidade" class="quantity-input" 
       value="0" min="1" max="<?php echo htmlspecialchars($row['estoque_produto']); ?>" 
       style="width: 60px; text-align: center;">
            </div>
            <button type="submit" class="btn">Adicionar ao Carrinho</button>
        </div>
    </form>
    </div>
            <?php
        }
        ?>
    </div>
</section>
<section class="about py-5" id="about">
  <div class="container">
    <h2 class="title text-center mb-5">Sobre <span>Nós</span></h2>
    <div class="row align-items-center">
      
      <div class="col-5 text-center">
        <img src="../../assets/img/fototia.jpeg" 
             alt="sobre-nos" 
             class="img-fluid rounded">
      </div>

      <div class="col-7 text-white">
        <h3 class="mb-3">O QUE FAZEM NOSSOS ÓCULOS ESPECIAIS?</h3>
        <p>
          Os óculos escuros Márcia Onório são a perfeita combinação de elegância,
          inovação e qualidade superior,
          refletindo a essência do estilo único e a paixão por cada detalhe.
        </p>
        <a href="#" class="btn">Saiba mais</a>
      </div>
    </div>
  </div>
</section>
<section class="footer">
    <div class="share">
        
    <a href=""><img width="30" height="30" src="https://img.icons8.com/ios-glyphs/30/ffffff/instagram-new.png" alt="instagram-new"/></a>

       <a href=""> <img width="30" height="30" src="https://img.icons8.com/ios-glyphs/30/ffffff/facebook-new.png" alt="facebook-new"/></a>

        <a href=""><img width="30" height="30" src="https://img.icons8.com/ios-glyphs/30/ffffff/twitterx--v1.png" alt="twitterx--v1"/></a>

    </div>
 </section>
   <?php include '../partials/footer.php' ?>
