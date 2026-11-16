// Verifica la tienda antes de publicarla. No necesita instalar nada:
// solo Node.js. Se usa en la etapa Test del pipeline.
//
//   node scripts/verificar-landing.mjs
//
// Revisa cuatro cosas:
//   1. Que cada archivo local que enlazan las páginas HTML exista.
//   2. Que data/catalogo.js cargue y cada producto tenga sus datos completos.
//   3. Que la foto de cada producto exista en el repositorio.
//   4. Que los archivos de js/ no tengan errores de sintaxis.

import { existsSync, readFileSync, readdirSync } from 'node:fs';
import { execFileSync } from 'node:child_process';
import vm from 'node:vm';

const errores = [];

// ---- 1. Enlaces locales de las páginas -----------------------------------
const paginas = readdirSync('.').filter((archivo) => archivo.endsWith('.html'));
let enlacesRevisados = 0;

for (const pagina of paginas) {
  const html = readFileSync(pagina, 'utf8');
  const enlaces = [...html.matchAll(/(?:src|href)="([^"#]+)"/g)]
    .map((coincidencia) => coincidencia[1].split('?')[0])
    .filter((ruta) => ruta && !/^(https?:|mailto:|tel:|data:)/.test(ruta));

  for (const ruta of new Set(enlaces)) {
    enlacesRevisados += 1;
    if (!existsSync(ruta)) errores.push(`${pagina} enlaza "${ruta}", pero ese archivo no existe`);
  }
}
if (!paginas.includes('index.html')) errores.push('Falta index.html en la raíz');
console.log(`Páginas revisadas: ${paginas.length} (${enlacesRevisados} enlaces locales)`);

// ---- 2 y 3. Copia estática del catálogo ----------------------------------
try {
  // El archivo asigna window.JBL_CATALOGO: se ejecuta en un entorno aislado.
  const entorno = { window: {} };
  vm.runInNewContext(readFileSync('data/catalogo.js', 'utf8'), entorno);
  const { tienda, categorias, productos } = entorno.window.JBL_CATALOGO || {};

  if (!tienda || !Array.isArray(categorias) || !Array.isArray(productos) || productos.length === 0) {
    errores.push('data/catalogo.js debe definir tienda, categorias y al menos un producto');
  } else {
    const campos = ['id', 'nombre', 'slug', 'categoria', 'precio', 'precio_final', 'stock', 'foto', 'foto_alt'];
    const slugs = new Set();
    const slugsCategorias = new Set(categorias.map((c) => c.slug));

    for (const p of productos) {
      const nombre = p.nombre || `producto ${p.id}`;
      for (const campo of campos) {
        if (p[campo] === undefined || p[campo] === null || p[campo] === '') errores.push(`Catálogo: a "${nombre}" le falta "${campo}"`);
      }
      if (slugs.has(p.slug)) errores.push(`Catálogo: el slug "${p.slug}" está repetido`);
      slugs.add(p.slug);
      if (!slugsCategorias.has(p.categoria)) errores.push(`Catálogo: "${nombre}" está en una categoría que no existe (${p.categoria})`);
      if (!(p.precio_final > 0)) errores.push(`Catálogo: "${nombre}" no tiene un precio válido`);
      if (p.precio_oferta !== null && !(p.precio_oferta < p.precio)) errores.push(`Catálogo: la oferta de "${nombre}" no es menor que su precio`);
      if (p.foto && !/^https?:/.test(p.foto) && !existsSync(p.foto)) errores.push(`Catálogo: falta la foto de "${nombre}" (${p.foto})`);
    }
    console.log(`Productos revisados: ${productos.length} en ${categorias.length} categorías`);
  }
} catch (error) {
  errores.push(`data/catalogo.js no se pudo leer: ${error.message}`);
}

// ---- 4. Sintaxis de JavaScript -------------------------------------------
const scripts = readdirSync('js').filter((archivo) => archivo.endsWith('.js'));
for (const archivo of scripts) {
  try {
    execFileSync(process.execPath, ['--check', `js/${archivo}`], { stdio: 'pipe' });
  } catch (error) {
    errores.push(`js/${archivo} tiene un error de sintaxis:\n${error.stderr}`);
  }
}
console.log(`Archivos de js/ revisados: ${scripts.length}`);

// ---- Resultado -----------------------------------------------------------
if (errores.length > 0) {
  console.error(`\nLa tienda tiene ${errores.length} problema(s):`);
  errores.forEach((error) => console.error(` - ${error}`));
  process.exit(1);
}
console.log('\nTienda verificada: todo en orden.');
