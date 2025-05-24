<?php

namespace App\Http\Requests;

use App\Enums\PostStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Platform;

class StorePostRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('scheduled_time') ? PostStatusEnum::SCHEDULED->value : PostStatusEnum::DRAFT->value
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'scheduled_time' => ['nullable', 'date', 'after_or_equal:now'],
            'platforms' => ['required', 'array'],
            'platforms.*' => ['required', 'integer', 'exists:platforms,id'],
            'status' => ['required', 'in:' . implode(',', PostStatusEnum::values())],
        ];
    }

    public function withValidator($validator)
    {
        if (!$validator->fails()) {
            $validator->after(function ($validator) {
                $this->validatePlatformSpecificRules($validator);
            });
        }
    }

    protected function validatePlatformSpecificRules($validator)
    {
        $platformIds = $this->input('platforms', []);
        $content = $this->input('content');

        $platforms = Platform::findMany($platformIds);

        foreach ($platforms as $platform) {
            if (strlen($content) > $platform->character_limit) {
                $validator->errors()->add(
                    'content',
                    "Content exceeds {$platform->name} character limit ({$platform->character_limit} characters)."
                );
            }
            if ($platform->is_image_required && empty($this->input('image_url'))) {
                $validator->errors()->add(
                    'image',
                    "An image is required for {$platform->name} posts."
                );
            }
        }
    }
}
