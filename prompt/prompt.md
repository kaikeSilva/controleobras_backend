# Checklist - Geração de PDF no Laravel 12 com DomPDF

## 📦 Etapa 2: Instalação do Pacote DomPDF
- [ ] Executar comando: `composer require barryvdh/laravel-dompdf`
- [ ] Verificar se o pacote foi instalado corretamente no `composer.json`
- [ ] Confirmar se não houve erros durante a instalação

## 🎮 Etapa 3: Criação do Controller
- [ ] Executar: `php artisan make:controller ReportController`
- [ ] Verificar se o arquivo foi criado em `app/Http/Controllers/ReportController.php`
- [ ] Implementar o método `generatePDF()` no controller


## 📊 Etapa 4: Preparação dos Dados de Teste
- [ ] Executar: `php artisan tinker`
- [ ] Criar dados fictícios: `User::factory()->count(10)->create()`
- [ ] Verificar se os usuários foram criados no banco de dados
- [ ] Sair do Tinker com `exit`

## 🛠️ Etapa 5: Implementação do Código do Controller
- [ ] Implementar o método `generatePDF()` com:
  - [ ] Busca dos gastos: `$gastos = Gasto::get();`
  - [ ] Criação do array de dados com título, data e gastos
  - [ ] Carregamento da view: `$pdf = PDF::loadView('myPDF', $data);`
  - [ ] Retorno do download: `return $pdf->download('itsolutionstuff.pdf');`

## 🛣️ Etapa 6: Configuração das Rotas
- [ ] Abrir arquivo `routes/web.php`
- [ ] Adicionar import: `use App\Http\Controllers\ReportController;`
- [ ] Criar rota: `Route::get('generate-pdf', [ReportController::class, 'generatePDF']);`
- [ ] Verificar se a sintaxe está correta

## 🎨 Etapa 7: Criação da View do PDF
- [ ] Criar arquivo `resources/views/myPDF.blade.php`
- [ ] Implementar estrutura HTML básica com:
  - [ ] DOCTYPE e tags HTML básicas
  - [ ] Título dinâmico usando `{{ $title }}`
  - [ ] Data dinâmica usando `{{ $date }}`
  - [ ] Texto de exemplo (Lorem ipsum)
  - [ ] Tabela com dados dos gastos usando loop `@foreach`

## 🔧 Etapa 8: Teste e Verificação
- [ ] Executar: `php artisan serve`
- [ ] Acessar: `http://localhost:8000/generate-pdf`
- [ ] Verificar se o PDF é gerado e baixado automaticamente
- [ ] Abrir o PDF e verificar se contém:
  - [ ] Título correto
  - [ ] Data atual
  - [ ] Texto Lorem ipsum
  - [ ] Tabela com dados dos usuários (ID, Nome, Email)
  - [ ] Formatação Bootstrap aplicada

## 🐛 Resolução de Problemas Comuns
- [ ] **Erro "Class PDF not found":**
  - [ ] Verificar se o pacote foi instalado corretamente
  - [ ] Limpar cache: `php artisan config:clear`
  - [ ] Verificar import no controller

- [ ] **Erro na view:**
  - [ ] Verificar se o arquivo `myPDF.blade.php` existe
  - [ ] Conferir sintaxe Blade ({{ }}, @foreach, @endforeach)
  - [ ] Verificar se as variáveis estão sendo passadas corretamente

- [ ] **Problemas com dados:**
  - [ ] Verificar se existem usuários no banco
  - [ ] Confirmar conexão com banco de dados
  - [ ] Testar query `User::get()` separadamente

- [ ] **Erro 404 na rota:**
  - [ ] Verificar se a rota foi adicionada corretamente
  - [ ] Confirmar sintaxe da rota
  - [ ] Limpar cache de rotas: `php artisan route:clear`

## ✅ Validação Final
- [ ] PDF é gerado sem erros
- [ ] Download funciona corretamente
- [ ] Conteúdo do PDF está completo e formatado
- [ ] Dados dos usuários aparecem corretamente na tabela
- [ ] Estilização Bootstrap é aplicada no PDF

## 🚀 Próximos Passos (Opcional)
- [ ] Personalizar layout do PDF
- [ ] Adicionar mais dados ao PDF
- [ ] Implementar filtros para os dados
- [ ] Adicionar opção de visualização em linha (em vez de download)
- [ ] Implementar geração de PDF para outros modelos
- [ ] Adicionar configurações avançadas do DomPDF