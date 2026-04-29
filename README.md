# Mobius Order Management API

API REST robusta construida con Laravel 13, enfocada en integridad de datos, seguridad y observabilidad para la gestión de pedidos, stock y autenticación de clientes.

## Stack técnico

- Laravel 13
- Laravel Sanctum (autenticación por token)
- MySQL / SQLite
- L5-Swagger (documentación OpenAPI)

## Instalación paso a paso

1) Instalar dependencias:

```bash
composer install
```

2) Crear y configurar entorno:

```bash
cp .env.example .env
php artisan key:generate
```

3) Ejecutar migraciones y seeders:

```bash
php artisan migrate:fresh --seed
```

> Este comando crea datos de prueba realistas, incluyendo 5 clientes y 30 productos.

4) Levantar el servidor:

```bash
php artisan serve
```

## Credenciales de prueba

- Usuario: `cliente@example.com`
- Password: `password`

## Documentación y pruebas

- Swagger UI: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
- Colección Postman incluida en la raíz: `Mobius.postman_collection.json`

Si necesitas regenerar la documentación OpenAPI:

```bash
php artisan l5-swagger:generate
```

## Decisiones de arquitectura

- **Capa de servicio (`OrderService`)**: desacopla la lógica de negocio del controlador para mantener endpoints limpios y escalables.
- **Integridad de datos**: uso de `DB::transaction` y `lockForUpdate` para prevenir inconsistencias de stock en escenarios concurrentes.
- **Observabilidad**: sistema de logs profesional en servicios, middleware y listeners para trazabilidad operativa y alertas de seguridad.
- **Rendimiento**: caché de 5 minutos en el catálogo (`GET /api/products`) para reducir carga y mejorar tiempos de respuesta.
- **Eventos desacoplados**: `OrderCreated` + `NotifyStockUpdate` para separar efectos secundarios de la lógica principal del pedido.

## Monitoreo de logs en tiempo real

Para seguir el flujo operativo en tiempo real:

```bash
tail -f storage/logs/laravel.log
```

## Consideraciones finales

Se optó por un enfoque defensivo en la validación de stock, verificando tanto en el FormRequest como dentro de la transacción del servicio para garantizar que nunca existan pedidos con stock negativo, cumpliendo estrictamente los requerimientos no funcionales.
