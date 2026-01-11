<?php

namespace App\Http\Requests;

use App\DTO\UserDTO;
use Support\Contracts\HasDTO;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rules;

class UserStoreRequest extends FormRequest implements HasDTO
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name'=> ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'email'     => ['required','string','email','max:255','unique:'.User::class],
            'password'  => ['required', Rules\Password::defaults()],
            'role_id'   => ['required'],
            'contact_no'=> ['required','numeric', 'digits:10'],
            'dob'       => ['required'],
            'address'   => ['required'],
        ];
    }

    public function DTO()
    {
        return UserDTO::LazyFromArray($this->input());
    }
    
}
