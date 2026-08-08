<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Muestra el formulario de perfil del usuario.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualiza la información del perfil del usuario.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Obtiene los datos ya validados por ProfileUpdateRequest
        $validatedData = $request->validated();

        // Asigna y guarda explícitamente cada campo (incluyendo 'apellido' y 'phone')
        $user = $request->user();
        $user->name = $validatedData['name'];
        $user->apellido = $validatedData['apellido'];
        $user->phone = $validatedData['phone'];
        $user->email = $validatedData['email'];

        // Si se cambió el correo electrónico, se invalida la verificación del mismo
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Elimina la cuenta del usuario.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}