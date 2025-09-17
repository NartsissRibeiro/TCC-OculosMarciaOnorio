<?php include '../partials/header.php'; ?>
<?php include '../partials/navbar.php'; ?>
<body>
    <?php include '../partials/navbar.php' ?>
    <div class="home-container">
        <section>
            <div class="content">
                <!--<img src="../../assets/img/beautiful-african-woman-monochrome-portrait (1).jpg" width="300"> -->
                <h3>VEJA O MUNDO COM ESTILO DESCUBRA OS ÓCULOS ESCUROS <b>MÁRCIA ONÓRIO</b></h3>
                <p>Márcia Onório: Enxergando o futuro com estilo e elegância, refletindo sua essência em cada par de óculos.</p>
                <a href="#" class="btn">Escolha o seu Agora</a>
            </div>
        </section>
    </div>

<section class="about" id="about">
    <h2 class="title">Sobre <span>Nós</span></h2>
    <div class="row">
        <div class="container-image">
            <img src="../../assets/img/fototia.jpeg" alt="sobre-nos" width="400" height="440"> <!-- Reduzido -->
        </div>
        <div class="content">
            <h3>O QUE FAZEM NOSSOS ÓCULOS ESPECIAIS?</h3>
            <p>Os óculos escuros Márcia Onório são a perfeita combinação de elegância, inovação e qualidade superior, refletindo a essência do estilo único e a paixão por cada detalhe.</p>
            <a href="#" class="btn">Saiba mais</a>
        </div>
    </div>
</section>
<section class="menu" id="menu">
    <h2 class="title">Nossos <span>Produtos</span></h2>
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
</section>
   <?php include '../partials/footer.php' ?>
