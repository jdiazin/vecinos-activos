<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Postulacion;
use App\Models\Event; 
use Illuminate\Http\Request;

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
    
    public function resolve($id)
    {
        $report = Report::findOrFail($id);
        $report->update(['status' => 'resuelto']);
        
        return back()->with('success', 'Reporte finalizado.');
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