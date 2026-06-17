<div class="btn-group btn-group-sm">
    @can('exams.view')
        <a class="btn btn-outline-secondary" href="{{ route('admin.exams.show', $exam) }}" title="View"><i class="bi bi-eye"></i></a>
    @endcan
    @can('results.view')
        <a class="btn btn-outline-info" href="{{ route('admin.exams.results.entry', $exam) }}" title="Results"><i class="bi bi-card-checklist"></i></a>
    @endcan
    @can('update', $exam)
        <a class="btn btn-outline-primary" href="{{ route('admin.exams.edit', $exam) }}" title="Edit"><i class="bi bi-pencil"></i></a>
    @endcan
    @can('delete', $exam)
        <button class="btn btn-outline-danger" onclick="fmcDelete('{{ route('admin.exams.destroy', $exam) }}')" title="Delete"><i class="bi bi-trash"></i></button>
    @endcan
</div>
