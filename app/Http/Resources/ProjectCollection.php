<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => ProjectResource::collection($this->collection),
            'pagination' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'next' => $this->nextPageUrl(),
                'prev' => $this->previousPageUrl()
            ]
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setData($this->toArray($request));
    }
}
