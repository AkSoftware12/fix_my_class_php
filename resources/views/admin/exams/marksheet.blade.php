<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Marksheet</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .sheet { border: 2px solid #2563EB; border-radius: 10px; padding: 24px; }
        .header { text-align: center; border-bottom: 2px solid #2563EB; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { color: #2563EB; font-size: 20px; margin: 0; }
        .header p { color: #6b7280; margin: 4px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td, th { padding: 8px 10px; border: 1px solid #dbeafe; }
        th { background: #eff6ff; text-align: left; color: #1e40af; }
        .big { font-size: 26px; font-weight: bold; color: #2563EB; }
        .result-pass { color: #15803d; font-weight: bold; }
        .result-fail { color: #b91c1c; font-weight: bold; }
        .footer { margin-top: 36px; display: table; width: 100%; }
        .footer div { display: table-cell; text-align: center; color: #6b7280; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="header">
            <h1>{{ $result->student?->branch?->coaching?->name ?? setting('app_name', 'Fix My Class') }}</h1>
            <p>{{ $result->student?->branch?->name }} · Official Marksheet</p>
        </div>

        <table>
            <tr><th style="width:30%">Student Name</th><td>{{ $result->student?->user?->name }}</td></tr>
            <tr><th>Admission Number</th><td>{{ $result->student?->admission_number }}</td></tr>
            <tr><th>Class</th><td>{{ $result->student?->schoolClass?->name ?? '—' }}</td></tr>
            <tr><th>Exam</th><td>{{ $exam->title }} ({{ strtoupper($exam->type) }})</td></tr>
            <tr><th>Exam Date</th><td>{{ $exam->exam_date?->format('d M Y') ?? '—' }}</td></tr>
        </table>

        <table>
            <tr>
                <th>Marks Obtained</th>
                <th>Total Marks</th>
                <th>Percentage</th>
                <th>Grade</th>
                <th>Rank</th>
                <th>Result</th>
            </tr>
            <tr>
                <td class="big">{{ rtrim(rtrim(number_format((float) $result->marks_obtained, 2), '0'), '.') }}</td>
                <td>{{ $exam->total_marks }}</td>
                <td>{{ $exam->total_marks > 0 ? number_format(($result->marks_obtained / $exam->total_marks) * 100, 1) : 0 }}%</td>
                <td class="big">{{ $result->grade }}</td>
                <td>{{ $result->rank ?? '—' }}</td>
                <td class="{{ $result->is_pass ? 'result-pass' : 'result-fail' }}">{{ $result->is_pass ? 'PASS' : 'FAIL' }}</td>
            </tr>
        </table>

        @if ($result->remarks)
            <table>
                <tr><th style="width:30%">Remarks</th><td>{{ $result->remarks }}</td></tr>
            </table>
        @endif

        <div class="footer">
            <div>Generated {{ now()->format('d M Y') }}</div>
            <div>__________________<br>Examiner</div>
            <div>__________________<br>Principal</div>
        </div>
    </div>
</body>
</html>
