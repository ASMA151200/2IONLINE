<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLiveSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'youtube_video_id' => ['sometimes', 'string', 'max:50'],
            'scheduled_at' => ['sometimes', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'status' => ['sometimes', 'in:scheduled,live,completed,cancelled'],
            'formation_id' => ['sometimes', 'exists:formations,id'],
        ];
    }
}
