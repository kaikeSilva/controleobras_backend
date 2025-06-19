<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obra;
use App\Models\Gasto;
use App\Models\EntradaRecurso;
use App\Models\CategoriaGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

class DashboardController extends Controller
{
    public function relatorio(Request $request)
    {
        // Definir período padrão se não fornecido
        $dataInicio = $request->get('data_inicio') 
            ? $request->get('data_inicio')
            : Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
            
        $dataFim = $request->get('data_fim') 
            ? $request->get('data_fim')
            : Carbon::now()->endOfMonth()->format('Y-m-d');

        $filters = $this->validateFilters($request);
        $data = [
            'resumo' => $this->getResumoFinanceiro($filters),
            'evolucao_mensal' => $this->getEvolucaoMensal($filters),
            'grafico_data' => $this->getGraficoData($filters),
        ];

        // Buscar gastos detalhados para a tabela
        $gastos = Gasto::with(['categoriaGasto', 'obra', 'fontePagadora']) // Adicionei fontePagadora
            ->when(!empty($filters['obras']), fn($q) => $q->whereIn('obra_id', $filters['obras']))
            ->when(!empty($filters['categorias_gasto']), fn($q) => $q->whereIn('categoria_gasto_id', $filters['categorias_gasto']))
            ->whereBetween('data_pagamento', [$dataInicio, $dataFim])
            ->whereNotNull('data_pagamento')
            ->orderBy('data_pagamento', 'desc')
            ->get();

        $data['gastos'] = $gastos;


        $chrome = '/usr/bin/chromium-browser';

        $pdf = Browsershot::html(
            view('reports.gastos', compact('data'))->render()
        )
        ->setChromePath('/usr/bin/chromium-browser')
        ->noSandbox()

        // ➜ flags extras
        ->setOption('args', [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',   // evita /dev/shm pequeno
            '--disable-gpu',             // headless = sem GPU
            '--no-zygote'
        ])

        // (se quiser, aumente também o timeout do CDP)
        ->setOption('protocolTimeout', 120_000)   // 120 s
        ->timeout(120)            // tempo total do processo
        ->landscape()
        ->format('a4')
        ->margins(10, 10, 10, 10)
        ->pdf();

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="relatorio_gastos_' . now()->format('Y-m-d H:i:s') . '.pdf"');
    }

