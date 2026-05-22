<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait DataTableTrait
{
    /**
     * Apply DataTables search, sort, and pagination to a query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  Request  $request
     * @param  array  $searchColumns   Column names to search against
     * @param  array  $sortingColumns  Indexed map of DataTables column index => column name
     * @param  int    $recordsTotal    Total records before filtering
     * @return int    $recordsFiltered
     */
    protected function applyDataTableQuery($query, Request $request, array $searchColumns, array $sortingColumns, int $recordsTotal): int
    {
        $searchValue = $request['search']['value'] ?? '';

        if ($searchValue !== '' && $searchValue !== null) {
            $query->where(function ($q) use ($searchValue, $searchColumns) {
                $first = true;
                foreach ($searchColumns as $column) {
                    if ($first) {
                        $q->where($column, 'LIKE', '%' . $searchValue . '%');
                        $first = false;
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . $searchValue . '%');
                    }
                }
            });
        }

        $recordsFiltered = $query->count();

        $orderData = $request['order'][0] ?? null;
        if ($orderData) {
            $columnIndex = (int) ($orderData['column'] ?? 0);
            $direction   = isset($orderData['dir']) && strtolower($orderData['dir']) === 'desc' ? 'desc' : 'asc';
            $sortColumn  = $sortingColumns[$columnIndex] ?? null;
            if ($sortColumn) {
                $query->orderBy($sortColumn, $direction);
            }
        }

        $start  = (int) ($request['start'] ?? 0);
        $length = (int) ($request['length'] ?? 10);

        $query->skip($start)->take($length > 0 ? $length : $recordsTotal);

        return $recordsFiltered;
    }

    /**
     * Build the standard DataTables JSON response.
     *
     * @param  Request  $request
     * @param  int      $recordsTotal
     * @param  int      $recordsFiltered
     * @param  array    $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function dataTableJson(Request $request, int $recordsTotal, int $recordsFiltered, array $data): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'draw'            => (int) $request->input('draw', 1),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
