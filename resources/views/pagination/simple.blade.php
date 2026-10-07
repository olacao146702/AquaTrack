@if ($paginator->hasPages())
<nav class="pager" role="navigation" aria-label="Pagination">
  @if ($paginator->onFirstPage())<span class="dis">&larr; Previous</span>@else<a href="{{ $paginator->previousPageUrl() }}" rel="prev">&larr; Previous</a>@endif
  <small>Page {{ $paginator->currentPage() }}@if (method_exists($paginator, 'lastPage')) of {{ $paginator->lastPage() }}@endif</small>
  @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" rel="next">Next &rarr;</a>@else<span class="dis">Next &rarr;</span>@endif
</nav>
@endif
