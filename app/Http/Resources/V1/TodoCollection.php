<?php

namespace App\Http\Resources\V1;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TodoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'content'    => parent::toArray($request),
            'meta'       => $this->withMeta($request),
            'pagination' => $this->withPagination(),
        ];
    }

    protected function withMeta(Request $request): array
    {
        return [
            'summary' => [
                'total_items'                 => $this->total(),
                'total_items_per_page'        => $this->count(),
                'total_time_tracked'          => (int) Todo::sum('time_tracked'),
                'total_time_tracked_per_page' => (int) $this->collection->sum('time_tracked'),
            ],
            'download_url' => route('api.todos.export', $request->query()),
        ];
    }

    protected function withPagination(): array
    {
        return [
            'total'                  => $this->total(),
            'count'                  => $this->count(),
            'per_page'               => $this->perPage(),
            'current_page'           => $this->currentPage(),
            'total_pages'            => $this->lastPage(),
            'links'                  => [
                'next'     => $this->nextPageUrl(),
                'previous' => $this->previousPageUrl(),
            ],
        ];
    }
}
