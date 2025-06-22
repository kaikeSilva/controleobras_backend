# PDF WebSocket Notifications

## Escopo

Esta funcionalidade implementa um sistema de notificações em tempo real via WebSockets para o processo de geração de relatórios PDF. O sistema permite que os usuários recebam atualizações instantâneas sobre o progresso da geração de seus relatórios, desde o início do processo até a conclusão ou falha.

## Descrição da Feature

A implementação utiliza Laravel Reverb como servidor WebSocket e Redis como backend de broadcasting para enviar notificações em tempo real aos usuários durante o processo de geração de PDFs. O sistema inclui:

1. **Eventos de Notificação**: Quatro eventos distintos para diferentes estágios do processo (iniciado, progresso, concluído, falha)
2. **Canais Privados**: Canais de broadcasting privados para garantir que apenas o usuário correto receba suas notificações
3. **Serviço de Notificação**: Um serviço centralizado para gerenciar o envio de notificações
4. **Job Modificado**: O job de geração de PDF foi modificado para enviar atualizações de progresso
5. **Endpoints API**: Novos endpoints para gerenciar relatórios (geração, cancelamento, status, download)

## Fluxo de Funcionamento

1. O usuário solicita a geração de um relatório PDF através do endpoint API
2. O backend gera um ID único para o job e inicia o processo de geração de forma assíncrona
3. Durante o processamento, o job envia notificações WebSocket para o usuário sobre o progresso
4. O frontend recebe as notificações e atualiza a interface do usuário em tempo real
5. Quando o PDF é concluído, uma notificação final é enviada com o link para download

## Tecnologias Utilizadas

- Laravel 12
- Laravel Reverb (servidor WebSocket)
- Redis (backend de broadcasting)
- Laravel Broadcasting
- Laravel Queues

## Arquivos Envolvidos

### Eventos
- `app/Events/PdfGenerationStarted.php`
- `app/Events/PdfGenerationProgress.php`
- `app/Events/PdfGenerationCompleted.php`
- `app/Events/PdfGenerationFailed.php`

### Serviços
- `app/Services/PdfNotificationService.php`

### Jobs
- `app/Jobs/GeneratePdfJob.php`

### Controllers
- `app/Http/Controllers/Api/DashboardController.php`

### Rotas
- `routes/api.php`
- `routes/channels.php`

## Como Testar

1. Inicie o servidor Reverb:
   ```bash
   docker compose exec app php artisan reverb:start
   ```

2. Faça uma requisição para gerar um relatório:
   ```
   GET /api/relatorios/gastos
   ```

3. Conecte-se ao canal WebSocket privado retornado na resposta:
   ```
   private-pdf.{userId}
   ```

4. Observe as notificações sendo enviadas em tempo real durante o processo de geração

5. Após a conclusão, use o endpoint de download para obter o arquivo PDF:
   ```
   GET /api/relatorios/download/{filename}
   ```

## Considerações de Segurança

- As notificações são enviadas através de canais privados que requerem autenticação
- Apenas o usuário que solicitou o relatório pode receber as notificações
- Os arquivos PDF são armazenados em um disco configurado especificamente para esse fim
