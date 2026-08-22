# AgroSupply

Plataforma de gestión de cadena de suministro para el sector agroindustrial. Conecta compradores, proveedores de insumos y administradores en un flujo completo de órdenes de compra: creación, aprobación multinivel, auditoría y facturación.

Proyecto de portafolio construido para demostrar arquitectura de software a nivel senior en el ecosistema Laravel: separación de responsabilidades por capas, multi-tenancy, control de acceso basado en roles, trazabilidad de cambios de estado, API REST versionada y cobertura de pruebas automatizadas.

> 📄 Las decisiones de arquitectura y su justificación están documentadas en [`ARCHITECTURE.md`](./ARCHITECTURE.md).

## Estado del proyecto

🚧 En construcción — ver la sección de [roadmap](#roadmap) más abajo.

## Stack

- **Backend:** Laravel 11 (PHP 8.3)
- **Frontend:** Blade + Alpine.js
- **Base de datos:** MySQL 8 (SQLite en memoria para tests)
- **Autenticación:** Laravel Breeze
- **Roles y permisos:** spatie/laravel-permission
- **PDF:** barryvdh/laravel-dompdf
- **Testing:** Pest

## Funcionalidades

- [ ] Autenticación y roles (Administrador, Comprador, Proveedor)
- [ ] Aislamiento multi-tenant (single-database, `tenant_id` + Global Scope)
- [ ] CRUD de proveedores e insumos
- [ ] Flujo de órdenes de compra con aprobación y auditoría de estados
- [ ] Notificaciones por correo en cambios de estado
- [ ] Exportación de órdenes aprobadas a PDF
- [ ] Dashboard con métricas (pedidos por mes, top proveedores)
- [ ] API REST versionada (`/api/v1`) con Sanctum
- [ ] Pruebas automatizadas sobre los flujos críticos (Pest)
- [ ] Despliegue público (Railway/Render)

## Instalación local

```bash
git clone https://github.com/TU_USUARIO/agrosupply.git
cd agrosupply
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

## Pruebas

```bash
php artisan test
```

## Roadmap

Decisiones explícitas de alcance, documentadas en detalle en `ARCHITECTURE.md`:

- Multi-tenancy con base de datos separada por cliente
- Facturación fiscal real (timbrado CFDI)
- Módulo de inventario con reabastecimiento automático
- Colas sobre Redis + Horizon (actualmente driver `database`)

## Licencia

MIT