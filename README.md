# 📦 Mobius Order Management API

<p align="center">
  <img src="https://laravel.com/img/logomark.min.svg" width="100" alt="Laravel Logo">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-Framework-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/Testing-Pest-00B1E1?style=for-the-badge" alt="Pest Testing">
  <img src="https://img.shields.io/badge/Code%20Style-Laravel%20Pint-ff2d20?style=for-the-badge" alt="Laravel Pint">
</p>

---

## 🧾 Descripción

API REST robusta para la gestión de pedidos y stock, diseñada con un enfoque en:

* 🔒 **Seguridad de acceso**
* 🔁 **Integridad transaccional**
* 📊 **Observabilidad operativa**

---

## 🚀 Stack Técnico

* ⚙️ **Runtime:** PHP 8.3 + Laravel 13
* 🔐 **Autenticación:** Laravel Sanctum (tokens)
* 🗄️ **Base de datos:** MySQL / SQLite
* 📄 **Documentación:** L5-Swagger (OpenAPI 3)
* 🧪 **Testing:** Pest PHP (v4.6+)
* 🧹 **Code Quality:** Laravel Pint

---

## 🛠️ Instalación y Setup

### ⚡ Instalación automática (recomendada)

composer run setup

### 🔧 Instalación manual

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve

---

## ⚠️ Nota Importante

El **Seeder Maestro** genera:

* 👤 5 clientes de prueba
* 📦 30 productos con distintos estados:

  * Agotados
  * Stock limitado
  * Stock normal

Ideal para probar validaciones reales y edge cases.

---

## 🔐 Credenciales de Prueba

Usuario: [cliente@example.com](mailto:cliente@example.com)
Password: password

---

## 📖 Documentación de API

* 🌐 Swagger UI:
  http://localhost:8000/api/documentation

* 📬 Postman:
  Archivo incluido → `Mobius.postman_collection.json`

### 🔄 Regenerar documentación

php artisan l5-swagger:generate o composer swagger

💡 **Tip:**
Si no ves cambios en Swagger → Ctrl + F5 para limpiar caché.

---

## 🧪 Testing (Pest PHP)

Ejecutar tests:

composer test o php artisan test

### ✔️ Cobertura incluida

* ✅ Validación de stock en tiempo real
* ✅ Seguridad de acceso (CheckOrderOwner middleware)
* ✅ Integridad transaccional (rollback automático)
* ✅ Flujo completo de autenticación
* ✅ Manejo de errores limpio (sin fugas internas)
* ✅ Cache y listado público de catálogo

---

## 👁️ Observabilidad (Logs)

Ubicación:

storage/logs/laravel.log

### 🔍 Eventos registrados

* 🛡️ Intentos de acceso no autorizado
* 📦 Creación de pedidos paso a paso
* 📉 Actualización de stock

### 📡 Monitoreo en tiempo real

tail -f storage/logs/laravel.log

---

## 🏗️ Arquitectura

### 🧩 Decisiones clave

* **Service Layer (OrderService)**
  → Lógica desacoplada de controladores

* **Transacciones DB**
  → Uso de DB::transaction + lockForUpdate para evitar race conditions

* **Eventos desacoplados**
  → OrderCreated → NotifyStockUpdate

* **Observers de dominio**
  → OrderItemObserver gestiona subtotales y totales

* **API Resources**
  → JsonResource para respuestas consistentes

---

## 🛡️ Seguridad

La API responde exclusivamente en JSON bajo `/api/*`.

### Manejo de errores

* 401 → Token inválido o ausente
* 404 → Recurso no encontrado
* 422 → Error de validación o stock
* 500 → Error interno sin exponer detalles

✔️ Sin exposición de:

* trace
* file
* detalles internos del framework

---

## 🧰 Scripts útiles (Composer)

composer setup     → Instalación completa automática
composer test      → Ejecutar tests
composer swagger   → Generar documentación OpenAPI
composer lint      → Analizar estilo de código (Pint)

---

## 📌 Filosofía del Proyecto

Este proyecto demuestra:

* Buenas prácticas en Laravel moderno
* Arquitectura limpia y escalable
* Manejo realista de concurrencia
* APIs seguras y observables

---

## 🤝 Contribución

1. Fork del repositorio
2. Crear branch (feature/nueva-feature)
3. Commit de cambios
4. Pull Request 🚀

---

## 📄 Licencia

Este proyecto está bajo licencia MIT.

---

<p align="center">
  Hecho con ❤️ usando Laravel
</p>
