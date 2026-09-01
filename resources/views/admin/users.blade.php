<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Gestión de Vecinos y Roles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
                <div class="p-6 text-slate-900">
                    <h3 class="text-lg font-bold text-slate-700 mb-4">Usuarios Registrados en la Plataforma</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-slate-500">
                            <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nombre</th>
                                    <th scope="col" class="px-6 py-3">Correo Electrónico</th>
                                    <th scope="col" class="px-6 py-3">Rol Actual</th>
                                    <th scope="col" class="px-6 py-3">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr class="bg-white border-b hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4">{{ $user->email }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $roleClass = 'bg-slate-100 text-slate-800';
                                                $roleName = 'Estándar';
                                                $normalizedRole = strtolower(trim($user->role ?? ''));

                                                if ($normalizedRole === 'admin') {
                                                    $roleClass = 'bg-purple-100 text-purple-800';
                                                    $roleName = 'Administrador';
                                                } elseif ($normalizedRole === 'vocero') {
                                                    $roleClass = 'bg-blue-100 text-blue-800';
                                                    $roleName = 'Vocero';
                                                } elseif ($normalizedRole === 'auditor') {
                                                    $roleClass = 'bg-amber-100 text-amber-800';
                                                    $roleName = 'Auditor';
                                                }
                                            @endphp

                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $roleClass }}">
                                                {{ $roleName }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $user->is_active ? 'Activo' : 'Suspendido' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 flex gap-2 justify-center items-center">
                                            <!-- Formulario Cambiar Rol -->
                                            <form action="{{ route('admin.users.toggleRole', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium text-xs rounded-md border border-indigo-200 transition">
                                                    Cambiar Rol
                                                </button>
                                            </form>

                                            <!-- Formulario Habilitar / Deshabilitar -->
                                            <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-md border transition {{ $user->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }}">
                                                    {{ $user->is_active ? 'Deshabilitar' : 'Habilitar' }}
                                                </button>
                                            </form>

                                            <!-- Formulario Eliminar Usuario -->
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente a este usuario? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 font-medium text-xs rounded-md border border-red-200 transition">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">No hay otros vecinos registrados todavía.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>