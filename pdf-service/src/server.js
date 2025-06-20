import express from 'express';
import puppeteer from 'puppeteer-core';

const app = express();
app.use(express.json({ limit: '20mb' }));

/** ------------------------------------------------------------------
 * Browser compartilhado
 * ------------------------------------------------------------------*/
const launch = puppeteer.launch({
    headless: 'new',
    executablePath: process.env.PUPPETEER_EXECUTABLE_PATH,
    args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-gpu'
    ],
});

let browserPromise = launch;

/** Health-check */
app.get('/health', (_, res) => res.json({ status: 'ok' }));

/** Render PDF - Versão Compatível */
app.post('/render/pdf', async (req, res) => {
    const { html, url, options = {} } = req.body;
    
    if (!html && !url) {
        return res.status(400).json({ 
            error: 'bad_request', 
            message: 'html OR url is required' 
        });
    }

    let page;
    try {
        const browser = await browserPromise;
        page = await browser.newPage();
        
        // Configurações básicas
        await page.setViewport({ 
            width: 1200, 
            height: 800
        });
        
        // Emula mídia print
        await page.emulateMediaType('print');

        // Navegação
        if (html) {
            await page.goto(`data:text/html,${encodeURIComponent(html)}`, {
                waitUntil: 'networkidle0',
                timeout: 30000,
            });
        } else {
            await page.goto(url, { 
                waitUntil: 'networkidle0', 
                timeout: 30000 
            });
        }

        // Aguarda fontes carregarem
        try {
            await page.evaluateHandle('document.fonts.ready');
        } catch (e) {
            console.log('document.fonts.ready not available, skipping...');
        }
        
        // Aguarda estabilização usando setTimeout (compatível com versões antigas)
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Configurações do PDF
        const defaultOptions = {
            format: 'A4',
            printBackground: true,
            preferCSSPageSize: true,
            displayHeaderFooter: false,
            margin: {
                top: '15mm',
                right: '10mm',
                bottom: '15mm',
                left: '10mm'
            },
            scale: 1,
            landscape: false
        };

        const pdfOptions = { ...defaultOptions, ...options };
        
        const pdfBuffer = await page.pdf(pdfOptions);

        res.type('application/pdf').send(pdfBuffer);
        
    } catch (err) {
        console.error('render_fail:', err.message);
        res.status(500).json({ 
            error: 'render_fail', 
            message: err.message 
        });
    } finally {
        if (page) {
            try {
                await page.close();
            } catch (closeError) {
                console.error('Error closing page:', closeError);
            }
        }
    }
});

/** ------------------------------------------------------------------
 * Fechamento limpo
 * ------------------------------------------------------------------*/
const cleanup = async () => {
    try {
        const browser = await browserPromise;
        await browser.close();
        console.log('Browser closed cleanly');
    } catch (error) {
        console.error('Error during cleanup:', error);
    } finally {
        process.exit(0);
    }
};

process.on('SIGTERM', cleanup);
process.on('SIGINT', cleanup);

const PORT = process.env.PORT || 3000;
app.listen(PORT, () =>
    console.log(`PDF-service up on :${PORT} (POST /render/pdf, GET /health)`),
);