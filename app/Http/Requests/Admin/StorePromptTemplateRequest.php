<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePromptTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'platform_id' => 'nullable|exists:platforms,id',
            'content_type_id' => 'nullable|exists:content_types,id',
            'prompt_template' => 'required|string',
            'variables' => 'nullable|array',
            'output_schema' => 'nullable|array',
            'model_preferences' => 'nullable|array',
            'scope' => 'required|in:system,user',
            'role' => 'required|in:system,user,assistant',
            'version' => 'nullable|integer|min:1',
            'is_default' => 'boolean',
            'status' => 'boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_default' => $this->boolean('is_default'),
            'status' => $this->boolean('status'),
            'variables' => is_string($this->variables) ? json_decode($this->variables, true) : $this->variables,
            'output_schema' => is_string($this->output_schema) ? json_decode($this->output_schema, true) : $this->output_schema,
            'model_preferences' => is_string($this->model_preferences) ? json_decode($this->model_preferences, true) : $this->model_preferences,
        ]);
    }
}
