<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\User;
use App\Repositories\ExamRepository;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function __construct(protected ExamRepository $exams)
    {
    }

    public function create(array $data, User $creator): Exam
    {
        return DB::transaction(function () use ($data, $creator) {
            /** @var Exam $exam */
            $exam = $this->exams->create([
                ...collect($data)->only([
                    'coaching_id', 'branch_id', 'school_class_id', 'batch_id', 'subject_id',
                    'title', 'type', 'instructions', 'exam_date', 'start_time',
                    'duration_minutes', 'total_marks', 'passing_marks', 'status',
                ])->all(),
                'created_by' => $creator->id,
            ]);

            $this->syncQuestions($exam, $data['questions'] ?? []);

            return $exam;
        });
    }

    public function update(Exam $exam, array $data): Exam
    {
        return DB::transaction(function () use ($exam, $data) {
            $exam = $this->exams->update($exam, collect($data)->only([
                'branch_id', 'school_class_id', 'batch_id', 'subject_id',
                'title', 'type', 'instructions', 'exam_date', 'start_time',
                'duration_minutes', 'total_marks', 'passing_marks', 'status',
            ])->all());

            if (array_key_exists('questions', $data)) {
                $this->syncQuestions($exam, $data['questions'] ?? []);
            }

            return $exam;
        });
    }

    public function delete(Exam $exam): void
    {
        $this->exams->delete($exam);
    }

    /**
     * Questions arrive as arrays: [question, type, options[], correct_option, marks].
     */
    protected function syncQuestions(Exam $exam, array $questions): void
    {
        abort_if(
            $exam->status === 'published' && $exam->results()->exists(),
            422,
            'Cannot modify questions for an exam that already has results.'
        );

        $keptIds = [];

        foreach (array_values($questions) as $index => $question) {
            if (empty($question['question'])) {
                continue;
            }

            $payload = [
                'question' => $question['question'],
                'type' => $question['type'] ?? 'mcq',
                'options' => ($question['type'] ?? 'mcq') === 'mcq'
                    ? array_values(array_filter($question['options'] ?? []))
                    : null,
                'correct_option' => $question['correct_option'] ?? null,
                'marks' => (int) ($question['marks'] ?? 1),
                'sort_order' => $index,
            ];

            if (! empty($question['id'])) {
                $existing = $exam->questions()->find($question['id']);
                if ($existing) {
                    $existing->update($payload);
                    $keptIds[] = $existing->id;

                    continue;
                }
            }

            $keptIds[] = $exam->questions()->create($payload)->id;
        }

        $exam->questions()->whereNotIn('id', $keptIds)->delete();
    }
}
