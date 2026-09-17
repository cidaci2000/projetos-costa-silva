/* ============================================================
   LE PARFUM - APP.JS (Versão PHP)
   JavaScript complementar para interações no cliente
   ============================================================ */

(function () {
  'use strict';

  // ============================================================
  // TOASTS / MENSAGENS FLASH
  // ============================================================
  function inicializarToasts() {
    const toast = document.getElementById('toast-flash');
    if (!toast) return;

    // Auto-remove após 4 segundos
    setTimeout(() => {
      toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => toast.remove(), 400);
    }, 4000);

    // Fecha ao clicar
    toast.style.cursor = 'pointer';
    toast.addEventListener('click', () => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => toast.remove(), 400);
    });
  }

  // Cria toast dinamicamente (para uso futuro via JS)
  window.mostrarToast = function (mensagem, tipo = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${tipo}`;
    toast.innerHTML = `<span>${tipo === 'success' ? '✅' : tipo === 'error' ? '❌' : 'ℹ️'}</span> <span>${mensagem}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => toast.remove(), 400);
    }, 3500);
  };

  // ============================================================
  // MÁSCARAS DE FORMULÁRIO
  // ============================================================
  function aplicarMascaraCEP(input) {
    input.addEventListener('input', (e) => {
      let v = e.target.value.replace(/\D/g, '').slice(0, 8);
      if (v.length > 5) v = v.replace(/^(\d{5})(\d)/, '$1-$2');
      e.target.value = v;
    });
  }

  function aplicarMascaraTelefone(input) {
    input.addEventListener('input', (e) => {
      let v = e.target.value.replace(/\D/g, '').slice(0, 11);
      if (v.length > 10) {
        v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
      } else if (v.length > 6) {
        v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
      } else if (v.length > 2) {
        v = v.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
      } else if (v.length > 0) {
        v = v.replace(/^(\d{0,2})$/, '($1');
      }
      e.target.value = v;
    });
  }

  function aplicarMascaraMoeda(input) {
    input.addEventListener('input', (e) => {
      let v = e.target.value.replace(/\D/g, '');
      if (v === '') {
        e.target.value = '';
        return;
      }
      v = (parseInt(v, 10) / 100).toFixed(2);
      v = v.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      e.target.value = 'R$ ' + v;
    });
  }

  function inicializarMascaras() {
    document.querySelectorAll('input[name="cep"], #checkout-cep').forEach(aplicarMascaraCEP);
    document.querySelectorAll('input[name="telefone"], #checkout-tel').forEach(aplicarMascaraTelefone);
    document.querySelectorAll('input[data-mask="moeda"]').forEach(aplicarMascaraMoeda);
  }

  // ============================================================
  // CONFIRMAÇÃO DE AÇÕES (deletar, etc.)
  // ============================================================
  function inicializarConfirmacoes() {
    document.querySelectorAll('[data-confirm]').forEach((el) => {
      el.addEventListener('click', (e) => {
        const msg = el.getAttribute('data-confirm') || 'Tem certeza?';
        if (!confirm(msg)) {
          e.preventDefault();
          return false;
        }
      });
    });
  }

  // ============================================================
  // ANIMAÇÃO DE ENTRADA DOS CARDS
  // ============================================================
  function animarCards() {
    const cards = document.querySelectorAll('.product-card');
    if (!cards.length) return;

    // Usa IntersectionObserver para animar apenas quando visível
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.style.opacity = '1';
              entry.target.style.transform = 'translateY(0)';
              observer.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.1 }
      );

      cards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.5s ease ${i * 0.04}s, transform 0.5s ease ${i * 0.04}s`;
        observer.observe(card);
      });
    }
  }

  // ============================================================
  // NAVBAR SCROLL EFFECT
  // ============================================================
  function inicializarNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    let lastScroll = 0;
    window.addEventListener('scroll', () => {
      const currentScroll = window.scrollY;
      if (currentScroll > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
      lastScroll = currentScroll;
    }, { passive: true });
  }

  // ============================================================
  // CONTADOR ANIMADO (opcional, para o admin)
  // ============================================================
  function animarContadores() {
    document.querySelectorAll('[data-count]').forEach((el) => {
      const target = parseInt(el.getAttribute('data-count'), 10);
      if (isNaN(target)) return;
      let atual = 0;
      const passo = Math.max(1, Math.ceil(target / 40));
      const timer = setInterval(() => {
        atual += passo;
        if (atual >= target) {
          atual = target;
          clearInterval(timer);
        }
        el.textContent = atual;
      }, 20);
    });
  }

  // ============================================================
  // VALIDAÇÃO DE FORMULÁRIOS (reforço)
  // ============================================================
  function inicializarValidacao() {
    document.querySelectorAll('form').forEach((form) => {
      form.addEventListener('submit', (e) => {
        const senha = form.querySelector('input[name="senha"]');
        const confirma = form.querySelector('input[name="confirma_senha"]');

        if (senha && confirma && senha.value !== confirma.value) {
          e.preventDefault();
          window.mostrarToast('As senhas não coincidem.', 'error');
          return false;
        }

        if (senha && senha.value && senha.value.length < 6 && senha.hasAttribute('minlength')) {
          // validação nativa já cobre, mas reforçamos
          if (senha.value.length < 6) {
            e.preventDefault();
            window.mostrarToast('A senha deve ter ao menos 6 caracteres.', 'error');
            return false;
          }
        }
      });
    });
  }

  // ============================================================
  // BOTÃO "VOLTAR AO TOPO"
  // ============================================================
  function inicializarBotaoTopo() {
    const btn = document.createElement('button');
    btn.innerHTML = '↑';
    btn.setAttribute('aria-label', 'Voltar ao topo');
    btn.style.cssText = `
      position: fixed;
      bottom: 24px;
      left: 24px;
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: var(--gold, #c9a96a);
      color: #fff;
      font-size: 1.4rem;
      font-weight: 700;
      border: none;
      cursor: pointer;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease, transform 0.3s ease;
      z-index: 1500;
      box-shadow: 0 6px 20px rgba(201, 169, 106, 0.4);
    `;
    document.body.appendChild(btn);

    window.addEventListener('scroll', () => {
      if (window.scrollY > 400) {
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
      } else {
        btn.style.opacity = '0';
        btn.style.pointerEvents = 'none';
      }
    }, { passive: true });

    btn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    btn.addEventListener('mouseenter', () => {
      btn.style.transform = 'translateY(-4px) scale(1.05)';
    });
    btn.addEventListener('mouseleave', () => {
      btn.style.transform = 'translateY(0) scale(1)';
    });
  }

  // ============================================================
  // DROPDOWN / MENU MOBILE (se quiser adicionar depois)
  // ============================================================
  function inicializarMenuMobile() {
    const nav = document.querySelector('.navbar-nav');
    const actions = document.querySelector('.navbar-actions');
    if (!nav || !actions) return;

    // Cria botão hamburguer apenas em telas pequenas
    if (window.innerWidth <= 768 && !document.getElementById('menu-toggle')) {
      const btn = document.createElement('button');
      btn.id = 'menu-toggle';
      btn.className = 'icon-btn';
      btn.innerHTML = '☰';
      btn.setAttribute('aria-label', 'Menu');
      actions.insertBefore(btn, actions.firstChild);

      btn.addEventListener('click', () => {
        nav.classList.toggle('mobile-open');
      });
    }
  }

  // ============================================================
  // SMOOTH SCROLL PARA ÂNCORAS
  // ============================================================
  function inicializarSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((link) => {
      link.addEventListener('click', (e) => {
        const id = link.getAttribute('href');
        if (id === '#' || id.length < 2) return;
        const target = document.querySelector(id);
        if (target) {
          e.preventDefault();
          const top = target.getBoundingClientRect().top + window.scrollY - 90;
          window.scrollTo({ top, behavior: 'smooth' });
        }
      });
    });
  }

  // ============================================================
  // LAZY LOADING DE IMAGENS (fallback para navegadores antigos)
  // ============================================================
  function inicializarLazyLoad() {
    if ('loading' in HTMLImageElement.prototype) return; // já suportado nativamente

    const imgs = document.querySelectorAll('img[loading="lazy"]');
    if (!imgs.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src || img.src;
          observer.unobserve(img);
        }
      });
    });

    imgs.forEach((img) => observer.observe(img));
  }

  // ============================================================
  // INICIALIZAÇÃO GERAL
  // ============================================================
  document.addEventListener('DOMContentLoaded', () => {
    inicializarToasts();
    inicializarMascaras();
    inicializarConfirmacoes();
    animarCards();
    inicializarNavbarScroll();
    animarContadores();
    inicializarValidacao();
    inicializarBotaoTopo();
    inicializarMenuMobile();
    inicializarSmoothScroll();
    inicializarLazyLoad();

    // Log de boas-vindas (remova em produção)
    console.log('%c🧴 Le Parfum', 'color: #c9a96a; font-size: 16px; font-weight: bold;');
    console.log('%cSistema carregado com sucesso!', 'color: #666; font-size: 12px;');
  });

  // Reaplica máscaras ao redimensionar (para novos elementos)
  window.addEventListener('resize', () => {
    inicializarMenuMobile();
  });
})();