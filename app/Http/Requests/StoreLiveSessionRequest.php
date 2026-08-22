<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLiveSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'youtube_video_id' => ['required', 'string', 'max:50'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:scheduled,live,completed,cancelled'],
            'formation_id' => ['required', 'exists:formations,id'],
        ];
    }
}
