@if ($paginator->hasPages())
  <nav class="paginacion" aria-label="Páginas">
    @if ($paginator->onFirstPage())
      <span class="paginacion__boton deshabilitado">Anterior</span>
    @else
      <a class="paginacion__boton" href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a>
    @endif

    <span class="paginacion__estado">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>

    @if ($paginator->hasMorePages())
      <a class="paginacion__boton" href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente</a>
    @else
      <span class="paginacion__boton deshabilitado">Siguiente</span>
    @endif
  </nav>
@endif
