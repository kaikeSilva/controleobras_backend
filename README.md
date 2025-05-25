<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## API - Filtros, Ordenação e Paginação

Todas as rotas de listagem da API (ex: `/api/clients`) seguem o mesmo padrão para filtros, ordenação e paginação.

### Filtros
Envie filtros usando o parâmetro `filter` na query string. Exemplos:

- Buscar por nome:
  ```http
  GET /api/clients?filter[name]=João
  ```
- Buscar por email exato:
  ```http
  GET /api/clients?filter[email]=joao@email.com
  ```
- Buscar por telefone contendo "99":
  ```http
  GET /api/clients?filter[phone]=99
  ```
- Buscar por data de criação:
  ```http
  GET /api/clients?filter[created_at]=2025-05-25
  ```
- Buscar por id exato:
  ```http
  GET /api/clients?filter[id]=10
  ```

### Ordenação
Envie os parâmetros `sort_by` e `direction`:

- Ordenar por nome crescente:
  ```http
  GET /api/clients?sort_by=name&direction=asc
  ```
- Ordenar por data de criação decrescente:
  ```http
  GET /api/clients?sort_by=created_at&direction=desc
  ```

Campos permitidos para ordenação: `id`, `name`, `email`, `phone`, `address`, `created_at`, `updated_at`.

### Paginação
Envie o parâmetro `per_page` para definir o número de itens por página (padrão: 15):

- 30 itens por página:
  ```http
  GET /api/clients?per_page=30
  ```

A resposta segue o padrão de paginação do Laravel, incluindo informações de links e meta dados.

#### Como acessar a próxima página

A resposta da API inclui um objeto `links` com a URL da próxima página em `links.next`.

Exemplo de resposta:
```json
{
  "data": [ ... ],
  "links": {
    "first": "http://localhost/api/clients?page=1",
    "last": "http://localhost/api/clients?page=10",
    "prev": null,
    "next": "http://localhost/api/clients?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    ...
  }
}
```

Para buscar a próxima página, basta acessar a URL indicada em `links.next` ou enviar o parâmetro `page` na query string:

```http
GET /api/clients?page=2
```

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
