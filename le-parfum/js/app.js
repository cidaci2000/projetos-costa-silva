// ============================================================
// LE PARFUM - JavaScript Principal
// ============================================================

// ─── CONFIGURAÇÃO ──────────────────────────────────────────

const API_URL = '/api/api.php';
let currentUser = null;

// ─── DADOS LOCAIS (fallback) ─────────────────────────────

const CATEGORIES = [
    { id: 'feminino', name: 'Feminino', icon: '🌸', count: 12 },
    { id: 'masculino', name: 'Masculino', icon: '🪄', count: 10 },
    { id: 'unissex', name: 'Unissex', icon: '✨', count: 8 },
    { id: 'exclusivo', name: 'Exclusivos', icon: '💎', count: 6 }
];

const PRODUCTS = [
    { id: 1, nome: 'Chanel N°5', marca: 'Chanel', categoria: 'feminino', preco: 899.90, preco_antigo: 999.90, imagem_emoji: '🌸', notas: 'Floral, Aldeídos, Baunilha', descricao: 'O icônico perfume...', destaque: 1 },
    // ... outros produtos
];

// ─── API CLIENT ────────────────────────────────────────────

const API = {
    async request(action, method = 'GET', data = null) {
        const url = `${API_URL}?action=${action}`;
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };
        
        if (data) {
            options.body = JSON.stringify(data);
        }
        
        // Adicionar token se autenticado
        const token = localStorage.getItem('auth_token');
        if (token) {
            options.headers['Authorization'] = `Bearer ${token}`;
        }
        
        try {
            const response = await fetch(url, options);
            const result = await response.json();
            return result;
        } catch (error) {
            console.error('API Error:', error);
            return { success: false, message: 'Erro de conexão com o servidor' };
        }
    },
    
    // Auth
    login(email, senha) {
        return this.request('login', 'POST', { email, senha });
    },
    
    register(dados) {
        return this.request('register', 'POST', dados);
    },
    
    logout() {
        return this.request('logout', 'POST');
    },
    
    // Produtos
    getProducts(filters = {}) {
        const params = new URLSearchParams(filters).toString();
        return this.request(`getProducts&${params}`);
    },
    
    getProduct(id) {
        return this.request(`getProduct&id=${id}`);
    },
    
    getCategories() {
        return this.request('getCategories');
    },
    
    getFeatured() {
        return this.request('getFeatured');
    },
    
    // Carrinho
    getCart() {
        return this.request('getCart');
    },
    
    addToCart(productId, quantity = 1) {
        return this.request('addToCart', 'POST', { product_id: productId, quantity });
    },
    
    removeFromCart(productId) {
        return this.request('removeFromCart', 'POST', { product_id: productId });
    },
    
    updateCartQuantity(productId, quantity) {
        return this.request('updateCartQuantity', 'POST', { product_id: productId, quantity });
    },
    
    clearCart() {
        return this.request('clearCart', 'POST');
    },
    
    // Pedidos
    checkout(endereco, metodoPagamento) {
        return this.request('checkout', 'POST', { endereco, metodo_pagamento: metodoPagamento });
    },
    
    getOrders() {
        return this.request('getOrders');
    },
    
    getOrder(id) {
        return this.request(`getOrder&id=${id}`);
    },
    
    // Favoritos
    getFavorites() {
        return this.request('getFavorites');
    },
    
    toggleFavorite(productId) {
        return this.request('toggleFavorite', 'POST', { product_id: productId });
    }
};

// ─── UI HELPERS ────────────────────────────────────────────

