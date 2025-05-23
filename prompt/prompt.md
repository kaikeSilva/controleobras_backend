segue um modelo html para uma pagina home com menu e layout moderno:

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Sistema de Controle de Obras</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Menu Lateral */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            z-index: 100;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            flex-shrink: 0;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
        }

        .toggle-btn {
            background: none;
            border: none;
            font-size: 18px;
            color: #64748b;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .toggle-btn:hover {
            background: rgba(100, 116, 139, 0.1);
            color: #475569;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-section-title {
            padding: 0 20px 10px;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .nav-section-title {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin: 4px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            color: #475569;
            transform: translateX(4px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            margin-right: 14px;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-text {
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar.collapsed .nav-badge {
            opacity: 0;
            width: 0;
        }

        /* Área Principal */
        .main-content {
            flex: 1;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            margin: 20px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .main-header {
            background: rgba(255, 255, 255, 0.8);
            padding: 25px 30px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .header-title {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .header-subtitle {
            color: #64748b;
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            margin-left: auto;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
            border: 1px solid rgba(100, 116, 139, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(100, 116, 139, 0.2);
            color: #475569;
        }

        .main-body {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .content-area {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            min-height: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .welcome-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .welcome-title {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .welcome-subtitle {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 30px;
            max-width: 500px;
            line-height: 1.6;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 40px;
            width: 100%;
            max-width: 800px;
        }

        .feature-card {
            background: rgba(102, 126, 234, 0.05);
            border: 1px solid rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin: 0 auto 16px;
        }

        .feature-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .feature-description {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }
            
            .sidebar .nav-text,
            .sidebar .nav-section-title,
            .sidebar .nav-badge,
            .sidebar .logo-text {
                display: none;
            }
            
            .main-content {
                margin: 10px;
                border-radius: 15px;
            }
            
            .main-header {
                padding: 20px;
            }
            
            .header-title {
                font-size: 24px;
            }
            
            .main-body {
                padding: 20px;
            }
            
            .content-area {
                padding: 30px 20px;
            }
        }

        /* Tooltip para menu colapsado */
        .nav-item {
            position: relative;
        }

        .nav-item:hover .tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        .tooltip {
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%) translateX(-10px);
            background: #1e293b;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            margin-left: 15px;
        }

        .tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #1e293b;
        }

        .sidebar:not(.collapsed) .tooltip {
            display: none;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Menu Lateral -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-hammer"></i>
                    </div>
                    <span class="logo-text">ControleObras</span>
                </div>
                <button class="toggle-btn" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="nav-menu">
                <!-- Dashboard -->
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <div class="nav-item">
                        <a href="#dashboard" class="nav-link active" data-route="dashboard">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <span class="nav-text">Visão Geral</span>
                        </a>
                        <div class="tooltip">Visão Geral</div>
                    </div>
                    <div class="nav-item">
                        <a href="#analytics" class="nav-link" data-route="analytics">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <span class="nav-text">Relatórios</span>
                        </a>
                        <div class="tooltip">Relatórios</div>
                    </div>
                </div>

                <!-- Gestão -->
                <div class="nav-section">
                    <div class="nav-section-title">Gestão</div>
                    <div class="nav-item">
                        <a href="#clientes" class="nav-link" data-route="clientes">
                            <i class="nav-icon fas fa-users"></i>
                            <span class="nav-text">Clientes</span>
                        </a>
                        <div class="tooltip">Clientes</div>
                    </div>
                    <div class="nav-item">
                        <a href="#obras" class="nav-link" data-route="obras">
                            <i class="nav-icon fas fa-building"></i>
                            <span class="nav-text">Obras</span>
                        </a>
                        <div class="tooltip">Obras</div>
                    </div>
                    <div class="nav-item">
                        <a href="#gastos" class="nav-link" data-route="gastos">
                            <i class="nav-icon fas fa-receipt"></i>
                            <span class="nav-text">Gastos</span>
                            <span class="nav-badge">12</span>
                        </a>
                        <div class="tooltip">Gastos</div>
                    </div>
                    <div class="nav-item">
                        <a href="#financeiro" class="nav-link" data-route="financeiro">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <span class="nav-text">Financeiro</span>
                        </a>
                        <div class="tooltip">Financeiro</div>
                    </div>
                </div>

                <!-- Configurações -->
                <div class="nav-section">
                    <div class="nav-section-title">Configurações</div>
                    <div class="nav-item">
                        <a href="#categorias" class="nav-link" data-route="categorias">
                            <i class="nav-icon fas fa-tags"></i>
                            <span class="nav-text">Categorias</span>
                        </a>
                        <div class="tooltip">Categorias de Gasto</div>
                    </div>
                    <div class="nav-item">
                        <a href="#fontes" class="nav-link" data-route="fontes">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <span class="nav-text">Fontes Pagadoras</span>
                        </a>
                        <div class="tooltip">Fontes Pagadoras</div>
                    </div>
                    <div class="nav-item">
                        <a href="#usuarios" class="nav-link" data-route="usuarios">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <span class="nav-text">Usuários</span>
                        </a>
                        <div class="tooltip">Gestão de Usuários</div>
                    </div>
                    <div class="nav-item">
                        <a href="#configuracoes" class="nav-link" data-route="configuracoes">
                            <i class="nav-icon fas fa-cog"></i>
                            <span class="nav-text">Configurações</span>
                        </a>
                        <div class="tooltip">Configurações do Sistema</div>
                    </div>
                </div>

                <!-- Sistema -->
                <div class="nav-section">
                    <div class="nav-section-title">Sistema</div>
                    <div class="nav-item">
                        <a href="#backup" class="nav-link" data-route="backup">
                            <i class="nav-icon fas fa-download"></i>
                            <span class="nav-text">Backup</span>
                        </a>
                        <div class="tooltip">Backup & Restauração</div>
                    </div>
                    <div class="nav-item">
                        <a href="#logs" class="nav-link" data-route="logs">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <span class="nav-text">Logs do Sistema</span>
                        </a>
                        <div class="tooltip">Logs do Sistema</div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Área Principal -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-info">
                    <h1 class="header-title" id="pageTitle">Dashboard</h1>
                    <p class="header-subtitle" id="pageSubtitle">Bem-vindo ao sistema de controle de obras</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary">
                        <i class="fas fa-bell"></i>
                        Notificações
                    </button>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nova Obra
                    </button>
                </div>
            </header>
            
            <div class="main-body">
                <div class="content-area" id="contentArea">
                    <!-- Tela de Boas-vindas -->
                    <div class="welcome-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h2 class="welcome-title">Bem-vindo ao ControleObras</h2>
                    <p class="welcome-subtitle">
                        Sistema completo para gestão financeira de obras por administração. 
                        Controle seus projetos, gastos e relatórios de forma simples e eficiente.
                    </p>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h3 class="feature-title">Relatórios Avançados</h3>
                            <p class="feature-description">Visualize o desempenho das suas obras com gráficos e relatórios detalhados</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h3 class="feature-title">Interface Responsiva</h3>
                            <p class="feature-description">Acesse o sistema de qualquer dispositivo com design moderno e intuitivo</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3 class="feature-title">Segurança Total</h3>
                            <p class="feature-description">Seus dados protegidos com as melhores práticas de segurança</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <h3 class="feature-title">Sincronização</h3>
                            <p class="feature-description">Dados sempre atualizados em tempo real para toda sua equipe</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Controle do Menu Lateral
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const navLinks = document.querySelectorAll('.nav-link');
        const pageTitle = document.getElementById('pageTitle');
        const pageSubtitle = document.getElementById('pageSubtitle');
        const contentArea = document.getElementById('contentArea');

        // Toggle do menu lateral
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });

        // Dados das páginas
        const pageData = {
            dashboard: {
                title: 'Dashboard',
                subtitle: 'Visão geral do sistema e métricas principais',
                content: `
                    <div class="welcome-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h2 class="welcome-title">Dashboard Principal</h2>
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

        // Navegação entre páginas
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remove active de todos os links
                navLinks.forEach(l => l.classList.remove('active'));
                
                // Adiciona active no link clicado
                link.classList.add('active');
                
                // Pega a rota do data-route
                const route = link.getAttribute('data-route');
                
                // Atualiza o conteúdo da página
                if (pageData[route]) {
                    pageTitle.textContent = pageData[route].title;
                    pageSubtitle.textContent = pageData[route].subtitle;
                    contentArea.innerHTML = pageData[route].content;
                }
                
                // Atualiza a URL (simulando roteamento)
                window.history.pushState({route: route}, '', `#${route}`);
            });
        });

        // Detecta mudanças na URL (botão voltar do navegador)
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.route) {
                const route = e.state.route;
                const link = document.querySelector(`[data-route="${route}"]`);
                if (link) {
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    
                    if (pageData[route]) {
                        pageTitle.textContent = pageData[route].title;
                        pageSubtitle.textContent = pageData[route].subtitle;
                        contentArea.innerHTML = pageData[route].content;
                    }
                }
            }
        });

        // Carrega a página inicial baseada na URL
        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.substring(1);
            if (hash && pageData[hash]) {
                const link = document.querySelector(`[data-route="${hash}"]`);
                if (link) {
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    pageTitle.textContent = pageData[hash].title;
                    pageSubtitle.textContent = pageData[hash].subtitle;
                    contentArea.innerHTML = pageData[hash].content;
                }
            }
        });

        // Efeitos de hover e animações
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Auto-collapse sidebar em telas pequenas
        function checkScreenSize() {
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
            }
        }

        window.addEventListener('resize', checkScreenSize);
        checkScreenSize();

        // Notificação de boas-vindas
        setTimeout(() => {
            if (document.querySelector('.nav-link.active').getAttribute('data-route') === 'dashboard') {
                showNotification('Bem-vindo ao ControleObras! 🚀', 'success');
            }
        }, 1000);

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
            
            // Auto remove após 5 segundos
            setTimeout(() => {
                notification.remove();
            }, 5000);
            
            // Remove ao clicar no X
            notification.querySelector('.notification-close').addEventListener('click', () => {
                notification.remove();
            });
        }
    </script>

    <style>
        /* Estilos para notificações */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            z-index: 1000;
            transform: translateX(400px);
            animation: slideIn 0.5s ease forwards;
        }

        .notification-success {
            border-left: 4px solid #10b981;
        }

        .notification-error {
            border-left: 4px solid #ef4444;
        }

        .notification-info {
            border-left: 4px solid #3b82f6;
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notification-content i {
            font-size: 18px;
        }

        .notification-success .notification-content i {
            color: #10b981;
        }

        .notification-error .notification-content i {
            color: #ef4444;
        }

        .notification-info .notification-content i {
            color: #3b82f6;
        }

        .notification-content span {
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
        }

        .notification-close {
            background: none;
            border: none;
            font-size: 14px;
            color: #64748b;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .notification-close:hover {
            background: rgba(100, 116, 139, 0.1);
            color: #475569;
        }

        @keyframes slideIn {
            to {
                transform: translateX(0);
            }
        }

        /* Melhorias adicionais */
        .nav-link {
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .main-content {
            position: relative;
            overflow: hidden;
        }

        .main-content::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.05) 50%, transparent 70%);
            animation: shimmer 3s infinite;
            pointer-events: none;
        }

        @keyframes shimmer {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Scrollbar personalizada */
        .main-body::-webkit-scrollbar {
            width: 8px;
        }

        .main-body::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        .main-body::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 4px;
        }

        .main-body::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
        }
    </style>
</body>
</html>