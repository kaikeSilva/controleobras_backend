<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Swagger UI - API de Clientes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui.css" />
    <style>
        html, body { height: 100%; margin: 0; padding: 0; }
        #swagger-ui { min-height: 100vh; }
    </style>
</head>
<body>
<div id="swagger-ui"></div>
<script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
    window.onload = () => {
        window.ui = SwaggerUIBundle({
            url: '{{ url('openapi.yaml') }}',
            dom_id: '#swagger-ui',
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.SwaggerUIStandalonePreset
            ],
            layout: "BaseLayout",
            deepLinking: true
        });
    };
</script>
</body>
</html>
