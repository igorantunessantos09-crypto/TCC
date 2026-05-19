// js/script.js
document.addEventListener('DOMContentLoaded', function() {
    // Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    }
    
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            menuToggle.classList.remove('active');
        });
    }
    
    // Seleção de Planos
    const planoCards = document.querySelectorAll('.plano-card');
    planoCards.forEach(card => {
        card.addEventListener('click', function() {
            planoCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            
            // Atualizar botão de compra
            const planoTipo = this.dataset.plano;
            const btnComprar = document.querySelector('.btn-comprar-plano');
            if (btnComprar) {
                btnComprar.dataset.plano = planoTipo;
            }
        });
    });
    
    // Carrossel de Notícias
    const carrossel = document.querySelector('.carrossel-slides');
    if (carrossel) {
        const slides = carrossel.querySelectorAll('.carrossel-slide');
        const dots = document.querySelectorAll('.carrossel-dot');
        const prevBtn = document.querySelector('.carrossel-btn.prev');
        const nextBtn = document.querySelector('.carrossel-btn.next');
        let currentSlide = 0;
        
        function updateCarrossel() {
            carrossel.style.transform = `translateX(-${currentSlide * 100}%)`;
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentSlide = (currentSlide + 1) % slides.length;
                updateCarrossel();
            });
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                updateCarrossel();
            });
        }
        
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                updateCarrossel();
            });
        });
        
        // Auto-play
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            updateCarrossel();
        }, 5000);
    }
    
    // Tema Escuro/Claro
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        // Carregar tema salvo
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        themeToggle.checked = savedTheme === 'dark';
        
        themeToggle.addEventListener('change', function() {
            const theme = this.checked ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            // Salvar no servidor se logado
            if (typeof usuarioId !== 'undefined') {
                fetch('php/salvar_tema.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `tema=${theme}`
                });
            }
        });
    }

    // Verificar se está logado antes de adicionar ao carrinho
    const botoesComprar = document.querySelectorAll('.btn-adicionar-carrinho, .btn-comprar-agora');
    botoesComprar.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Verificar se o usuário está logado
            if (typeof usuarioLogado === 'undefined' || !usuarioLogado) {
                e.preventDefault();
                window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.href);
                return;
            }
        });
    });
    
    // Adicionar ao carrinho
    const btnAdicionarCarrinho = document.querySelectorAll('.btn-adicionar-carrinho');
    btnAdicionarCarrinho.forEach(btn => {
        btn.addEventListener('click', async function() {
            const produtoId = this.dataset.produto;
            try {
                const response = await fetch('php/adicionar_carrinho.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `produto_id=${produtoId}`
                });
                const data = await response.json();
                
                if (data.success) {
                    atualizarContadorCarrinho(data.count);
                    mostrarNotificacao('Produto adicionado ao carrinho!', 'success');
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        });
    });
    
    function atualizarContadorCarrinho(count) {
        const countElement = document.querySelector('.cart-count');
        if (countElement) {
            countElement.textContent = count;
            countElement.style.display = count > 0 ? 'flex' : 'none';
        }
    }
    
    function mostrarNotificacao(mensagem, tipo) {
        const notif = document.createElement('div');
        notif.className = `notification ${tipo}`;
        notif.textContent = mensagem;
        notif.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem 2rem;
            background: ${tipo === 'success' ? 'var(--success)' : 'var(--danger)'};
            color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(notif);
        
        setTimeout(() => {
            notif.remove();
        }, 3000);
    }
    
    // Deleção de conta
    const btnExcluirConta = document.querySelector('.btn-excluir-conta');
    if (btnExcluirConta) {
        btnExcluirConta.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Tem certeza que deseja excluir sua conta? Esta ação é irreversível!')) {
                window.location.href = 'php/excluir_conta.php';
            }
        });
    }
});