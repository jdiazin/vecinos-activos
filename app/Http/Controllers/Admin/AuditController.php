<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query();

        // Filtro por búsqueda de texto (usuario, evento, descripción o IP)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Filtro por componente
        if ($request->filled('component')) {
            $query->where('component', $request->input('component'));
        }

        $logs = $query->latest()->paginate(25)->withQueryString();
        $components = AuditLog::select('component')->distinct()->pluck('component');

        return view('admin.audits.index', compact('logs', 'components'));
    }

    public function export(Request $request)
    {
        $fileName = 'auditoria_sistema_' . date('Y-m-d_H-i-s') . '.csv';
        $logs = AuditLog::latest()->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Hora', 'Usuario', 'Contexto', 'Componente', 'Evento', 'Descripción', 'Origen', 'IP'];

        $callback = function() use($logs, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8 en Excel
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('d-m-Y H:i:s'),
                    $log->user_name,
                    $log->event_context,
                    $log->component,
                    $log->event_name,
                    $log->description,
                    $log->origin,
                    $log->ip_address
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}