<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\AdmissionLeadRepository;
use App\Repositories\ExamRepository;
use App\Repositories\HomeworkRepository;
use App\Repositories\NoticeRepository;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;

class ReportService
{
    public function __construct(
        protected StudentRepository $students,
        protected TeacherRepository $teachers,
        protected HomeworkRepository $homework,
        protected NoticeRepository $notices,
        protected ExamRepository $exams,
        protected AdmissionLeadRepository $leads,
    ) {
    }

    /**
     * @return array{headings: array, rows: \Illuminate\Support\LazyCollection, title: string}
     */
    public function build(string $type, User $user, array $filters = []): array
    {
        return match ($type) {
            'students' => $this->studentReport($user, $filters),
            'teachers' => $this->teacherReport($user, $filters),
            'homework' => $this->homeworkReport($user, $filters),
            'notices' => $this->noticeReport($user, $filters),
            'exams' => $this->examReport($user, $filters),
            'leads' => $this->leadReport($user, $filters),
            default => throw new \InvalidArgumentException("Unknown report type [{$type}]."),
        };
    }

    protected function studentReport(User $user, array $filters): array
    {
        $rows = $this->students->filtered($user, $filters)->latest()->lazy()->map(fn ($s) => [
            $s->admission_number,
            $s->user?->name,
            $s->user?->mobile,
            $s->user?->email,
            $s->branch?->name,
            $s->schoolClass?->name,
            $s->batch?->name,
            $s->is_active ? 'Active' : 'Inactive',
            $s->created_at?->format('d M Y'),
        ]);

        return [
            'title' => 'Student Report',
            'headings' => ['Admission No', 'Name', 'Mobile', 'Email', 'Branch', 'Class', 'Batch', 'Status', 'Joined'],
            'rows' => $rows,
        ];
    }

    protected function teacherReport(User $user, array $filters): array
    {
        $rows = $this->teachers->filtered($user, $filters)->latest()->lazy()->map(fn ($t) => [
            $t->user?->name,
            $t->user?->mobile,
            $t->user?->email,
            $t->qualification,
            $t->subject?->name,
            $t->branch?->name,
            $t->is_active ? 'Active' : 'Inactive',
        ]);

        return [
            'title' => 'Teacher Report',
            'headings' => ['Name', 'Mobile', 'Email', 'Qualification', 'Subject', 'Branch', 'Status'],
            'rows' => $rows,
        ];
    }

    protected function homeworkReport(User $user, array $filters): array
    {
        $rows = $this->homework->filtered($user, $filters)->latest()->lazy()->map(fn ($h) => [
            $h->title,
            ucfirst($h->type),
            $h->subject?->name,
            $h->creator?->name,
            $h->due_date?->format('d M Y'),
            ucfirst($h->status),
            $h->submissions_count,
        ]);

        return [
            'title' => 'Homework Report',
            'headings' => ['Title', 'Type', 'Subject', 'Created By', 'Due Date', 'Status', 'Submissions'],
            'rows' => $rows,
        ];
    }

    protected function noticeReport(User $user, array $filters): array
    {
        $rows = $this->notices->filtered($user, $filters)->latest()->lazy()->map(fn ($n) => [
            $n->title,
            ucfirst($n->type),
            ucfirst($n->audience),
            $n->publish_at?->format('d M Y H:i'),
            $n->expires_at?->format('d M Y'),
            $n->reads_count,
            $n->is_active ? 'Active' : 'Inactive',
        ]);

        return [
            'title' => 'Notice Report',
            'headings' => ['Title', 'Type', 'Audience', 'Published', 'Expires', 'Reads', 'Status'],
            'rows' => $rows,
        ];
    }

    protected function examReport(User $user, array $filters): array
    {
        $rows = $this->exams->filtered($user, $filters)->latest()->lazy()->map(fn ($e) => [
            $e->title,
            strtoupper($e->type),
            $e->subject?->name,
            $e->exam_date?->format('d M Y'),
            $e->total_marks,
            $e->results_count,
            ucfirst($e->status),
        ]);

        return [
            'title' => 'Exam Report',
            'headings' => ['Title', 'Type', 'Subject', 'Date', 'Total Marks', 'Results', 'Status'],
            'rows' => $rows,
        ];
    }

    protected function leadReport(User $user, array $filters): array
    {
        $rows = $this->leads->filtered($user, $filters)->latest()->lazy()->map(fn ($l) => [
            $l->student_name,
            $l->mobile,
            $l->email,
            $l->interested_class,
            $l->source,
            $l->stage_label,
            $l->assignee?->name,
            $l->next_follow_up_at?->format('d M Y'),
        ]);

        return [
            'title' => 'CRM Lead Report',
            'headings' => ['Student', 'Mobile', 'Email', 'Interested Class', 'Source', 'Stage', 'Assigned To', 'Next Follow-up'],
            'rows' => $rows,
        ];
    }
}
