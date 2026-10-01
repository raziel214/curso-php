# curso-php

Hoja de vida pública + panel de administración con **CRUD completo** de Trabajos, Proyectos y Usuarios,
organizado con **arquitectura limpia** (capas Dominio → Aplicación → Infraestructura) y *package-by-feature*.

## Requisitos

- PHP 8.1+ con `pdo_mysql` (o `pdo_sqlite`)
- Composer 2
- MySQL/MariaDB (o SQLite para desarrollo)

## Puesta en marcha

```bash
composer install                 # genera composer.lock con las versiones nuevas
cp .env.example .env             # ajusta credenciales de BD
composer migrate                 # crea/actualiza tablas jobs, projects, users
php bin/create-user.php admin@correo.com "clave-segura"
composer serve                   # http://localhost:8000
composer test                    # pruebas unitarias
```

Para SQLite: `DB_DRIVER=sqlite` y `DB_DATABASE=database.sqlite`.

## Estructura

```
src/
├── Shared/
│   ├── Domain/                 # Excepciones de dominio y guardas (sin dependencias externas)
│   └── Infrastructure/         # Kernel HTTP, sesión/CSRF, Twig, contenedor DI, Eloquent bootstrap
├── Job/
│   ├── Domain/                 # Entidad Job + interfaz JobRepository (puerto)
│   ├── Application/            # Casos de uso: List/Get/Create/Update/DeleteJob + JobInput (DTO)
│   └── Infrastructure/
│       ├── Persistence/        # JobModel (Eloquent) + EloquentJobRepository (adaptador)
│       └── Http/               # JobController
├── Project/                    # Misma forma que Job
└── User/                       # Igual + PasswordHasher (puerto), AuthenticateUser, AuthController
config/
├── bootstrap.php               # Carga .env y arranca la BD
├── container.php               # Composition root: conecta puertos con adaptadores
└── routes.php                  # Rutas públicas y /admin protegidas
views/                          # Plantillas Twig por feature
tests/                          # PHPUnit con repositorios en memoria
```

**Regla de dependencias:** `Domain` no conoce a nadie; `Application` solo conoce `Domain`;
`Infrastructure` implementa las interfaces del dominio. Eloquent, Twig y PSR-7 nunca aparecen en
`Domain` ni en `Application`, por eso los casos de uso se prueban sin base de datos.

## Rutas

| Método | Ruta | Acción |
|---|---|---|
| GET | `/` | Hoja de vida pública |
| GET/POST | `/login` | Inicio de sesión |
| POST | `/logout` | Cerrar sesión |
| GET | `/admin` | Tablero (requiere sesión) |
| GET | `/admin/{recurso}` | Listar |
| GET | `/admin/{recurso}/new` | Formulario de creación |
| POST | `/admin/{recurso}` | Crear |
| GET | `/admin/{recurso}/{id}/edit` | Formulario de edición |
| POST | `/admin/{recurso}/{id}` | Actualizar |
| POST | `/admin/{recurso}/{id}/delete` | Eliminar |

`{recurso}` = `jobs`, `projects` o `users`. Todos los POST validan token CSRF.
