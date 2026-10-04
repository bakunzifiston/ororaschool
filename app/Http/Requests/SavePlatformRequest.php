<?php

namespace App\Http\Requests;

use App\Models\Platform;
use App\Models\User;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;
use App\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SavePlatformRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::SuperAdmin;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isEdit = $this->route('platform') !== null;
        $fixtureSlugs = array_column(Platforms::all(), 'slug');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => $isEdit
                ? ['nullable', 'string', 'max:64']
                : [
                    'required',
                    'string',
                    'max:64',
                    'alpha_dash',
                    'lowercase',
                    Rule::unique(Platform::class, 'slug'),
                    Rule::notIn($fixtureSlugs),
                ],
            'description' => ['nullable', 'string', 'max:2000'],
            'active' => ['sometimes', 'boolean'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => ['nullable', 'string', 'email', 'max:255'],
            'admin_password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'admin_district' => ['nullable', 'string', Rule::in(People::districts())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.not_in' => 'That academy already exists on the estate.',
            'slug.unique' => 'That academy already exists on the estate.',
        ];
    }

    /**
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $email = $this->string('admin_email')->trim()->lower()->toString();
                $started = filled($this->input('admin_name'))
                    || filled($this->input('admin_password'))
                    || filled($this->input('admin_district'));

                if ($email === '') {
                    if ($started) {
                        $validator->errors()->add('admin_email', 'Enter the academy admin’s email.');
                    }

                    return;
                }

                $existing = User::query()->where('email', $email)->first();

                if ($existing === null) {
                    if (blank($this->input('admin_name'))) {
                        $validator->errors()->add('admin_name', 'Enter the academy admin’s name.');
                    }

                    if (blank($this->input('admin_password'))) {
                        $validator->errors()->add('admin_password', 'Set a password for the academy admin.');
                    }

                    if (blank($this->input('admin_district'))) {
                        $validator->errors()->add('admin_district', 'Choose a district for the academy admin.');
                    }

                    return;
                }

                if ($existing->role !== UserRole::PlatformStaff) {
                    $validator->errors()->add(
                        'admin_email',
                        'That account is not academy staff, so it cannot be assigned as this academy’s admin.',
                    );
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [
            'active' => $this->boolean('active'),
        ];

        if ($this->exists('slug')) {
            $merge['slug'] = Str::slug($this->string('slug')->toString());
        }

        if ($this->exists('admin_email')) {
            $merge['admin_email'] = $this->string('admin_email')->trim()->lower()->toString();
        }

        $this->merge($merge);
    }
}
