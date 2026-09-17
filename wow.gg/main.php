<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WOW.GG</title>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg-dark: #0d0b1e;
    --bg-mid: #150f2e;
    --neon-pink: #ff2d9a;
    --neon-cyan: #00f5ff;
    --neon-purple: #8b5cf6;
    --neon-magenta: #e040fb;
    --card-bg: rgba(255,255,255,0.07);
    --card-border: #ff2d9a;
    --text-white: #ffffff;
    --input-bg: rgba(255,255,255,0.9);
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Rajdhani', sans-serif;
    background: var(--bg-dark);
    color: var(--text-white);
    min-height: 100vh;
    overflow-x: hidden;
  }

  /* SHARED BG */
  .neon-bg {
    position: fixed; inset: 0; z-index: 0;
    background: radial-gradient(ellipse at 20% 50%, #1a0a3a 0%, #0d0b1e 60%),
                radial-gradient(ellipse at 80% 20%, #2a0a3a 0%, transparent 50%);
  }
  .neon-bg::before, .neon-bg::after {
    content: '';
    position: absolute;
    border: 2px solid;
    opacity: 0.5;
  }
  .neon-bg::before {
    width: 200px; height: 200px;
    bottom: 20%; left: 5%;
    border-color: var(--neon-pink);
    border-right: none; border-top: none;
    box-shadow: -4px 4px 20px var(--neon-pink);
    transform: rotate(-10deg);
  }
  .neon-bg::after {
    width: 200px; height: 200px;
    top: 10%; right: 5%;
    border-color: var(--neon-pink);
    border-left: none; border-bottom: none;
    box-shadow: 4px -4px 20px var(--neon-pink);
    transform: rotate(-10deg);
  }

  /* PAGES */
  .page { display: none; position: relative; z-index: 1; min-height: 100vh; }
  .page.active { display: flex; flex-direction: column; align-items: center; justify-content: center; }

  /* ====== SPLASH PAGE ====== */
  #page-splash {
    gap: 30px;
  }
  .splash-logo {
    position: relative;
    display: flex; align-items: center; justify-content: center;
    width: 320px; height: 260px;
    animation: floatLogo 3s ease-in-out infinite;
  }
  @keyframes floatLogo {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
  }
  .controller-outline {
    position: absolute;
    width: 280px; height: 220px;
    border: 8px solid var(--neon-pink);
    border-radius: 50% 50% 60% 60% / 40% 40% 60% 60%;
    box-shadow: 0 0 30px var(--neon-pink), 0 0 60px rgba(255,45,154,0.4);
    background: rgba(0,0,0,0.5);
  }
  .controller-outline::before {
    content: '';
    position: absolute;
    top: -35px; left: 50%; transform: translateX(-50%);
    width: 80px; height: 40px;
    background: var(--bg-dark);
    border: 8px solid var(--neon-pink);
    border-radius: 10px 10px 0 0;
    box-shadow: 0 0 15px var(--neon-pink);
  }
  .splash-title {
    font-family: 'Orbitron', monospace;
    font-size: 3rem; font-weight: 900;
    color: var(--neon-cyan);
    text-shadow: 0 0 20px var(--neon-cyan), 0 0 40px rgba(0,245,255,0.5);
    letter-spacing: 4px;
    position: relative; z-index: 1;
  }

  .controllers-deco {
    position: absolute;
    display: flex; gap: 500px;
    pointer-events: none;
  }
  .ctrl-img {
    font-size: 7rem;
    opacity: 0.8;
    filter: drop-shadow(0 0 15px var(--neon-purple));
  }
  .ctrl-img.left { transform: rotate(-15deg); }
  .ctrl-img.right { transform: rotate(15deg) scaleX(-1); }

  .btn-primary {
    background: #8b3a6b;
    color: white;
    font-family: 'Orbitron', monospace;
    font-size: 1.2rem; font-weight: 700;
    letter-spacing: 3px;
    border: none; border-radius: 14px;
    padding: 18px 100px;
    cursor: pointer;
    box-shadow: 0 0 20px rgba(139,58,107,0.5);
    transition: all 0.2s;
    text-transform: uppercase;
  }
  .btn-primary:hover {
    background: #b04d88;
    box-shadow: 0 0 30px rgba(139,58,107,0.8);
    transform: scale(1.04);
  }

  .btn-secondary {
    background: var(--input-bg);
    color: #b060c0;
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.3rem; font-weight: 600;
    letter-spacing: 2px;
    border: none; border-radius: 14px;
    padding: 18px 100px;
    cursor: pointer;
    transition: all 0.2s;
    text-transform: lowercase;
  }
  .btn-secondary:hover {
    background: #f0d0f8;
    transform: scale(1.04);
  }

  /* ====== AUTH PAGES (login / cadastro) ====== */
  #page-login, #page-cadastro {
    gap: 0;
  }

  .auth-container {
    width: 100%; max-width: 700px;
    padding: 40px 20px;
    display: flex; flex-direction: column; align-items: center; gap: 30px;
  }

  .auth-top {
    align-self: flex-start;
  }
  .auth-mini-logo {
    display: flex; align-items: center; gap: 12px;
    cursor: pointer;
  }
  .mini-controller {
    width: 70px; height: 55px;
    border: 4px solid var(--neon-pink);
    border-radius: 40% 40% 50% 50% / 35% 35% 50% 50%;
    position: relative;
    box-shadow: 0 0 12px var(--neon-pink);
  }
  .mini-controller::before {
    content: '';
    position: absolute;
    top: -18px; left: 50%; transform: translateX(-50%);
    width: 35px; height: 18px;
    border: 4px solid var(--neon-pink);
    border-radius: 6px 6px 0 0;
    box-shadow: 0 0 8px var(--neon-pink);
  }
  .mini-logo-text {
    font-family: 'Orbitron', monospace;
    font-size: 0.9rem; font-weight: 700;
    color: var(--neon-cyan);
    text-shadow: 0 0 10px var(--neon-cyan);
  }

  .auth-title {
    font-family: 'Orbitron', monospace;
    font-size: 3.5rem; font-weight: 700;
    color: var(--neon-cyan);
    text-shadow: 0 0 20px var(--neon-cyan), 0 0 50px rgba(0,245,255,0.4);
    letter-spacing: 6px;
    align-self: flex-start; margin-left: 20px;
  }

  .auth-input {
    width: 100%;
    background: var(--input-bg);
    border: none; border-radius: 14px;
    padding: 22px 28px;
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.1rem;
    color: #888;
    outline: none;
    transition: all 0.2s;
  }
  .auth-input:focus {
    box-shadow: 0 0 0 3px var(--neon-cyan);
    color: #333;
  }

  .btn-entrar {
    background: var(--input-bg);
    color: #b060c0;
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.4rem; font-weight: 600;
    letter-spacing: 2px;
    border: none; border-radius: 16px;
    padding: 20px 80px;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-entrar:hover {
    background: #f0d0f8;
    transform: scale(1.04);
    box-shadow: 0 0 20px rgba(176,96,192,0.4);
  }

  /* ====== GAME LIBRARY PAGE ====== */
  #page-library {
    align-items: stretch;
    padding: 0;
  }

  .library-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 40px;
    position: relative; z-index: 10;
  }

  .search-bar {
    display: flex; align-items: center; gap: 12px;
    background: white;
    border-radius: 12px;
    padding: 12px 20px;
    width: 340px;
    box-shadow: 0 0 20px rgba(255,255,255,0.1);
  }
  .search-bar input {
    border: none; outline: none;
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.1rem; color: #888;
    width: 100%;
    background: transparent;
  }
  .search-icon { font-size: 1.2rem; color: #aaa; }

  .games-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    padding: 10px 40px 40px;
    flex: 1;
  }

  .game-card {
    border: 3px solid var(--neon-pink);
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s;
    aspect-ratio: 4/3;
    position: relative;
    background: #111;
  }
  .game-card:hover {
    border-color: var(--neon-cyan);
    box-shadow: 0 0 20px var(--neon-cyan), 0 0 40px rgba(0,245,255,0.2);
    transform: scale(1.03);
    z-index: 5;
  }
  .game-card canvas, .game-card .game-preview {
    width: 100%; height: 100%;
    display: block;
  }
  .game-label {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: rgba(0,0,0,0.7);
    text-align: center;
    font-family: 'Orbitron', monospace;
    font-size: 0.75rem;
    color: var(--neon-cyan);
    padding: 6px;
    opacity: 0;
    transition: opacity 0.2s;
  }
  .game-card:hover .game-label { opacity: 1; }

  /* ====== GAME PLAYER PAGE ====== */
  #page-game {
    align-items: stretch;
  }

  .game-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 40px;
    position: relative; z-index: 10;
  }

  .game-main {
    flex: 1;
    display: flex; gap: 20px;
    padding: 0 40px 20px;
  }

  .game-canvas-wrapper {
    flex: 1;
    border: 3px solid var(--neon-cyan);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 0 30px rgba(0,245,255,0.3);
  }

  #active-canvas {
    width: 100%; height: 100%;
    display: block;
  }

  .game-sidebar {
    width: 140px;
    display: flex; flex-direction: column; gap: 12px;
    overflow-y: auto;
  }

  .thumb-card {
    border: 3px solid var(--neon-pink);
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    aspect-ratio: 4/3;
    transition: all 0.2s;
    flex-shrink: 0;
    position: relative;
    background: #111;
  }
  .thumb-card:hover, .thumb-card.active-thumb {
    border-color: var(--neon-cyan);
    box-shadow: 0 0 15px var(--neon-cyan);
    transform: scale(1.05);
  }
  .thumb-canvas { width: 100%; height: 100%; display: block; }

  .btn-back {
    background: transparent;
    border: 2px solid var(--neon-pink);
    color: var(--neon-pink);
    font-family: 'Orbitron', monospace;
    font-size: 0.8rem;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-back:hover {
    background: var(--neon-pink);
    color: white;
    box-shadow: 0 0 15px var(--neon-pink);
  }

  .ctrl-deco-left, .ctrl-deco-right {
    position: fixed; top: 50%; transform: translateY(-50%);
    font-size: 9rem; opacity: 0.15; pointer-events: none; z-index: 0;
    filter: drop-shadow(0 0 20px var(--neon-purple));
  }
  .ctrl-deco-left { left: -40px; transform: translateY(-50%) rotate(-10deg); }
  .ctrl-deco-right { right: -40px; transform: translateY(-50%) rotate(10deg) scaleX(-1); }

  /* GAME CANVASES HIDDEN */
  .hidden-canvas-pool { display: none; }

</style>
</head>
<body>

<div class="neon-bg"></div>

<!-- SPLASH PAGE -->
<div class="page active" id="page-splash">
  <div class="ctrl-deco-left">🎮</div>
  <div class="ctrl-deco-right">🎮</div>

  <div class="splash-logo">
    <div class="controller-outline"></div>
    <span class="splash-title">WOW.GG</span>
  </div>

  <button class="btn-primary" onclick="showPage('page-login')">LOGIN</button>
  <button class="btn-secondary" onclick="showPage('page-cadastro')">cadastro</button>
</div>

<!-- LOGIN PAGE -->
<div class="page" id="page-login">
  <div class="ctrl-deco-left">🎮</div>
  <div class="ctrl-deco-right">🎮</div>

  <div class="auth-container">
    <div class="auth-top">
      <div class="auth-mini-logo" onclick="showPage('page-splash')">
        <div class="mini-controller"></div>
        <span class="mini-logo-text">WOW.GG</span>
      </div>
    </div>
    <div class="auth-title">LOGIN</div>
    <input class="auth-input" type="text" placeholder="Nome do usuário" id="login-user">
    <input class="auth-input" type="password" placeholder="senha do usário" id="login-pass">
    <button class="btn-entrar" onclick="doLogin()">entrar</button>
  </div>
</div>

<!-- CADASTRO PAGE -->
<div class="page" id="page-cadastro">
  <div class="ctrl-deco-left">🎮</div>
  <div class="ctrl-deco-right">🎮</div>

  <div class="auth-container">
    <div class="auth-top">
      <div class="auth-mini-logo" onclick="showPage('page-splash')">
        <div class="mini-controller"></div>
        <span class="mini-logo-text">WOW.GG</span>
      </div>
    </div>
    <div class="auth-title" style="font-size:2.8rem;letter-spacing:2px;font-family:'Rajdhani',sans-serif;font-weight:700;color:var(--neon-cyan)">Cadastrar</div>
    <input class="auth-input" type="text" placeholder="Nome do usuário" id="cad-user">
    <input class="auth-input" type="password" placeholder="senha do usário" id="cad-pass">
    <button class="btn-entrar" onclick="doCadastro()">entrar</button>
  </div>
</div>

<!-- LIBRARY PAGE -->
<div class="page" id="page-library">
  <div style="position:fixed;top:50%;left:-40px;transform:translateY(-50%) rotate(-10deg);font-size:9rem;opacity:0.12;pointer-events:none;z-index:0;filter:drop-shadow(0 0 20px #8b5cf6)">🎮</div>
  <div style="position:fixed;top:50%;right:-40px;transform:translateY(-50%) rotate(10deg) scaleX(-1);font-size:9rem;opacity:0.12;pointer-events:none;z-index:0;filter:drop-shadow(0 0 20px #8b5cf6)">🎮</div>

  <div class="library-header">
    <div style="width:80px"></div>
    <div class="auth-mini-logo" style="cursor:pointer" onclick="showPage('page-splash')">
      <div class="mini-controller"></div>
      <span class="mini-logo-text">WOW.GG</span>
    </div>
    <div class="search-bar">
      <span class="search-icon">🔍</span>
      <input type="text" placeholder="pesquisar" id="search-input" oninput="filterGames(this.value)">
    </div>
  </div>

  <div class="games-grid" id="games-grid">
    <!-- Populated by JS -->
  </div>
</div>

<!-- GAME PLAYER PAGE -->
<div class="page" id="page-game">
  <div class="game-header">
    <button class="btn-back" onclick="showPage('page-library')">← VOLTAR</button>
    <div class="auth-mini-logo" onclick="showPage('page-splash')">
      <div class="mini-controller"></div>
      <span class="mini-logo-text">WOW.GG</span>
    </div>
    <div class="search-bar" style="width:260px">
      <span class="search-icon">🔍</span>
      <input type="text" placeholder="pesquisar">
    </div>
  </div>
  <div class="game-main">
    <div class="game-canvas-wrapper" id="game-canvas-wrapper">
      <canvas id="active-canvas"></canvas>
    </div>
    <div class="game-sidebar" id="game-sidebar">
      <!-- thumb cards populated by JS -->
    </div>
  </div>
</div>

<!-- Hidden game canvases for thumbnails -->
<div class="hidden-canvas-pool" id="canvas-pool"></div>

<script>
// ===== NAVIGATION =====
let currentGame = null;
let gameLoops = {};

function showPage(id) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  if (id === 'page-library') {
    renderLibrary();
  }
  if (id !== 'page-game') {
    stopAllGames();
  }
}

