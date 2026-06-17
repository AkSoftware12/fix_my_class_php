{{-- $route: named route for export; $module: permission prefix --}}
@can($module.'.export')
    <div class="btn-group">
        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" type="button">
            <i class="bi bi-download me-1"></i> Export
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item js-export" data-format="csv" href="{{ route($route, ['format' => 'csv']) }}"><i class="bi bi-filetype-csv me-2"></i>CSV</a></li>
            <li><a class="dropdown-item js-export" data-format="excel" href="{{ route($route, ['format' => 'excel']) }}"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
            <li><a class="dropdown-item js-export" data-format="pdf" href="{{ route($route, ['format' => 'pdf']) }}"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
        </ul>
    </div>
@endcan
