<?php /* =========================================================
   REYVIE — STYLESHEET CENTRAL
   Inclua com: <?php include __DIR__ . '/style.php'; ?>
   Ou em subpastas: <?php include __DIR__ . '/../style.php'; ?>
   ========================================================= */ ?>
<style>
/* =========================================================
   1. VARIÁVEIS GLOBAIS
   ========================================================= */
:root {
    --primary-coral:  #e8857d;
    --primary-pink:   #f4a8a0;
    --light-pink:     #f5d5d0;
    --soft-pink:      #fdf6f5;

    --dark-text:      #333333;
    --gray-text:      #777777;
    --muted-text:     #999999;

    --success-start:  #5FA86D;
    --success-end:    #4A8A58;
    --error-start:    #D64545;
    --error-end:      #B83535;

    --shadow:         0 4px 20px rgba(0, 0, 0, 0.08);
    --radius-sm:      8px;
    --radius-md:      10px;
    --radius-lg:      15px;
    --transition:     .2s ease;
}

/* =========================================================
   2. RESET
   ========================================================= */
* { margin: 0; padding: 0; box-sizing: border-box; }

html { scroll-behavior: smooth; }

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: var(--soft-pink);
    color: var(--dark-text);
    line-height: 1.6;
}

img { max-width: 100%; display: block; }

/* =========================================================
   3. NOTIFICAÇÕES
   ========================================================= */
.notificacao {
    position: fixed;
    top: 80px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: var(--radius-sm);
    color: #fff;
    font-weight: 600;
    z-index: 2000;
    animation: slideIn .3s ease-out;
    box-shadow: var(--shadow);
    max-width: 400px;
}
.notificacao.sucesso {
    background: linear-gradient(135deg, var(--success-start), var(--success-end));
}
.notificacao.erro {
    background: linear-gradient(135deg, var(--error-start), var(--error-end));
}

@keyframes slideIn {
    from { transform: translateX(120%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}

/* =========================================================
   4. LAYOUT PRINCIPAL
   ========================================================= */
.home-container {
    display: grid;
    grid-template-columns: 260px 1fr 280px;
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    min-height: 100vh;
}

.wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.wrap-sm { max-width: 760px; margin: 0 auto; padding: 2rem; }

/* =========================================================
   5. SIDEBAR ESQUERDA
   ========================================================= */
.sidebar-left {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.logo-circle {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-coral), var(--primary-pink));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: var(--shadow);
}
.beauty-text {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--dark-text);
    line-height: 1.2;
}
.loading-text {
    font-size: .8rem;
    color: var(--gray-text);
    font-style: italic;
}
.feature-item {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    font-size: .9rem;
    color: var(--gray-text);
}
.feature-icon {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-coral), var(--primary-pink));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    flex-shrink: 0;
    margin-top: 2px;
}

/* =========================================================
   6. CONTEÚDO PRINCIPAL
   ========================================================= */
.main-content {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
}

/* =========================================================
   7. HERO
   ========================================================= */
.hero-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: center;
    background: #fff;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: var(--shadow);
}
.hero-text h1 {
    font-size: 2.2rem;
    margin-bottom: 1rem;
    line-height: 1.2;
}
.hero-text p {
    color: var(--gray-text);
    margin-bottom: 1.5rem;
}
.hero-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.hero-image img {
    width: 100%;
    border-radius: var(--radius-lg);
    display: block;
}

/* =========================================================
   8. BOTÕES
   ========================================================= */
.btn {
    border: none;
    border-radius: 50px;
    padding: .8rem 1.6rem;
    font-weight: 600;
    cursor: pointer;
    font-size: .95rem;
    transition: transform var(--transition), box-shadow var(--transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    font-family: inherit;
    line-height: 1;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}
.btn-primary {
    background: linear-gradient(135deg, var(--primary-coral), var(--primary-pink));
    color: #fff;
}
.btn-secondary {
    background: #fff;
    color: var(--dark-text);
    border: 2px solid var(--light-pink);
}
.btn-danger {
    background: linear-gradient(135deg, var(--error-start), var(--error-end));
    color: #fff;
    border: none;
}
.btn-sm { padding: .4rem .9rem; font-size: .8rem; }
.btn-block { width: 100%; }
.play-icon { font-size: .8rem; }

/* =========================================================
   9. PRODUTOS
   ========================================================= */
.products-section {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}
.products-description {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: center;
}
.products-description h2 { font-size: 1.6rem; }
.products-description p { color: var(--gray-text); }

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.5rem;
}
.product-card {
    background: #fff;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: transform var(--transition);
    display: flex;
    flex-direction: column;
}
.product-card:hover { transform: translateY(-4px); }
.product-image {
    width: 100%;
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    background: linear-gradient(135deg, var(--light-pink), var(--primary-pink));
    overflow: hidden;
}
.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--radius-md);
    display: block;
}
.product-info {
    padding: 1rem;
    flex: 1;
}
.product-name {
    font-weight: 600;
    font-size: .95rem;
    margin-bottom: .4rem;
}
.product-price {
    color: var(--primary-coral);
    font-weight: 700;
    font-size: 1.05rem;
}

