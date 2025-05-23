// Controle do Menu Lateral
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleSidebar');
const navLinks = document.querySelectorAll('.nav-link');
const pageTitle = document.getElementById('pageTitle');
const pageSubtitle = document.getElementById('pageSubtitle');
const contentArea = document.getElementById('contentArea');

// Toggle do menu lateral
if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
    });
}

// Dados das páginas (Exemplo, idealmente viria do backend ou de um arquivo de configuração)
const pageData = {
    dashboard: {
        title: 'Dashboard',
        subtitle: 'Visão geral do sistema e métricas principais',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h2 class="welcome-title">Dashboard Principal dadsa</h2>
            <p class="welcome-subtitle">
                Aqui você encontrará um resumo completo de todas as suas obras, 
                gastos recentes e métricas importantes para sua administração.
            </p>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-building"></i></div>
                    <h3 class="feature-title">15 Obras Ativas</h3>
                    <p class="feature-description">Projetos em andamento</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-dollar-sign"></i></div>
                    <h3 class="feature-title">R$ 2.847.230</h3>
                    <p class="feature-description">Total investido este mês</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3 class="feature-title">8 Clientes</h3>
                    <p class="feature-description">Clientes ativos</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-chart-pie"></i></div>
                    <h3 class="feature-title">12% Taxa Média</h3>
                    <p class="feature-description">Taxa de administração</p>
                </div>
            </div>
        `
    },
    analytics: {
        title: 'Relatórios e Análises',
        subtitle: 'Relatórios detalhados e análises financeiras',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h2 class="welcome-title">Central de Relatórios</h2>
            <p class="welcome-subtitle">
                Gere relatórios personalizados e analise o desempenho 
                financeiro das suas obras com gráficos interativos.
            </p>
        `
    },
    clientes: {
        title: 'Gestão de Clientes',
        subtitle: 'Cadastro e gerenciamento de clientes',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-users"></i>
            </div>
            <h2 class="welcome-title">Gestão de Clientes</h2>
            <p class="welcome-subtitle">
                Cadastre e gerencie todos os seus clientes, 
                mantendo informações atualizadas e organizadas.
            </p>
        `
    },
    obras: {
        title: 'Gestão de Obras',
        subtitle: 'Cadastro e acompanhamento de obras',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-building"></i>
            </div>
            <h2 class="welcome-title">Gestão de Obras</h2>
            <p class="welcome-subtitle">
                Controle completo das suas obras: prazos, orçamentos, 
                progresso e todas as informações importantes.
            </p>
        `
    },
    gastos: {
        title: 'Controle de Gastos',
        subtitle: 'Registro e controle de todas as despesas',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <h2 class="welcome-title">Controle de Gastos</h2>
            <p class="welcome-subtitle">
                Registre todos os gastos das obras com comprovantes, 
                categorização e controle detalhado de despesas.
            </p>
        `
    },
    financeiro: {
        title: 'Gestão Financeira',
        subtitle: 'Fluxo de caixa e controle financeiro',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <h2 class="welcome-title">Gestão Financeira</h2>
            <p class="welcome-subtitle">
                Controle completo do fluxo de caixa, entradas, saídas 
                e cálculo automático das taxas de administração.
            </p>
        `
    },
    categorias: {
        title: 'Categorias de Gasto',
        subtitle: 'Configuração das categorias de classificação',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-tags"></i>
            </div>
            <h2 class="welcome-title">Categorias de Gasto</h2>
            <p class="welcome-subtitle">
                Configure e organize as categorias para classificar 
                os gastos das suas obras de forma padronizada.
            </p>
        `
    },
    fontes: {
        title: 'Fontes Pagadoras',
        subtitle: 'Configuração das fontes de pagamento',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <h2 class="welcome-title">Fontes Pagadoras</h2>
            <p class="welcome-subtitle">
                Gerencie todas as fontes de pagamento: contas bancárias, 
                cartões e outras formas de financiamento das obras.
            </p>
        `
    },
    usuarios: {
        title: 'Gestão de Usuários',
        subtitle: 'Controle de acesso e permissões',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-user-cog"></i>
            </div>
            <h2 class="welcome-title">Gestão de Usuários</h2>
            <p class="welcome-subtitle">
                Controle o acesso ao sistema, defina permissões 
                e gerencie a equipe que utiliza a plataforma.
            </p>
        `
    },
    configuracoes: {
        title: 'Configurações do Sistema',
        subtitle: 'Configurações gerais e personalização',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-cog"></i>
            </div>
            <h2 class="welcome-title">Configurações</h2>
            <p class="welcome-subtitle">
                Personalize o sistema de acordo com suas necessidades 
                e configure parâmetros gerais de funcionamento.
            </p>
        `
    },
    backup: {
        title: 'Backup e Restauração',
        subtitle: 'Segurança e backup dos dados',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-download"></i>
            </div>
            <h2 class="welcome-title">Backup e Restauração</h2>
            <p class="welcome-subtitle">
                Mantenha seus dados seguros com backups automáticos 
                e opções de restauração quando necessário.
            </p>
        `
    },
    logs: {
        title: 'Logs do Sistema',
        subtitle: 'Auditoria e logs de atividades',
        content: `
            <div class="welcome-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h2 class="welcome-title">Logs do Sistema</h2>
            <p class="welcome-subtitle">
                Monitore todas as atividades do sistema, acompanhe 
                alterações e mantenha um histórico completo de auditoria.
            </p>
        `
    }
};

// Navegação entre páginas (Client-side SPA-like behavior)
if (navLinks.length > 0) {
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            
            const route = link.getAttribute('data-route');
            
            if (pageData[route] && pageTitle && pageSubtitle && contentArea) {
                pageTitle.textContent = pageData[route].title;
                pageSubtitle.textContent = pageData[route].subtitle;
                contentArea.innerHTML = pageData[route].content;
            }
            
            window.history.pushState({route: route}, '', `#${route}`);
        });
    });
}

