@extends('layouts.app')

@section('title', $title)

@section('content')
    @include('admin.partials.page-header', [
        'title' => $title,
        'subtitle' => 'Showing up to 500 rows — export for the full dataset',
        'actions' => auth()->user()->can('reports.export')
            ? '<div class="btn-group">'
                .'<a class="btn btn-outline-primary" href="'.route('admin.reports.export', [$type, 'format' => 'csv'] + request()->query()).'"><i class="bi bi-filetype-csv me-1"></i>CSV</a>'
                .'<a class="btn btn-outline-primary" href="'.route('admin.reports.export', [$type, 'format' => 'excel'] + request()->query()).'"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>'
                .'<a class="btn btn-outline-primary" href="'.route('admin.reports.export', [$type, 'format' => 'pdf'] + request()->query()).'"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>'
                .'</div>'
            : '',
    ])

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="reportTable" style="width:100%">
                    <thead>
                        <tr>
                            @foreach ($headings as $heading)
                                <th>{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($headings) }}" class="text-muted">No data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $('#reportTable').DataTable({ serverSide: false, processing: false, ajax: null, paging: true, ordering: true });
</script>
@endpush