function doLogin() {
  const u = document.getElementById('login-user').value.trim();
  const p = document.getElementById('login-pass').value.trim();
  if (!u || !p) { alert('Preencha usuário e senha!'); return; }
  showPage('page-library');
}

function doCadastro() {
  const u = document.getElementById('cad-user').value.trim();
  const p = document.getElementById('cad-pass').value.trim();
  if (!u || !p) { alert('Preencha usuário e senha!'); return; }
  showPage('page-library');
}

// ===== GAME DEFINITIONS =====
const GAMES = [
  { id: 'spaceinvaders', name: 'Space Invaders', color: '#000010', draw: drawSpaceInvaders, init: initSpaceInvaders },
  { id: 'snake', name: 'Snake', color: '#6b7a00', draw: drawSnake, init: initSnake },
  { id: 'platformer', name: 'Platformer', color: '#a8d8a8', draw: drawPlatformer, init: initPlatformer },
  { id: 'rpg', name: 'RPG Town', color: '#7aaa7a', draw: drawRPG, init: initRPG },
  { id: 'pong', name: 'Pong', color: '#0a0a1a', draw: drawPong, init: initPong },
  { id: 'breakout', name: 'Breakout', color: '#000b3a', draw: drawBreakout, init: initBreakout },
];

// ===== LIBRARY =====
function renderLibrary() {
  const grid = document.getElementById('games-grid');
  grid.innerHTML = '';
  GAMES.forEach(g => {
    const card = document.createElement('div');
    card.className = 'game-card';
    card.dataset.id = g.id;
    const canvas = document.createElement('canvas');
    canvas.width = 400; canvas.height = 300;
    canvas.style.background = g.color;
    card.appendChild(canvas);
    const label = document.createElement('div');
    label.className = 'game-label';
    label.textContent = g.name;
    card.appendChild(label);
    card.onclick = () => openGame(g.id);
    grid.appendChild(card);
    // Draw preview
    const ctx = canvas.getContext('2d');
    if (g.init) g.init(ctx, true);
    if (g.draw) g.draw(ctx, null, true);
  });
}

