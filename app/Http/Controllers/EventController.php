<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Muestra los eventos activos (cuya fecha sea hoy o futura).
     */
    public function index()
    {
        // Filtra para que desaparezcan automáticamente los eventos pasados
        $events = Event::where('event_date', '>=', now())
                       ->orderBy('event_date', 'asc')
                       ->get();

        return view('events.index', compact('events'));
    }

    /**
     * Almacena un nuevo evento (Permitido para Admin y Vocero).
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'vocero'])) {
            abort(403, 'No autorizado.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'event_date'  => 'required|date',
            'location'    => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        Event::create([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'event_date'  => $validated['event_date'],
            'location'    => $validated['location'],
            'image_path'  => $imagePath,
        ]);

        return redirect()->back()->with('success', '¡Evento comunitario publicado con éxito!');
    }

    /**
     * Actualiza un evento existente (Permitido para Admin y Vocero).
     */
    public function update(Request $request, Event $event)
    {
        if (!in_array(auth()->user()->role, ['admin', 'vocero'])) {
            abort(403, 'No autorizado.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'event_date'  => 'required|date',
            'location'    => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Borramos la imagen anterior si existía para no acumular basura en el servidor
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $event->image_path = $request->file('image')->store('events', 'public');
        }

        $event->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'event_date'  => $validated['event_date'],
            'location'    => $validated['location'],
            'image_path'  => $event->image_path,
        ]);

        return redirect()->back()->with('success', '¡Evento actualizado correctamente!');
    }

    /**
     * Elimina un evento de la base de datos (Permitido para Admin y Vocero).
     */
    public function destroy(Event $event)
    {
        if (!in_array(auth()->user()->role, ['admin', 'vocero'])) {
            abort(403, 'No autorizado.');
        }

        // Borramos su imagen del disco
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return redirect()->back()->with('success', '¡El evento ha sido eliminado de la cartelera!');
    }
}