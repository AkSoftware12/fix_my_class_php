<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Dependent-dropdown data for cascading selects (AJAX).
 */
class LookupController extends Controller
{
    public function branches(Request $request): JsonResponse
    {
        $branches = Branch::visibleTo($request->user())
            ->active()
            ->when($request->input('coaching_id'), fn ($q, $id) => $q->where('coaching_id', $id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($branches);
    }

    public function classes(Request $request): JsonResponse
    {
        $classes = SchoolClass::visibleTo($request->user())
            ->active()
            ->when($request->input('branch_id'), fn ($q, $id) => $q
                ->where(fn ($w) => $w->where('branch_id', $id)->orWhereNull('branch_id')))
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($classes);
    }

    public function batches(Request $request): JsonResponse
    {
        $batches = Batch::visibleTo($request->user())
            ->active()
            ->when($request->input('branch_id'), fn ($q, $id) => $q->where('branch_id', $id))
            ->when($request->input('school_class_id'), fn ($q, $id) => $q->where('school_class_id', $id))
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($batches);
    }

    public function students(Request $request): JsonResponse
    {
        $students = Student::visibleTo($request->user())
            ->active()
            ->with('user:id,name')
            ->when($request->input('branch_id'), fn ($q, $id) => $q->where('branch_id', $id))
            ->when($request->input('batch_id'), fn ($q, $id) => $q
                ->where(fn ($w) => $w->where('batch_id', $id)
                    ->orWhereHas('batches', fn ($b) => $b->where('batches.id', $id))))
            ->when($request->input('q'), fn ($q, $term) => $q
                ->where(fn ($w) => $w->where('admission_number', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"))))
            ->limit(50)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => ($s->user?->name ?? 'Student').' ('.$s->admission_number.')',
            ]);

        return response()->json($students);
    }
}