function filterGames(q) {
  document.querySelectorAll('.game-card').forEach(c => {
    const id = c.dataset.id;
    const game = GAMES.find(g => g.id === id);
    c.style.display = game.name.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
  });
}

// ===== OPEN GAME =====
let activeGameState = null;
let activeGameId = null;
let activeAnimFrame = null;

function openGame(id) {
  showPage('page-game');
  activeGameId = id;
  const wrapper = document.getElementById('game-canvas-wrapper');
  const canvas = document.getElementById('active-canvas');
  canvas.width = wrapper.offsetWidth || 800;
  canvas.height = wrapper.offsetHeight || 500;

  // Build sidebar
  const sidebar = document.getElementById('game-sidebar');
  sidebar.innerHTML = '';
  const others = GAMES.filter(g => g.id !== id);
  others.forEach(g => {
    const tc = document.createElement('div');
    tc.className = 'thumb-card';
    const tCanvas = document.createElement('canvas');
    tCanvas.width = 120; tCanvas.height = 90;
    tCanvas.style.background = g.color;
    tc.appendChild(tCanvas);
    tc.onclick = () => openGame(g.id);
    sidebar.appendChild(tc);
    const tCtx = tCanvas.getContext('2d');
    if (g.init) g.init(tCtx, true);
    if (g.draw) g.draw(tCtx, null, true);
  });

  startGame(id, canvas);
}

function startGame(id, canvas) {
  if (activeAnimFrame) cancelAnimationFrame(activeAnimFrame);
  const game = GAMES.find(g => g.id === id);
  const ctx = canvas.getContext('2d');
  activeGameState = {};
  game.init(ctx, false, activeGameState, canvas);

  function loop() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = GAMES.find(g=>g.id===id).color;
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    game.draw(ctx, activeGameState, false);
    activeAnimFrame = requestAnimationFrame(loop);
  }
  loop();
}

function stopAllGames() {
  if (activeAnimFrame) cancelAnimationFrame(activeAnimFrame);
  activeAnimFrame = null;
  // Remove all key listeners
  document.onkeydown = null;
  document.onkeyup = null;
}

// ============================
// ===== GAME IMPLEMENTATIONS =====
// ============================

// --- SPACE INVADERS ---
function initSpaceInvaders(ctx, preview, state, canvas) {
  if (preview) return;
  const W = canvas.width, H = canvas.height;
  state.aliens = [];
  for (let row = 0; row < 3; row++) {
    for (let col = 0; col < 8; col++) {
      state.aliens.push({ x: 80 + col * 80, y: 60 + row * 55, alive: true, color: ['#00ffff','#ff4444','#44ff44'][row] });
    }
  }
  state.player = { x: W/2 - 20, y: H - 60, w: 40, h: 30 };
  state.bullets = [];
  state.enemyBullets = [];
  state.direction = 1;
  state.moveTimer = 0;
  state.score = 0;
  state.gameOver = false;
  state.W = W; state.H = H;

  document.onkeydown = (e) => {
    if (e.key === 'ArrowLeft') state.playerMoveL = true;
    if (e.key === 'ArrowRight') state.playerMoveR = true;
    if (e.key === ' ') {
      state.bullets.push({ x: state.player.x + 20, y: state.player.y, speed: 7 });
    }
  };
  document.onkeyup = (e) => {
    if (e.key === 'ArrowLeft') state.playerMoveL = false;
    if (e.key === 'ArrowRight') state.playerMoveR = false;
  };
}

