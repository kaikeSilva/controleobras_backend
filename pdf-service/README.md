# PDF Service

Microserviço para geração de PDFs usando Puppeteer e Express.js. Este serviço recebe HTML ou URLs e retorna documentos PDF gerados pelo Chromium.

## Funcionalidades

- Renderização de HTML para PDF
- Renderização de URL para PDF
- Configuração de opções de PDF (formato, orientação, margens, etc.)
- Endpoint de health check para monitoramento

## Requisitos

- Node.js 20+
- Docker (para execução em contêiner)

## Instalação

### Desenvolvimento local

```bash
cd pdf-service
npm install
node src/server.js
```

### Docker

O serviço já está configurado no `docker-compose.prod.yaml` e será iniciado junto com os outros serviços:

```bash
docker-compose -f docker-compose.prod.yaml up -d
```

## Endpoints

### Verificação de saúde

```
GET /health
```

Resposta:
```json
{
  "status": "ok"
}
```

### Renderização de PDF

```
POST /render/pdf
```

Corpo da requisição:
```json
{
  "html": "<html>...</html>",
  // OU
  "url": "https://example.com",
  "options": {
    "format": "A4",
    "landscape": true,
    "margin": {
      "top": "10mm",
      "right": "10mm",
      "bottom": "10mm",
      "left": "10mm"
    }
  }
}
```

Resposta: Arquivo PDF binário com Content-Type: application/pdf

## Rotas da API Laravel

O serviço de PDF é acessado através das seguintes rotas da API Laravel:

```
GET  /api/relatorios/gastos              # Solicita geração de um relatório
GET  /api/relatorios/status/{filename}   # Verifica o status de geração
GET  /api/relatorios/download/{filename} # Faz download do PDF gerado
```

## Integração com Laravel

O serviço está integrado ao Laravel através de um job assíncrono:

```php
use App\Jobs\GeneratePdfJob;

// Dispatch do job para a fila
dispatch(new GeneratePdfJob(
    view:     'reports.gastos',
    data:     ['data' => $data],
    filename: 'relatorio_'.now()->format('Ymd_His').'.pdf',
    options:  [
        'landscape' => true, 
        'margin' => [
            'top' => '10mm',
            'right' => '10mm',
            'bottom' => '10mm',
            'left' => '10mm'
        ]
    ],
))->onQueue('pdf');
```

Os PDFs gerados são armazenados no disco `pdfs` configurado em `config/filesystems.php`.

## Integração com Frontend

Para integrar com um frontend (Vue.js, React, etc.), siga este fluxo:

```javascript
// 1. Solicitar a geração do PDF
async function solicitarRelatorio(filtros) {
  const response = await fetch('/api/relatorios/gastos?data_inicio=2025-01-01&data_fim=2025-06-01', {
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json'
    }
  });
  
  const data = await response.json();
  return data.filename; // Nome do arquivo para consultar status
}

// 2. Verificar o status periodicamente
async function verificarStatus(filename) {
  const response = await fetch(`/api/relatorios/status/${filename}`, {
    headers: {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json'
    }
  });
  
  return await response.json();
}

// 3. Implementar polling para verificar status
async function aguardarPDF(filename) {
  return new Promise((resolve) => {
    const interval = setInterval(async () => {
      const status = await verificarStatus(filename);
      
      if (status.status === 'completed') {
        clearInterval(interval);
        resolve(status.download_url);
      }
    }, 2000); // Verificar a cada 2 segundos
  });
}

// 4. Uso completo
async function gerarEBaixarPDF() {
  try {
    // Exibir indicador de carregamento
    showLoading('Solicitando relatório...');
    
    // Solicitar geração
    const filename = await solicitarRelatorio(filtros);
    
    // Atualizar mensagem de carregamento
    updateLoading('Gerando PDF, aguarde...');
    
    // Aguardar conclusão
    const downloadUrl = await aguardarPDF(filename);
    
    // Baixar o arquivo
    hideLoading();
    window.location.href = downloadUrl;
  } catch (error) {
    hideLoading();
    showError('Erro ao gerar relatório');
    console.error(error);
  }
}
```

Este código demonstra como implementar o fluxo completo de solicitação, verificação de status e download do PDF gerado.

## Opções de PDF

O serviço suporta todas as opções do método `page.pdf()` do Puppeteer:

- `format`: Formato do papel ('A4', 'Letter', etc.)
- `width`, `height`: Dimensões personalizadas (em pixels)
- `landscape`: Orientação paisagem (boolean)
- `margin`: Margens (objeto com top, right, bottom, left)
- `printBackground`: Imprimir fundo (boolean)
- `scale`: Escala do documento (número)

## Configuração

O serviço usa as seguintes variáveis de ambiente:

- `PUPPETEER_EXECUTABLE_PATH`: Caminho para o executável do Chromium (padrão: /usr/bin/chromium-browser)
- `NODE_ENV`: Ambiente de execução (padrão: production)

## Segurança

O serviço é executado com um usuário não-root (pptr) e com as flags de segurança do Chromium:
- `--no-sandbox`
- `--disable-setuid-sandbox`


## COmandos
npm install --package-lock-only 