<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HighlightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_id' => $this->document_id,
            'page_number' => $this->page_number,
            'text_content' => $this->text_content,
            'note' => $this->note,
            'position' => [
                'x' => $this->position_x,
                'y' => $this->position_y,
                'width' => $this->position_width,
                'height' => $this->position_height,
            ],
            'ai_analysis' => [
                'translation' => $this->ai_translation,
                'explanation' => $this->ai_explanation,
                'vocabulary' => $this->ai_vocabulary,
                'grammar' => $this->ai_grammar,
                'idiom_note' => $this->ai_idiom_note,
            ],
            'color' => $this->color,
            'ai_details' => $this->ai_details,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