function drawSpaceInvaders(ctx, state, preview) {
  const W = ctx.canvas.width, H = ctx.canvas.height;
  if (preview) {
    // Static preview
    const colors = ['#00ffff','#ff4444','#44ff44'];
    for (let row = 0; row < 3; row++) {
      for (let col = 0; col < 8; col++) {
        drawAlienPixel(ctx, 20 + col * (W/9), 20 + row * (H/5), W/12, colors[row]);
      }
    }
    // Bunkers
    ctx.fillStyle = '#888';
    for (let i = 0; i < 3; i++) {
      ctx.fillRect(30 + i * (W/3.5), H - H*0.25, W/6, H/8);
    }
    return;
  }

  if (!state) return;
  const { aliens, player, bullets, enemyBullets, W: sw, H: sh } = state;

  // Update
  if (!state.gameOver) {
    if (state.playerMoveL) state.player.x = Math.max(0, state.player.x - 5);
    if (state.playerMoveR) state.player.x = Math.min(sw - 40, state.player.x + 5);

    state.moveTimer++;
    if (state.moveTimer > 20) {
      state.moveTimer = 0;
      let hitEdge = false;
      aliens.filter(a=>a.alive).forEach(a => {
        a.x += state.direction * 15;
        if (a.x > sw - 60 || a.x < 20) hitEdge = true;
      });
      if (hitEdge) {
        state.direction *= -1;
        aliens.filter(a=>a.alive).forEach(a => a.y += 20);
      }
    }

    // Bullets
    bullets.forEach(b => b.y -= b.speed);
    state.bullets = bullets.filter(b => b.y > 0);

    // Enemy shoot
    if (Math.random() < 0.015) {
      const aliveAliens = aliens.filter(a => a.alive);
      if (aliveAliens.length) {
        const a = aliveAliens[Math.floor(Math.random() * aliveAliens.length)];
        state.enemyBullets.push({ x: a.x + 12, y: a.y + 20, speed: 4 });
      }
    }
    state.enemyBullets.forEach(b => b.y += b.speed);
    state.enemyBullets = state.enemyBullets.filter(b => b.y < sh);

    // Collision bullets vs aliens
    state.bullets.forEach(b => {
      aliens.forEach(a => {
        if (a.alive && b.x > a.x && b.x < a.x+24 && b.y > a.y && b.y < a.y+20) {
          a.alive = false; b.y = -100; state.score += 10;
        }
      });
    });

    // Enemy bullets vs player
    state.enemyBullets.forEach(b => {
      if (b.x > player.x && b.x < player.x+40 && b.y > player.y && b.y < player.y+30) {
        state.gameOver = true;
      }
    });

    if (!aliens.some(a => a.alive)) {
      state.gameOver = true;
    }
  }

  // Draw aliens
  const colors = ['#00ffff','#ff4444','#44ff44'];
  aliens.forEach((a, i) => {
    if (!a.alive) return;
    drawAlienPixel(ctx, a.x, a.y, 24, a.color);
  });

  // Draw bunkers
  ctx.fillStyle = '#666';
  for (let i = 0; i < 3; i++) {
    ctx.fillRect(60 + i * (sw/3.5), sh - 100, 70, 35);
  }

  // Draw player (ship)
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(player.x + 14, player.y - 10, 12, 12);
  ctx.fillRect(player.x, player.y, 40, 20);

  // Draw bullets
  ctx.fillStyle = '#fff';
  bullets.forEach(b => { ctx.fillRect(b.x - 2, b.y, 4, 12); });

  ctx.fillStyle = '#ff4444';
  state.enemyBullets.forEach(b => { ctx.fillRect(b.x - 2, b.y, 4, 12); });

  // Score
  ctx.fillStyle = '#00ffff';
  ctx.font = 'bold 16px Orbitron, monospace';
  ctx.fillText('SCORE: ' + state.score, 10, 30);

  if (state.gameOver) {
    ctx.fillStyle = 'rgba(0,0,0,0.6)';
    ctx.fillRect(0, 0, sw, sh);
    ctx.fillStyle = '#ff2d9a';
    ctx.font = 'bold 40px Orbitron, monospace';
    ctx.textAlign = 'center';
    ctx.fillText('GAME OVER', sw/2, sh/2 - 20);
    ctx.fillStyle = '#00ffff';
    ctx.font = '20px Orbitron, monospace';
    ctx.fillText('Score: ' + state.score, sw/2, sh/2 + 20);
    ctx.fillText('Recarregue para jogar', sw/2, sh/2 + 55);
    ctx.textAlign = 'left';
  }
}

function drawAlienPixel(ctx, x, y, size, color) {
  ctx.fillStyle = color;
  const s = size / 11;
  const map = [
    [0,0,1,0,0,0,0,0,1,0,0],
    [0,0,0,1,0,0,0,1,0,0,0],
    [0,0,1,1,1,1,1,1,1,0,0],
    [0,1,1,0,1,1,1,0,1,1,0],
    [1,1,1,1,1,1,1,1,1,1,1],
    [1,0,1,1,1,1,1,1,1,0,1],
    [1,0,1,0,0,0,0,0,1,0,1],
    [0,0,0,1,1,0,1,1,0,0,0],
  ];
  map.forEach((row, ri) => row.forEach((cell, ci) => {
    if (cell) ctx.fillRect(x + ci*s, y + ri*s, s-0.5, s-0.5);
  }));
}

// --- SNAKE ---
function initSnake(ctx, preview, state, canvas) {
  if (preview) return;
  const CELL = 20;
  const cols = Math.floor(canvas.width / CELL);
  const rows = Math.floor(canvas.height / CELL);
  state.cell = CELL;
  state.cols = cols; state.rows = rows;
  state.snake = [{x:5,y:5},{x:4,y:5},{x:3,y:5}];
  state.dir = {x:1,y:0};
  state.nextDir = {x:1,y:0};
  state.food = spawnFood(state);
  state.score = 0;
  state.timer = 0;
  state.speed = 8;
  state.dead = false;

  document.onkeydown = (e) => {
    if (e.key === 'ArrowUp' && state.dir.y !== 1) state.nextDir = {x:0,y:-1};
    if (e.key === 'ArrowDown' && state.dir.y !== -1) state.nextDir = {x:0,y:1};
    if (e.key === 'ArrowLeft' && state.dir.x !== 1) state.nextDir = {x:-1,y:0};
    if (e.key === 'ArrowRight' && state.dir.x !== -1) state.nextDir = {x:1,y:0};
  };
}

function spawnFood(state) {
  return {
    x: Math.floor(Math.random() * state.cols),
    y: Math.floor(Math.random() * state.rows)
  };
}

function drawSnake(ctx, state, preview) {
  const W = ctx.canvas.width, H = ctx.canvas.height;
  if (preview) {
    const cell = 12;
    ctx.fillStyle = '#1a1a00';
    ctx.fillRect(0,0,W,H);
    ctx.fillStyle = '#000000';
    const snakeP = [{x:5,y:5},{x:4,y:5},{x:3,y:5},{x:2,y:5},{x:1,y:5},{x:1,y:6},{x:1,y:7},{x:2,y:7},{x:3,y:7},{x:3,y:6}];
    snakeP.forEach(s => ctx.fillRect(s.x*cell, s.y*cell, cell-1, cell-1));
    ctx.fillRect(W - 20, 20, 8, 8);
    return;
  }
  if (!state) return;

  state.timer++;
  if (state.timer >= state.speed && !state.dead) {
    state.timer = 0;
    state.dir = {...state.nextDir};
    const head = { x: state.snake[0].x + state.dir.x, y: state.snake[0].y + state.dir.y };

    // Wall collision
    if (head.x < 0 || head.x >= state.cols || head.y < 0 || head.y >= state.rows) {
      state.dead = true;
    } else if (state.snake.some(s => s.x === head.x && s.y === head.y)) {
      state.dead = true;
    } else {
      state.snake.unshift(head);
      if (head.x === state.food.x && head.y === state.food.y) {
        state.score++;
        state.food = spawnFood(state);
        if (state.speed > 3) state.speed = Math.max(3, state.speed - 0.3);
      } else {
        state.snake.pop();
      }
    }
  }

  const cell = state.cell;

  // Draw snake
  state.snake.forEach((s, i) => {
    const alpha = 1 - (i / state.snake.length) * 0.4;
    ctx.fillStyle = `rgba(0,0,0,${alpha})`;
    ctx.fillRect(s.x * cell + 1, s.y * cell + 1, cell - 2, cell - 2);
  });

  // Draw food
  ctx.fillStyle = '#000000';
  ctx.fillRect(state.food.x * cell + 2, state.food.y * cell + 2, cell - 4, cell - 4);

  // Score
  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 14px monospace';
  ctx.fillText('Score: ' + state.score, 8, 20);

  if (state.dead) {
    ctx.fillStyle = 'rgba(0,0,0,0.5)';
    ctx.fillRect(0, 0, W, H);
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 36px monospace';
    ctx.textAlign = 'center';
    ctx.fillText('GAME OVER', W/2, H/2);
    ctx.font = '18px monospace';
    ctx.fillText('Score: ' + state.score, W/2, H/2 + 35);
    ctx.textAlign = 'left';
  }
}

