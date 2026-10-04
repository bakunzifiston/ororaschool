<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;
use App\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateAdminUserRequest extends FormRequest
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
        $account = $this->route('user');
        $accountId = $account instanceof User ? $account->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($accountId)],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'district' => ['required', 'string', Rule::in(People::districts())],
            'role' => ['required', Rule::enum(UserRole::class)],
            'status' => ['required', 'string', Rule::in(['draft', 'active', 'pending_review', 'archived'])],
            'platforms' => $this->string('role')->toString() === UserRole::PlatformStaff->value
                ? ['required', 'array', 'min:1']
                : ['nullable', 'array'],
            'platforms.*' => ['string', Rule::in(Platforms::slugs())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'platforms.required' => 'Choose at least one academy dashboard this person can open.',
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
