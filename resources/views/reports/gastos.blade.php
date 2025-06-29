<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro - {{ now()->format('d/m/Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -webkit-system-font, 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #2d3748;
            background: #ffffff;
        }

        .container {
            padding: 20px;
            max-width: 100%;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2b6cb0;
            padding-bottom: 20px;
            page-break-inside: avoid;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #718096;
            margin-bottom: 10px;
        }

        .header .date {
            font-size: 12px;
            color: #a0aec0;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .card {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .card h3 {
            font-size: 12px;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .card .value {
            font-size: 20px;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 5px;
        }

        .card.positive .value {
            color: #38a169;
        }

        .card.negative .value {
            color: #e53e3e;
        }

        .card .change {
            font-size: 10px;
            color: #718096;
        }

        /* Chart Section */
        .chart-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            page-break-inside: avoid;
            min-height: 350px;
        }

        .chart-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .chart-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .chart-header p {
            font-size: 12px;
            color: #718096;
        }

        .chart-container {
            width: 100%;
            height: 300px;
            margin: 0 auto;
        }

        /* Tables - PRINCIPAIS CORREÇÕES AQUI */
        .table-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 25px;
            overflow: visible;
            /* CRÍTICO: Remove quebra de página dentro da seção da tabela */
            page-break-inside: avoid;
        }

        .table-header {
            background: #edf2f7;
            padding: 15px 20px;
            border-bottom: 1px solid #e2e8f0;
            /* Evita quebra no cabeçalho da seção */
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .table-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            display: inline-block;
        }

        .table-header .count {
            float: right;
            background: #bee3f8;
            color: #2c5282;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .table-wrapper {
            overflow: visible;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* CRÍTICO: Configurações de quebra de página para tabela */
            page-break-inside: auto;
        }

        /* CRÍTICO: Configurações específicas para thead */
        thead {
            display: table-header-group;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        thead tr {
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        /* CRÍTICO: Configurações para tbody */
        tbody {
            display: table-row-group;
        }

        /* CRÍTICO: Evita quebra de linha individual no meio */
        tr {
            page-break-inside: avoid;
            page-break-before: auto;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f7fafc;
        }

        th {
            background: #f7fafc;
            font-weight: 600;
            color: #4a5568;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            /* CRÍTICO: Força o cabeçalho a não quebrar */
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        td {
            font-size: 11px;
            color: #2d3748;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-green {
            color: #38a169;
            font-weight: 600;
        }

        .text-red {
            color: #e53e3e;
            font-weight: 600;
        }

        .badge {
            background: #edf2f7;
            color: #4a5568;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
        }

        /* CONFIGURAÇÕES ESPECÍFICAS PARA PDF */
        @page { 
            size: A4;
            margin: 15mm 10mm 15mm 10mm;
        }

        /* Classes auxiliares para controle de quebra */
        .no-break {
            page-break-inside: avoid !important;
        }

        .break-before {
            page-break-before: always;
        }

        .break-after {
            page-break-after: always;
        }

        /* Configurações para impressão/PDF */
        @media print {
            .container {
                padding: 10px;
            }
            
            .cards-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
                page-break-inside: avoid;
            }
            
            .card {
                padding: 15px;
            }
            
            .chart-container {
                height: 250px;
            }

            /* CRÍTICO: Configurações específicas de impressão para tabelas */
            .table-section {
                page-break-inside: avoid;
                margin-bottom: 20px;
            }

            table {
                page-break-inside: auto;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            page-break-inside: avoid;
        }

        /* Estilos para quando a tabela for muito grande */
        .large-table .table-section {
            page-break-inside: auto;
        }

        .large-table table {
            page-break-inside: auto;
        }

        .large-table tbody tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header no-break">
            <h1>Relatório Financeiro</h1>
            <p>Análise detalhada do desempenho financeiro</p>
            <div class="date">Gerado em {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>

        <!-- Cards com Resumo Financeiro -->
        @if(isset($data['resumo']['valores_brutos']))
        <div class="cards-grid no-break">
            <div class="card negative">
                <h3>Total de Gastos</h3>
                <div class="value">R$ {{ number_format($data['resumo']['valores_brutos']['total_gastos'], 2, ',', '.') }}</div>
                <div class="change">Saídas do período</div>
            </div>

            <div class="card positive">
                <h3>Faturamento</h3>
                <div class="value">R$ {{ number_format($data['resumo']['valores_brutos']['total_faturamento'], 2, ',', '.') }}</div>
                <div class="change">Taxa de administração</div>
            </div>

            <div class="card positive">
                <h3>Entradas de Recurso</h3>
                <div class="value">R$ {{ number_format($data['resumo']['valores_brutos']['total_entradas'], 2, ',', '.') }}</div>
                <div class="change">Recursos recebidos</div>
            </div>

            <div class="card {{ $data['resumo']['valores_brutos']['saldo_liquido'] >= 0 ? 'positive' : 'negative' }}">
                <h3>Saldo Líquido</h3>
                <div class="value">R$ {{ number_format($data['resumo']['valores_brutos']['saldo_liquido'], 2, ',', '.') }}</div>
                <div class="change">{{ $data['resumo']['valores_brutos']['saldo_liquido'] >= 0 ? 'Resultado positivo' : 'Resultado negativo' }}</div>
            </div>
        </div>
        @endif

        <!-- Gráfico de Evolução Mensal -->
        @if(isset($data['grafico_data']) && !empty($data['grafico_data']['labels']))
        <div class="chart-section no-break">
            <div class="chart-header">
                <h2>Evolução Mensal</h2>
                <p>Comparativo de gastos, faturamento e entradas ao longo do período</p>
            </div>
            
            <div class="chart-container">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
        @endif

        <!-- Tabela de Evolução Mensal -->
        @if(isset($data['evolucao_mensal']) && count($data['evolucao_mensal']) > 0)
        <div class="table-section no-break">
            <div class="table-header">
                <h3>Detalhamento Mensal</h3>
                <span class="count">{{ count($data['evolucao_mensal']) }} meses</span>
                <div style="clear: both;"></div>
            </div>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Período</th>
                            <th class="text-right">Gastos</th>
                            <th class="text-right">Faturamento</th>
                            <th class="text-right">Entradas</th>
                            <th class="text-right">Saldo Mensal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['evolucao_mensal'] as $mes)
                        @php
                            $saldo = ($mes['entradas'] + $mes['faturamento']) - $mes['gastos'];
                        @endphp
                        <tr>
                            <td><strong>{{ $mes['mes_nome'] ?? 'N/A' }}</strong></td>
                            <td class="text-right text-red">R$ {{ number_format($mes['gastos'], 2, ',', '.') }}</td>
                            <td class="text-right text-green">R$ {{ number_format($mes['faturamento'], 2, ',', '.') }}</td>
                            <td class="text-right text-green">R$ {{ number_format($mes['entradas'], 2, ',', '.') }}</td>
                            <td class="text-right {{ $saldo >= 0 ? 'text-green' : 'text-red' }}">
                                R$ {{ number_format($saldo, 2, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Tabela de Gastos Detalhados -->
        @if(isset($data['gastos']) && $data['gastos']->count() > 0)
        <!-- Para tabelas muito grandes, usamos uma abordagem diferente -->
        <div class="table-section {{ $data['gastos']->count() > 50 ? 'large-table' : 'no-break' }}">
            <div class="table-header">
                <h3>Gastos Detalhados</h3>
                <span class="count">{{ $data['gastos']->count() }} registros</span>
                <div style="clear: both;"></div>
            </div>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descrição</th>
                            <th>Obra</th>
                            <th>Categoria</th>
                            <th class="text-right">Valor</th>
                            <th class="text-center">Data Pagamento</th>
                            <th>Fonte Pagadora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['gastos'] as $index => $gasto)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ Str::limit($gasto->descricao ?? 'N/A', 40) }}</td>
                            <td>{{ Str::limit($gasto->obra->nome ?? 'N/A', 25) }}</td>
                            <td>
                                <span class="badge">{{ $gasto->categoriaGasto->nome ?? 'N/A' }}</span>
                            </td>
                            <td class="text-right">
                                <strong>R$ {{ number_format($gasto->valor, 2, ',', '.') }}</strong>
                            </td>
                            <td class="text-center">
                                {{ $gasto->data_pagamento ? \Carbon\Carbon::parse($gasto->data_pagamento)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>{{ Str::limit($gasto->fontePagadora->nome ?? 'N/A', 20) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Resumo Final -->
        @if(isset($data['resumo']['valores_brutos']))
        <div class="table-section no-break" style="margin-top: 30px;">
            <div class="table-header">
                <h3>Resumo Executivo</h3>
            </div>
            <div style="padding: 20px;">
                <p style="margin-bottom: 10px; font-size: 12px; line-height: 1.6;">
                    <strong>Análise do Período:</strong> 
                    @if($data['resumo']['valores_brutos']['saldo_liquido'] >= 0)
                        O período apresentou resultado positivo de <strong>R$ {{ number_format($data['resumo']['valores_brutos']['saldo_liquido'], 2, ',', '.') }}</strong>, 
                        indicando que as entradas de recursos (R$ {{ number_format($data['resumo']['valores_brutos']['total_entradas'], 2, ',', '.') }}) 
                        somadas ao faturamento (R$ {{ number_format($data['resumo']['valores_brutos']['total_faturamento'], 2, ',', '.') }}) 
                        superaram os gastos totais (R$ {{ number_format($data['resumo']['valores_brutos']['total_gastos'], 2, ',', '.') }}).
                    @else
                        O período apresentou resultado negativo de <strong>R$ {{ number_format(abs($data['resumo']['valores_brutos']['saldo_liquido']), 2, ',', '.') }}</strong>, 
                        indicando que os gastos totais (R$ {{ number_format($data['resumo']['valores_brutos']['total_gastos'], 2, ',', '.') }}) 
                        superaram as entradas de recursos (R$ {{ number_format($data['resumo']['valores_brutos']['total_entradas'], 2, ',', '.') }}) 
                        somadas ao faturamento (R$ {{ number_format($data['resumo']['valores_brutos']['total_faturamento'], 2, ',', '.') }}).
                    @endif
                </p>
                
                @if(isset($data['evolucao_mensal']) && count($data['evolucao_mensal']) > 0)
                <p style="font-size: 12px; line-height: 1.6;">
                    <strong>Período Analisado:</strong> {{ count($data['evolucao_mensal']) }} meses, 
                    de {{ $data['evolucao_mensal'][0]['mes_nome'] ?? 'N/A' }} 
                    até {{ end($data['evolucao_mensal'])['mes_nome'] ?? 'N/A' }}.
                </p>
                @endif
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            Sistema de Controle de Obras • Relatório gerado automaticamente
        </div>
    </div>

    <!-- Chart.js Script -->
    @if(isset($data['grafico_data']) && !empty($data['grafico_data']['labels']))
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('evolutionChart');
        if (!ctx) return;

        const chartData = @json($data['grafico_data']);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels || [],
                datasets: [
                    {
                        label: 'Gastos',
                        data: chartData.datasets.gastos || [],
                        backgroundColor: 'rgba(229, 62, 62, 0.7)',
                        borderColor: 'rgba(229, 62, 62, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Faturamento',
                        data: chartData.datasets.faturamento || [],
                        backgroundColor: 'rgba(56, 161, 105, 0.7)',
                        borderColor: 'rgba(56, 161, 105, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Entradas',
                        data: chartData.datasets.entradas || [],
                        backgroundColor: 'rgba(66, 153, 225, 0.7)',
                        borderColor: 'rgba(66, 153, 225, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rect',
                            padding: 15,
                            color: '#2d3748',
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#2d3748',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 6,
                        padding: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 11 },
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                return context.dataset.label + ': R$ ' + value.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e2e8f0',
                            borderColor: '#cbd5e0'
                        },
                        ticks: {
                            color: '#718096',
                            font: { size: 10 },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'R$ ' + (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return 'R$ ' + (value / 1000).toFixed(1) + 'K';
                                }
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#718096',
                            font: { size: 10 }
                        }
                    }
                },
                animation: {
                    duration: 500
                }
            }
        });
    });
    </script>
    @endif
</body>
</html>