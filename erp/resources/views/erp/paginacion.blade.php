{{-- Paginación del panel: "Mostrando 1–15 de 48" a la izquierda y las páginas a la derecha. --}}
@if ($paginator->total() > 0)
  <nav class="paginacion" aria-label="Páginas">
    <span>
      Mostrando <b class="tabular">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</b>
      de <b class="tabular">{{ $paginator->total() }}</b>
    </span>

    @if ($paginator->hasPages())
      <div class="paginacion__paginas">
        @if ($paginator->onFirstPage())
          <span class="inactivo" aria-hidden="true">‹</span>
        @else
          <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Página anterior">‹</a>
        @endif

        @foreach ($elements as $elemento)
          @if (is_string($elemento))
            <span class="inactivo" aria-hidden="true">…</span>
          @endif

          @if (is_array($elemento))
            @foreach ($elemento as $pagina => $url)
              @if ($pagina == $paginator->currentPage())
                <span class="activo" aria-current="page">{{ $pagina }}</span>
              @else
                <a href="{{ $url }}" aria-label="Página {{ $pagina }}">{{ $pagina }}</a>
              @endif
            @endforeach
          @endif
        @endforeach

        @if ($paginator->hasMorePages())
          <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Página siguiente">›</a>
        @else
          <span class="inactivo" aria-hidden="true">›</span>
        @endif
      </div>
    @endif
  </nav>
@endif
