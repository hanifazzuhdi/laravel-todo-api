<?php

namespace Tests\Unit;

use App\Models\Todo;
use App\Services\TodoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class TodoServiceTest extends TestCase
{
    use RefreshDatabase;

    private TodoService $todoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->todoService = new TodoService();

        // sample todos for testing
        $this->createSampleTodos();
    }

    public function test_can_get_all_todos_without_filters()
    {
        $result = $this->todoService->getAllTodos();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_todos_by_title()
    {
        $request = ['title' => 'Test'];
        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(2, $result->total());
    }

    public function test_can_filter_todos_by_assignee()
    {
        $request = ['assignee' => 'John Doe'];
        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(2, $result->total());
        foreach ($result->items() as $todo) {
            $this->assertEquals('John Doe', $todo->assignee);
        }
    }

    public function test_can_filter_todos_by_multiple_assignees()
    {
        $request = ['assignee' => 'John Doe, Jane Smith'];
        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_filter_todos_by_status()
    {
        $request = ['status' => 'completed'];
        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('completed', $result->items()[0]->status);
    }

    public function test_can_filter_todos_by_multiple_statuses()
    {
        $request = ['status' => 'pending, completed'];
        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(2, $result->total());
    }

    public function test_can_filter_todos_by_priority()
    {
        $request = ['priority' => 'high'];
        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('high', $result->items()[0]->priority);
    }

    public function test_can_filter_todos_by_due_date_range()
    {
        $request = [
            'start_due_date' => '2025-09-20',
            'end_due_date' => '2025-10-02'
        ];
        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(2, $result->total());
    }

    public function test_can_filter_todos_by_time_tracked_range()
    {
        $request = [
            'min_time_tracked' => 5,
            'max_time_tracked' => 10
        ];
        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(2, $result->total());
    }

    public function test_can_create_todo()
    {
        $todoData = [
            'title' => 'New Todo',
            'assignee' => 'New User',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => '2025-10-20',
            'time_tracked' => 0,
        ];

        $todo = $this->todoService->createTodo($todoData);

        $this->assertInstanceOf(Todo::class, $todo);
        $this->assertEquals('New Todo', $todo->title);
        $this->assertEquals('New User', $todo->assignee);
        $this->assertDatabaseHas('todos', [
            'title' => 'New Todo',
            'assignee' => 'New User',
            'status' => 'pending',
            'priority' => 'medium',
            'time_tracked' => 0,
        ]);
    }

    public function test_can_prepare_status_chart_data()
    {
        $result = $this->todoService->prepareChartData('status');

        $this->assertArrayHasKey('status_summary', $result);
        $statusSummary = $result['status_summary'];

        $this->assertArrayHasKey('pending', $statusSummary);
        $this->assertArrayHasKey('completed', $statusSummary);
        $this->assertArrayHasKey('in_progress', $statusSummary);

        $this->assertEquals(1, $statusSummary['pending']);
        $this->assertEquals(1, $statusSummary['completed']);
        $this->assertEquals(1, $statusSummary['in_progress']);
    }

    public function test_can_prepare_priority_chart_data()
    {
        $result = $this->todoService->prepareChartData('priority');

        $this->assertArrayHasKey('priority_summary', $result);
        $prioritySummary = $result['priority_summary'];

        $this->assertArrayHasKey('high', $prioritySummary);
        $this->assertArrayHasKey('medium', $prioritySummary);
        $this->assertArrayHasKey('low', $prioritySummary);

        $this->assertEquals(1, $prioritySummary['high']);
        $this->assertEquals(1, $prioritySummary['medium']);
        $this->assertEquals(1, $prioritySummary['low']);
    }

    public function test_can_prepare_assignee_chart_data()
    {
        $result = $this->todoService->prepareChartData('assignee');

        $this->assertArrayHasKey('assignee_summary', $result);
        $assigneeSummary = $result['assignee_summary'];

        $this->assertArrayHasKey('John Doe', $assigneeSummary);
        $this->assertArrayHasKey('Jane Smith', $assigneeSummary);

        $johnData = $assigneeSummary['John Doe'];
        $this->assertEquals(2, $johnData['total_todos']);
        $this->assertEquals(1, $johnData['total_pending_todos']);

        $janeData = $assigneeSummary['Jane Smith'];
        $this->assertEquals(1, $janeData['total_todos']);
        $this->assertEquals(0, $janeData['total_pending_todos']);
    }

    public function test_throws_exception_for_invalid_chart_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid chart type specified.');

        $this->todoService->prepareChartData('invalid_type');
    }

    public function test_can_handle_empty_filters()
    {
        $request = [
            'title' => '',
            'assignee' => '',
            'status' => '',
            'priority' => '',
        ];

        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(3, $result->total());
    }

    public function test_can_paginate_results()
    {
        // additional todos for pagination test
        for ($i = 1; $i <= 10; $i++) {
            Todo::create([
                'title' => "Pagination Todo $i",
                'assignee' => 'Test User',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => '2025-10-01',
                'time_tracked' => 1,
            ]);
        }

        $request = ['per_page' => 5];
        $result = $this->todoService->getAllTodos($request);

        $this->assertEquals(5, $result->perPage());
        $this->assertEquals(13, $result->total()); // 3 original + 10 new
        $this->assertEquals(3, $result->lastPage());
    }

    private function createSampleTodos(): void
    {
        Todo::create([
            'title' => 'Test Todo 1',
            'assignee' => 'John Doe',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => '2025-10-01',
            'time_tracked' => 5,
        ]);

        Todo::create([
            'title' => 'Test Todo 2',
            'assignee' => 'Jane Smith',
            'status' => 'completed',
            'priority' => 'medium',
            'due_date' => '2025-09-25',
            'time_tracked' => 10,
        ]);

        Todo::create([
            'title' => 'Another Task',
            'assignee' => 'John Doe',
            'status' => 'in_progress',
            'priority' => 'low',
            'due_date' => '2025-10-15',
            'time_tracked' => 3,
        ]);
    }
}