// --- PLATFORMER ---
function initPlatformer(ctx, preview, state, canvas) {
  if (preview) return;
  const W = canvas.width, H = canvas.height;
  state.W = W; state.H = H;
  state.player = { x: 80, y: H - 120, w: 32, h: 40, vx: 0, vy: 0, onGround: false, facing: 1 };
  state.platforms = [
    { x: 0, y: H - 60, w: W * 0.45, h: 20 },
    { x: W * 0.55, y: H - 60, w: W * 0.45, h: 20 },
    { x: W * 0.25, y: H - 180, w: 200, h: 15 },
    { x: W * 0.6, y: H - 220, w: 180, h: 15 },
    { x: 0, y: H - 300, w: 150, h: 15 },
  ];
  state.enemies = [
    { x: W * 0.3, y: H - 95, w: 28, h: 28, vx: 1.5, alive: true },
    { x: W * 0.6, y: H - 215, w: 28, h: 28, vx: -1, alive: true },
  ];
  state.collectibles = [
    { x: W*0.35, y: H-210, collected: false },
    { x: W*0.65, y: H-255, collected: false },
    { x: 60, y: H-335, collected: false },
  ];
  state.score = 0;
  state.gravity = 0.5;
  state.keys = {};

  document.onkeydown = (e) => { state.keys[e.key] = true; };
  document.onkeyup = (e) => { state.keys[e.key] = false; };
}

function drawPlatformer(ctx, state, preview) {
  const W = ctx.canvas.width, H = ctx.canvas.height;
  if (preview) {
    // Ground
    ctx.fillStyle = '#5a3a1a';
    ctx.fillRect(0, H*0.75, W*0.45, H*0.25);
    ctx.fillRect(W*0.55, H*0.75, W*0.45, H*0.25);
    ctx.fillStyle = '#4a8a4a';
    ctx.fillRect(0, H*0.73, W*0.45, 8);
    ctx.fillRect(W*0.55, H*0.73, W*0.45, 8);
    // Platform
    ctx.fillStyle = '#5a3a1a';
    ctx.fillRect(W*0.25, H*0.55, 200*(W/800), 8);
    // Player (ninja)
    drawNinja(ctx, W*0.12, H*0.55, 32*(W/800), 40*(H/500), '#5599ff', 1);
    // Enemies (mushrooms)
    drawMushroom(ctx, W*0.35, H*0.62, 28*(W/800));
    return;
  }
  if (!state) return;

  const { player, platforms, enemies, collectibles, keys, gravity } = state;

  // Update player
  if (keys['ArrowLeft'] || keys['a']) { player.vx = -4; player.facing = -1; }
  else if (keys['ArrowRight'] || keys['d']) { player.vx = 4; player.facing = 1; }
  else player.vx *= 0.8;

  if ((keys['ArrowUp'] || keys['w'] || keys[' ']) && player.onGround) {
    player.vy = -12;
    player.onGround = false;
  }

  player.vy += gravity;
  player.x += player.vx;
  player.y += player.vy;
  player.onGround = false;

  // Platform collision
  platforms.forEach(p => {
    if (player.x + player.w > p.x && player.x < p.x + p.w &&
        player.y + player.h > p.y && player.y + player.h < p.y + p.h + 15 && player.vy > 0) {
      player.y = p.y - player.h;
      player.vy = 0;
      player.onGround = true;
    }
  });

  // Clamp
  player.x = Math.max(0, Math.min(state.W - player.w, player.x));
  if (player.y > state.H) { player.y = 0; player.vy = 0; }

  // Enemies
  enemies.forEach(e => {
    if (!e.alive) return;
    e.x += e.vx;
    if (e.x < 0 || e.x > state.W - e.w) e.vx *= -1;
    // Bounce on platforms
    platforms.forEach(p => {
      if (e.x + e.w > p.x && e.x < p.x + p.w &&
          e.y + e.h > p.y && e.y + e.h < p.y + p.h + 10) {
        e.y = p.y - e.h;
      }
    });
    // Player jump on enemy
    if (player.x + player.w > e.x && player.x < e.x + e.w &&
        player.y + player.h > e.y && player.y + player.h < e.y + e.h * 0.4 && player.vy > 0) {
      e.alive = false;
      player.vy = -8;
      state.score += 50;
    }
  });

  // Collectibles
  collectibles.forEach(c => {
    if (c.collected) return;
    if (player.x + player.w > c.x && player.x < c.x + 16 &&
        player.y + player.h > c.y && player.y < c.y + 16) {
      c.collected = true;
      state.score += 20;
    }
  });

  // Draw platforms
  platforms.forEach(p => {
    ctx.fillStyle = '#4a8a4a';
    ctx.fillRect(p.x, p.y, p.w, 8);
    ctx.fillStyle = '#5a3a1a';
    ctx.fillRect(p.x, p.y + 8, p.w, p.h - 8);
  });

  // Draw collectibles (strawberries)
  collectibles.forEach(c => {
    if (c.collected) return;
    drawStrawberry(ctx, c.x, c.y, 16);
  });

  // Draw enemies
  enemies.forEach(e => {
    if (!e.alive) return;
    drawMushroom(ctx, e.x, e.y, e.w);
  });

  // Draw player
  drawNinja(ctx, player.x, player.y, player.w, player.h, '#5599ff', player.facing);

  // Score
  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 14px monospace';
  ctx.fillText('Score: ' + state.score, 10, 22);
}

function drawNinja(ctx, x, y, w, h, color, dir) {
  ctx.save();
  ctx.translate(x + w/2, y + h/2);
  if (dir < 0) ctx.scale(-1, 1);
  ctx.translate(-w/2, -h/2);
  // Body
  ctx.fillStyle = color;
  ctx.fillRect(4, h*0.3, w-8, h*0.5);
  // Head
  ctx.fillStyle = color;
  ctx.fillRect(3, 0, w-6, h*0.35);
  // Eyes
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(w*0.55, h*0.1, w*0.12, h*0.12);
  // Belt
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(4, h*0.55, w-8, 4);
  // Legs
  ctx.fillStyle = '#333399';
  ctx.fillRect(4, h*0.75, (w-10)/2, h*0.25);
  ctx.fillRect(w/2+1, h*0.75, (w-10)/2, h*0.25);
  ctx.restore();
}

