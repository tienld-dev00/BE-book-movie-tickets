<?php

namespace App\Http\Resources;

trait PaginationCollectionTrait
{
    /**
     * @return array<string, mixed>
     */
    protected function getPaginationData(): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->resource->total(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'next_page' => $this->resource->nextPageUrl() ? $this->resource->currentPage() + 1 : null,
                'previous_page' => $this->resource->previousPageUrl() ? $this->resource->currentPage() - 1 : null,
            ]
        ];
    }
}
