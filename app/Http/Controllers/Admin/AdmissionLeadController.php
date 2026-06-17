<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdmissionLeadRequest;
use App\Http\Requests\LeadFollowUpRequest;
use App\Models\AdmissionLead;
use App\Models\Branch;
use App\Models\User;
use App\Repositories\AdmissionLeadRepository;
use App\Services\AdmissionLeadService;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdmissionLeadController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected AdmissionLeadRepository $leads,
        protected AdmissionLeadService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', AdmissionLead::class);

        if ($request->ajax()) {
            $query = $this->leads->filtered($request->user(), $request->only([
                'search', 'stage', 'branch_id', 'assigned_to',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($l) => $l->id,
                'student_name' => fn ($l) => e($l->student_name),
                'mobile' => fn ($l) => e($l->mobile),
                'interested_class' => fn ($l) => e($l->interested_class ?? '—'),
                'source' => fn ($l) => e($l->source ?? '—'),
                'stage' => fn ($l) => view('admin.leads.partials.stage', ['stage' => $l->stage])->render(),
                'assignee' => fn ($l) => e($l->assignee?->name ?? 'Unassigned'),
                'next_follow_up' => fn ($l) => $l->next_follow_up_at?->format('d M Y') ?? '—',
                'actions' => fn ($l) => view('admin.leads.partials.actions', ['lead' => $l])->render(),
            ], ['id', 'student_name', 'mobile', 'interested_class', 'source', 'stage', null, 'next_follow_up_at']);
        }

        return view('admin.leads.index', [
            'stageCounts' => $this->leads->stageCounts($request->user()),
        ] + $this->formOptions($request));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', AdmissionLead::class);

        return view('admin.leads.create', $this->formOptions($request));
    }

    public function store(AdmissionLeadRequest $request): RedirectResponse
    {
        $this->authorize('create', AdmissionLead::class);

        $data = $request->validated();
        $data['coaching_id'] = $request->user()->coaching_id
            ?? Branch::find($data['branch_id'] ?? null)?->coaching_id
            ?? abort(422, 'Please select a branch to associate this lead with a coaching.');

        $lead = $this->service->create($data, $request->user());

        return redirect()->route('admin.leads.index')
            ->with('success', "Lead \"{$lead->student_name}\" added.");
    }

    public function show(AdmissionLead $lead): View
    {
        $this->authorize('view', $lead);

        $lead->load(['branch', 'assignee', 'followUps.user']);

        return view('admin.leads.show', ['lead' => $lead]);
    }

    public function edit(Request $request, AdmissionLead $lead): View
    {
        $this->authorize('update', $lead);

        return view('admin.leads.edit', ['lead' => $lead] + $this->formOptions($request));
    }

    public function update(AdmissionLeadRequest $request, AdmissionLead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $this->service->update($lead, $request->validated(), $request->user());

        return redirect()->route('admin.leads.index')
            ->with('success', "Lead \"{$lead->student_name}\" updated.");
    }

    public function destroy(AdmissionLead $lead): JsonResponse
    {
        $this->authorize('delete', $lead);

        $this->service->delete($lead);

        return response()->json(['message' => "Lead \"{$lead->student_name}\" deleted."]);
    }

    public function followUp(LeadFollowUpRequest $request, AdmissionLead $lead): JsonResponse
    {
        $this->authorize('update', $lead);

        $lead = $this->service->addFollowUp($lead, $request->validated(), $request->user());

        return response()->json([
            'message' => 'Follow-up recorded.',
            'stage' => $lead->stage,
            'stage_label' => $lead->stage_label,
        ]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', AdmissionLead::class);

        $rows = $this->leads->filtered($request->user(), $request->only(['search', 'stage', 'branch_id', 'assigned_to']))
            ->latest()
            ->lazy()
            ->map(fn ($l) => [
                $l->id, $l->student_name, $l->guardian_name, $l->mobile, $l->email,
                $l->interested_class, $l->source, $l->stage_label,
                $l->assignee?->name, $l->next_follow_up_at?->format('d M Y'),
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'admission-leads-'.now()->format('Ymd-His'),
            'Admission Leads',
            ['ID', 'Student', 'Guardian', 'Mobile', 'Email', 'Class', 'Source', 'Stage', 'Assigned To', 'Next Follow-up'],
            $rows,
        );
    }

    protected function formOptions(Request $request): array
    {
        $user = $request->user();

        return [
            'branches' => Branch::visibleTo($user)->active()->orderBy('name')->get(),
            'staff' => User::visibleTo($user)->active()
                ->role(['Coaching Admin', 'Branch Admin', 'Teacher'])
                ->orderBy('name')
                ->get(),
            'stages' => AdmissionLead::STAGES,
        ];
    }
}