function drawMushroom(ctx, x, y, w) {
  ctx.fillStyle = '#cc2222';
  ctx.beginPath();
  ctx.arc(x + w/2, y + w*0.4, w*0.55, Math.PI, 0);
  ctx.fill();
  ctx.fillStyle = '#ff4444';
  ctx.fillRect(x + 2, y + w*0.35, w - 4, w*0.6);
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(x + w*0.2, y + w*0.5, w*0.2, w*0.15);
  ctx.fillRect(x + w*0.6, y + w*0.5, w*0.2, w*0.15);
  // Dots on cap
  ctx.fillStyle = '#ffffff';
  ctx.beginPath(); ctx.arc(x+w*0.3, y+w*0.25, 4, 0, Math.PI*2); ctx.fill();
  ctx.beginPath(); ctx.arc(x+w*0.7, y+w*0.2, 3, 0, Math.PI*2); ctx.fill();
}

function drawStrawberry(ctx, x, y, s) {
  ctx.fillStyle = '#dd2222';
  ctx.beginPath();
  ctx.arc(x + s*0.35, y + s*0.4, s*0.4, 0, Math.PI*2);
  ctx.fill();
  ctx.beginPath();
  ctx.arc(x + s*0.65, y + s*0.4, s*0.4, 0, Math.PI*2);
  ctx.fill();
  ctx.beginPath();
  ctx.moveTo(x+s*0.5, y+s);
  ctx.lineTo(x+s*0.1, y+s*0.4);
  ctx.lineTo(x+s*0.9, y+s*0.4);
  ctx.closePath();
  ctx.fill();
  ctx.fillStyle = '#22aa22';
  ctx.fillRect(x+s*0.45, y, 3, s*0.3);
}

// --- PONG ---
function initPong(ctx, preview, state, canvas) {
  if (preview) return;
  const W = canvas.width, H = canvas.height;
  state.W = W; state.H = H;
  state.p1 = { x: 30, y: H/2 - 50, w: 14, h: 80, score: 0 };
  state.p2 = { x: W - 44, y: H/2 - 50, w: 14, h: 80, score: 0 };
  state.ball = { x: W/2, y: H/2, r: 8, vx: 4, vy: 3 };
  state.keys = {};
  state.ballColor = '#ff6633';
  state.frameCount = 0;

  document.onkeydown = (e) => { state.keys[e.key] = true; };
  document.onkeyup = (e) => { state.keys[e.key] = false; };
}

function drawPong(ctx, state, preview) {
  const W = ctx.canvas.width, H = ctx.canvas.height;
  if (preview) {
    // Dashed center line
    ctx.strokeStyle = '#ffffff';
    ctx.setLineDash([8, 8]);
    ctx.lineWidth = 3;
    ctx.beginPath(); ctx.moveTo(W/2, 0); ctx.lineTo(W/2, H); ctx.stroke();
    ctx.setLineDash([]);
    // Paddles
    ctx.fillStyle = '#ffff44';
    ctx.fillRect(W*0.22, H*0.3, 14, 70);
    ctx.fillStyle = '#ff4444';
    ctx.fillRect(W*0.74, H*0.4, 14, 70);
    // Ball
    ctx.fillStyle = '#ff8844';
    ctx.beginPath(); ctx.arc(W*0.6, H*0.45, 8, 0, Math.PI*2); ctx.fill();
    return;
  }
  if (!state) return;

  const { p1, p2, ball, keys } = state;
  state.frameCount++;

  // Move players
  if (keys['w'] || keys['W']) p1.y -= 5;
  if (keys['s'] || keys['S']) p1.y += 5;
  p1.y = Math.max(0, Math.min(state.H - p1.h, p1.y));

  // AI for p2
  if (ball.y > p2.y + p2.h/2 + 5) p2.y += 4;
  else if (ball.y < p2.y + p2.h/2 - 5) p2.y -= 4;
  p2.y = Math.max(0, Math.min(state.H - p2.h, p2.y));

  // Ball movement
  ball.x += ball.vx;
  ball.y += ball.vy;

  // Wall bounce
  if (ball.y - ball.r < 0) { ball.y = ball.r; ball.vy *= -1; }
  if (ball.y + ball.r > state.H) { ball.y = state.H - ball.r; ball.vy *= -1; }

  // Paddle collision P1
  if (ball.x - ball.r < p1.x + p1.w && ball.x > p1.x && ball.y > p1.y && ball.y < p1.y + p1.h) {
    ball.x = p1.x + p1.w + ball.r;
    ball.vx = Math.abs(ball.vx) * 1.05;
    ball.vy += (ball.y - (p1.y + p1.h/2)) * 0.1;
  }
  // Paddle collision P2
  if (ball.x + ball.r > p2.x && ball.x < p2.x + p2.w && ball.y > p2.y && ball.y < p2.y + p2.h) {
    ball.x = p2.x - ball.r;
    ball.vx = -Math.abs(ball.vx) * 1.05;
    ball.vy += (ball.y - (p2.y + p2.h/2)) * 0.1;
  }

  // Clamp speed
  ball.vx = Math.max(-10, Math.min(10, ball.vx));
  ball.vy = Math.max(-8, Math.min(8, ball.vy));

  // Score
  if (ball.x < 0) { p2.score++; ball.x = state.W/2; ball.y = state.H/2; ball.vx = 4; ball.vy = 3; }
  if (ball.x > state.W) { p1.score++; ball.x = state.W/2; ball.y = state.H/2; ball.vx = -4; ball.vy = 3; }

  // Borders
  ctx.strokeStyle = '#ffffff';
  ctx.lineWidth = 3;
  ctx.strokeRect(1, 1, state.W - 2, state.H - 2);

  // Center dashed line
  ctx.strokeStyle = 'rgba(255,255,255,0.4)';
  ctx.setLineDash([12, 12]);
  ctx.lineWidth = 3;
  ctx.beginPath(); ctx.moveTo(state.W/2, 0); ctx.lineTo(state.W/2, state.H); ctx.stroke();
  ctx.setLineDash([]);

  // Paddles
  ctx.fillStyle = '#ffff44';
  ctx.fillRect(p1.x, p1.y, p1.w, p1.h);
  ctx.fillStyle = '#ff4444';
  ctx.fillRect(p2.x, p2.y, p2.w, p2.h);

  // Ball
  ctx.fillStyle = state.ballColor;
  ctx.beginPath(); ctx.arc(ball.x, ball.y, ball.r, 0, Math.PI*2); ctx.fill();

  // Scores
  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 28px monospace';
  ctx.textAlign = 'center';
  ctx.fillText(p1.score, state.W * 0.25, 40);
  ctx.fillText(p2.score, state.W * 0.75, 40);
  ctx.textAlign = 'left';

  ctx.fillStyle = 'rgba(255,255,255,0.4)';
  ctx.font = '12px monospace';
  ctx.fillText('W/S para mover', 10, state.H - 10);
}

