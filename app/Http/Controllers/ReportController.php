<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Postulacion;
use App\Models\Event; 
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::where('status', 'pendiente')->latest()->get();
        $postulaciones = Postulacion::latest()->take(5)->get();
        
        // Obtiene los próximos eventos usando event_date (coincidiendo con EventController)
        $eventos = Event::where('event_date', '>=', now())
                        ->orderBy('event_date', 'asc')
                        ->get();
        
        // Pasa $eventos a la vista con compact
        return view('welcome', compact('reports', 'postulaciones', 'eventos'));
    }

    // Método para mostrar la vista dedicada de gestión/listado de reportes
    public function gestionIndex()
    {
        $reports = Report::latest()->get();
        return view('reports.index', compact('reports'));
    }
    
    public function resolve(Request $request, $id)
    {
        // 1. Validar que se obligatoriamente la evidencia y las notas de solución
        $request->validate([
            'evidence'       => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'solution_notes' => 'required|string|max:1000',
        ]);

        $report = Report::findOrFail($id);
        
        // 2. Almacenar el archivo de evidencia en el disco público
        $path = $request->file('evidence')->store('solutions_evidence', 'public');

        // 3. Actualizar el estado y los datos de cierre del reporte
        $report->update([
            'status'         => 'resuelto',
            'evidence_path'  => $path,
            'solution_notes' => $request->solution_notes,
            'resolved_by'    => auth()->id(),
            'resolved_at'    => now(),
        ]);

        // 4. Registrar la acción en la tabla de auditoría usando tu estructura existente
        $user = auth()->user();
        AuditLog::create([
            'user_id'       => $user->id,
            'user_name'     => $user->name,
            'event_context' => 'Gestión de Incidencias',
            'component'     => 'Reportes',
            'event_name'    => 'Resolución de Reporte',
            'description'   => "El usuario {$user->name} marcó como resuelto el reporte #{$report->id} ubicado en '{$report->location}'.",
            'origin'        => 'Web',
            'ip_address'    => request()->ip(),
        ]);

        return back()->with('success', '¡Reporte finalizado y evidencia registrada correctamente!');
    }

    public function store(Request $request)
    {
        // 0. Bloquear si el usuario es auditor
        $userRole = strtolower(trim(auth()->user()->role ?? ''));
        if ($userRole === 'auditor') {
            return redirect()->back()->with('error', 'Los auditores tienen acceso de solo lectura y no pueden crear reportes.');
        }

        $validated = $request->validate([
            'issue_type'  => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Report::create([
            'user_id'     => auth()->id(),
            'issue_type'  => $validated['issue_type'],
            'location'    => $validated['location'],
            'description' => $validated['description'],
            'status'      => 'pendiente',
        ]);

        return redirect()->back()->with('success', '¡Reporte enviado exitosamente!');
    }
}