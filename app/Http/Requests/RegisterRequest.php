<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\Rwanda;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', Rule::in(Rwanda::districts())],
            'sector' => ['required', 'string', Rule::in(Rwanda::sectorsIn($this->string('district')->toString()))],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', Password::defaults()],
        ];
    }

    public function fullName(): string
    {
        return trim($this->string('first_name')->toString().' '.$this->string('last_name')->toString());
    }

    protected function prepareForValidation(): void
    {
        $merged = [];

        if ($this->exists('email')) {
            $merged['email'] = $this->string('email')->trim()->lower()->toString();
        }

        foreach (['first_name', 'last_name'] as $field) {
            if ($this->exists($field)) {
                $merged[$field] = $this->string($field)->trim()->toString();
            }
        }

        if ($merged !== []) {
            $this->merge($merged);
        }
    }
}
