<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Postulacion;
use Illuminate\Support\Facades\DB;
use App\Models\CensoFamilia;
use App\Models\Setting;
use App\Models\FamilyMember;

class AdminController extends Controller
{
    // Muestra el panel con la lista de usuarios
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->orderBy('name', 'asc')->get();
        return view('admin.users', compact('users'));
    }

    // Cambia el rol de un usuario de forma cíclica (estándar -> vocero -> admin -> auditor -> estándar)
    public function toggleRole(User $user)
    {
        $currentRole = strtolower(trim($user->role ?? 'estandar'));

        if ($currentRole === 'estandar' || $currentRole === 'user' || empty($currentRole)) {
            $newRole = 'vocero';
        } elseif ($currentRole === 'vocero') {
            $newRole = 'admin';
        } elseif ($currentRole === 'admin') {
            $newRole = 'auditor'; 
        } else {
            $newRole = 'estandar';
        }
        
        // Guardamos directamente en la columna role
        $user->role = $newRole;
        $user->save();

        return redirect()->back()->with('success', "Rol de {$user->name} actualizado a " . ucfirst($newRole) . " con éxito.");
    }

    // Habilita o deshabilita a un vecino
    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'habilitado' : 'deshabilitado';
        return redirect()->back()->with('success', "El usuario {$user->name} ha sido {$status}.");
    }

    // Elimina permanentemente a un usuario (Registrado con detalles específicos en la auditoría)
    public function destroyUser(User $user)
    {
        // Evitar que el administrador elimine su propia cuenta de forma accidental
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        
        $user->delete();

        // Flash para que el middleware de auditoría capture exactamente a quién afectó/eliminó
        session()->flash('audit_description', "El administrador eliminó al usuario: {$userName} ({$userEmail}).");

        return redirect()->back()->with('success', "El usuario {$userName} ha sido eliminado exitosamente.");
    }

    public function verPostulaciones()
    {
        $postulaciones = Postulacion::withCount('votos')->latest()->get();
        return view('admin.postulaciones', compact('postulaciones'));
    }

    // Método para resultados de votación
    public function resultadosVotacion()
    {
        $resultados = DB::table('votos')
            ->join('postulacions', 'votos.postulado_id', '=', 'postulacions.id')
            ->select(
                'votos.voceria_name', 
                'postulacions.nombre', 
                DB::raw('count(votos.id) as total_votos')
            )
            ->groupBy('votos.voceria_name', 'postulacions.nombre')
            ->orderBy('votos.voceria_name')
            ->get();

        return view('admin.resultados', compact('resultados'));
    }

    // Método para ver la lista y estadísticas de censos comunitarios
    public function verCensos()
    {
        $censos = CensoFamilia::with(['user', 'integrantes'])->latest()->get();
        
        // Métricas generales e indicadores cuantitativos
        $totalCensos = $censos->count();
        $totalFamiliasMiembros = $censos->sum(fn($c) => $c->integrantes->count());
        $totalPoblacion = $totalCensos + $totalFamiliasMiembros; // Jefes de hogar + carga familiar

        // Vulnerabilidad social
        $vulnerabilidadEmbarazadas = CensoFamilia::where('embarazadas_status', 'Si')->count();
        $vulnerabilidadEncamados = CensoFamilia::where('encamados_status', 'Si')->count();
        
        // Agrupaciones seguras procesadas en memoria a partir de la colección
        $tiposVivienda = $censos->groupBy(fn($item) => $item->tipo_vivienda ?: 'No especificado')->map->count();
        $ingresosFamiliar = $censos->groupBy(fn($item) => $item->ingreso_familiar ?: 'No especificado')->map->count();
        $abastecimientoAgua = $censos->groupBy(fn($item) => $item->abastecimiento_agua ?: 'No especificado')->map->count();
        $recibeClap = $censos->groupBy(fn($item) => $item->recibe_clap ?: 'No especificado')->map->count();
        
        // Agrupaciones corregidas con los nombres exactos de tus columnas en la base de datos
        $tipoParedes = $censos->groupBy(fn($item) => $item->material_paredes ?: 'No especificado')->map->count();
        $tipoTechos = $censos->groupBy(fn($item) => $item->material_techo ?: 'No especificado')->map->count();
        $servicioGas = $censos->groupBy(fn($item) => $item->acceso_gas ?: 'No especificado')->map->count();

        return view('admin.censos', compact(
            'censos', 
            'totalCensos', 
            'totalFamiliasMiembros', 
            'totalPoblacion',
            'vulnerabilidadEmbarazadas',
            'vulnerabilidadEncamados',
            'tiposVivienda', 
            'ingresosFamiliar',
            'abastecimientoAgua',
            'recibeClap',
            'tipoParedes',
            'tipoTechos',
            'servicioGas'
        ));
    }

    // Método para exportar todos los censos a CSV (compatible con Excel) con datos completos
    public function exportarCensosExcel()
    {
        $censos = CensoFamilia::with(['user', 'integrantes'])->latest()->get();

        $filename = "reporte-general-censos.csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($censos) {
            $file = fopen('php://output', 'w');
            // BOM para soporte correcto de tildes y caracteres especiales en Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados completos de todas las columnas relevantes del censo
            fputcsv($file, [
                'ID Censo', 'Registrado por (Usuario)', 'Email Usuario', 'Sector / Calle', 'Nro Vivienda', 
                'Fecha Censo', 'Jefe Nombre', 'Jefe Nacionalidad', 'Jefe Cédula', 'Jefe Edad', 
                'Jefe Sexo', 'Jefe Estado Civil', 'Jefe Teléfono', 'Jefe Ocupación', 
                'Carnet de la Patria', 'Código Carnet', 'Serial Carnet',
                'Tipo Vivienda', 'Condición Jurídica', 'Estado Infraestructura', 
                'Material Paredes', 'Material Techo', 'Abastecimiento Agua', 
                'Aguas Servidas', 'Acceso Gas', 'Empresa Gas', 'Conexión Eléctrica', 
                'Aseo Urbano', 'Recibe CLAP', 'Frecuencia CLAP', 
                'Ingreso Familiar', 'Recibe Remesas', 'Monto Remesas', 'Dificultad Canasta', 
                'Embarazadas Status', 'Embarazadas Cantidad', 'Lactantes Status', 'Lactantes Cantidad', 
                'Adultos Mayores Status', 'Adultos Mayores Cantidad', 'Encamados Status', 'Encamados Cantidad', 
                'Enfermedades Crónicas', 'Total Miembros Familiares', 'Fecha de Creación'
            ]);

            foreach ($censos as $census) {
                fputcsv($file, [
                    $census->id,
                    $census->user->name ?? 'N/D',
                    $census->user->email ?? 'N/D',
                    $census->sector_calle ?? 'N/D',
                    $census->numero_vivienda_dir ?? 'N/D',
                    $census->fecha_censo ?? 'N/D',
                    $census->jefe_nombre ?? 'N/D',
                    $census->jefe_nacionalidad ?? 'N/D',
                    $census->jefe_cedula ?? 'N/D',
                    $census->jefe_edad ?? 'N/D',
                    $census->jefe_sexo ?? 'N/D',
                    $census->jefe_estado_civil ?? 'N/D',
                    $census->jefe_telefono ?? 'N/D',
                    $census->jefe_ocupacion ?? 'N/D',
                    $census->posee_carnet_patria ?? 'N/D',
                    $census->codigo_carnet ?? 'N/D',
                    $census->serial_carnet ?? 'N/D',
                    $census->tipo_vivienda ?? 'N/D',
                    $census->condicion_juridica ?? 'N/D',
                    $census->estado_infraestructura ?? 'N/D',
                    $census->material_paredes ?? 'N/D',
                    $census->material_techo ?? 'N/D',
                    $census->abastecimiento_agua ?? 'N/D',
                    $census->aguas_servidas ?? 'N/D',
                    $census->acceso_gas ?? 'N/D',
                    $census->empresa_gas ?? 'N/D',
                    $census->conexion_electrica ?? 'N/D',
                    $census->aseo_urbano ?? 'N/D',
                    $census->recibe_clap ?? 'N/D',
                    $census->frecuencia_clap ?? 'N/D',
                    $census->ingreso_familiar ?? 'N/D',
                    $census->recibe_remesas ?? 'N/D',
                    $census->monto_remesas ?? 'N/D',
                    $census->dificultad_canasta ?? 'N/D',
                    $census->embarazadas_status ?? 'N/D',
                    $census->embarazadas_cantidad ?? 0,
                    $census->lactantes_status ?? 'N/D',
                    $census->lactantes_cantidad ?? 0,
                    $census->adultos_status ?? 'N/D',
                    $census->adultos_cantidad ?? 0,
                    $census->encamados_status ?? 'N/D',
                    $census->encamados_cantidad ?? 0,
                    $census->enfermedades_cronicas_status ?? 'N/D',
                    $census->integrantes->count(),
                    $census->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Método para mostrar la vista de configuración en el admin
    public function settingsIndex()
    {
        $votarActivo = Setting::firstOrCreate(['key' => 'votar_activo'], ['value' => true])->value;
        $postularActivo = Setting::firstOrCreate(['key' => 'postular_activo'], ['value' => true])->value;

        return view('admin.settings', compact('votarActivo', 'postularActivo'));
    }

    // Método para alternar el estado
    public function toggleSetting(Request $request, $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        $setting->value = !$setting->value;
        $setting->save();

        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    }
}