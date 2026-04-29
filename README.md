# Mobius API de Pedidos

API REST construida con Laravel para gestionar autenticación, catálogo de productos y flujo de pedidos con control de stock.

## Requisitos

- PHP 8.3+
- Composer
- Base de datos compatible con Laravel (MySQL, PostgreSQL o SQLite)

## Instalación rápida

1. Clonar el repositorio.
2. Instalar dependencias:

```bash
composer install
```

3. Crear archivo de entorno:

```bash
cp .env.example .env
```

4. Generar clave de aplicación:

```bash
php artisan key:generate
```

5. Configurar la conexión a base de datos en `.env`.
6. Ejecutar migraciones y seeders:

```bash
php artisan migrate --seed
```

7. Levantar servidor local:

```bash
php artisan serve
```

## Usuario de prueba

- **Email:** `admin@example.com`
- **Password:** `password`

## Documentación Swagger

1. Generar documentación OpenAPI:

```bash
php artisan l5-swagger:generate
```

2. Abrir en navegador:

- [`/api/documentation`](http://127.0.0.1:8000/api/documentation)

> Si usas otro host/puerto, reemplaza `127.0.0.1:8000` por tu URL local.

## Decisiones técnicas clave

- **Transacciones (`DB::transaction`)**: evitan inconsistencias en creación de pedidos, para no dejar pedidos parciales sin stock o sin ítems válidos.
- **Observer de `OrderItem`**: centraliza cálculo de `subtotal` y actualización de `total` del pedido para no duplicar lógica en distintos endpoints.
- **Eventos + Listeners**: `OrderCreated` + `NotifyStockUpdate` desacoplan efectos secundarios (notificaciones/logs) de la lógica principal del controlador.
- **Middleware de ownership**: `CheckOrderOwner` garantiza que cada usuario solo pueda consultar/cancelar sus propios pedidos.

## Comandos útiles

- Ejecutar tests:

```bash
php artisan test --compact
```

- Formatear código:

```bash
vendor/bin/pint --dirty --format agent
```
