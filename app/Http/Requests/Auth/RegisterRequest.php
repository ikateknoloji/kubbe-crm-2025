<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\PasswordRule;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                new PasswordRule,
            ],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role_ids'      => 'required|array',
            'role_ids.*'    => 'exists:roles,id',
        ];
    }

    /**
     * Özel hata mesajları.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'          => 'İsim zorunludur.',
            'name.string'            => 'İsim metin olmalıdır.',
            'name.max'               => 'İsim en fazla 255 karakter olabilir.',
            'email.required'         => 'E-posta zorunludur.',
            'email.string'           => 'E-posta metin olmalıdır.',
            'email.email'            => 'Geçerli bir e-posta adresi giriniz.',
            'email.max'              => 'E-posta en fazla 255 karakter olabilir.',
            'email.unique'           => 'Bu e-posta zaten kullanılıyor.',
            'password.required'      => 'Şifre zorunludur.',
            'password.string'        => 'Şifre metin olmalıdır.',
            'password.min'           => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed'     => 'Şifre onayı eşleşmiyor.',
            'profile_image.image'    => 'Profil fotoğrafı bir resim olmalıdır.',
            'profile_image.mimes'    => 'Profil fotoğrafı jpeg, png, jpg, gif formatlarında olmalıdır.',
            'profile_image.max'      => 'Profil fotoğrafı en fazla 2MB olabilir.',
            'role_ids.required'      => 'En az bir rol seçilmelidir.',
            'role_ids.array'         => 'Rol ID\'leri dizi formatında olmalıdır.',
            'role_ids.*.exists'      => 'Seçilen rol ID\'lerinden biri geçersiz.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Doğrulama Hataları.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
