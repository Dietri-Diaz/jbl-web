#!/usr/bin/env bash
# =========================================================================
# Seguimiento de incidentes de JBL Web
#
# Deja listo el sistema de issues del repositorio:
#   1. Crea (o actualiza) las 6 etiquetas del equipo.
#   2. Crea los 2 milestones.
#   3. Clasifica los 5 issues que ya existen (#20 a #24).
#   4. Crea los 7 issues nuevos (5 del tercer avance y 2 de la tienda).
#
# Requisitos: GitHub CLI (gh) con sesión iniciada en una cuenta con permiso
# de escritura sobre el repositorio.
#
#   bash trabajo/seguimiento-issues.sh
#
# Se puede ejecutar más de una vez: las etiquetas y los milestones no se
# duplican. Los issues nuevos SÍ se crearían otra vez, por eso el script
# pregunta antes de crearlos.
# =========================================================================
set -euo pipefail

REPO="Dietri-Diaz/jbl-web"

# ---- Responsables (usuarios de GitHub del equipo) ------------------------
DIETRI="Dietri-Diaz"
JORGE="jorgeo26"
FABRIZIO="Alba-devjxt"
JULIBETH="Anto-Jaimes"

# ---- Milestones: [EDITAR] cambia las fechas por las del sílabo -----------
M1="Tercer avance: CI/CD y nube"
M1_FECHA="2026-11-22T23:59:59Z"   # [EDITAR] fecha de entrega del tercer avance
M2="Entrega final (semana 18)"
M2_FECHA="2026-12-06T23:59:59Z"   # [EDITAR] fecha de la exposición final

echo "== 1. Etiquetas =="
gh label create "bug"           --repo "$REPO" --force --color "D73A4A" --description "Algo no funciona como debería"
gh label create "mejora"        --repo "$REPO" --force --color "A2EEEF" --description "Función nueva o cambio que mejora lo existente"
gh label create "documentación" --repo "$REPO" --force --color "0075CA" --description "README, wiki, informe o comentarios del código"
gh label create "urgente"       --repo "$REPO" --force --color "B60205" --description "Bloquea al equipo o a la página publicada"
gh label create "base-de-datos" --repo "$REPO" --force --color "5319E7" --description "Tablas, migraciones, datos de prueba o schema.sql"
gh label create "ci-cd"         --repo "$REPO" --force --color "FBCA04" --description "Jenkins, GitHub Actions, Docker o despliegue en Vercel"

echo "== 2. Milestones =="
crear_milestone() {
  local titulo="$1" fecha="$2" descripcion="$3"
  if gh api "repos/$REPO/milestones?state=all" --jq '.[].title' | grep -Fxq "$titulo"; then
    echo "Ya existe: $titulo"
  else
    gh api "repos/$REPO/milestones" -f title="$titulo" -f due_on="$fecha" -f description="$descripcion" --jq '"Creado: " + .title'
  fi
}
crear_milestone "$M1" "$M1_FECHA" "Seguimiento de incidentes, integración continua, entrega continua y despliegue en Vercel."
crear_milestone "$M2" "$M2_FECHA" "Tienda completa, ERP ampliado, documentación y exposición del proyecto."

echo "== 3. Issues que ya existen (#20 a #24) =="
gh issue edit 20 --repo "$REPO" --add-label "mejora"        --add-assignee "$FABRIZIO" --milestone "$M2"
gh issue edit 21 --repo "$REPO" --add-label "mejora"        --add-assignee "$JULIBETH" --milestone "$M2"
gh issue edit 22 --repo "$REPO" --add-label "documentación" --add-assignee "$JULIBETH" --milestone "$M1"
gh issue edit 23 --repo "$REPO" --add-label "mejora"        --add-assignee "$JORGE"    --milestone "$M2"
gh issue edit 24 --repo "$REPO" --add-label "documentación" --add-assignee "$DIETRI"   --milestone "$M1"

echo "== 4. Issues nuevos del tercer avance =="
read -r -p "¿Crear los 7 issues nuevos? Hazlo solo la primera vez (s/n): " respuesta
if [ "$respuesta" != "s" ]; then
  echo "No se crearon issues nuevos."
  exit 0
fi

