<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $predictionService = new \App\Services\StockPredictionService();
        $prediction = $predictionService->predictRupture($this->id);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'current_stock' => $this->current_stock,
            'prediction' => $prediction, // Inclut le statut et la recommandation
            'is_alert' => $prediction['status'] === 'Alerte Rupture',
        ];
    }
}
