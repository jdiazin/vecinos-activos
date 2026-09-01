<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordRequestController extends Controller
{
    // Muestra la vista pública para que el usuario pida ayuda con sus credenciales
    public function create()
    {
        return view('auth.forgot-request');
    }

    // Guarda la solicitud enviada por el vecino desde el login
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'motivo' => 'required|string|max:500',
        ]);

        PasswordRequest::create([
            'email' => $request->email,
            'cedula' => $request->cedula,
            'motivo' => $request->motivo,
            'status' => 'pendiente'
        ]);

        return redirect()->route('login')->with('success', 'Solicitud enviada con éxito. Un administrador la revisará pronto.');
    }

    // Panel del Admin: Muestra la lista de solicitudes de recuperación
    public function index()
    {
        $solicitudes = PasswordRequest::latest()->get();
        return view('admin.password-requests', compact('solicitudes'));
    }

    // Admin: Atiende la solicitud, actualiza el correo/contraseña del usuario y marca como atendido
    public function update(Request $request, PasswordRequest $passwordRequest)
    {
        $request->validate([
            'new_email' => 'required|email',
            'new_password' => 'required|min:6',
        ]);

        // Buscar si el usuario existe por el correo original de la solicitud
        $user = User::where('email', $passwordRequest->email)->first();

        if ($user) {
            $user->email = $request->new_email;
            $user->password = Hash::make($request->new_password);
            $user->save();
        }

        $passwordRequest->update(['status' => 'atendido']);

        // Registrar detalles específicos en los logs de auditoría
        session()->flash('audit_description', "El administrador atendió la solicitud de recuperación y actualizó las credenciales para el correo: {$passwordRequest->email}.");

        return redirect()->back()->with('success', 'Credenciales actualizadas y solicitud marcada como atendida.');
    }
}