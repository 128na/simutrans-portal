<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UserStoreRequest extends FormRequest
{
    /**
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
        ];
    }
}