/* =========================================================
   10. SIDEBAR DIREITA
   ========================================================= */
.sidebar-right {
    background: #fff;
    border-radius: 20px;
    padding: 1.8rem;
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-self: start;
}
.sidebar-right h3 { font-size: 1.3rem; line-height: 1.3; }
.sidebar-right p { color: var(--gray-text); font-size: .9rem; }
.sidebar-image img {
    width: 100%;
    border-radius: var(--radius-lg);
    display: block;
}

/* =========================================================
   11. IMAGEM PREVIEW (ADMIN)
   ========================================================= */
.img-preview {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: var(--radius-md);
    border: 2px solid var(--light-pink);
    margin-top: .5rem;
    display: block;
}

/* =========================================================
   12. TABELAS ADMIN
   ========================================================= */
.tabela-admin {
    width: 100%;
    background: #fff;
    border-radius: var(--radius-lg);
    overflow: hidden;
    border-collapse: collapse;
    box-shadow: var(--shadow);
}
.tabela-admin th,
.tabela-admin td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--light-pink);
    vertical-align: middle;
}
.tabela-admin th {
    background: linear-gradient(135deg, var(--primary-coral), var(--primary-pink));
    color: #fff;
    font-weight: 600;
}
.tabela-admin tr:last-child td { border-bottom: none; }
.tabela-admin tbody tr { transition: background var(--transition); }
.tabela-admin tbody tr:hover { background: var(--soft-pink); }
.tabela-admin tfoot td {
    background: var(--soft-pink);
    font-weight: 600;
    border-top: 2px solid var(--light-pink);
}

/* =========================================================
   13. CARDS ADMIN
   ========================================================= */
.admin-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.admin-card {
    background: #fff;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    border-left: 4px solid var(--primary-coral);
    transition: transform var(--transition);
}
.admin-card:hover { transform: translateY(-3px); }
.admin-card h3 {
    font-size: .85rem;
    color: var(--gray-text);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: .5rem;
}
.admin-card .valor {
    font-size: 2rem;
    font-weight: 700;
    color: var(--dark-text);
}

/* =========================================================
   14. PAINÉIS ADMIN
   ========================================================= */
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}
.admin-header h1 { font-size: 1.6rem; }
.admin-actions { display: flex; gap: .5rem; flex-wrap: wrap; }

.admin-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.admin-panel {
    background: #fff;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
}
.admin-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    gap: .5rem;
    flex-wrap: wrap;
}
.admin-panel-header h2 { font-size: 1.1rem; }

.empty {
    color: var(--gray-text);
    text-align: center;
    padding: 1.5rem;
}

/* =========================================================
   15. BADGES
   ========================================================= */