// --- BREAKOUT ---
function initBreakout(ctx, preview, state, canvas) {
  if (preview) return;
  const W = canvas.width, H = canvas.height;
  state.W = W; state.H = H;
  const brickW = (W - 40) / 12;
  const brickH = 20;
  state.bricks = [];
  const brickColors = ['#ff2222','#ff8800','#ffff00','#ff44ff','#44ff44'];
  for (let row = 0; row < 5; row++) {
    for (let col = 0; col < 12; col++) {
      state.bricks.push({ x: 20 + col * brickW, y: 60 + row * 28, w: brickW - 3, h: brickH, alive: true, color: brickColors[row] });
    }
  }
  state.paddle = { x: W/2 - 50, y: H - 40, w: 100, h: 14 };
  state.ball = { x: W/2, y: H - 80, r: 8, vx: 3, vy: -5 };
  state.score = 0;
  state.lives = 3;
  state.started = false;
  state.gameOver = false;
  state.win = false;
  state.frameCount = 0;

  canvas.onmousemove = (e) => {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    state.paddle.x = (e.clientX - rect.left) * scaleX - state.paddle.w / 2;
    state.paddle.x = Math.max(0, Math.min(W - state.paddle.w, state.paddle.x));
    state.started = true;
  };
  canvas.onclick = () => { state.started = true; };
}

function drawBreakout(ctx, state, preview) {
  const W = ctx.canvas.width, H = ctx.canvas.height;
  if (preview) {
    const brickColors = ['#ff2222','#ff8800','#ffff00','#ff44ff','#44ff44'];
    const bW = (W - 20) / 12;
    for (let row = 0; row < 5; row++) {
      for (let col = 0; col < 12; col++) {
        ctx.fillStyle = brickColors[row];
        ctx.fillRect(10 + col * bW, 20 + row * 20, bW - 2, 16);
      }
    }
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(W/2 - 40, H - 30, 80, 10);
    ctx.beginPath(); ctx.arc(W/2 + 20, H - 50, 6, 0, Math.PI*2); ctx.fill();
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 12px monospace';
    ctx.fillText('LEVEL 1', W/2 - 25, H/2 + 30);
    return;
  }
  if (!state) return;

  const { paddle, ball, bricks } = state;
  state.frameCount++;

  if (!state.started) {
    // Draw static preview and instruction
    bricks.forEach(b => {
      if (!b.alive) return;
      ctx.fillStyle = b.color;
      ctx.fillRect(b.x, b.y, b.w, b.h);
    });
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(paddle.x, paddle.y, paddle.w, paddle.h);
    ctx.fillStyle = '#ffffff';
    ctx.beginPath(); ctx.arc(ball.x, ball.y, ball.r, 0, Math.PI*2); ctx.fill();
    ctx.fillStyle = 'rgba(0,0,0,0.5)';
    ctx.fillRect(0,0,state.W,state.H);
    ctx.fillStyle = '#00ffff';
    ctx.font = 'bold 24px Orbitron, monospace';
    ctx.textAlign = 'center';
    ctx.fillText('Mova o mouse para jogar', state.W/2, state.H/2);
    ctx.textAlign = 'left';
    return;
  }

  if (!state.gameOver && !state.win) {
    // Move ball
    ball.x += ball.vx;
    ball.y += ball.vy;

    // Wall collision
    if (ball.x - ball.r < 0) { ball.x = ball.r; ball.vx *= -1; }
    if (ball.x + ball.r > state.W) { ball.x = state.W - ball.r; ball.vx *= -1; }
    if (ball.y - ball.r < 0) { ball.y = ball.r; ball.vy *= -1; }

    // Paddle collision
    if (ball.y + ball.r > paddle.y && ball.x > paddle.x && ball.x < paddle.x + paddle.w && ball.vy > 0) {
      ball.vy = -Math.abs(ball.vy);
      ball.vx += (ball.x - (paddle.x + paddle.w/2)) * 0.05;
    }

    // Lost ball
    if (ball.y > state.H + 20) {
      state.lives--;
      if (state.lives <= 0) { state.gameOver = true; }
      else {
        ball.x = state.W/2; ball.y = state.H - 80;
        ball.vx = 3; ball.vy = -5;
      }
    }

    // Brick collision
    bricks.forEach(b => {
      if (!b.alive) return;
      if (ball.x + ball.r > b.x && ball.x - ball.r < b.x + b.w &&
          ball.y + ball.r > b.y && ball.y - ball.r < b.y + b.h) {
        b.alive = false;
        state.score += 10;
        ball.vy *= -1;
      }
    });

    if (!bricks.some(b => b.alive)) state.win = true;
  }

  // Side borders
  ctx.strokeStyle = 'rgba(255,255,255,0.3)';
  ctx.lineWidth = 4;
  ctx.strokeRect(2, 2, state.W - 4, state.H - 4);

  // Bricks
  bricks.forEach(b => {
    if (!b.alive) return;
    ctx.fillStyle = b.color;
    ctx.fillRect(b.x, b.y, b.w, b.h);
    ctx.strokeStyle = 'rgba(0,0,0,0.3)';
    ctx.lineWidth = 1;
    ctx.strokeRect(b.x, b.y, b.w, b.h);
  });

  // Ball
  ctx.fillStyle = '#88eeff';
  ctx.beginPath(); ctx.arc(ball.x, ball.y, ball.r, 0, Math.PI*2); ctx.fill();

  // Paddle
  ctx.fillStyle = '#ff66aa';
  ctx.fillRect(paddle.x, paddle.y, paddle.w, paddle.h);

  // HUD
  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 14px monospace';
  ctx.fillText('LEVEL 1', state.W/2 - 30, state.H/2 + 20);

  // Lives (hearts)
  for (let i = 0; i < state.lives; i++) {
    ctx.fillStyle = '#ff2222';
    ctx.font = '18px sans-serif';
    ctx.fillText('♥', state.W - 30 - i * 22, 25);
  }

  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 14px monospace';
  ctx.fillText('Score: ' + state.score, 10, 25);

  if (state.gameOver) {
    ctx.fillStyle = 'rgba(0,0,0,0.6)'; ctx.fillRect(0,0,state.W,state.H);
    ctx.fillStyle = '#ff2d9a';
    ctx.font = 'bold 40px Orbitron, monospace';
    ctx.textAlign = 'center';
    ctx.fillText('GAME OVER', state.W/2, state.H/2);
    ctx.textAlign = 'left';
  }
  if (state.win) {
    ctx.fillStyle = 'rgba(0,0,0,0.6)'; ctx.fillRect(0,0,state.W,state.H);
    ctx.fillStyle = '#00ffff';
    ctx.font = 'bold 40px Orbitron, monospace';
    ctx.textAlign = 'center';
    ctx.fillText('YOU WIN!', state.W/2, state.H/2);
    ctx.textAlign = 'left';
  }
}

// --- RPG ---
function initRPG(ctx, preview, state, canvas) {
  if (preview) return;
  const W = canvas.width, H = canvas.height;
  state.W = W; state.H = H;
  state.player = { x: W/2, y: H*0.65, speed: 3 };
  state.keys = {};
  state.camX = 0; state.camY = 0;
  state.npc = { x: W/2 - 80, y: H*0.45 };
  state.treePos = [
    {x: 50, y: 50}, {x: 150, y: 80}, {x: W-100, y: 60}, {x: W-180, y: 100},
    {x: 30, y: H-150}, {x: W-50, y: H-200}
  ];
  state.frameCount = 0;

  document.onkeydown = (e) => { state.keys[e.key] = true; };
  document.onkeyup = (e) => { state.keys[e.key] = false; };
}

