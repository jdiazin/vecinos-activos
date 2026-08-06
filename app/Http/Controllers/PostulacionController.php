<?php

namespace App\Http\Controllers;

use App\Models\Postulacion;
use Illuminate\Http\Request;
use App\Models\Voto;

class PostulacionController extends Controller
{
    public function store(Request $request)
    {
        // 0. Bloquear si el usuario es auditor
        $userRole = strtolower(trim(auth()->user()->role ?? ''));
        if ($userRole === 'auditor') {
            return redirect()->back()->with('error', 'Los auditores tienen acceso de solo lectura y no pueden realizar postulaciones.');
        }

        // 1. Validar que el usuario no se haya postulado previamente
        $yaPostulado = Postulacion::where('user_id', auth()->id())->exists();

        if ($yaPostulado) {
            return redirect()->back()->with('error', 'Ya te has postulado a una vocería. Solo se permite una postulación por vecino.');
        }

        // 2. Validar que los datos vengan correctos
        $validated = $request->validate([
            'voceria'   => 'required|string',
            'propuesta' => 'required|string',
        ]);

        // 3. Guardar en la base de datos usando el modelo
        Postulacion::create([
            'user_id'   => auth()->id(),
            'nombre'    => auth()->user()->name,
            'voceria'   => $validated['voceria'],
            'propuesta' => $validated['propuesta'],
        ]);

        // 4. Volver a la página anterior con un mensaje
        return redirect()->back()->with('success', '¡Tu postulación ha sido enviada con éxito!');
    }

    public function indexVotaciones()
    {
        $postulaciones = Postulacion::all();
        return view('votaciones', compact('postulaciones'));
    }

    public function votar($id)
    {
        // 0. Bloquear si el usuario es auditor
        $userRole = strtolower(trim(auth()->user()->role ?? ''));
        if ($userRole === 'auditor') {
            return redirect()->back()->with('error', 'Los auditores tienen acceso de solo lectura y no pueden emitir votos.');
        }

        // Verifica si el usuario ya votó
        $yaVoto = Voto::where('user_id', auth()->id())->exists();
        
        if ($yaVoto) {
            return back()->with('error', 'Ya has emitido tu voto anteriormente.');
        }

        Voto::create([
            'user_id' => auth()->id(),
            'postulacion_id' => $id
        ]);

        return back()->with('success', '¡Gracias por participar en la elección!');
    }
}