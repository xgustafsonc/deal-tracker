<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{
    return [
        'id'                  => $this->id,
        'title'               => $this->title,
        'value'               => $this->value,
        'stage'               => [
            'value' => $this->stage->value,
            'label' => $this->stage->label(),
            'color' => $this->stage->color(),
        ],
        'expected_close_date' => $this->expected_close_date?->toDateString(),
        'notes'               => $this->notes,
        'company'             => new CompanyResource($this->whenLoaded('company')),
        'contact'             => new ContactResource($this->whenLoaded('contact')),
        'created_at'          => $this->created_at,
    ];
}
}
