# Cómo contribuir a JBL Web

Esta guía resume el flujo de trabajo del equipo. El detalle está en la página
[Flujo de trabajo](https://github.com/Dietri-Diaz/jbl-web/wiki/Flujo-de-trabajo) de la wiki.

## Antes de empezar

1. Revisa el [tablero Kanban](https://github.com/users/Dietri-Diaz/projects/1) y elige un issue, o crea uno con la plantilla de error o de mejora.
2. Asígnate el issue y muévelo a **En progreso**.
3. Instala el proyecto siguiendo la página [Instalación y ejecución](https://github.com/Dietri-Diaz/jbl-web/wiki/Instalacion-y-ejecucion).

## Ramas

Nadie trabaja directo en `main` ni en `develop`. Cada cambio va en su propia rama, creada desde `develop`:

| Prefijo | Para qué | Ejemplo |
|---------|----------|---------|
| `feature/` | Funcionalidad nueva | `feature/tienda-web` |
| `bugfix/` | Corrección de un error | `bugfix/fichas-tecnicas` |
| `docs/` | Documentación | `docs/wiki-capturas` |
| `chore/` | Mantenimiento | `chore/ignorar-env-local` |

```bash
git switch develop
git pull
git switch -c feature/nombre-corto
```

## Commits

Un commit por cambio, con el tipo al inicio y el mensaje en español:

```
feat: agrega el filtro por precio al catálogo
fix: corrige el total del carrito con envío gratis
docs: actualiza la página de despliegue
ci: agrega la etapa Package al pipeline
chore: ignora el archivo .env.local
```

Para enlazar el commit con su issue se escribe `Closes #n` (lo cierra al llegar a `main`) o `Refs #n` (solo lo enlaza).

## Antes de abrir el pull request

Ejecuta las mismas comprobaciones que corren en Jenkins y en GitHub Actions:

```bash
node scripts/verificar-landing.mjs
cd erp && php artisan test
```

## Pull request

1. Sube la rama y abre el pull request hacia `develop`.
2. Completa la plantilla: qué cambia, cómo se probó y qué issue cierra.
3. Espera a que los checks estén en verde y a la revisión del líder del equipo.
4. Mueve la tarjeta a **En revisión**.

De `develop` a `main` solo se pasa con un pull request de versión. Al fusionarlo, Jenkins despliega la tienda en Vercel.
