<?php
$titulo = 'Contato';
require_once __DIR__ . '/../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    flash('Mensagem enviada! Entraremos em contato em breve.', 'success');
    redirect(BASE_URL . 'pages/contato.php');
}
?>
<section class="page-hero"><div class="container"><h1>Fale Conosco</h1></div></section>
<section class="page-content">
  <div class="container">
    <div class="form-card">
      <h2>Envie sua mensagem</h2>
      <form method="post">
        <div class="form-group"><label>Nome</label><input type="text" name="nome" required /></div>
        <div class="form-group"><label>E-mail</label><input type="email" name="email" required /></div>
        <div class="form-group"><label>Mensagem</label>
          <textarea name="mensagem" rows="5" required style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;font-family:inherit;"></textarea>
        </div>
        <button type="submit" class="btn btn-gold">Enviar</button>
      </form>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>