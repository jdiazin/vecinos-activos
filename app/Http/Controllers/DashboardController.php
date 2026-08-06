<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\CensoFamilia;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = strtolower(trim($user->role ?? ''));

        // 1. Si es administrador, vocero o auditor, ve el panel principal de gestión
        if (isset($user->is_admin) && $user->is_admin) {
            return view('admin.dashboard');
        }

        if (in_array($role, ['admin', 'vocero', 'auditor'])) {
            return view('admin.dashboard');
        }

        // 2. Si es un vecino regular, cargamos sus reportes y su censo asociado
        $misReportes = Report::where('user_id', $user->id)
            ->latest()
            ->get();

        $census = CensoFamilia::with('integrantes')
            ->where('user_id', $user->id)
            ->first();

        return view('dashboard', compact('misReportes', 'census'));
    }
}