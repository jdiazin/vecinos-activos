<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyOption;
use App\Models\SurveyVote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EncuestaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra la lista de encuestas disponibles según el rol.
     */
    public function index()
    {
        $surveys = Survey::with(['creator', 'options'])->orderBy('created_at', 'desc')->get();
        return view('encuestas.index', compact('surveys'));
    }

    /**
     * Muestra el formulario para crear una encuesta (Solo Admin y Voceros).
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('vocero') && $user->role !== 'admin' && $user->role !== 'vocero') {
            abort(403, 'No autorizado para crear encuestas.');
        }

        return view('encuestas.create');
    }

    /**
     * Almacena una nueva encuesta y sus opciones.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('vocero') && $user->role !== 'admin' && $user->role !== 'vocero') {
            abort(403, 'No autorizado para almacenar encuestas.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255'
        ]);

        DB::transaction(function () use ($request, $user) {
            $survey = Survey::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'activo',
                'created_by' => $user->id,
            ]);

            foreach ($request->options as $optionText) {
                SurveyOption::create([
                    'survey_id' => $survey->id,
                    'option_text' => $optionText,
                ]);
            }
        });

        return redirect()->route('encuestas.index')->with('success', 'Encuesta creada exitosamente.');
    }

    /**
     * Permite registrar el voto del usuario estándar.
     */
    public function vote(Request $request, $id)
    {
        $request->validate([
            'option_id' => 'required|exists:survey_options,id'
        ]);

        $survey = Survey::findOrFail($id);

        // Validar si la encuesta está activa y dentro de las fechas
        if ($survey->status !== 'activo' || now()->isAfter($survey->end_date)) {
            return back()->with('error', 'Esta encuesta ya no está disponible para votar.');
        }

        // Verificar si el usuario ya votó
        $existingVote = SurveyVote::where('survey_id', $id)
                                  ->where('user_id', Auth::id())
                                  ->first();

        if ($existingVote) {
            return back()->with('error', 'Usted ya ha emitido su voto en esta encuesta.');
        }

        // Registrar el voto
        SurveyVote::create([
            'survey_id' => $id,
            'option_id' => $request->option_id,
            'user_id' => Auth::id(),
            'voted_at' => now(),
        ]);

        return redirect()->route('encuestas.index')->with('success', 'Su voto ha sido registrado correctamente.');
    }

    /**
     * Muestra los resultados de la encuesta (Disponible para Auditores, Admins y Voceros).
     */
    public function results($id)
    {
        $survey = Survey::with(['options.votes', 'creator'])->findOrFail($id);
        
        return view('encuestas.results', compact('survey'));
    }
}