gh issue create --repo "$REPO" \
  --title "[BUG] Las pruebas del ERP fallan: la raíz responde 302 y la prueba espera 200" \
  --label "bug" --label "urgente" --assignee "$DIETRI" --milestone "$M1" \
  --body "## Descripción
\`php artisan test\` termina en rojo. La prueba de ejemplo de Laravel pide la raíz del ERP y espera un 200, pero la raíz redirige a \`/panel\` (302). Mientras falle, el pipeline de integración continua no puede pasar.

## Pasos para reproducir
1. Entrar a la carpeta \`erp/\`.
2. Ejecutar \`composer install\` y \`php artisan test\`.
3. Ver el resultado de \`Tests\\Feature\\ExampleTest\`.

## Resultado esperado
Todas las pruebas pasan.

## Resultado actual
\`Expected response status code [200] but received 302.\` — 1 prueba falla y 1 pasa.

## Solución propuesta
Corregir la prueba para que compruebe la redirección y agregar pruebas reales de la API y del panel."

gh issue create --repo "$REPO" \
  --title "[BUG] La landing publicada intenta conectarse a 127.0.0.1:8000" \
  --label "bug" --assignee "$DIETRI" --milestone "$M1" \
  --body "## Descripción
\`js/catalogo-erp.js\` y \`js/contacto.js\` usan \`http://127.0.0.1:8000\` como dirección del ERP cuando la página no trae el atributo \`data-erp\`. En la computadora de desarrollo está bien, pero en el sitio publicado en Vercel el navegador del visitante intenta conectarse a su propia máquina.

## Pasos para reproducir
1. Abrir la landing desde una dirección pública (por ejemplo, la de Vercel).
2. Abrir las herramientas de desarrollo, pestaña Red.
3. Recargar la página y enviar el formulario de contacto.

## Resultado esperado
Si no hay un ERP configurado, la landing muestra el catálogo escrito en el HTML sin hacer peticiones, y el formulario avisa que todavía no está conectado.

## Resultado actual
Se ve una petición fallida a \`http://127.0.0.1:8000/api/parlantes\`; algunos navegadores piden permiso para acceder a la red local, y el formulario responde «el servidor no responde».

## Solución propuesta
Usar \`127.0.0.1\` solo cuando la página se abre en \`localhost\`, \`127.0.0.1\` o como archivo. En cualquier otro dominio, usar únicamente \`data-erp\`."

gh issue create --repo "$REPO" \
  --title "[MEJORA] Configurar la integración continua con Jenkins" \
  --label "mejora" --label "ci-cd" --assignee "$JORGE" --milestone "$M1" \
  --body "## Qué se propone
Levantar Jenkins con Docker y agregar un \`Jenkinsfile\` con las etapas Checkout, Build, Test y Package, más un workflow de GitHub Actions que repita las comprobaciones en cada pull request.

## Por qué hace falta
Hoy nadie comprueba que la landing y el ERP sigan funcionando antes de fusionar una rama. La rúbrica del tercer avance pide integración continua con evidencia del pipeline ejecutado.

## Pasos para comprobarlo
1. \`docker compose -f jenkins/docker-compose.yml up -d --build\`
2. Crear el job tipo Pipeline apuntando al repositorio.
3. Hacer un push y ver las cuatro etapas en verde.

## Criterios de aceptación
- [ ] \`jenkins/Dockerfile\` y \`jenkins/docker-compose.yml\` en el repositorio
- [ ] \`Jenkinsfile\` con Checkout, Build, Test y Package
- [ ] \`.github/workflows/ci.yml\` con el check en los pull requests
- [ ] Webhook de GitHub que dispara el pipeline en cada push
- [ ] Captura del pipeline ejecutado sin errores"

gh issue create --repo "$REPO" \
  --title "[MEJORA] Desplegar la landing en Vercel desde Jenkins" \
  --label "mejora" --label "ci-cd" --assignee "$FABRIZIO" --milestone "$M1" \
  --body "## Qué se propone
Agregar la etapa Deploy al \`Jenkinsfile\`: cuando un cambio llega a \`main\` y las pruebas pasan, Jenkins publica la landing en Vercel con la CLI, usando un token guardado en Jenkins Credentials.

## Por qué hace falta
La landing solo se puede ver en la computadora de cada integrante. La rúbrica pide entrega continua, una plataforma en la nube y el enlace al proyecto desplegado.

## Pasos para comprobarlo
1. Fusionar un pull request en \`main\`.
2. Ver en Jenkins que la etapa Deploy termina en verde.
3. Abrir la dirección de Vercel y comprobar que el cambio está publicado.

## Criterios de aceptación
- [ ] Proyecto creado en Vercel y enlazado al repositorio
- [ ] Token de Vercel guardado en Jenkins como Secret text (nunca en el código)
- [ ] Etapa Deploy que solo corre en \`main\`
- [ ] \`vercel.json\` y \`.vercelignore\` para publicar únicamente la landing
- [ ] Enlace del sitio publicado en el README y en el About del repositorio"

gh issue create --repo "$REPO" \
  --title "[MEJORA] Agregar database/schema.sql y el diccionario de datos" \
  --label "mejora" --label "base-de-datos" --label "documentación" --assignee "$JULIBETH" --milestone "$M1" \
  --body "## Qué se propone
Agregar un script SQL con todas las tablas del ERP y datos de prueba, y documentar el modelo entidad-relación y el diccionario de datos en la wiki.

## Por qué hace falta
La base de datos solo se puede conocer leyendo las migraciones de Laravel. Quien no tenga PHP instalado no puede crearla ni revisarla.

## Pasos para comprobarlo
1. Ejecutar \`mysql -u root -p < database/schema.sql\`.
2. Comprobar que se crean las 16 tablas con los datos de ejemplo (usuarios, categorías, productos y pedidos).

## Criterios de aceptación
- [ ] \`database/schema.sql\` con claves primarias, foráneas y restricciones
- [ ] Datos de ejemplo en cada tabla del negocio
- [ ] Diagrama entidad-relación y diccionario de datos en la wiki"

gh issue create --repo "$REPO" \
  --title "[MEJORA] Convertir la landing en una tienda con carrito y pedidos" \
  --label "mejora" --assignee "$DIETRI" --milestone "$M2" \
  --body "## Qué se propone
Pasar de una landing publicitaria a una tienda de demostración: catálogo con filtros, ficha de producto, carrito, pago y seguimiento del pedido.

## Por qué hace falta
El proyecto final necesita un flujo completo que use la base de datos: hoy la página solo muestra seis parlantes y un formulario de contacto.

## Pasos para comprobarlo
1. Encender el ERP (\`php artisan serve\`) y servir la tienda en el puerto 5500.
2. Agregar productos al carrito y finalizar la compra.
3. Ver el pedido en el panel y seguirlo en \`pedido.html\` con su código y el correo.

## Criterios de aceptación
- [ ] Catálogo con filtros por categoría, precio, oferta y stock
- [ ] Carrito guardado en el navegador, con tope según el stock
- [ ] Pedido registrado en la base de datos con código de seguimiento
- [ ] La tienda funciona con la copia estática cuando el ERP no está disponible
- [ ] Aviso visible de que es una tienda de demostración"

gh issue create --repo "$REPO" \
  --title "[MEJORA] Ampliar el ERP: categorías, inventario, pedidos, roles y bitácora" \
  --label "mejora" --label "base-de-datos" --assignee "$DIETRI" --milestone "$M2" \
  --body "## Qué se propone
Ampliar el panel para administrar la tienda completa: productos con precio y stock, categorías, existencias y movimientos de inventario, pedidos con sus estados, usuarios con roles y bitácora de acciones.

## Por qué hace falta
El panel solo administraba seis parlantes y los mensajes. Una tienda necesita controlar el stock, atender pedidos y saber quién cambió qué.

## Pasos para comprobarlo
1. \`php artisan migrate --seed\` dentro de \`erp/\`.
2. Ingresar con \`admin@jbl.pe\` y recorrer cada módulo.
3. Cancelar un pedido y comprobar que su stock vuelve al inventario.
4. Ingresar con \`consulta@jbl.pe\` y comprobar que no puede modificar nada.

## Criterios de aceptación
- [ ] Migraciones nuevas con sus claves foráneas
- [ ] Cada cambio de stock queda como un movimiento de inventario
- [ ] Tres roles: administrador, editor y solo lectura
- [ ] Bitácora con usuario, acción, módulo y fecha
- [ ] Pruebas automáticas del panel y de la API en verde"

echo
echo "Listo. Revisa el resultado en https://github.com/$REPO/issues"
