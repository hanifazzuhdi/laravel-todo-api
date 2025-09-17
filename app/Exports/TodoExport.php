<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TodoExport implements FromArray, WithHeadings, WithMapping
{
    use Exportable;

    private $todos;

    public function __construct($todos)
    {
        $this->todos = $todos;
    }

    public function array(): array
    {
        $todosData =  $this->todos['content'] ?? [];

        // add empty row - separator
        $todosData[] = ['', '', '', '', '', ''];

        // add summary
        foreach ($this->appendSummaryRows() as $summaryRow) {
            $todosData[] = $summaryRow;
        }

        return $todosData;
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Status',
            'Priority',
            'Due Date',
            'Time Tracked',
        ];
    }

    public function map($todo): array
    {
        if (isset($todo[0]) && is_string($todo[0])) {
            return $todo;
        }

        return [
            $todo['title'] ?? '',
            $todo['assignee'] ?? '',
            $todo['status'] ?? '',
            $todo['priority'] ?? '',
            $todo['due_date'] ?? '',
            $todo['time_tracked'] ?? '',
        ];
    }

    protected function appendSummaryRows(): array
    {
        $total_items        = $this->todos['meta']['summary']['total_items_per_page'] ?? 0;
        $total_time_tracked = $this->todos['meta']['summary']['total_time_tracked_per_page'] ?? 0;

        return [
            ['Summary', '', '', '', '', ''],
            ['Total Items', $total_items, '', '', '', ''],
            ['Total Time Tracked', $total_time_tracked, '', '', '', '']
        ];
    }
}