// Detecta mudanças na URL (botão voltar do navegador)
window.addEventListener('popstate', (e) => {
    if (e.state && e.state.route) {
        const route = e.state.route;
        const link = document.querySelector(`.nav-link[data-route="${route}"]`);
        if (link) {
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            
            if (pageData[route] && pageTitle && pageSubtitle && contentArea) {
                pageTitle.textContent = pageData[route].title;
                pageSubtitle.textContent = pageData[route].subtitle;
                contentArea.innerHTML = pageData[route].content;
            }
        }
    }
});

// Carrega a página inicial baseada na URL ou default para dashboard
window.addEventListener('DOMContentLoaded', () => {
    let initialRoute = window.location.hash.substring(1);
    if (!initialRoute || !pageData[initialRoute]) {
        initialRoute = 'dashboard'; // Default to dashboard
        window.history.replaceState({route: initialRoute}, '', `#${initialRoute}`);
    }

    const link = document.querySelector(`.nav-link[data-route="${initialRoute}"]`);
    if (link) {
        navLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        if (pageData[initialRoute] && pageTitle && pageSubtitle && contentArea) {
            pageTitle.textContent = pageData[initialRoute].title;
            pageSubtitle.textContent = pageData[initialRoute].subtitle;
            contentArea.innerHTML = pageData[initialRoute].content;
        }
    }

    // Auto-collapse sidebar em telas pequenas
    checkScreenSize();

    // Notificação de boas-vindas (apenas se na dashboard)
    if (initialRoute === 'dashboard') {
        setTimeout(() => {
            showNotification('Bem-vindo ao ControleObras! 🚀', 'success');
        }, 1000);
    }
});

// Efeitos de hover e animações para feature cards (se existirem)
document.addEventListener('DOMContentLoaded', () => {
    // Re-apply hover effects if content changes dynamically
    const observer = new MutationObserver(() => {
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });
    });

    if(contentArea){
        observer.observe(contentArea, { childList: true, subtree: true });
    }
});


// Auto-collapse sidebar em telas pequenas
function checkScreenSize() {
    if (sidebar) {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
        } else {
            sidebar.classList.remove('collapsed');
        }
    }
}

window.addEventListener('resize', checkScreenSize);

// Sistema de notificações
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)'; // Animate in
    }, 10);

    const closeButton = notification.querySelector('.notification-close');
    
    const removeNotification = () => {
        notification.style.transform = 'translateX(400px)'; // Animate out
        setTimeout(() => {
            notification.remove();
        }, 500);
    };

    closeButton.addEventListener('click', removeNotification);
    
    setTimeout(removeNotification, 5000);
}
