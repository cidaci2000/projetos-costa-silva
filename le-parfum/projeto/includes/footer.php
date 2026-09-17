<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-logo">Le Parfum</div>
        <p class="footer-desc">
          Fragrâncias exclusivas para quem busca sofisticação e elegância.
        </p>
        <div class="footer-social">
          <a href="#">📱</a><a href="#">📷</a><a href="#">🐦</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Institucional</h4>
        <ul>
          <li><a href="<?= BASE_URL ?>pages/sobre.php">Sobre Nós</a></li>
          <li><a href="#">Blog</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Atendimento</h4>
        <ul>
          <li><a href="<?= BASE_URL ?>pages/contato.php">Contato</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Minha Conta</h4>
        <ul>
          <li><a href="<?= BASE_URL ?>pages/perfil.php">Meus Pedidos</a></li>
          <li><a href="<?= BASE_URL ?>pages/perfil.php#favoritos">Favoritos</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> Le Parfum. Todos os direitos reservados.</p>
      <p>💳 Parcelamos em até 6x sem juros</p>
    </div>
  </div>
</footer>

<div id="toast-container"></div>

<script src="<?= BASE_URL ?>assets/js/app.js"></script>
</body>
</html>