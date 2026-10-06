<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\DemoData\People;
use App\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateAdminProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::SuperAdmin;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()?->id),
            ],
            'district' => ['required', 'string', Rule::in(People::districts())],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->exists('email')) {
            $this->merge([
                'email' => $this->string('email')->trim()->lower()->toString(),
            ]);
        }

        if ($this->exists('name')) {
            $this->merge([
                'name' => $this->string('name')->trim()->toString(),
            ]);
        }
    }
}
