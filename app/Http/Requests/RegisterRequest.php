<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\DemoData\People;
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
            'name' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', Rule::in(People::districts())],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', Password::defaults()],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->exists('email')) {
            $this->merge([
                'email' => $this->string('email')->trim()->lower()->toString(),
            ]);
        }
    }
}
