<?php

namespace App\Http\Controllers;

use App\Models\CensoFamilia;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; 

class CensusController extends Controller
{
    /**
     * Muestra la vista del formulario del censo.
     */
    public function index()
    {
        return view('census.index');
    }

    /**
     * Almacena o actualiza el censo completo y su carga familiar en la base de datos.
     */
    public function store(Request $request)
    {
        // Obtenemos el censo actual del usuario (si ya existe) para ignorar su cédula en la regla unique
        $censoExistente = CensoFamilia::where('user_id', auth()->id())->first();
        $censoId = $censoExistente ? $censoExistente->id : 'NULL';

        // Validación general de los campos del formulario con mensajes personalizados en español
        $validatedData = $request->validate([
            'sector_calle' => 'required|string|max:255',
            'numero_vivienda_dir' => 'required|string|max:255',
            'fecha_censo' => 'required|date',
            
            // Jefe de familia (ignorando la cédula del propio usuario al actualizar)
            'jefe_nombre' => 'required|string|max:255',
            'jefe_nacionalidad' => 'required|string|max:1',
            'jefe_cedula' => 'required|string|max:10|unique:censuses,jefe_cedula,' . $censoId,
            'jefe_fecha_nacimiento' => 'required|date',
            'jefe_sexo' => 'required|string',
            'jefe_estado_civil' => 'required|string',
            'jefe_telefono' => 'required|string',
            'jefe_telefono_alt' => 'nullable|string',
            'jefe_telefono' => 'required|digits:11',     
            'jefe_telefono_alt' => 'nullable|digits:11',
            'jefe_instruccion' => 'required|string',
            'jefe_ocupacion' => 'required|string',
            'posee_carnet_patria' => 'required|string',
            'codigo_carnet' => 'nullable|digits:10',
            'serial_carnet' => 'nullable|digits:10',

            // Vivienda y servicios
            'tipo_vivienda' => 'required|string',
            'condicion_juridica' => 'required|string',
            'estado_infraestructura' => 'required|string',
            'material_paredes' => 'required|string',
            'material_techo' => 'required|string',
            'abastecimiento_agua' => 'required|string',
            'aguas_servidas' => 'required|string',
            'acceso_gas' => 'required|string',
            'empresa_gas' => 'required|string',
            'conexion_electrica' => 'required|string',
            'aseo_urbano' => 'required|string',

            // Socioeconómico
            'recibe_clap' => 'required|string',
            'frecuencia_clap' => 'nullable|string',
            'ingreso_familiar' => 'required|string',
            'recibe_remesas' => 'required|string',
            'monto_remesas' => 'nullable|numeric',
            'frecuencia_remesas' => 'nullable|string',
            'dificultad_canasta' => 'required|string',
            'motivo_dificultad_canasta' => 'nullable|string',

            // Salud y vulnerabilidad
            'embarazadas_status' => 'required|string',
            'embarazadas_cantidad' => 'nullable|integer',
            'embarazadas_control' => 'nullable|string',
            'lactantes_status' => 'required|string',
            'lactantes_cantidad' => 'nullable|integer',
            'adultos_status' => 'required|string',
            'adultos_cantidad' => 'nullable|integer',
            'encamados_status' => 'required|string',
            'encamados_cantidad' => 'nullable|integer',
            'enfermedades_cronicas_status' => 'required|string',
            'enfermedades_cronicas_detalle' => 'nullable|string',
            'conapdis' => 'required|string',

            'observaciones' => 'nullable|string',

            // Carga familiar (Array opcional)
            'familiares' => 'nullable|array',
            'familiares.*.nombre' => 'required|string|max:255',
            'familiares.*.parentesco' => 'required|string',
            'familiares.*.sexo' => 'required|string',
            'familiares.*.fecha_nacimiento' => 'required|date',
            'familiares.*.nivel_educativo' => 'required|string',
            'familiares.*.tiene_discapacidad' => 'required|string',
        ], [
            'jefe_cedula.unique' => 'Esta cédula ya ha sido registrada anteriormente.',
            'jefe_cedula.required' => 'La cédula del jefe de familia es obligatoria.',
            'codigo_carnet.digits' => 'El código del carnet de la patria debe tener exactamente 10 dígitos.',
            'serial_carnet.digits' => 'El serial del carnet de la patria debe tener exactamente 10 dígitos.',
        ]);

        // Usamos una transacción para garantizar que se guarde todo o nada
        DB::beginTransaction();
        try {
            // 1. Calcular la edad del jefe de familia automáticamente
            $fechaNacJefe = new \DateTime($request->jefe_fecha_nacimiento);
            $hoy = new \DateTime();
            $edadJefe = $hoy->diff($fechaNacJefe)->y;

            // Datos base de la familia a registrar
            $datosFamilia = $request->except('familiares');
            $datosFamilia['jefe_edad'] = $edadJefe;
            $datosFamilia['user_id'] = auth()->id();

            // 2. Comprobar si actualizamos el existente o creamos uno nuevo
            if ($censoExistente) {
                $censoExistente->update($datosFamilia);
                $censoFamilia = $censoExistente;
                
                // Limpiamos los familiares anteriores para reemplazar con los nuevos actualizados
                $censoFamilia->integrantes()->delete();
            } else {
                $censoFamilia = CensoFamilia::create($datosFamilia);
            }

            // 3. Registrar la carga familiar (nueva o actualizada)
            if ($request->has('familiares') && is_array($request->familiares)) {
                foreach ($request->familiares as $fam) {
                    // Calcular edad de cada integrante
                    $fechaNacFam = new \DateTime($fam['fecha_nacimiento']);
                    $edadFam = $hoy->diff($fechaNacFam)->y;

                    FamilyMember::create([
                        'census_id' => $censoFamilia->id,
                        'nombre' => $fam['nombre'],
                        'es_menor' => isset($fam['es_menor']) ? true : false,
                        'nacionalidad' => $fam['nacionalidad'] ?? null,
                        'cedula' => $fam['cedula'] ?? null,
                        'parentesco' => $fam['parentesco'],
                        'sexo' => $fam['sexo'],
                        'fecha_nacimiento' => $fam['fecha_nacimiento'],
                        'edad' => $edadFam . ' años',
                        'nivel_educativo' => $fam['nivel_educativo'],
                        'ocupacion' => $fam['ocupacion'] ?? null,
                        'tiene_discapacidad' => $fam['tiene_discapacidad'],
                        'discapacidad' => $fam['discapacidad'] ?? null,
                        'otra_discapacidad' => $fam['otra_discapacidad'] ?? null,
                    ]);
                }
            }

            DB::commit();

            // Redirigir al tablero indicando éxito y pasando el ID del censo para autodescarga en pestaña nueva
            return redirect()->route('dashboard')
                             ->with('success', '¡Censo actualizado con éxito!')
                             ->with('pdf_id', $censoFamilia->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ocurrió un error al guardar el censo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Genera el PDF con todas las respuestas del censo.
     */
    public function generarPdf($id = null)
    {
        $user = auth()->user();

        if ($id) {
            // Cargar la familia específica mediante su ID con sus integrantes
            $censo = CensoFamilia::with('integrantes')->findOrFail($id);

            // Permisos: El administrador (is_admin == 1 o rol admin) tiene acceso total. Si no es admin, debe ser dueño del censo.
            $isAdmin = ($user->is_admin ?? 0) == 1 || ($user->role ?? '') === 'admin';

            if (!$isAdmin && $censo->user_id !== $user->id) {
                abort(403, 'No tienes autorización para ver este documento.');
            }
        } else {
            // Si no se pasa ID, buscamos el censo propio del usuario autenticado
            $censo = CensoFamilia::with('integrantes')->where('user_id', $user->id)->firstOrFail();
        }

        // Renderizar la vista PDF con los datos
        $pdf = Pdf::loadView('census.pdf', compact('censo'));

        // Retornar el PDF para visualización en navegador o pestaña nueva
        return $pdf->stream("censo-familiar-{$censo->jefe_cedula}.pdf");
    }
}