<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'icon' => $this->icon,
            'transactions_count' => $this->when(isset($this->transactions_count), $this->transactions_count),
            'created_at' => $this->created_at,
        ];
    }
}