function drawRPG(ctx, state, preview) {
  const W = ctx.canvas.width, H = ctx.canvas.height;
  if (preview) {
    // Stone path
    ctx.fillStyle = '#c0c0c0';
    ctx.fillRect(W*0.35, H*0.35, W*0.3, H*0.5);
    ctx.fillRect(W*0.2, H*0.6, W*0.6, H*0.15);
    // Grass
    ctx.fillStyle = '#5a8a5a';
    ctx.fillRect(0, 0, W, H*0.15);
    ctx.fillRect(0, H*0.15, W*0.2, H*0.7);
    ctx.fillRect(W*0.8, H*0.15, W*0.2, H*0.7);
    // Building
    ctx.fillStyle = '#8a8a6a';
    ctx.fillRect(W*0.05, H*0.05, W*0.4, H*0.35);
    ctx.fillRect(W*0.55, H*0.05, W*0.4, H*0.35);
    // Roof
    ctx.fillStyle = '#aa9955';
    ctx.fillRect(W*0.05, H*0.02, W*0.4, H*0.06);
    ctx.fillRect(W*0.55, H*0.02, W*0.4, H*0.06);
    // Character
    drawRPGChar(ctx, W*0.35, H*0.6, 20, 28, '#888855');
    return;
  }
  if (!state) return;

  state.frameCount++;
  const { player, keys } = state;

  if (keys['ArrowLeft'] || keys['a']) player.x -= player.speed;
  if (keys['ArrowRight'] || keys['d']) player.x += player.speed;
  if (keys['ArrowUp'] || keys['w']) player.y -= player.speed;
  if (keys['ArrowDown'] || keys['s']) player.y += player.speed;

  player.x = Math.max(20, Math.min(state.W - 20, player.x));
  player.y = Math.max(20, Math.min(state.H - 20, player.y));

  // BG
  ctx.fillStyle = '#b8d4a8';
  ctx.fillRect(0, 0, state.W, state.H);

  // Stone path pattern
  ctx.fillStyle = '#c8c8c0';
  const stoneSize = 40;
  for (let row = 0; row < state.H / stoneSize + 1; row++) {
    for (let col = 0; col < state.W / stoneSize + 1; col++) {
      if ((row + col) % 2 === 0) {
        ctx.fillStyle = '#c5c5bb';
      } else {
        ctx.fillStyle = '#babab0';
      }
      // Only draw path area
      if (col >= 4 && col <= 7) {
        ctx.fillRect(col * stoneSize, row * stoneSize, stoneSize - 2, stoneSize - 2);
      }
      if (row >= 4 && row <= 6) {
        ctx.fillRect(col * stoneSize, row * stoneSize, stoneSize - 2, stoneSize - 2);
      }
    }
  }

  // Buildings
  const buildingColor = '#888877';
  const roofColor = '#aa9944';
  // Left building
  ctx.fillStyle = buildingColor;
  ctx.fillRect(10, 10, state.W * 0.38, state.H * 0.38);
  ctx.fillStyle = roofColor;
  ctx.fillRect(10, 5, state.W * 0.38, state.H * 0.06);
  // Windows left
  ctx.fillStyle = '#666655';
  ctx.fillRect(40, 40, 50, 40); ctx.fillRect(120, 40, 50, 40); ctx.fillRect(200, 40, 50, 40);
  ctx.fillRect(40, 100, 50, 40); ctx.fillRect(120, 100, 50, 40); ctx.fillRect(200, 100, 50, 40);

  // Right building
  ctx.fillStyle = buildingColor;
  ctx.fillRect(state.W * 0.62, 10, state.W * 0.38 - 10, state.H * 0.38);
  ctx.fillStyle = roofColor;
  ctx.fillRect(state.W * 0.62, 5, state.W * 0.38 - 10, state.H * 0.06);
  ctx.fillStyle = '#666655';
  const rx = state.W * 0.62;
  ctx.fillRect(rx + 30, 40, 50, 40); ctx.fillRect(rx + 100, 40, 50, 40); ctx.fillRect(rx + 170, 40, 50, 40);
  ctx.fillRect(rx + 30, 100, 50, 40); ctx.fillRect(rx + 100, 100, 50, 40);

  // Fence
  ctx.fillStyle = '#8b6a3a';
  for (let i = 0; i < state.W / 40; i++) {
    ctx.fillRect(i * 40, state.H * 0.42, 8, 30);
    ctx.fillRect(i * 40 + 12, state.H * 0.42, 8, 30);
    ctx.fillRect(i * 40 + 24, state.H * 0.42, 8, 30);
    if (i > 0) ctx.fillRect(i * 40 - 20, state.H * 0.49, 40, 6);
  }
  // Fence bottom gap
  ctx.fillStyle = '#b8d4a8';
  ctx.fillRect(state.W * 0.38, state.H * 0.42, state.W * 0.24, 40);

  // Trees
  state.treePos.forEach(t => {
    ctx.fillStyle = '#228822';
    ctx.beginPath(); ctx.arc(t.x, t.y, 25, 0, Math.PI*2); ctx.fill();
    ctx.fillStyle = '#6a4a2a';
    ctx.fillRect(t.x - 5, t.y + 15, 10, 25);
  });

  // Barrel
  ctx.fillStyle = '#8b5a2a';
  ctx.fillRect(player.x - 150, state.H*0.65, 24, 30);
  ctx.fillRect(player.x + 120, state.H*0.65, 24, 30);

  // NPC
  const bob = Math.sin(state.frameCount * 0.05) * 2;
  drawRPGChar(ctx, state.npc.x, state.npc.y + bob, 22, 32, '#887755');

  // Barrels at path
  ctx.fillStyle = '#6a4a1a';
  ctx.fillRect(state.W*0.36 - 30, state.H*0.65, 22, 28);

  // Player (bottom of canvas)
  const playerBob = Math.sin(state.frameCount * 0.1) * 1;
  drawRPGChar(ctx, player.x, player.y + playerBob, 22, 32, '#5555aa');

  // Controls hint
  ctx.fillStyle = 'rgba(255,255,255,0.6)';
  ctx.font = '12px monospace';
  ctx.fillText('WASD / Setas para mover', 10, state.H - 10);
}

function drawRPGChar(ctx, x, y, w, h, color) {
  // Body
  ctx.fillStyle = color;
  ctx.fillRect(x - w/2, y - h*0.6, w, h*0.5);
  // Head
  ctx.fillStyle = '#d4aa88';
  ctx.fillRect(x - w/2 + 3, y - h, w - 6, h*0.42);
  // Legs
  ctx.fillStyle = color;
  ctx.fillRect(x - w/2, y - h*0.1, w/2 - 2, h*0.4);
  ctx.fillRect(x + 2, y - h*0.1, w/2 - 2, h*0.4);
  // Eyes
  ctx.fillStyle = '#333';
  ctx.fillRect(x - 5, y - h*0.72, 4, 4);
  ctx.fillRect(x + 2, y - h*0.72, 4, 4);
}

// Initial render
renderLibrary();
</script>
</body>
</html>