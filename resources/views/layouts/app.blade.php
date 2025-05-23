<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Admin Panel')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Vite Assets -->
    @vite(['resources/sass/admin_panel.scss', 'resources/js/admin_panel.js'])

</head>
<body class="font-sans antialiased">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="#" class="logo">
                    <span class="logo-icon"><i class="fas fa-cogs"></i></span>
                    <span class="logo-text">ControleObras</span>
                </a>
                <button class="toggle-btn" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="nav-menu">
                <div class="nav-section">
                    <span class="nav-section-title">Principal</span>
                    <div class="nav-item">
                        <a href="#dashboard" class="nav-link active" data-route="dashboard">
                            <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                        <span class="tooltip">Dashboard</span>
                    </div>
                    <div class="nav-item">
                        <a href="#analytics" class="nav-link" data-route="analytics">
                            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                            <span class="nav-text">Relatórios</span>
                        </a>
                        <span class="tooltip">Relatórios</span>
                    </div>
                </div>

                <div class="nav-section">
                    <span class="nav-section-title">Gerenciamento</span>
                    <div class="nav-item">
                        <a href="#clientes" class="nav-link" data-route="clientes">
                            <span class="nav-icon"><i class="fas fa-users"></i></span>
                            <span class="nav-text">Clientes</span>
                        </a>
                        <span class="tooltip">Clientes</span>
                    </div>
                    <div class="nav-item">
                        <a href="#obras" class="nav-link" data-route="obras">
                            <span class="nav-icon"><i class="fas fa-building"></i></span>
                            <span class="nav-text">Obras</span>
                        </a>
                        <span class="tooltip">Obras</span>
                    </div>
                    <div class="nav-item">
                        <a href="#gastos" class="nav-link" data-route="gastos">
                            <span class="nav-icon"><i class="fas fa-receipt"></i></span>
                            <span class="nav-text">Controle de Gastos</span>
                        </a>
                        <span class="tooltip">Controle de Gastos</span>
                    </div>
                    <div class="nav-item">
                        <a href="#financeiro" class="nav-link" data-route="financeiro">
                            <span class="nav-icon"><i class="fas fa-dollar-sign"></i></span>
                            <span class="nav-text">Financeiro</span>
                        </a>
                        <span class="tooltip">Financeiro</span>
                    </div>
                </div>

                <div class="nav-section">
                    <span class="nav-section-title">Configurações</span>
                    <div class="nav-item">
                        <a href="#categorias" class="nav-link" data-route="categorias">
                            <span class="nav-icon"><i class="fas fa-tags"></i></span>
                            <span class="nav-text">Categorias de Gasto</span>
                        </a>
                        <span class="tooltip">Categorias de Gasto</span>
                    </div>
                    <div class="nav-item">
                        <a href="#fontes" class="nav-link" data-route="fontes">
                            <span class="nav-icon"><i class="fas fa-credit-card"></i></span>
                            <span class="nav-text">Fontes Pagadoras</span>
                        </a>
                        <span class="tooltip">Fontes Pagadoras</span>
                    </div>
                    <div class="nav-item">
                        <a href="#usuarios" class="nav-link" data-route="usuarios">
                            <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
                            <span class="nav-text">Usuários</span>
                        </a>
                        <span class="tooltip">Usuários</span>
                    </div>
                    <div class="nav-item">
                        <a href="#configuracoes" class="nav-link" data-route="configuracoes">
                            <span class="nav-icon"><i class="fas fa-cog"></i></span>
                            <span class="nav-text">Configurações</span>
                        </a>
                        <span class="tooltip">Configurações</span>
                    </div>
                </div>

                <div class="nav-section">
                    <span class="nav-section-title">Sistema</span>
                    <div class="nav-item">
                        <a href="#backup" class="nav-link" data-route="backup">
                            <span class="nav-icon"><i class="fas fa-download"></i></span>
                            <span class="nav-text">Backup</span>
                        </a>
                        <span class="tooltip">Backup</span>
                    </div>
                    <div class="nav-item">
                        <a href="#logs" class="nav-link" data-route="logs">
                            <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                            <span class="nav-text">Logs</span>
                        </a>
                        <span class="tooltip">Logs</span>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-info">
                    <h1 class="header-title" id="pageTitle">Dashboard</h1>
                    <p class="header-subtitle" id="pageSubtitle">Visão geral do sistema e métricas principais</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary">
                        <i class="fas fa-bell"></i>
                        <span>Notificações</span>
                    </button>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Nova Obra</span>
                    </button>
                </div>
            </header>

            <div class="main-body">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
