<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Postulacion; 
use App\Models\Voto;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class VotacionController extends Controller
{
    /**
     * Instancia el controlador y restringe el acceso solo a usuarios autenticados.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $postuladosPorVoceria = Postulacion::all()->groupBy('voceria');
        return view('votaciones.votar', compact('postuladosPorVoceria'));
    }

    public function store(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            if (strpos($key, 'voceria_') === 0 && !empty($value) && is_numeric($value)) {
                
                // Normalizamos el nombre de la vocería a slug antes de guardar
                $voceriaSlug = Str::slug(str_replace('voceria_', '', $key));

                try {
                    Voto::create([
                        'user_id'      => auth()->id(),
                        'postulado_id' => $value,
                        'voceria_name' => $voceriaSlug 
                    ]);
                } catch (QueryException $e) {
                    continue; // Ignoramos si ya existe el voto
                }
            }
        }

        return back()->with('success', '¡Tus votos han sido registrados correctamente!');
    }
}