const UI = {
    toastContainer: null,
    
    init() {
        this.toastContainer = document.getElementById('toast-container') || this.createToastContainer();
        this.setupScrollTop();
        this.setupMobileMenu();
        this.setupModalTabs();
        this.setupAuthForms();
        this.setupNewsletter();
        this.renderCart();
    },
    
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
        return container;
    },
    
    showToast(message, type = 'info') {
        const container = this.toastContainer;
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    },
    
    setupScrollTop() {
        const btn = document.querySelector('.scroll-top');
        if (!btn) return;
        
        window.addEventListener('scroll', () => {
            btn.classList.toggle('visible', window.scrollY > 300);
        });
        
        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    },
    
    setupMobileMenu() {
        const hamburger = document.querySelector('.hamburger');
        const closeBtn = document.getElementById('mobile-menu-close');
        const menu = document.getElementById('mobile-menu');
        
        if (hamburger && menu) {
            hamburger.addEventListener('click', () => menu.classList.toggle('open'));
            if (closeBtn) {
                closeBtn.addEventListener('click', () => menu.classList.remove('open'));
            }
        }
    },
    
    setupModalTabs() {
        const tabs = document.querySelectorAll('.modal-tab');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                const target = tab.dataset.tab;
                if (loginForm && registerForm) {
                    if (target === 'login') {
                        loginForm.classList.remove('hidden');
                        registerForm.classList.add('hidden');
                    } else {
                        loginForm.classList.add('hidden');
                        registerForm.classList.remove('hidden');
                    }
                }
            });
        });
    },
    
    setupAuthForms() {
        // Login
        document.getElementById('login-submit')?.addEventListener('click', async () => {
            const email = document.getElementById('login-email').value;
            const senha = document.getElementById('login-password').value;
            
            if (!email || !senha) {
                UI.showToast('Preencha todos os campos', 'error');
                return;
            }
            
            const result = await API.login(email, senha);
            if (result.success) {
                localStorage.setItem('auth_token', result.data.token);
                localStorage.setItem('usuario', JSON.stringify(result.data.usuario));
                Auth.updateUI(result.data.usuario);
                UI.closeModal('auth-modal');
                UI.showToast(`Bem-vindo(a), ${result.data.usuario.nome}!`, 'success');
                window.location.reload();
            } else {
                UI.showToast(result.message, 'error');
            }
        });
        
        // Register
        document.getElementById('register-submit')?.addEventListener('click', async () => {
            const dados = {
                nome: document.getElementById('reg-nome').value,
                email: document.getElementById('reg-email').value,
                senha: document.getElementById('reg-senha').value,
                telefone: document.getElementById('reg-telefone').value,
                cidade: document.getElementById('reg-cidade').value,
                cep: document.getElementById('reg-cep').value,
                endereco: document.getElementById('reg-endereco').value
            };
            
            // Validação básica
            if (!dados.nome || !dados.email || !dados.senha) {
                UI.showToast('Preencha todos os campos obrigatórios (*)', 'error');
                return;
            }
            
            if (dados.senha.length < 6) {
                UI.showToast('A senha deve ter no mínimo 6 caracteres', 'error');
                return;
            }
            
            const result = await API.register(dados);
            if (result.success) {
                UI.showToast('Conta criada com sucesso! Faça login.', 'success');
                // Mudar para aba de login
                document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
                document.querySelector('[data-tab="login"]').classList.add('active');
                document.getElementById('login-form').classList.remove('hidden');
                document.getElementById('register-form').classList.add('hidden');
                document.getElementById('register-form').querySelectorAll('input').forEach(i => i.value = '');
            } else {
                UI.showToast(result.message, 'error');
            }
        });
    },
    
    setupNewsletter() {
        document.getElementById('newsletter-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            const input = e.target.querySelector('input');
            if (input && input.value) {
                UI.showToast('✅ Inscrito com sucesso!', 'success');
                input.value = '';
            }
        });
    },
    
    openModal(id) {
        document.getElementById(id)?.classList.add('open');
    },
    
    closeModal(id) {
        document.getElementById(id)?.classList.remove('open');
    },
    
    openCart() {
        document.getElementById('cart-drawer')?.classList.add('open');
        document.getElementById('cart-overlay')?.classList.add('open');
        this.renderCart();
    },
    
    closeCart() {
        document.getElementById('cart-drawer')?.classList.remove('open');
        document.getElementById('cart-overlay')?.classList.remove('open');
    },
    
    async renderCart() {
        const container = document.getElementById('cart-items');
        const footerInfo = document.getElementById('cart-footer-info');
        const countBadge = document.querySelector('.cart-count');
        
        try {
            const result = await API.getCart();
            if (!result.success) {
                throw new Error(result.message);
            }
            
            const cart = result.data.cart || [];
            const total = result.data.total || 0;
            
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="cart-empty">
                        <div class="cart-empty-icon">🛒</div>
                        <p>Seu carrinho está vazio</p>
                        <small style="color:var(--gray);">Explore nosso catálogo e encontre sua fragrância perfeita</small>
                    </div>
                `;
                footerInfo.innerHTML = '';
                if (countBadge) {
                    countBadge.style.display = 'none';
                    countBadge.textContent = '0';
                }
                return;
            }
            
            let html = '';
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.preco * item.quantidade;
                subtotal += itemTotal;
                
                html += `
                    <div class="cart-item">
                        <div class="cart-item-img">${item.imagem_emoji || '🌸'}</div>
                        <div class="cart-item-info">
                            <div class="cart-item-brand">${item.marca}</div>
                            <div class="cart-item-name">${item.nome}</div>
                            <div class="cart-item-price">R$ ${item.preco.toFixed(2)}</div>
                            <div class="cart-item-qty">
                                <button class="qty-btn" onclick="UI.updateCartQty(${item.id}, ${item.quantidade - 1})">−</button>
                                <span class="qty-value">${item.quantidade}</span>
                                <button class="qty-btn" onclick="UI.updateCartQty(${item.id}, ${item.quantidade + 1})">+</button>
                                <button class="cart-item-remove" onclick="UI.removeFromCart(${item.id})">✕</button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            
            const frete = subtotal >= 500 ? 0 : 25.90;
            const totalFinal = subtotal + frete;
            
            footerInfo.innerHTML = `
                <div class="cart-subtotal"><span>Subtotal</span><span>R$ ${subtotal.toFixed(2)}</span></div>
                ${frete > 0 ? `<div class="cart-subtotal"><span>Frete</span><span>R$ ${frete.toFixed(2)}</span></div>` : '<div style="font-size:0.8rem;color:var(--success);">🚚 Frete Grátis</div>'}
                <div class="cart-total"><span>Total</span><span>R$ ${totalFinal.toFixed(2)}</span></div>
            `;
            
            if (countBadge) {
                const totalItems = cart.reduce((sum, item) => sum + item.quantidade, 0);
                countBadge.textContent = totalItems;
                countBadge.style.display = 'flex';
            }
            
        } catch (error) {
            console.error('Erro ao renderizar carrinho:', error);
            container.innerHTML = `
                <div class="cart-empty">
                    <p>Erro ao carregar carrinho</p>
                </div>
            `;
        }
    },
    
    async updateCartQty(productId, newQty) {
        if (newQty <= 0) {
            await this.removeFromCart(productId);
            return;
        }
        
        const result = await API.updateCartQuantity(productId, newQty);
        if (result.success) {
            await this.renderCart();
        } else {
            UI.showToast(result.message, 'error');
        }
    },
    
    async removeFromCart(productId) {
        const result = await API.removeFromCart(productId);
        if (result.success) {
            await this.renderCart();
            UI.showToast('Produto removido do carrinho', 'info');
        } else {
            UI.showToast(result.message, 'error');
        }
    },
    
    async addToCart(productId) {
        const result = await API.addToCart(productId);
        if (result.success) {
            await this.renderCart();
            UI.showToast('Produto adicionado ao carrinho!', 'success');
        } else {
            UI.showToast(result.message, 'error');
        }
    }
};

// ─── AUTH ──────────────────────────────────────────────────

const Auth = {
    init() {
        this.checkAuth();
    },
    
    checkAuth() {
        const usuarioStr = localStorage.getItem('usuario');
        if (usuarioStr) {
            try {
                const usuario = JSON.parse(usuarioStr);
                currentUser = usuario;
                this.updateUI(usuario);
                return usuario;
            } catch {
                return null;
            }
        }
        return null;
    },
    
    updateUI(usuario) {
        const authBtn = document.getElementById('auth-btn');
        const userMenu = document.getElementById('user-menu');
        const userName = document.getElementById('user-name');
        
        if (usuario) {
            if (authBtn) authBtn.style.display = 'none';
            if (userMenu) userMenu.style.display = 'flex';
            if (userName) userName.textContent = usuario.nome;
        } else {
            if (authBtn) authBtn.style.display = 'block';
            if (userMenu) userMenu.style.display = 'none';
        }
    },
    
    async logout() {
        await API.logout();
        localStorage.removeItem('auth_token');
        localStorage.removeItem('usuario');
        currentUser = null;
        this.updateUI(null);
        UI.showToast('Até logo!', 'info');
        window.location.reload();
    }
};

