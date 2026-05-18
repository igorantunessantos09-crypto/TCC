// js/carrinho.js
document.addEventListener('DOMContentLoaded', function() {
    // Atualizar quantidade no carrinho
    const qtyInputs = document.querySelectorAll('.cart-qty');
    qtyInputs.forEach(input => {
        input.addEventListener('change', async function() {
            const itemId = this.dataset.itemId;
            const quantidade = parseInt(this.value);
            
            if (quantidade < 1) {
                this.value = 1;
                return;
            }
            
            if (quantidade > 10) {
                this.value = 10;
                return;
            }
            
            try {
                const response = await fetch('php/atualizar_carrinho.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `item_id=${itemId}&quantidade=${quantidade}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Atualizar total sem recarregar
                    atualizarTotais(data);
                    mostrarNotificacao('Quantidade atualizada!', 'success');
                } else {
                    mostrarNotificacao('Erro ao atualizar', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                mostrarNotificacao('Erro de conexão', 'error');
            }
        });
    });
    
    // Remover item do carrinho
    const removeButtons = document.querySelectorAll('.btn-remover-item');
    removeButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const itemId = this.dataset.itemId;
            
            if (confirm('Remover este item do carrinho?')) {
                try {
                    const response = await fetch('php/remover_carrinho.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `item_id=${itemId}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Remover item da tela ou recarregar
                        const cartItem = this.closest('.cart-item');
                        if (cartItem) {
                            cartItem.style.transition = 'all 0.3s';
                            cartItem.style.opacity = '0';
                            cartItem.style.transform = 'translateX(100px)';
                            setTimeout(() => {
                                cartItem.remove();
                                atualizarTotais(data);
                                
                                // Se carrinho vazio, mostrar mensagem
                                const cartItems = document.querySelectorAll('.cart-item');
                                if (cartItems.length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                        
                        mostrarNotificacao('Item removido!', 'success');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    mostrarNotificacao('Erro ao remover item', 'error');
                }
            }
        });
    });
    
    // Selecionar método de pagamento no checkout
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            paymentMethods.forEach(m => m.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });
    
    // Máscara de CEP
    const cepInput = document.querySelector('input[name="cep"]');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value;
            
            // Buscar endereço automaticamente quando tiver 8 dígitos
            if (value.replace(/\D/g, '').length === 8) {
                buscarEndereco(value);
            }
        });
    }
    
    // Máscara de telefone
    const telefoneInput = document.querySelector('input[name="telefone"]');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 7) + 
                        (value.length > 7 ? '-' + value.substring(7, 11) : '');
            }
            e.target.value = value;
        });
    }
    
    // Validação do formulário de checkout
    const checkoutForm = document.querySelector('.checkout-container form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const metodoSelecionado = document.querySelector('input[name="metodo_pagamento"]:checked');
            
            if (!metodoSelecionado) {
                e.preventDefault();
                mostrarNotificacao('Selecione um método de pagamento!', 'error');
                return;
            }
            
            // Desabilitar botão para evitar duplo envio
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processando...';
                submitBtn.style.opacity = '0.7';
            }
        });
    }
    
    // Função para buscar endereço por CEP
    async function buscarEndereco(cep) {
        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep.replace(/\D/g, '')}/json/`);
            const data = await response.json();
            
            if (!data.erro) {
                document.querySelector('input[name="rua"]').value = data.logradouro || '';
                document.querySelector('input[name="bairro"]').value = data.bairro || '';
                document.querySelector('input[name="cidade"]').value = data.localidade || '';
                document.querySelector('select[name="estado"]').value = data.uf || '';
                document.querySelector('input[name="numero"]').focus();
                
                mostrarNotificacao('Endereço encontrado!', 'success');
            }
        } catch (error) {
            console.error('Erro ao buscar CEP:', error);
        }
    }
    
    // Função para atualizar totais
    function atualizarTotais(data) {
        const totalElement = document.querySelector('.total-price');
        const cartCountElement = document.querySelector('.cart-count');
        
        if (totalElement && data.total) {
            totalElement.textContent = `R$ ${parseFloat(data.total).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;
        }
        
        if (cartCountElement && data.count !== undefined) {
            cartCountElement.textContent = data.count;
            cartCountElement.style.display = data.count > 0 ? 'flex' : 'none';
        }
        
        // Atualizar subtotal de cada item
        if (data.itens) {
            data.itens.forEach(item => {
                const itemElement = document.querySelector(`[data-item-id="${item.id}"]`);
                if (itemElement) {
                    const priceElement = itemElement.closest('.cart-item').querySelector('.cart-item-price');
                    if (priceElement) {
                        priceElement.textContent = `R$ ${parseFloat(item.subtotal).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })}`;
                    }
                }
            });
        }
    }
    
    // Função para mostrar notificações
    function mostrarNotificacao(mensagem, tipo) {
        // Remover notificações existentes
        const existingNotif = document.querySelector('.notification');
        if (existingNotif) {
            existingNotif.remove();
        }
        
        const notif = document.createElement('div');
        notif.className = `notification ${tipo}`;
        notif.textContent = mensagem;
        notif.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem 2rem;
            background: ${tipo === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 10000;
            animation: slideInRight 0.3s ease;
            font-weight: 500;
            max-width: 400px;
        `;
        
        document.body.appendChild(notif);
        
        setTimeout(() => {
            notif.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notif.remove(), 300);
        }, 3000);
    }
    
    // Animações CSS para notificações
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Inicializar primeiro método de pagamento como selecionado
    const firstPaymentMethod = document.querySelector('.payment-method');
    if (firstPaymentMethod && !document.querySelector('.payment-method.selected')) {
        firstPaymentMethod.classList.add('selected');
        const radio = firstPaymentMethod.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }
});