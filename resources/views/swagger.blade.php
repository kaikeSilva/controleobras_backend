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
<div style="padding: 16px 0; text-align: center;">
    <button id="btn-auth" type="button" style="margin: 0 8px; padding: 8px 20px; border-radius: 6px; border: 1px solid #3B82F6; background: #3B82F6; color: #fff; font-weight: 600; cursor: pointer;">Autenticação</button>
    <button id="btn-client" type="button" style="margin: 0 8px; padding: 8px 20px; border-radius: 6px; border: 1px solid #6B7280; background: #6B7280; color: #fff; font-weight: 600; cursor: pointer;">Clientes</button>
    <button id="btn-fonte" type="button" style="margin: 0 8px; padding: 8px 20px; border-radius: 6px; border: 1px solid #6B7280; background: #6B7280; color: #fff; font-weight: 600; cursor: pointer;">Fontes Pagadoras</button>
    <button id="btn-autocomplete" type="button" style="margin: 0 8px; padding: 8px 20px; border-radius: 6px; border: 1px solid #6B7280; background: #6B7280; color: #fff; font-weight: 600; cursor: pointer;">Autocomplete</button>
    <button id="btn-user" type="button" style="margin: 0 8px; padding: 8px 20px; border-radius: 6px; border: 1px solid #6B7280; background: #6B7280; color: #fff; font-weight: 600; cursor: pointer;">Usuários</button>
</div>
<div id="swagger-ui"></div>
<script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
    const files = {
        auth: '{{ url('swagger/authentication.yaml') }}',
        client: '{{ url('swagger/client.yaml') }}',
        fonte: '{{ url('swagger/fonte-pagadora.yaml') }}',
        autocomplete: '{{ url('swagger/autocomplete.yaml') }}',
        user: '{{ url('swagger/user.yaml') }}'
    };
    let current = 'auth';
    function renderSwagger(fileKey) {
        window.ui = SwaggerUIBundle({
            url: files[fileKey],
            dom_id: '#swagger-ui',
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.SwaggerUIStandalonePreset
            ],
            layout: "BaseLayout",
            deepLinking: true,
            validatorUrl: null
        });
        current = fileKey;
        // Botão ativo
        document.getElementById('btn-auth').style.background = (fileKey === 'auth') ? '#3B82F6' : '#6B7280';
        document.getElementById('btn-client').style.background = (fileKey === 'client') ? '#3B82F6' : '#6B7280';
        document.getElementById('btn-fonte').style.background = (fileKey === 'fonte') ? '#3B82F6' : '#6B7280';
        document.getElementById('btn-autocomplete').style.background = (fileKey === 'autocomplete') ? '#3B82F6' : '#6B7280';
        document.getElementById('btn-user').style.background = (fileKey === 'user') ? '#3B82F6' : '#6B7280';
    }
    document.getElementById('btn-auth').onclick = () => renderSwagger('auth');
    document.getElementById('btn-client').onclick = () => renderSwagger('client');
    document.getElementById('btn-fonte').onclick = () => renderSwagger('fonte');
    document.getElementById('btn-autocomplete').onclick = () => renderSwagger('autocomplete');
    document.getElementById('btn-user').onclick = () => renderSwagger('user');
    window.onload = () => renderSwagger('auth');
</script>
</body>
</html>
