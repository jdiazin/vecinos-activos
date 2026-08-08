<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'apellido' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/'],
            'phone' => ['required', 'string', 'digits:11'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.regex' => 'El formato del campo nombre es inválido.',
            'apellido.required' => 'El campo apellido es obligatorio.',
            'apellido.regex' => 'El formato del campo apellido es inválido.',
            'phone.required' => 'El campo teléfono es obligatorio.',
            'phone.digits' => 'El teléfono debe tener exactamente 11 dígitos.',
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar una dirección de correo electrónico válida.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
        ];
    }
}