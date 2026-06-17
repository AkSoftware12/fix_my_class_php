<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Server-side processing responder for jQuery DataTables.
 */
trait RespondsWithDataTable
{
    /**
     * @param  array<string, callable>  $columns  column-name => row formatter
     * @param  array<int, string>  $orderable  index-aligned column names for ordering
     */
    protected function dataTable(Request $request, Builder $query, array $columns, array $orderable = []): JsonResponse
    {
        $total = (clone $query)->count();

        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $orderColumn = $orderable[$orderColumnIndex] ?? null;

        if ($orderColumn) {
            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->latest($query->getModel()->getQualifiedKeyName());
        }

        $length = (int) $request->input('length', 25);
        $length = $length > 0 ? min($length, 200) : 25;
        $start = max((int) $request->input('start', 0), 0);

        $records = $query->skip($start)->take($length)->get();

        $data = $records->map(function ($record) use ($columns) {
            $row = [];
            foreach ($columns as $key => $formatter) {
                $row[$key] = $formatter($record);
            }

            return $row;
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }
}
