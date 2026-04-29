# Mobius Order Management API

API REST robusta para gestión de pedidos y stock, construida con foco en integridad transaccional, seguridad de acceso y observabilidad operativa.

## Stack técnico

- PHP 8.3
- Laravel 13.0
- Laravel Sanctum para autenticación por tokens
- MySQL / SQLite
- L5-Swagger para documentación OpenAPI
- Pest PHP (v4.6+) para testing

## Instalación

1. Instalar dependencias:

```bash
composer install
```

2. Crear entorno y generar clave:

```bash
cp .env.example .env
php artisan key:generate
```

3. Levantar base con datos de prueba:

```bash
php artisan migrate:fresh --seed
```

Este seed crea una base evaluable desde el primer momento con:

- 1 usuario administrador demo
- 10 productos de ejemplo

4. Levantar servidor:

```bash
php artisan serve
```

## Credenciales de prueba

- Usuario: `admin@example.com`
- Password: `password`

## Documentación y pruebas de API

- Swagger UI: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
- Colección Postman incluida en raíz: `Mobius.postman_collection.json`

Para regenerar docs:

```bash
php artisan l5-swagger:generate
```

o con Composer:

```bash
composer swagger
```

Si no aparece un endpoint nuevo en Swagger UI, regenera y recarga el navegador (Ctrl+F5).

## Testing

El proyecto utiliza Pest PHP (v4.6+) como framework de pruebas.

Comandos:

```bash
php artisan test
```

o

```bash
composer test
```

Cobertura funcional clave:

- Validación de stock
- Middleware de seguridad `CheckOrderOwner`
- Integridad de transacciones en creación de pedidos
- Flujo de autenticación de API
- Respuesta corta de errores en `api/*` (sin `trace`, `file`, `exception`)
- Listado público de productos (`GET /api/products`)

## Observabilidad

El sistema registra eventos relevantes en:

```bash
storage/logs/laravel.log
```

Se registran, entre otros:

- Intentos de acceso no autorizado (middleware)
- Flujo de creación transaccional de pedidos (servicio)
- Actualizaciones de stock posteriores a la creación (listeners)

Monitoreo en tiempo real:

```bash
tail -f storage/logs/laravel.log
```

## Scripts de Composer

Scripts útiles:

- `composer setup`: automatiza instalación inicial (dependencias, entorno y pasos base).
- `composer test`: ejecuta pruebas.
- `composer swagger`: regenera documentación OpenAPI.

## Decisiones de arquitectura

- **Capa de servicio**: `OrderService` desacopla lógica de negocio del controlador.
- **Integridad de datos**: `DB::transaction` + `lockForUpdate` para escenarios concurrentes.
- **Eventos desacoplados**: `OrderCreated` y `NotifyStockUpdate` para efectos secundarios.
- **Observers de dominio**: `OrderItemObserver` centraliza cálculo de subtotal y recálculo de total.
- **Recursos API**: respuestas consistentes mediante `JsonResource`.

## Consideraciones de seguridad

La API está blindada para responder en JSON bajo rutas `api/*`, incluyendo errores de autenticación (`401`), validación (`422`) y no encontrado (`404`). Para errores inesperados se devuelve un `500` con mensaje corto, sin exponer traza, archivo ni detalles internos del framework.

Este proyecto fue desarrollado siguiendo principios SOLID y Clean Code, priorizando la integridad de los datos y la claridad del contrato API.
