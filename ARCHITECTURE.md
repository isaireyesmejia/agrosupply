# Arquitectura de AgroSupply

> Plataforma de gestión de cadena de suministro para el sector agroindustrial. Este documento describe las decisiones de arquitectura tomadas antes de escribir código, y el porqué de cada una.

## 1. Visión general

AgroSupply conecta **compradores**, **proveedores de insumos** y **administradores** dentro de una misma organización (tenant) para gestionar el ciclo completo de una orden de compra: creación, aprobación multinivel, seguimiento y facturación.

El proyecto está diseñado como una pieza de portafolio que demuestra:

- Separación de responsabilidades más allá del MVC básico de Laravel
- Multi-tenancy real (aislamiento de datos por organización)
- Control de acceso basado en roles y permisos
- Trazabilidad/auditoría de cambios de estado
- Exposición de API REST versionada junto a una interfaz web con Blade
- Cobertura de pruebas automatizadas sobre los flujos críticos

## 2. Stack tecnológico

| Capa | Tecnología | Justificación |
|---|---|---|
| Backend | Laravel 11 (PHP 8.3) | Framework maduro, ecosistema robusto, mismo paradigma de ORM/migraciones que ya domino en EF Core |
| Frontend | Blade + Alpine.js | Renderizado del lado del servidor rápido de construir; Alpine.js cubre interactividad puntual sin añadir un build de SPA |
| Base de datos | MySQL 8 (dev: SQLite en memoria para tests) | Estándar de facto en el ecosistema Laravel |
| Autenticación | Laravel Breeze | Scaffolding mínimo y auditable, sin magia oculta |
| Roles y permisos | spatie/laravel-permission | Librería de referencia en el ecosistema, basada en tablas relacionales estándar |
| PDF | barryvdh/laravel-dompdf | Generación de órdenes de compra y facturas en PDF |
| Testing | Pest | Sintaxis expresiva sobre PHPUnit, estándar actual en proyectos Laravel modernos |
| Colas | Laravel Queue (driver `database`) | Suficiente para portafolio; documentado el camino a Redis en producción |

## 3. Organización del código

Se evita el anti-patrón de "controlador gordo" típico de proyectos junior. La lógica de negocio vive fuera de los controladores, en tres capas explícitas:

```
app/
├── Actions/              Casos de uso puntuales y con nombre de negocio
│   ├── CrearOrdenCompraAction.php
│   ├── AprobarOrdenCompraAction.php
│   └── RechazarOrdenCompraAction.php
├── Models/                Entidades Eloquent (sin lógica de negocio pesada)
├── Repositories/
│   ├── Contracts/         Interfaces (para poder sustituir la implementación)
│   └── Eloquent/          Implementación concreta sobre Eloquent
├── Services/               Orquestación: combina repositorios + actions + eventos
├── Events/                 OrdenAprobada, OrdenRechazada, StockBajo
├── Listeners/               EnviarNotificacionCambioEstado, ActualizarInventario
├── Notifications/
├── Http/
│   ├── Controllers/
│   │   ├── Web/            Controladores Blade (delgados, delegan a Actions/Services)
│   │   └── Api/V1/         Controladores API versionados
│   ├── Requests/            Form Requests (toda validación vive aquí, no en el controlador)
│   └── Resources/           API Resources (transformación de respuestas JSON)
├── Policies/                 Autorización por modelo (OrdenCompraPolicy, ProveedorPolicy)
├── Jobs/                     Trabajos en cola (generación de PDF, envío de reportes)
└── Exceptions/
```

**Por qué esta separación:** un controlador solo debe traducir HTTP en una llamada a una Action o Service, y una respuesta. La lógica de negocio (¿qué pasa cuando se aprueba una orden?) vive en un lugar con nombre de dominio, testeable sin simular una petición HTTP completa, y reutilizable tanto desde el controlador Web como desde el de la API.

## 4. Multi-tenancy

**Estrategia elegida:** base de datos única con `tenant_id` en cada tabla relevante, aplicado automáticamente mediante un **Global Scope** de Eloquent.

Se descartaron las alternativas de *base de datos por tenant* y *esquema por tenant* porque:

- Añaden complejidad operativa (migraciones, backups y conexiones múltiples) desproporcionada para el tamaño de este proyecto
- El patrón de `tenant_id` + Global Scope es el más común en aplicaciones SaaS de tamaño pequeño/mediano en producción real, y es el que se documentó como plan para el SaaS agroindustrial propio a futuro

Cada modelo con datos de tenant implementa un trait `BelongsToTenant` que:

1. Aplica un Global Scope filtrando por `tenant_id` del usuario autenticado
2. Asigna automáticamente el `tenant_id` al crear un registro

## 5. Roles y permisos

Tres roles principales, gestionados con `spatie/laravel-permission`:

- **Administrador** — gestiona usuarios, proveedores e insumos; aprueba órdenes de cualquier monto
- **Comprador** — crea órdenes de compra; puede aprobar hasta un monto límite configurable
- **Proveedor** — consulta únicamente las órdenes dirigidas a él y actualiza su catálogo de insumos

La autorización a nivel de modelo se implementa con **Policies** (`OrdenCompraPolicy::approve()`, por ejemplo), nunca con condicionales `if ($user->role === 'admin')` dispersos en los controladores.

## 6. Flujo de dominio: orden de compra

1. Un **Comprador** crea una orden de compra con uno o más insumos (`CrearOrdenCompraAction`)
2. La orden entra en estado `pendiente`
3. Un **Administrador** (o el propio Comprador si está dentro de su límite) la aprueba o rechaza
4. Cada cambio de estado se registra en `orden_historial` (quién, cuándo, estado anterior/nuevo) — mismo patrón de auditoría que el flujo de desbloqueo de facturas de ABiERP
5. Al aprobar, se dispara el evento `OrdenAprobada`, que desencadena de forma desacoplada:
   - Notificación por correo al proveedor
   - Actualización de inventario reservado
   - Generación de una factura en estado `pendiente_pago`
6. La orden aprobada puede exportarse a PDF en cualquier momento

## 7. API REST

Se expone una API versionada (`/api/v1/...`) autenticada con **Laravel Sanctum**, con los endpoints mínimos para demostrar la capacidad (no se busca paridad completa con la interfaz web):

- `GET /api/v1/ordenes` — listar órdenes del tenant autenticado
- `GET /api/v1/ordenes/{id}` — detalle de una orden
- `POST /api/v1/ordenes` — crear una orden

Los controladores de API reutilizan las mismas Actions/Services que los controladores Web — la lógica de negocio no se duplica entre ambas interfaces.

## 8. Testing

Cobertura con **Pest**, priorizando pruebas de feature sobre el flujo crítico:

- Creación de una orden de compra con insumos válidos e inválidos
- Flujo completo de aprobación (incluye verificación de que se registra el historial y se dispara la notificación)
- Aislamiento de tenant (un usuario del Tenant A nunca puede ver datos del Tenant B)
- Autorización por rol (un Proveedor no puede aprobar órdenes)

## 9. Fuera de alcance (documentado como roadmap, no implementado)

Decisiones explícitas para mantener el proyecto del tamaño de un portafolio, no de un ERP completo:

- Multi-tenancy con base de datos separada por cliente
- Facturación fiscal real (timbrado CFDI)
- Módulo de inventario con reglas de reabastecimiento automático
- Colas sobre Redis con Horizon (se documenta como el paso natural de escalar el driver `database`)

## 10. Despliegue

Documentado en `README.md`, con instrucciones para desplegar en Railway/Render. El objetivo es que el proyecto esté siempre accesible en una URL pública, no solo demostrable en local.
