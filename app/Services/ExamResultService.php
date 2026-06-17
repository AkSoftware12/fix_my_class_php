<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExamResultService
{
    /**
     * Bulk save marks for an exam: [{student_id, marks_obtained, remarks}].
     */
    public function saveResults(Exam $exam, array $rows, User $enteredBy): void
    {
        DB::transaction(function () use ($exam, $rows, $enteredBy) {
            foreach ($rows as $row) {
                if (! isset($row['student_id']) || $row['marks_obtained'] === null || $row['marks_obtained'] === '') {
                    continue;
                }

                $marks = min((float) $row['marks_obtained'], (float) $exam->total_marks);

                ExamResult::updateOrCreate(
                    ['exam_id' => $exam->id, 'student_id' => $row['student_id']],
                    [
                        'marks_obtained' => $marks,
                        'grade' => $this->grade($marks, $exam->total_marks),
                        'is_pass' => $marks >= $exam->passing_marks,
                        'remarks' => $row['remarks'] ?? null,
                        'entered_by' => $enteredBy->id,
                    ],
                );
            }

            $this->recomputeRanks($exam);
        });
    }

    public function publish(Exam $exam): void
    {
        DB::transaction(function () use ($exam) {
            $this->recomputeRanks($exam);
            $exam->results()->update(['is_published' => true]);
            $exam->update(['status' => 'completed']);
        });
    }

    public function unpublish(Exam $exam): void
    {
        $exam->results()->update(['is_published' => false]);
    }

    public function grade(float $marks, int $totalMarks): string
    {
        $percent = $totalMarks > 0 ? ($marks / $totalMarks) * 100 : 0;

        return match (true) {
            $percent >= 90 => 'A+',
            $percent >= 80 => 'A',
            $percent >= 70 => 'B+',
            $percent >= 60 => 'B',
            $percent >= 50 => 'C',
            $percent >= 33 => 'D',
            default => 'F',
        };
    }

    protected function recomputeRanks(Exam $exam): void
    {
        $results = $exam->results()->orderByDesc('marks_obtained')->get();

        $rank = 0;
        $previousMarks = null;
        $position = 0;

        foreach ($results as $result) {
            $position++;

            if ($previousMarks === null || (float) $result->marks_obtained < (float) $previousMarks) {
                $rank = $position;
                $previousMarks = $result->marks_obtained;
            }

            $result->update(['rank' => $rank]);
        }
    }
}
