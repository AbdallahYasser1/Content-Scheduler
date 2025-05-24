<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => PostResource::collection($this->collection),
            ... $this->paginationInformation()
        ];
    }

    public function paginationInformation()
    {
        return [
            'pagination' => [
                'total' => $this->resource->total(),
                'perPage' => $this->resource->perPage(),
                'currentPage' => $this->resource->currentPage(),
                'lastPage' => $this->resource->lastPage(),
                'nextPage' => $this->getNextPageNumber(),
                'prevPage' => $this->getPreviousPageNumber(),
            ]
        ];
    }

    private function getNextPageNumber()
    {
        return ($this->resource->currentPage() < $this->resource->lastPage()) ? $this->resource->currentPage(
            ) + 1 : null;
    }

    private function getPreviousPageNumber()
    {
        return ($this->resource->currentPage() > 1) ? $this->resource->currentPage() - 1 : null;
    }
}

