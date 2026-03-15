<?php

namespace App\Services;

use App\Models\Template;

class PromptBuilder
{
    /**
     * Build the final prompt string from a template and its matching prompt template.
     */
    public function buildFromTemplate(Template $template): string
    {
        $promptTemplate = $this->resolvePromptTemplate($template);

        if (!$promptTemplate) {
            return '';
        }

        $prompt = $promptTemplate->prompt_template;

        $prompt = $this->replaceStandardVariables($prompt, $template);
        $prompt = $this->replaceDynamicAttributes($prompt, $template);

        return $prompt;
    }

    /**
     * Load the prompt template for the template's platform_id and content_type_id.
     */
    protected function resolvePromptTemplate(Template $template)
    {
        $query = \App\Models\PromptTemplate::query()
            ->where('status', true);

        if ($template->platform_id) {
            $query->where('platform_id', $template->platform_id);
        } else {
            $query->whereNull('platform_id');
        }

        if ($template->content_type_id) {
            $query->where('content_type_id', $template->content_type_id);
        } else {
            $query->whereNull('content_type_id');
        }

        return $query->first();
    }

    /**
     * Replace standard placeholders: content_type, platform, category, goal, tone, audience, style.
     */
    protected function replaceStandardVariables(string $prompt, Template $template): string
    {
        $replacements = [
            '{{content_type}}' => $template->contentType?->name ?? '',
            '{{platform}}'     => $template->platform?->name ?? '',
            '{{category}}'     => $template->category?->name ?? '',
            '{{goal}}'         => $template->contentGoal?->name ?? '',
            '{{tone}}'         => $template->tone?->name ?? '',
            '{{audience}}'     => $template->audience?->name ?? '',
            '{{style}}'        => $template->style?->name ?? '',
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $prompt
        );
    }

    /**
     * Replace dynamic attribute placeholders (e.g. {{image_subject}}, {{image_style}}) from template_attributes.
     */
    protected function replaceDynamicAttributes(string $prompt, Template $template): string
    {
        $template->loadMissing('templateAttributes.attribute');

        foreach ($template->templateAttributes as $templateAttribute) {
            $attribute = $templateAttribute->attribute;
            if (!$attribute) {
                continue;
            }

            $placeholder = '{{' . $attribute->slug . '}}';
            $prompt = str_replace($placeholder, $templateAttribute->value ?? '', $prompt);
        }

        return $prompt;
    }
}
