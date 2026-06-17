<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    protected const TYPES = ['students', 'teachers', 'homework', 'notices', 'exams', 'leads'];

    public function __construct(
        protected ReportService $reports,
        protected ExportService $export,
    ) {
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('reports.view'), 403);

        return view('admin.reports.index', ['types' => self::TYPES]);
    }

    public function show(Request $request, string $type): View
    {
        abort_unless($request->user()->can('reports.view'), 403);
        abort_unless(in_array($type, self::TYPES), 404);

        $report = $this->reports->build($type, $request->user(), $request->query());

        return view('admin.reports.show', [
            'type' => $type,
            'title' => $report['title'],
            'headings' => $report['headings'],
            'rows' => $report['rows']->take(500)->all(),
        ]);
    }

    public function export(Request $request, string $type)
    {
        abort_unless($request->user()->can('reports.export'), 403);
        abort_unless(in_array($type, self::TYPES), 404);

        $report = $this->reports->build($type, $request->user(), $request->query());

        return $this->export->download(
            $request->input('format', 'csv'),
            $type.'-report-'.now()->format('Ymd-His'),
            $report['title'],
            $report['headings'],
            $report['rows'],
        );
    }
}