.badge {
    display: inline-block;
    padding: .25rem .7rem;
    border-radius: 50px;
    font-size: .78rem;
    font-weight: 600;
    background: #eee;
    color: #555;
    text-transform: capitalize;
}
.badge-ok       { background: #d4edda; color: #2c6b3a; }
.badge-aviso    { background: #fff3cd; color: #856404; }
.badge-erro     { background: #f8d7da; color: #a12b33; }
.badge-pendente { background: #fff3cd; color: #856404; }
.badge-pago     { background: #d1ecf1; color: #0c5460; }
.badge-enviado  { background: #cce5ff; color: #004085; }
.badge-entregue { background: #d4edda; color: #2c6b3a; }
.badge-cancelado{ background: #f8d7da; color: #a12b33; }

/* =========================================================
   16. GALERIA DE MINIATURAS
   ========================================================= */
.galeria-miniaturas {
    display: flex;
    gap: .5rem;
    margin-top: .75rem;
    flex-wrap: wrap;
}
.galeria-miniaturas img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: var(--radius-sm);
    cursor: pointer;
    border: 2px solid transparent;
    transition: border var(--transition), transform var(--transition);
}
.galeria-miniaturas img:hover {
    border-color: var(--primary-coral);
    transform: scale(1.05);
}

/* =========================================================
   17. PÁGINA DE DETALHE DO PRODUTO
   ========================================================= */
.produto-detalhe {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    background: #fff;
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
}
.produto-detalhe .imagem-principal {
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: linear-gradient(135deg, var(--light-pink), var(--primary-pink));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6rem;
}
.produto-detalhe .imagem-principal img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* =========================================================
   18. FORMULÁRIOS
   ========================================================= */
.form-box {
    background: #fff;
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: var(--shadow);
    width: 100%;
    max-width: 420px;
}
.form-box h1 {
    margin-bottom: 1.5rem;
    text-align: center;
}
.form-box label {
    display: block;
    margin: .8rem 0 .3rem;
    font-weight: 600;
    font-size: .9rem;
}
.form-box input,
.form-box textarea,
.form-box select {
    width: 100%;
    padding: .9rem;
    border: 2px solid var(--light-pink);
    border-radius: var(--radius-md);
    margin-bottom: 1rem;
    outline: none;
    font-family: inherit;
    font-size: .95rem;
    background: #fff;
    color: var(--dark-text);
    transition: border-color var(--transition);
}
.form-box input:focus,
.form-box textarea:focus,
.form-box select:focus {
    border-color: var(--primary-coral);
}
.form-box textarea { resize: vertical; min-height: 80px; }

.form-erro {
    color: var(--error-start);
    margin-bottom: 1rem;
    text-align: center;
    font-weight: 600;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.form-col { display: flex; flex-direction: column; }

.form-check {
    display: flex !important;
    align-items: center;
    gap: .5rem;
    margin: 1rem 0 !important;
    cursor: pointer;
}
.form-check input {
    width: auto !important;
    margin: 0 !important;
}

/* =========================================================
   19. AUTENTICAÇÃO (LOGIN/CADASTRO)
   ========================================================= */
.auth-bg {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 1rem;
    background: linear-gradient(135deg, var(--light-pink), var(--primary-pink));
}
.auth-bg .btn { width: 100%; }

.auth-link {
    text-align: center;
    margin-top: 1rem;
    font-size: .9rem;
    color: var(--gray-text);
}
.auth-link a {
    color: var(--primary-coral);
    text-decoration: none;
    font-weight: 600;
}
.auth-link a:hover { text-decoration: underline; }

/* =========================================================
   20. BUSCA E FILTROS
   ========================================================= */
.search-form {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}
.search-form input {
    padding: .7rem 1rem;
    border: 2px solid var(--light-pink);
    border-radius: 50px;
    outline: none;
    font-family: inherit;
    font-size: .95rem;
    min-width: 200px;
}
.search-form input:focus { border-color: var(--primary-coral); }

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.filtros {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.filtros .btn.ativo {
    background: linear-gradient(135deg, var(--primary-coral), var(--primary-pink));
    color: #fff;
    border-color: transparent;
}

/* =========================================================
   21. UTILITÁRIOS
   ========================================================= */
.text-center { text-align: center; }
.mt-1 { margin-top: .5rem; }
.mt-2 { margin-top: 1rem; }
.mt-3 { margin-top: 1.5rem; }
.mb-1 { margin-bottom: .5rem; }
.mb-2 { margin-bottom: 1rem; }
.mb-3 { margin-bottom: 1.5rem; }
.d-flex { display: flex; }
.gap-1 { gap: .5rem; }
.gap-2 { gap: 1rem; }
.flex-wrap { flex-wrap: wrap; }
.align-center { align-items: center; }
.justify-between { justify-content: space-between; }
.w-100 { width: 100%; }
.text-muted { color: var(--gray-text); }
.text-small { font-size: .85rem; }
.text-coral { color: var(--primary-coral); font-weight: 700; }
.strike { text-decoration: line-through; color: var(--muted-text); font-size: .85rem; }

.overflow-x { overflow-x: auto; }

/* =========================================================
   22. RESPONSIVO
   ========================================================= */
@media (max-width: 1100px) {
    .home-container { grid-template-columns: 220px 1fr; }
    .sidebar-right { display: none; }
    .admin-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .home-container {
        grid-template-columns: 1fr;
        padding: 1rem;
    }
    .sidebar-left {
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
    }
    .hero-section { grid-template-columns: 1fr; padding: 1.5rem; }
    .hero-text h1 { font-size: 1.7rem; }
    .products-description { grid-template-columns: 1fr; }
    .produto-detalhe { grid-template-columns: 1fr; gap: 1.5rem; }
    .tabela-admin { font-size: .85rem; }
    .tabela-admin th,
    .tabela-admin td { padding: .6rem; }
    .notificacao {
        left: 10px;
        right: 10px;
        max-width: none;
    }
    .wrap, .wrap-sm { padding: 1rem; }
    .form-box { padding: 1.5rem; }
    .admin-header { flex-direction: column; align-items: flex-start; }
    .admin-actions { width: 100%; }
    .admin-actions .btn { flex: 1; }
    .form-row { grid-template-columns: 1fr; }
    .admin-cards { grid-template-columns: 1fr 1fr; }
    .search-form { width: 100%; }
    .search-form input { flex: 1; min-width: 0; }
}

@media (max-width: 480px) {
    .admin-cards { grid-template-columns: 1fr; }
    .product-card .btn { font-size: .78rem; }
    .logo-circle { width: 70px; height: 70px; font-size: .9rem; }
    .beauty-text { font-size: 1.1rem; }
}

/* =========================================================
   23. IMPRESSÃO
   ========================================================= */
@media print {
    .sidebar-left, .sidebar-right, .btn, .notificacao { display: none !important; }
    .home-container { grid-template-columns: 1fr; }
    body { background: #fff; }
}
</style>