    /**
     * Retorna os dados do dashboard financeiro
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $this->validateFilters($request);
        $data = [
            'resumo' => $this->getResumoFinanceiro($filters),
            'evolucao_mensal' => $this->getEvolucaoMensal($filters),
            'grafico_data' => $this->getGraficoData($filters),
            'filtros_disponiveis' => $this->getFiltrosDisponiveis()
        ];

        return response()->json($data);
    }

    /**
     * Valida os filtros recebidos
     */
    private function validateFilters(Request $request): array
    {
        return $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'obras' => 'nullable|array',
            'obras.*' => 'integer|exists:obras,id',
            'categorias_gasto' => 'nullable|array',
            'categorias_gasto.*' => 'integer|exists:categoria_gastos,id'
        ]);
    }

    /**
     * Obtém o resumo financeiro (cards do topo)
     */
    private function getResumoFinanceiro(array $filters): array
    {
        $dataInicio = $filters['data_inicio'];
        $dataFim = $filters['data_fim'];
        $obras = $filters['obras'] ?? [];
        $categorias = $filters['categorias_gasto'] ?? [];

        // Total de Gastos
        $totalGastos = Gasto::query()
            ->when(!empty($obras), fn($q) => $q->whereIn('obra_id', $obras))
            ->when(!empty($categorias), fn($q) => $q->whereIn('categoria_gasto_id', $categorias))
            ->whereBetween('data_pagamento', [$dataInicio, $dataFim])
            ->whereNotNull('data_pagamento')
            ->sum('valor');

        // Total de Entradas de Recurso
        $totalEntradas = EntradaRecurso::query()
            ->when(!empty($obras), fn($q) => $q->whereIn('obra_id', $obras))
            ->whereBetween('data_entrada', [$dataInicio, $dataFim])
            ->sum('valor');

        // Faturamento = Soma(Gasto.valor * Obra.taxa_administracao)
        $totalFaturamento = Gasto::query()
            ->join('obras', 'gastos.obra_id', '=', 'obras.id')
            ->when(!empty($obras), fn($q) => $q->whereIn('gastos.obra_id', $obras))
            ->when(!empty($categorias), fn($q) => $q->whereIn('gastos.categoria_gasto_id', $categorias))
            ->whereBetween('gastos.data_pagamento', [$dataInicio, $dataFim])
            ->whereNotNull('gastos.data_pagamento')
            ->selectRaw('SUM(gastos.valor * (obras.taxa_administracao/100)) as total_faturamento')
            ->value('total_faturamento') ?? 0;

        // Saldo Líquido = Entradas - Gastos
        $saldoLiquido = $totalEntradas - $totalGastos;

        // Formatação de valores para exibição
        $totalGastosFormatado = $this->formatarValor($totalGastos);
        $totalEntradasFormatado = $this->formatarValor($totalEntradas);
        $totalFaturamentoFormatado = $this->formatarValor($totalFaturamento);
        $saldoLiquidoFormatado = $this->formatarValor($saldoLiquido);

        return [
            // Dados brutos
            'valores_brutos' => [
                'total_gastos' => (float) $totalGastos,
                'total_entradas' => (float) $totalEntradas,
                'total_faturamento' => (float) $totalFaturamento,
                'saldo_liquido' => (float) $saldoLiquido,
            ],
            
            // Dados formatados para os cards Vue
            'cards' => [
                [
                    'title' => 'Total Gastos',
                    'value' => $totalGastosFormatado,
                    'icon' => 'IconCircleCheck',
                    // 'change' => [
                    //     'direction' => '↗',
                    //     'value' => '+0,0%',
                    //     'isPositive' => false
                    // ]
                ],
                [
                    'title' => 'Faturamento',
                    'value' => $totalFaturamentoFormatado,
                    'icon' => 'IconMoney',
                    // 'change' => [
                    //     'direction' => '↗',
                    //     'value' => '+0,0%',
                    //     'isPositive' => true
                    // ]
                ],
                [
                    'title' => 'Entradas de Recurso',
                    'value' => $totalEntradasFormatado,
                    'icon' => 'IconPlus',
                    // 'change' => [
                    //     'direction' => '↗',
                    //     'value' => '+0,0%',
                    //     'isPositive' => true
                    // ]
                ],
                [
                    'title' => 'Caixa',
                    'value' => $saldoLiquidoFormatado,
                    'icon' => 'IconCalendar',
                    // 'change' => [
                    //     'direction' => $saldoLiquido >= 0 ? '↗' : '↘',
                    //     'value' => '+0,0%',
                    //     'isPositive' => $saldoLiquido >= 0
                    // ]
                ]
            ]
        ];
    }

    /**
     * Formatar valor monetário
     */
    private function formatarValor(float $valor): string
    {
        // valor formatado deve ser mostrado no formato R$ 1.000,00
        $valor = number_format($valor, 2, ',', '.');
        return 'R$ ' . $valor;
    }

    /**
     * Obtém dados formatados especificamente para o gráfico Chart.js
     */
    private function getGraficoData(array $filters): array
    {
        $evolucaoMensal = $this->getEvolucaoMensal($filters);
        
        if (empty($evolucaoMensal)) {
            return [
                'labels' => [],
                'datasets' => [
                    'gastos' => [],
                    'faturamento' => [],
                    'entradas' => []
                ]
            ];
        }

        return [
            'labels' => array_map(fn($item) => $item['mes_nome'], $evolucaoMensal),
            'datasets' => [
                'gastos' => array_map(fn($item) => $item['gastos'], $evolucaoMensal),
                'faturamento' => array_map(fn($item) => $item['faturamento'], $evolucaoMensal),
                'entradas' => array_map(fn($item) => $item['entradas'], $evolucaoMensal)
            ]
        ];
    }

    /**
     * Obtém a evolução mensal para o gráfico
     */
    private function getEvolucaoMensal(array $filters): array
    {
        $dataInicio = $filters['data_inicio'];
        $dataFim = $filters['data_fim'];
        $obras = $filters['obras'] ?? [];
        $categorias = $filters['categorias_gasto'] ?? [];

        // Detectar se é SQLite ou MySQL/PostgreSQL
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite usa strftime()
            $yearSelect = "strftime('%Y', data_pagamento) as ano";
            $monthSelect = "strftime('%m', data_pagamento) as mes";
            $yearSelectEntrada = "strftime('%Y', data_entrada) as ano";
            $monthSelectEntrada = "strftime('%m', data_entrada) as mes";
        } else {
            // MySQL/PostgreSQL usa YEAR() e MONTH()
            $yearSelect = "YEAR(data_pagamento) as ano";
            $monthSelect = "MONTH(data_pagamento) as mes";
            $yearSelectEntrada = "YEAR(data_entrada) as ano";
            $monthSelectEntrada = "MONTH(data_entrada) as mes";
        }

        // Gastos por mês
        $gastosMensais = Gasto::query()
            ->when(!empty($obras), fn($q) => $q->whereIn('obra_id', $obras))
            ->when(!empty($categorias), fn($q) => $q->whereIn('categoria_gasto_id', $categorias))
            ->whereBetween('data_pagamento', [$dataInicio, $dataFim])
            ->whereNotNull('data_pagamento')
            ->selectRaw("
                {$yearSelect},
                {$monthSelect},
                SUM(valor) as total
            ")
            ->groupBy('ano', 'mes')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get()
            ->keyBy(fn($item) => $item->ano . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT));

        // Entradas por mês
        $entradasMensais = EntradaRecurso::query()
            ->when(!empty($obras), fn($q) => $q->whereIn('obra_id', $obras))
            ->whereBetween('data_entrada', [$dataInicio, $dataFim])
            ->selectRaw("
                {$yearSelectEntrada},
                {$monthSelectEntrada},
                SUM(valor) as total
            ")
            ->groupBy('ano', 'mes')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get()
            ->keyBy(fn($item) => $item->ano . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT));

        // Faturamento por mês
        $faturamentoMensal = Gasto::query()
            ->join('obras', 'gastos.obra_id', '=', 'obras.id')
            ->when(!empty($obras), fn($q) => $q->whereIn('gastos.obra_id', $obras))
            ->when(!empty($categorias), fn($q) => $q->whereIn('gastos.categoria_gasto_id', $categorias))
            ->whereBetween('gastos.data_pagamento', [$dataInicio, $dataFim])
            ->whereNotNull('gastos.data_pagamento')
            ->selectRaw("
                {$yearSelect},
                {$monthSelect},
                SUM(gastos.valor * (obras.taxa_administracao/100)) as total
            ")
            ->groupBy('ano', 'mes')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get()
            ->keyBy(fn($item) => $item->ano . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT));

        // Gerar array de meses no período
        $meses = [];
        $dataAtual = Carbon::parse($dataInicio)->startOfMonth();
        $dataFinal = Carbon::parse($dataFim)->endOfMonth();

        while ($dataAtual <= $dataFinal) {
            $chave = $dataAtual->format('Y-m');
            $meses[] = [
                'mes' => $chave,
                'mes_nome' => $dataAtual->locale('pt_BR')->monthName,
                'mes_ano' => $dataAtual->format('M/Y'),
                'ano' => $dataAtual->year,
                'gastos' => (float) ($gastosMensais[$chave]->total ?? 0),
                'entradas' => (float) ($entradasMensais[$chave]->total ?? 0),
                'faturamento' => (float) ($faturamentoMensal[$chave]->total ?? 0)
            ];
            $dataAtual->addMonth();
        }

        return $meses;
    }

    /**
     * Obtém os filtros disponíveis para os dropdowns
     */
    private function getFiltrosDisponiveis(): array
    {
        return [
            'obras' => Obra::select('id', 'nome', 'status')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get()
                ->map(function ($obra) {
                    return [
                        'id' => $obra->id,
                        'nome' => $obra->nome,
                        'status' => $obra->status,
                        'status_formatado' => Obra::STATUS[$obra->status] ?? $obra->status
                    ];
                }),
            
            'categorias_gasto' => CategoriaGasto::select('id', 'nome', 'cor')
                ->where('status', CategoriaGasto::STATUS_ATIVO)
                ->orderBy('nome')
                ->get()
                ->map(function ($categoria) {
                    return [
                        'id' => $categoria->id,
                        'nome' => $categoria->nome,
                        'cor' => $categoria->cor
                    ];
                })
        ];
    }

    /**
     * Endpoint para dados resumidos (apenas números dos cards)
     */
    public function getResumo(Request $request): JsonResponse
    {
        $filters = $this->validateFilters($request);
        $resumo = $this->getResumoFinanceiro($filters);

        return response()->json($resumo);
    }

    /**
     * Endpoint para dados do gráfico (evolução mensal)
     */
    public function getEvolucao(Request $request): JsonResponse
    {
        $filters = $this->validateFilters($request);
        $evolucao = $this->getEvolucaoMensal($filters);

        return response()->json($evolucao);
    }

    /**
     * Método auxiliar para debugging (remover em produção)
     */
    public function debug(Request $request): JsonResponse
    {
        $filters = $this->validateFilters($request);
        
        // Query de exemplo para ver os dados brutos
        $gastosDebug = Gasto::with(['obra', 'categoriaGasto'])
            ->when(!empty($filters['obras']), fn($q) => $q->whereIn('obra_id', $filters['obras']))
            ->when(!empty($filters['categorias_gasto']), fn($q) => $q->whereIn('categoria_gasto_id', $filters['categorias_gasto']))
            ->whereBetween('data_pagamento', [$filters['data_inicio'], $filters['data_fim']])
            ->whereNotNull('data_pagamento')
            ->limit(10)
            ->get()
            ->map(function ($gasto) {
                return [
                    'id' => $gasto->id,
                    'obra' => $gasto->obra->nome,
                    'categoria' => $gasto->categoriaGasto->nome,
                    'valor' => $gasto->valor,
                    'data_pagamento' => $gasto->data_pagamento->format('d/m/Y'),
                    'taxa_admin_obra' => $gasto->obra->taxa_administracao,
                    'faturamento_calculado' => $gasto->valor * $gasto->obra->taxa_administracao
                ];
            });

        return response()->json([
            'database_driver' => DB::connection()->getDriverName(),
            'filters_aplicados' => $filters,
            'sample_gastos' => $gastosDebug,
            'total_gastos_encontrados' => Gasto::when(!empty($filters['obras']), fn($q) => $q->whereIn('obra_id', $filters['obras']))
                ->when(!empty($filters['categorias_gasto']), fn($q) => $q->whereIn('categoria_gasto_id', $filters['categorias_gasto']))
                ->whereBetween('data_pagamento', [$filters['data_inicio'], $filters['data_fim']])
                ->whereNotNull('data_pagamento')
                ->count()
        ]);
    }
}