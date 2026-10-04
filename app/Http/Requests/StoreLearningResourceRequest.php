<?php

namespace App\Http\Requests;

use App\Models\LearningResource;
use App\Models\Platform;
use App\Support\DemoData\Resources;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLearningResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user() === null) {
            return false;
        }

        $slug = (string) $this->route('resource');

        if ($slug !== '') {
            abort_unless(Resources::find($slug, (string) $this->route('platform')) !== null, 404);
        }

        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = (string) $this->input('type');

        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(array_keys(Resources::formTypes()))],
            'attached_kind' => ['required', 'string', Rule::in(['academy', 'course', 'module', 'lesson'])],
            'attached_key' => ['required', 'string'],
            'source_url' => ['nullable', 'string', 'max:500', 'url'],
            'file' => [
                'nullable',
                'file',
                'max:51200',
                Rule::when(
                    array_key_exists($type, Resources::formTypes()),
                    ['mimes:'.implode(',', Resources::mimesFor($type))],
                ),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Choose a type.',
            'attached_kind.required' => 'Choose where this file sits.',
            'attached_key.required' => 'Choose the academy, course, module or lesson.',
            'file.mimes' => 'That file does not match the type you chose.',
            'source_url.url' => 'Use a YouTube link.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $url = trim((string) $this->input('source_url'));

        if ($url === '') {
            $this->merge(['source_url' => null]);

            return;
        }

        if (! str_contains($url, '://')) {
            $url = 'https://'.$url;
        }

        $this->merge(['source_url' => $url]);
    }

    /**
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $sourceUrl = $this->input('source_url');

                if (filled($sourceUrl) && Resources::youtubeId($sourceUrl) === null) {
                    $validator->errors()->add('source_url', 'Use a YouTube link.');
                }

                if ($validator->errors()->hasAny(['attached_kind', 'attached_key'])) {
                    return;
                }

                $platform = $this->platform();

                if ($platform === null) {
                    return;
                }

                $target = LearningResource::findTarget(
                    $platform,
                    (string) $this->input('attached_kind'),
                    (string) $this->input('attached_key'),
                );

                if ($target === null) {
                    $validator->errors()->add('attached_key', 'That target is not on this academy.');
                }
            },
        ];
    }

    public function platform(): ?Platform
    {
        $slug = (string) $this->route('platform');

        if ($slug === '') {
            return null;
        }

        return Platform::query()->where('slug', $slug)->first();
    }
}
