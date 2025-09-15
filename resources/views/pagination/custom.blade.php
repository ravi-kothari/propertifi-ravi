@if ($paginator->hasPages())
<div class="row">
  <div class="col-sm-6 text-left">
    <div class="dataTables_paginate">
    Page {{$paginator->currentPage()}} of {{$paginator->total()}}, showing {{$paginator->count()}} records out of {{$paginator->currentPage()}} total, starting on record {{$paginator->firstItem()}}, ending on {{$paginator->lastItem()}}
    </div>
  </div>
  <div class="col-sm-6 text-right">
    <div class="dataTables_paginate" id="DataTables_Table_0_paginate">
      <ul class="pagination">
      	@if ($paginator->onFirstPage()) 
        <li class="disabled paginate_button previous" id="DataTables_Table_0_previous"><a href="#" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0">Previous</a></li>
        @else
        <li class="paginate_button previous" id="DataTables_Table_0_previous"><a href="{{ $paginator->previousPageUrl() }}" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0">Previous</a></li>
        @endif
        
        @foreach ($elements as $element)
        @if(is_string($element))
        <li class="paginate_button disabled"><span>{{ $element }}</span></li>
        @endif
        @if(is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="active paginate_button"><span>{{ $page }}</span></li>
                @else
                    <li class="paginate_button"><a href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach
        @endif
        @endforeach
        
        @if ($paginator->hasMorePages())        
        <li class="paginate_button next" id="DataTables_Table_0_next"><a href="{{ $paginator->nextPageUrl() }}" aria-controls="DataTables_Table_0" data-dt-idx="7" tabindex="0">Next</a></li>
        @else
        <li class="disabled paginate_button next" id="DataTables_Table_0_next"><a href="#" aria-controls="DataTables_Table_0" data-dt-idx="7" tabindex="0">Next</a></li>
        @endif
      </ul>
    </div>
  </div>
</div>
@endif