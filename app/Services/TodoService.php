<?php

namespace App\Services;

use App\Models\Todo;
use Illuminate\Support\Facades\DB;

class TodoService
{
    public function getAllTodos($request = [])
    {
        $query = Todo::query();

        if (!empty($request['title'])) {
            $query->where('title', 'like', '%' . $request['title'] . '%');
        }

        if (!empty($request['assignee'])) {
            $assignees = array_map('trim', explode(',', $request['assignee']));
            $query->whereIn('assignee', $assignees);
        }

        if (!empty($request['start_due_date'])) {
            $query->where('due_date', '>=', $request['start_due_date']);
        }

        if (!empty($request['end_due_date'])) {
            $query->where('due_date', '<=', $request['end_due_date']);
        }

        if (!empty($request['min_time_tracked'])) {
            $query->where('time_tracked', '>=', $request['min_time_tracked']);
        }

        if (!empty($request['max_time_tracked'])) {
            $query->where('time_tracked', '<=', $request['max_time_tracked']);
        }

        if (!empty($request['status'])) {
            $statuses = array_map('trim', explode(',', $request['status']));
            $query->whereIn('status', $statuses);
        }

        if (!empty($request['priority'])) {
            $priorities = array_map('trim', explode(',', $request['priority']));
            $query->whereIn('priority', $priorities);
        }

        return $query->paginate($request['per_page'] ?? 50);
    }

    public function createTodo(array $data)
    {
        return Todo::create([
            'title'        => $data['title'],
            'assignee'     => $data['assignee'],
            'status'       => $data['status'] ?? 'pending',
            'priority'     => $data['priority'],
            'due_date'     => $data['due_date'],
            'time_tracked' => $data['time_tracked'],
        ]);
    }

    public function prepareChartData($type)
    {
        switch ($type) {
            case 'status':
                $rawData = Todo::select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status');

                $data = [];
                foreach (Todo::STATUSES as $status) {
                    $data[$status] = $rawData->get($status, 0);
                }

                return ['status_summary' => $data];
            case 'priority':
                $rawData = Todo::select('priority', DB::raw('count(*) as total'))
                    ->groupBy('priority')
                    ->pluck('total', 'priority');

                $data = [];
                foreach (Todo::PRIORITIES as $priority) {
                    $data[$priority] = $rawData->get($priority, 0);
                }

                return ['priority_summary' => $data];
            case 'assignee':
                $data = Todo::select(
                    'assignee',
                    DB::raw('count(*) as total_todos'),
                    DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as total_pending_todos")
                )
                    ->groupBy('assignee')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [
                            $item->assignee => [
                                'total_todos' => $item->total_todos,
                                'total_pending_todos' => $item->total_pending_todos,
                                'total_timetracked_completed_todos' => Todo::where(['assignee' => $item->assignee, 'status' => 'completed'])->sum('time_tracked'),
                            ]
                        ];
                    });

                return ['assignee_summary' => $data];
            default:
                throw new \InvalidArgumentException('Invalid chart type specified.', 400);
        }
    }
}