// ─── PRODUCTS UI ──────────────────────────────────────────

const ProductsUI = {
    renderGrid(products, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        if (!products || products.length === 0) {
            container.innerHTML = `
                <div style="text-align:center;padding:40px;color:var(--gray);">
                    <span style="font-size:3rem;">🔍</span>
                    <p>Nenhum produto encontrado</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = products.map(p => `
            <div class="product-card fade-in">
                <div class="product-image" onclick="window.location.href='pages/produto.html?id=${p.id}'">
                    <span class="product-emoji">${p.imagem_emoji || '🌸'}</span>
                    <div class="product-badges">
                        ${p.destaque ? '<span class="badge badge-gold">Destaque</span>' : ''}
                        ${p.preco_antigo ? '<span class="badge badge-sale">Oferta</span>' : ''}
                    </div>
                    <button class="product-wishlist" onclick="event.stopPropagation(); ProductsUI.toggleFavorite(${p.id})">
                        ♡
                    </button>
                </div>
                <div class="product-info">
                    <div class="product-brand">${p.marca}</div>
                    <div class="product-name">${p.nome}</div>
                    <div class="product-notes">${p.notas || ''}</div>
                    <div class="product-footer">
                        <div>
                            ${p.preco_antigo ? `<span class="product-price-old">R$ ${p.preco_antigo.toFixed(2)}</span>` : ''}
                            <span class="product-price">R$ ${p.preco.toFixed(2)}</span>
                        </div>
                        <button class="add-to-cart-btn" onclick="UI.addToCart(${p.id})">Add</button>
                    </div>
                </div>
            </div>
        `).join('');
    },
    
    async toggleFavorite(productId) {
        const result = await API.toggleFavorite(productId);
        if (result.success) {
            UI.showToast(result.message, 'info');
            // Atualizar botão visualmente
            const buttons = document.querySelectorAll('.product-wishlist');
            buttons.forEach(btn => {
                const parent = btn.closest('.product-card');
                if (parent) {
                    // Buscar produto pelo ID (simplificado)
                }
            });
        } else {
            UI.showToast(result.message, 'error');
        }
    }
};

// ─── CATÁLOGO ─────────────────────────────────────────────

const Catalog = {
    currentFilters: {},
    currentPage: 1,
    pageSize: 12,
    products: [],
    filteredProducts: [],
    
    init() {
        this.loadProducts();
        this.setupFilters();
    },
    
    async loadProducts() {
        const params = new URLSearchParams(window.location.search);
        const category = params.get('cat') || '';
        const search = params.get('search') || '';
        
        this.currentFilters = { category, search };
        
        const result = await API.getProducts(this.currentFilters);
        if (result.success) {
            this.products = result.data.products || [];
            this.filteredProducts = [...this.products];
            this.render();
        } else {
            UI.showToast('Erro ao carregar produtos', 'error');
        }
    },
    
    render() {
        const grid = document.getElementById('catalog-grid');
        const results = document.getElementById('catalog-results');
        
        if (!grid) return;
        
        const start = (this.currentPage - 1) * this.pageSize;
        const end = start + this.pageSize;
        const pageProducts = this.filteredProducts.slice(start, end);
        
        ProductsUI.renderGrid(pageProducts, 'catalog-grid');
        
        if (results) {
            results.textContent = `Mostrando ${this.filteredProducts.length} produtos`;
        }
    },
    
    setupFilters() {
        // Filtro de categoria
        const categoryLinks = document.querySelectorAll('.filter-option input[type="checkbox"]');
        categoryLinks.forEach(input => {
            input.addEventListener('change', () => {
                this.applyFilters();
            });
        });
        
        // Filtro de preço
        const priceMin = document.getElementById('price-min');
        const priceMax = document.getElementById('price-max');
        const applyPrice = document.getElementById('apply-price');
        
        if (applyPrice) {
            applyPrice.addEventListener('click', () => {
                this.applyFilters();
            });
        }
        
        // Ordenação
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                this.applySort(sortSelect.value);
            });
        }
        
        // Busca
        const searchInput = document.getElementById('search-input');
        const searchBtn = document.getElementById('search-btn');
        if (searchBtn && searchInput) {
            searchBtn.addEventListener('click', () => {
                this.currentFilters.search = searchInput.value;
                this.loadProducts();
            });
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.currentFilters.search = searchInput.value;
                    this.loadProducts();
                }
            });
        }
    },
    
    applyFilters() {
        const categories = document.querySelectorAll('.filter-option input[type="checkbox"]:checked');
        const selectedCategories = Array.from(categories).map(c => c.value);
        
        const minPrice = parseFloat(document.getElementById('price-min')?.value || 0);
        const maxPrice = parseFloat(document.getElementById('price-max')?.value || 99999);
        
        this.filteredProducts = this.products.filter(p => {
            // Filtrar por categoria
            if (selectedCategories.length > 0 && !selectedCategories.includes(p.categoria)) {
                return false;
            }
            
            // Filtrar por preço
            if (p.preco < minPrice || p.preco > maxPrice) {
                return false;
            }
            
            return true;
        });
        
        this.currentPage = 1;
        this.render();
    },
    
    applySort(sort) {
        const sorted = [...this.filteredProducts];
        
        switch (sort) {
            case 'preco-asc':
                sorted.sort((a, b) => a.preco - b.preco);
                break;
            case 'preco-desc':
                sorted.sort((a, b) => b.preco - a.preco);
                break;
            case 'nome':
                sorted.sort((a, b) => a.nome.localeCompare(b.nome));
                break;
            default:
                // destaque
                sorted.sort((a, b) => (b.destaque || 0) - (a.destaque || 0));
        }
        
        this.filteredProducts = sorted;
        this.render();
    }
};

// ─── CHECKOUT ─────────────────────────────────────────────

const Checkout = {
    init() {
        this.loadCart();
        this.setupForm();
    },
    
    async loadCart() {
        const result = await API.getCart();
        if (!result.success || !result.data.cart || result.data.cart.length === 0) {
            document.getElementById('checkout-items').innerHTML = `
                <p style="text-align:center;padding:20px;color:var(--gray);">
                    Seu carrinho está vazio. <a href="catalogo.html" style="color:var(--gold);">Voltar ao catálogo</a>
                </p>
            `;
            return;
        }
        
        const cart = result.data.cart;
        let html = '';
        let subtotal = 0;
        
        cart.forEach(item => {
            const itemTotal = item.preco * item.quantidade;
            subtotal += itemTotal;
            html += `
                <div class="summary-item">
                    <span>${item.nome} x ${item.quantidade}</span>
                    <span>R$ ${itemTotal.toFixed(2)}</span>
                </div>
            `;
        });
        
        const frete = subtotal >= 500 ? 0 : 25.90;
        const total = subtotal + frete;
        
        document.getElementById('checkout-items').innerHTML = html;
        document.getElementById('checkout-subtotal').textContent = `R$ ${subtotal.toFixed(2)}`;
        document.getElementById('checkout-frete').textContent = frete === 0 ? 'Grátis' : `R$ ${frete.toFixed(2)}`;
        document.getElementById('checkout-total').textContent = `R$ ${total.toFixed(2)}`;
        
        // Armazenar para finalizar
        this.cartData = { cart, subtotal, frete, total };
    },
    
    setupForm() {
        const form = document.getElementById('checkout-form');
        if (!form) return;
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const endereco = document.getElementById('checkout-endereco')?.value;
            const metodo = document.querySelector('.payment-method.selected')?.dataset.method || 'pix';
            
            if (!endereco) {
                UI.showToast('Preencha o endereço de entrega', 'error');
                return;
            }
            
            const result = await API.checkout(endereco, metodo);
            if (result.success) {
                UI.showToast('✅ Pedido finalizado com sucesso!', 'success');
                window.location.href = `perfil.html?tab=pedidos`;
            } else {
                UI.showToast(result.message, 'error');
            }
        });
        
        // Métodos de pagamento
        document.querySelectorAll('.payment-method').forEach(el => {
            el.addEventListener('click', () => {
                document.querySelectorAll('.payment-method').forEach(e => e.classList.remove('selected'));
                el.classList.add('selected');
            });
        });
    }
};

// ─── PERFIL ───────────────────────────────────────────────

const Profile = {
    init() {
        this.loadOrders();
        this.loadFavorites();
        this.setupTabs();
    },
    
    async loadOrders() {
        const container = document.getElementById('profile-orders');
        if (!container) return;
        
        const result = await API.getOrders();
        if (!result.success) {
            container.innerHTML = '<p style="color:var(--gray);">Erro ao carregar pedidos</p>';
            return;
        }
        
        const orders = result.data.orders || [];
        
        if (orders.length === 0) {
            container.innerHTML = '<p style="color:var(--gray);">Você ainda não tem pedidos</p>';
            return;
        }
        
        container.innerHTML = orders.map(order => `
            <div style="border:1px solid var(--gray-light);border-radius:8px;padding:16px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                    <div>
                        <strong>#${order.numero_pedido}</strong>
                        <span style="color:var(--gray);font-size:0.85rem;margin-left:12px;">
                            ${new Date(order.data_pedido).toLocaleDateString('pt-BR')}
                        </span>
                    </div>
                    <div>
                        <span class="status-badge status-${order.status}">${order.status}</span>
                        <span style="font-weight:700;margin-left:12px;">R$ ${parseFloat(order.total).toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `).join('');
    },
    
    async loadFavorites() {
        const container = document.getElementById('profile-favorites');
        if (!container) return;
        
        const result = await API.getFavorites();
        if (!result.success) {
            container.innerHTML = '<p style="color:var(--gray);">Erro ao carregar favoritos</p>';
            return;
        }
        
        const favorites = result.data.favorites || [];
        
        if (favorites.length === 0) {
            container.innerHTML = '<p style="color:var(--gray);">Você ainda não tem favoritos</p>';
            return;
        }
        
        ProductsUI.renderGrid(favorites, 'profile-favorites');
    },
    
    setupTabs() {
        const tabs = document.querySelectorAll('.profile-nav-item');
        const contents = {
            perfil: document.getElementById('profile-info'),
            pedidos: document.getElementById('profile-orders'),
            favoritos: document.getElementById('profile-favorites')
        };
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                const target = tab.dataset.tab;
                Object.keys(contents).forEach(key => {
                    if (contents[key]) {
                        contents[key].style.display = key === target ? 'block' : 'none';
                    }
                });
            });
        });
        
        // Verificar tab na URL
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab') || 'perfil';
        const targetTab = document.querySelector(`.profile-nav-item[data-tab="${tab}"]`);
        if (targetTab) {
            targetTab.click();
        }
    }
};

// ─── ADMIN ─────────────────────────────────────────────────

const Admin = {
    init() {
        this.loadStats();
        this.loadOrders();
        this.loadUsers();
        this.setupTabs();
    },
    
    async loadStats() {
        const result = await API.request('adminStats');
        if (!result.success) {
            document.getElementById('admin-stats').innerHTML = '<p>Erro ao carregar estatísticas</p>';
            return;
        }
        
        const stats = result.data.stats;
        const html = `
            <div class="stat-card">
                <div class="stat-icon gold">👤</div>
                <div class="stat-info">
                    <div class="stat-value">${stats.usuarios}</div>
                    <div class="stat-label">Usuários</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">🧴</div>
                <div class="stat-info">
                    <div class="stat-value">${stats.produtos}</div>
                    <div class="stat-label">Produtos</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">📦</div>
                <div class="stat-info">
                    <div class="stat-value">${stats.pedidos}</div>
                    <div class="stat-label">Pedidos</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">💰</div>
                <div class="stat-info">
                    <div class="stat-value">R$ ${stats.vendas.toFixed(2)}</div>
                    <div class="stat-label">Vendas</div>
                </div>
            </div>
        `;
        
        document.getElementById('admin-stats').innerHTML = html;
    },
    
    async loadOrders() {
        const container = document.getElementById('admin-orders');
        if (!container) return;
        
        const result = await API.request('getOrders');
        if (!result.success) {
            container.innerHTML = '<p>Erro ao carregar pedidos</p>';
            return;
        }
        
        const orders = result.data.orders || [];
        
        if (orders.length === 0) {
            container.innerHTML = '<p style="color:var(--gray);">Nenhum pedido encontrado</p>';
            return;
        }
        
        container.innerHTML = `
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    ${orders.map(order => `
                        <tr>
                            <td>#${order.numero_pedido}</td>
                            <td>${order.usuario_id}</td>
                            <td>R$ ${parseFloat(order.total).toFixed(2)}</td>
                            <td><span class="status-badge status-${order.status}">${order.status}</span></td>
                            <td>${new Date(order.data_pedido).toLocaleDateString('pt-BR')}</td>
                            <td>
                                <button class="action-btn action-btn-edit" onclick="Admin.changeStatus(${order.id}, 'aprovado')">Aprovar</button>
                                <button class="action-btn action-btn-edit" onclick="Admin.changeStatus(${order.id}, 'enviado')">Enviar</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    },
    
    async changeStatus(orderId, status) {
        const result = await API.request('updateOrderStatus', 'POST', { order_id: orderId, status });
        if (result.success) {
            UI.showToast('Status atualizado!', 'success');
            this.loadOrders();
        } else {
            UI.showToast(result.message, 'error');
        }
    },
    
    async loadUsers() {
        const container = document.getElementById('admin-users');
        if (!container) return;
        
        const result = await API.request('adminUsers');
        if (!result.success) {
            container.innerHTML = '<p>Erro ao carregar usuários</p>';
            return;
        }
        
        const users = result.data.users || [];
        
        if (users.length === 0) {
            container.innerHTML = '<p style="color:var(--gray);">Nenhum usuário encontrado</p>';
            return;
        }
        
        container.innerHTML = `
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Cidade</th>
                        <th>Papel</th>
                        <th>Cadastro</th>
                    </tr>
                </thead>
                <tbody>
                    ${users.map(user => `
                        <tr>
                            <td>${user.nome}</td>
                            <td>${user.email}</td>
                            <td>${user.cidade || '-'}</td>
                            <td><span class="badge ${user.papel === 'admin' ? 'badge-gold' : ''}">${user.papel}</span></td>
                            <td>${new Date(user.data_cadastro).toLocaleDateString('pt-BR')}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    },
    
    setupTabs() {
        const tabs = document.querySelectorAll('.admin-nav-item');
        const contents = {
            dashboard: document.getElementById('admin-dashboard'),
            pedidos: document.getElementById('admin-orders-container'),
            usuarios: document.getElementById('admin-users-container')
        };
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                const target = tab.dataset.tab;
                Object.keys(contents).forEach(key => {
                    if (contents[key]) {
                        contents[key].style.display = key === target ? 'block' : 'none';
                    }
                });
            });
        });
    }
};

// ─── INICIALIZAÇÃO ────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function() {
    UI.init();
    Auth.init();
    
    // Renderizar categorias (página inicial)
    const catGrid = document.getElementById('categories-grid');
    if (catGrid) {
        API.getCategories().then(result => {
            if (result.success) {
                const categories = result.data.categories || CATEGORIES;
                catGrid.innerHTML = categories.map(c => `
                    <div class="category-card fade-in" onclick="window.location.href='pages/catalogo.html?cat=${c.slug || c.id}'">
                        <div class="category-icon">${c.icone || c.icon || '🌸'}</div>
                        <div class="category-name">${c.nome || c.name}</div>
                        <div class="category-count">${c.total || c.count || 0} produtos</div>
                    </div>
                `).join('');
            } else {
                // Fallback para dados locais
                catGrid.innerHTML = CATEGORIES.map(c => `
                    <div class="category-card fade-in" onclick="window.location.href='pages/catalogo.html?cat=${c.id}'">
                        <div class="category-icon">${c.icon}</div>
                        <div class="category-name">${c.name}</div>
                        <div class="category-count">${c.count} produtos</div>
                    </div>
                `).join('');
            }
        });
    }
    
    // Renderizar produtos em destaque
    const featuredContainer = document.getElementById('featured-products');
    if (featuredContainer) {
        API.getFeatured().then(result => {
            if (result.success) {
                ProductsUI.renderGrid(result.data.products || [], 'featured-products');
            } else {
                ProductsUI.renderGrid(PRODUCTS.slice(0, 8), 'featured-products');
            }
        });
    }
    
    // Inicializar catálogo (página de catálogo)
    if (document.getElementById('catalog-grid')) {
        Catalog.init();
    }
    
    // Inicializar checkout
    if (document.getElementById('checkout-form')) {
        Checkout.init();
    }
    
    // Inicializar perfil
    if (document.getElementById('profile-orders') || document.getElementById('profile-favorites')) {
        Profile.init();
    }
    
    // Inicializar admin
    if (document.getElementById('admin-dashboard')) {
        Admin.init();
    }
});
// ============================================================
// SISTEMA DE CARRINHO DE COMPRAS
// ============================================================

// Estado do carrinho
let carrinho = [];
let carrinhoTotal = 0;

// ============================================================
// FUNÇÕES PRINCIPAIS
// ============================================================

// Adicionar item ao carrinho
function adicionarAoCarrinho(id) {
  const perfume = perfumes.find(p => p.id === id);
  if (!perfume) return;
  
  // Verificar se já existe no carrinho
  const itemExistente = carrinho.find(item => item.id === id);
  
  if (itemExistente) {
    itemExistente.quantidade += 1;
  } else {
    carrinho.push({
      id: perfume.id,
      nome: perfume.nome,
      preco: perfume.preco,
      imagem: perfume.imagem,
      emoji: perfume.emoji || '🧴',
      quantidade: 1
    });
  }
  
  // Atualizar contador do carrinho
  atualizarContador();
  
  // Mostrar toast de confirmação
  showToast(`✨ ${perfume.nome} adicionado ao carrinho!`, 'success');
  
  // Abrir carrinho automaticamente
  abrirCarrinho();
}

// Remover item do carrinho
function removerItemCarrinho(id) {
  carrinho = carrinho.filter(item => item.id !== id);
  atualizarCarrinho();
  atualizarContador();
  showToast('Item removido do carrinho', 'info');
}

// Alterar quantidade de um item
function alterarQuantidade(id, delta) {
  const item = carrinho.find(i => i.id === id);
  if (!item) return;
  
  const novaQuantidade = item.quantidade + delta;
  
  if (novaQuantidade <= 0) {
    removerItemCarrinho(id);
    return;
  }
  
  item.quantidade = novaQuantidade;
  atualizarCarrinho();
  atualizarContador();
}

// ============================================================
// FUNÇÕES DE UI
// ============================================================

// Abrir carrinho
function abrirCarrinho() {
  const drawer = document.getElementById('cart-drawer');
  const overlay = document.getElementById('cart-overlay');
  if (drawer) drawer.classList.add('open');
  if (overlay) overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
  atualizarCarrinho();
}

// Fechar carrinho
function fecharCarrinho() {
  const drawer = document.getElementById('cart-drawer');
  const overlay = document.getElementById('cart-overlay');
  if (drawer) drawer.classList.remove('open');
  if (overlay) overlay.classList.remove('open');
  document.body.style.overflow = '';
}

// Atualizar contador do carrinho (badge)
function atualizarContador() {
  const contador = document.querySelector('.cart-count');
  if (contador) {
    const total = carrinho.reduce((sum, item) => sum + item.quantidade, 0);
    contador.textContent = total;
    contador.style.display = total > 0 ? 'flex' : 'none';
  }
}

// Atualizar conteúdo do carrinho
function atualizarCarrinho() {
  const itemsContainer = document.getElementById('cart-items');
  const footer = document.getElementById('cart-footer');
  const subtotalEl = document.getElementById('cart-subtotal');
  const totalEl = document.getElementById('cart-total');
  
  if (!itemsContainer) return;
  
  // Se carrinho vazio
  if (carrinho.length === 0) {
    itemsContainer.innerHTML = `
      <div class="cart-empty">
        <div class="cart-empty-icon">🛍️</div>
        <h4>Seu carrinho está vazio</h4>
        <p style="color:var(--gray);font-size:0.9rem;">Explore nossas fragrâncias e encontre o perfume perfeito!</p>
        <button class="btn btn-gold" onclick="fecharCarrinho()" style="margin-top:8px;">
          Continuar Comprando
        </button>
      </div>
    `;
    if (footer) footer.style.display = 'none';
    return;
  }
  
  // Renderizar itens
  itemsContainer.innerHTML = carrinho.map(item => `
    <div class="cart-item" data-id="${item.id}">
      <div class="cart-item-img">
        ${item.emoji}
      </div>
      <div class="cart-item-info">
        <div class="cart-item-brand">Le Parfum</div>
        <div class="cart-item-name">${item.nome}</div>
        <div class="cart-item-price">R$ ${item.preco.toFixed(2).replace('.', ',')}</div>
        <div class="cart-item-qty">
          <button class="qty-btn" onclick="alterarQuantidade(${item.id}, -1)">−</button>
          <span class="qty-value">${item.quantidade}</span>
          <button class="qty-btn" onclick="alterarQuantidade(${item.id}, 1)">+</button>
        </div>
      </div>
      <button class="cart-item-remove" onclick="removerItemCarrinho(${item.id})" title="Remover">
        ✕
      </button>
    </div>
  `).join('');
  
  // Calcular totais
  const subtotal = carrinho.reduce((sum, item) => sum + (item.preco * item.quantidade), 0);
  const total = subtotal; // Pode adicionar frete aqui
  
  if (subtotalEl) subtotalEl.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
  if (totalEl) totalEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
  
  // Mostrar footer
  if (footer) footer.style.display = 'block';
}

// Finalizar compra
function finalizarCompra() {
  if (carrinho.length === 0) {
    showToast('Seu carrinho está vazio!', 'error');
    return;
  }
  
  const total = carrinho.reduce((sum, item) => sum + (item.preco * item.quantidade), 0);
  
  // Simular finalização
  showToast(`✅ Pedido finalizado com sucesso! Total: R$ ${total.toFixed(2).replace('.', ',')}`, 'success');
  
  // Limpar carrinho
  carrinho = [];
  atualizarCarrinho();
  atualizarContador();
  
  // Fechar carrinho após 1.5s
  setTimeout(() => {
    fecharCarrinho();
  }, 1500);
}

// ============================================================
// EVENT LISTENERS DO CARRINHO
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
  // Fechar com overlay
  const overlay = document.getElementById('cart-overlay');
  if (overlay) {
    overlay.addEventListener('click', fecharCarrinho);
  }
  
  // Fechar com botão X
  const closeBtn = document.getElementById('cart-close');
  if (closeBtn) {
    closeBtn.addEventListener('click', fecharCarrinho);
  }
  
  // Fechar com tecla ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      fecharCarrinho();
    }
  });
  
  // Atualizar contador inicial
  atualizarContador();
});

// ============================================================
// DADOS DOS PERFUMES
// ============================================================

const perfumes = [
  { id: 1, nome: "Nebula Noir", categoria: "unissex", preco: 459.90, precoAntigo: null, notas: ["Pimenta Preta", "Couro", "Fumaça"], imagem: "https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop", emoji: "🌌", destaque: true, lancamento: false },
  { id: 2, nome: "Solar Bloom", categoria: "feminino", preco: 389.90, precoAntigo: null, notas: ["Jasmim", "Néctar de Laranjeira", "Ambra"], imagem: "https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop", emoji: "☀️", destaque: true, lancamento: true },
  { id: 3, nome: "Mistwood", categoria: "masculino", preco: 499.90, precoAntigo: 549.90, notas: ["Vetiver", "Cedro", "Pinho"], imagem: "https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop", emoji: "🌲", destaque: false, lancamento: false },
  { id: 4, nome: "Velvet Aphrodite", categoria: "feminino", preco: 559.90, precoAntigo: null, notas: ["Rosa Turca", "Íris", "Baunilha"], imagem: "https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop", emoji: "🌹", destaque: true, lancamento: true },
  { id: 5, nome: "Ocean Ghost", categoria: "unissex", preco: 429.90, precoAntigo: null, notas: ["Algas Marinhas", "Sal", "Madeira"], imagem: "https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400&h=400&fit=crop", emoji: "🌊", destaque: false, lancamento: false },
  { id: 6, nome: "Royal Oud", categoria: "masculino", preco: 699.90, precoAntigo: 799.90, notas: ["Agarwood", "Açafrão", "Âmbar"], imagem: "https://images.unsplash.com/photo-1590736969956-0f0ea9f2d904?w=400&h=400&fit=crop", emoji: "👑", destaque: true, lancamento: false },
  { id: 7, nome: "Petal Eclipse", categoria: "feminino", preco: 349.90, precoAntigo: null, notas: ["Peônia", "Lychee", "Almíscar"], imagem: "https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop", emoji: "🌸", destaque: false, lancamento: true },
  { id: 8, nome: "Cyber Saffron", categoria: "unissex", preco: 579.90, precoAntigo: null, notas: ["Açafrão", "Pimenta Rosa", "Couro"], imagem: "https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop", emoji: "🤖", destaque: false, lancamento: true },
  { id: 9, nome: "Golden Tabac", categoria: "masculino", preco: 479.90, precoAntigo: 529.90, notas: ["Tabaco", "Mel", "Especiarias"], imagem: "https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop", emoji: "🔥", destaque: false, lancamento: false },
  { id: 10, nome: "Mystic Lily", categoria: "feminino", preco: 419.90, precoAntigo: null, notas: ["Lírio", "Baunilha", "Sândalo"], imagem: "https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop", emoji: "⚜️", destaque: false, lancamento: false },
  { id: 11, nome: "Crimson Wood", categoria: "unissex", preco: 389.90, precoAntigo: null, notas: ["Cedro", "Cardamomo", "Âmbar"], imagem: "https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400&h=400&fit=crop", emoji: "🪵", destaque: false, lancamento: false },
  { id: 12, nome: "Soleil Blanc", categoria: "feminino", preco: 459.90, precoAntigo: null, notas: ["Coco", "Flor de Tiaré", "Baunilha"], imagem: "https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop", emoji: "🥥", destaque: true, lancamento: false },
  { id: 13, nome: "Noir Intense", categoria: "masculino", preco: 629.90, precoAntigo: null, notas: ["Cardamomo", "Couro", "Cacau"], imagem: "https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop", emoji: "🌙", destaque: true, lancamento: true },
  { id: 14, nome: "Rose Noir", categoria: "feminino", preco: 499.90, precoAntigo: 559.90, notas: ["Rosa Negra", "Pimenta", "Patchouli"], imagem: "https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop", emoji: "🥀", destaque: false, lancamento: false },
  { id: 15, nome: "Aqua Vitae", categoria: "unissex", preco: 359.90, precoAntigo: null, notas: ["Bergamota", "Chá Verde", "Musgo"], imagem: "https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=400&h=400&fit=crop", emoji: "💧", destaque: false, lancamento: true }
];

// ============================================================
// SISTEMA DE CARRINHO
// ============================================================

let carrinho = [];

function adicionarAoCarrinho(id) {
  const perfume = perfumes.find(p => p.id === id);
  if (!perfume) return;
  
  const itemExistente = carrinho.find(item => item.id === id);
  
  if (itemExistente) {
    itemExistente.quantidade += 1;
  } else {
    carrinho.push({
      id: perfume.id,
      nome: perfume.nome,
      preco: perfume.preco,
      emoji: perfume.emoji || '🧴',
      quantidade: 1
    });
  }
  
  atualizarContador();
  showToast(`✨ ${perfume.nome} adicionado ao carrinho!`, 'success');
  abrirCarrinho();
}

function removerItemCarrinho(id) {
  carrinho = carrinho.filter(item => item.id !== id);
  atualizarCarrinho();
  atualizarContador();
  showToast('Item removido do carrinho', 'info');
}

function alterarQuantidade(id, delta) {
  const item = carrinho.find(i => i.id === id);
  if (!item) return;
  
  const novaQuantidade = item.quantidade + delta;
  
  if (novaQuantidade <= 0) {
    removerItemCarrinho(id);
    return;
  }
  
  item.quantidade = novaQuantidade;
  atualizarCarrinho();
  atualizarContador();
}

function abrirCarrinho() {
  const drawer = document.getElementById('cart-drawer');
  const overlay = document.getElementById('cart-overlay');
  if (drawer) drawer.classList.add('open');
  if (overlay) overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
  atualizarCarrinho();
}

function fecharCarrinho() {
  const drawer = document.getElementById('cart-drawer');
  const overlay = document.getElementById('cart-overlay');
  if (drawer) drawer.classList.remove('open');
  if (overlay) overlay.classList.remove('open');
  document.body.style.overflow = '';
}

function atualizarContador() {
  const contador = document.getElementById('cart-count');
  if (contador) {
    const total = carrinho.reduce((sum, item) => sum + item.quantidade, 0);
    contador.textContent = total;
    contador.style.display = total > 0 ? 'flex' : 'none';
  }
}

function atualizarCarrinho() {
  const itemsContainer = document.getElementById('cart-items');
  const footer = document.getElementById('cart-footer');
  const subtotalEl = document.getElementById('cart-subtotal');
  const totalEl = document.getElementById('cart-total');
  
  if (!itemsContainer) return;
  
  if (carrinho.length === 0) {
    itemsContainer.innerHTML = `
      <div class="cart-empty">
        <div class="cart-empty-icon">🛍️</div>
        <h4>Seu carrinho está vazio</h4>
        <p style="color:var(--gray);font-size:0.9rem;">Explore nossas fragrâncias e encontre o perfume perfeito!</p>
        <button class="btn btn-gold" onclick="fecharCarrinho()" style="margin-top:8px;">
          Continuar Comprando
        </button>
      </div>
    `;
    if (footer) footer.style.display = 'none';
    return;
  }
  
  itemsContainer.innerHTML = carrinho.map(item => `
    <div class="cart-item" data-id="${item.id}">
      <div class="cart-item-img">
        ${item.emoji}
      </div>
      <div class="cart-item-info">
        <div class="cart-item-brand">Le Parfum</div>
        <div class="cart-item-name">${item.nome}</div>
        <div class="cart-item-price">R$ ${item.preco.toFixed(2).replace('.', ',')}</div>
        <div class="cart-item-qty">
          <button class="qty-btn" onclick="alterarQuantidade(${item.id}, -1)">−</button>
          <span class="qty-value">${item.quantidade}</span>
          <button class="qty-btn" onclick="alterarQuantidade(${item.id}, 1)">+</button>
        </div>
      </div>
      <button class="cart-item-remove" onclick="removerItemCarrinho(${item.id})" title="Remover">
        ✕
      </button>
    </div>
  `).join('');
  
  const subtotal = carrinho.reduce((sum, item) => sum + (item.preco * item.quantidade), 0);
  
  if (subtotalEl) subtotalEl.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
  if (totalEl) totalEl.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
  
  if (footer) footer.style.display = 'block';
}

function finalizarCompra() {
  if (carrinho.length === 0) {
    showToast('Seu carrinho está vazio!', 'error');
    return;
  }
  
  const total = carrinho.reduce((sum, item) => sum + (item.preco * item.quantidade), 0);
  
  showToast(`✅ Pedido finalizado! Total: R$ ${total.toFixed(2).replace('.', ',')}`, 'success');
  
  carrinho = [];
  atualizarCarrinho();
  atualizarContador();
  
  setTimeout(() => fecharCarrinho(), 1500);
}

function toggleWishlist(id) {
  const perfume = perfumes.find(p => p.id === id);
  if (perfume) {
    perfume.destaque = !perfume.destaque;
    showToast(`${perfume.nome} ${perfume.destaque ? '⭐ adicionado aos' : '♡ removido dos'} favoritos!`, 'info');
  }
}

// ============================================================
// TOAST (notificações)
// ============================================================

function showToast(mensagem, tipo = 'info') {
  const container = document.getElementById('toast-container');
  if (!container) return;
  
  const toast = document.createElement('div');
  toast.className = `toast ${tipo}`;
  toast.textContent = mensagem;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

// ============================================================
// EVENT LISTENERS
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
  // Fechar carrinho
  document.getElementById('cart-overlay')?.addEventListener('click', fecharCarrinho);
  document.getElementById('cart-close')?.addEventListener('click', fecharCarrinho);
  
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') fecharCarrinho();
  });
  
  // Busca
  document.getElementById('search-btn')?.addEventListener('click', function() {
    const termo = document.getElementById('search-input').value.toLowerCase();
    const cards = document.querySelectorAll('.product-card');
    let encontrados = 0;
    
    cards.forEach(card => {
      const nome = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
      const notas = card.querySelector('.product-notes')?.textContent.toLowerCase() || '';
      const match = nome.includes(termo) || notas.includes(termo);
      card.style.display = match ? '' : 'none';
      if (match) encontrados++;
    });
    
    document.getElementById('catalog-results').textContent = `${encontrados} perfumes encontrados`;
  });
  
  // Enter na busca
  document.getElementById('search-input')?.addEventListener('keyup', function(e) {
    if (e.key === 'Enter') document.getElementById('search-btn')?.click();
  });
  
  // Ordenação
  document.getElementById('sort-select')?.addEventListener('change', function() {
    const grid = document.getElementById('catalog-grid');
    const cards = Array.from(grid.querySelectorAll('.product-card'));
    const sort = this.value;
    
    const precoMap = {
      1: 459.90, 2: 389.90, 3: 499.90, 4: 559.90, 5: 429.90,
      6: 699.90, 7: 349.90, 8: 579.90, 9: 479.90, 10: 419.90,
      11: 389.90, 12: 459.90, 13: 629.90, 14: 499.90, 15: 359.90
    };
    
    const nomeMap = {
      1: 'Nebula Noir', 2: 'Solar Bloom', 3: 'Mistwood', 4: 'Velvet Aphrodite',
      5: 'Ocean Ghost', 6: 'Royal Oud', 7: 'Petal Eclipse', 8: 'Cyber Saffron',
      9: 'Golden Tabac', 10: 'Mystic Lily', 11: 'Crimson Wood', 12: 'Soleil Blanc',
      13: 'Noir Intense', 14: 'Rose Noir', 15: 'Aqua Vitae'
    };
    
    const destaqueMap = {
      1: true, 2: true, 3: false, 4: true, 5: false, 6: true, 7: false,
      8: false, 9: false, 10: false, 11: false, 12: true, 13: true,
      14: false, 15: false
    };
    
    cards.sort((a, b) => {
      const idA = parseInt(a.dataset.id || a.querySelector('.add-to-cart-btn')?.onclick?.toString().match(/\d+/)?.[0] || 1);
      const idB = parseInt(b.dataset.id || b.querySelector('.add-to-cart-btn')?.onclick?.toString().match(/\d+/)?.[0] || 1);
      
      if (sort === 'preco-asc') return (precoMap[idA] || 0) - (precoMap[idB] || 0);
      if (sort === 'preco-desc') return (precoMap[idB] || 0) - (precoMap[idA] || 0);
      if (sort === 'nome') return (nomeMap[idA] || '').localeCompare(nomeMap[idB] || '');
      return (destaqueMap[idB] ? 1 : 0) - (destaqueMap[idA] ? 1 : 0);
    });
    
    cards.forEach(card => grid.appendChild(card));
  });
  
  // Filtro de preço
  document.getElementById('apply-price')?.addEventListener('click', function() {
    const min = parseFloat(document.getElementById('price-min').value) || 0;
    const max = parseFloat(document.getElementById('price-max').value) || Infinity;
    const cards = document.querySelectorAll('.product-card');
    let encontrados = 0;
    
    const precoMap = {
      1: 459.90, 2: 389.90, 3: 499.90, 4: 559.90, 5: 429.90,
      6: 699.90, 7: 349.90, 8: 579.90, 9: 479.90, 10: 419.90,
      11: 389.90, 12: 459.90, 13: 629.90, 14: 499.90, 15: 359.90
    };
    
    cards.forEach(card => {
      const id = parseInt(card.querySelector('.add-to-cart-btn')?.onclick?.toString().match(/\d+/)?.[0] || 1);
      const preco = precoMap[id] || 0;
      const match = preco >= min && preco <= max;
      card.style.display = match ? '' : 'none';
      if (match) encontrados++;
    });
    
    document.getElementById('catalog-results').textContent = `${encontrados} perfumes encontrados`;
  });
});