<?php

namespace App\Services;

use App\Models\AdmissionLead;
use App\Models\User;
use App\Repositories\AdmissionLeadRepository;
use Illuminate\Support\Facades\DB;

class AdmissionLeadService
{
    public function __construct(protected AdmissionLeadRepository $leads)
    {
    }

    public function create(array $data, User $user): AdmissionLead
    {
        return DB::transaction(function () use ($data, $user) {
            /** @var AdmissionLead $lead */
            $lead = $this->leads->create(collect($data)->only([
                'coaching_id', 'branch_id', 'assigned_to', 'student_name', 'guardian_name',
                'mobile', 'email', 'interested_class', 'source', 'stage',
                'next_follow_up_at', 'notes',
            ])->all());

            $lead->followUps()->create([
                'user_id' => $user->id,
                'stage' => $lead->stage,
                'remarks' => 'Lead created.',
                'followed_up_at' => now(),
            ]);

            return $lead;
        });
    }

    public function update(AdmissionLead $lead, array $data, User $user): AdmissionLead
    {
        return DB::transaction(function () use ($lead, $data, $user) {
            $stageChanged = isset($data['stage']) && $data['stage'] !== $lead->stage;

            $lead = $this->leads->update($lead, collect($data)->only([
                'branch_id', 'assigned_to', 'student_name', 'guardian_name',
                'mobile', 'email', 'interested_class', 'source', 'stage',
                'next_follow_up_at', 'notes',
            ])->all());

            if ($stageChanged) {
                $lead->followUps()->create([
                    'user_id' => $user->id,
                    'stage' => $lead->stage,
                    'remarks' => $data['stage_remarks'] ?? 'Stage updated.',
                    'followed_up_at' => now(),
                ]);
            }

            return $lead;
        });
    }

    public function addFollowUp(AdmissionLead $lead, array $data, User $user): AdmissionLead
    {
        return DB::transaction(function () use ($lead, $data, $user) {
            $lead->followUps()->create([
                'user_id' => $user->id,
                'stage' => $data['stage'],
                'remarks' => $data['remarks'] ?? null,
                'followed_up_at' => now(),
            ]);

            $lead->update([
                'stage' => $data['stage'],
                'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
            ]);

            return $lead->refresh();
        });
    }

    public function delete(AdmissionLead $lead): void
    {
        $this->leads->delete($lead);
    }
}
