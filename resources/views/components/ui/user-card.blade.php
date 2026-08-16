@props([
    'name',
    'email',
    'avatar' => null,
    'role' => 'Colaborador',
    'department' => null,
    'employeeNumber' => null,
    'status' => 'active',
    'actions' => [],
])

@php
    $statusClasses = [
        'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'inactive' => 'bg-slate-100 text-slate-600 border-slate-200',
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
    ][$status] ?? 'bg-slate-100 text-slate-600 border-slate-200';

    $statusLabel = [
        'active' => 'Activo',
        'inactive' => 'Inactivo',
        'pending' => 'Pendiente',
    ][$status] ?? ucfirst($status);
@endphp

<div {{ $attributes->merge(['class' => 'relative bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4']) }}>
    <!-- Información de Usuario -->
    <div class="flex items-center gap-4 w-full sm:w-auto">
        <!-- Avatar / Iniciales -->
        <div class="relative shrink-0">
            @if ($avatar)
                <img src="{{ $avatar }}" alt="{{ $name }}" class="w-12 h-12 rounded-xl object-cover ring-2 ring-indigo-50 border border-slate-200" />
            @else
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-lg flex items-center justify-center shadow-sm">
                    {{ strtoupper(substr($name, 0, 2)) }}
                </div>
            @endif
            <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white {{ $status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
        </div>

        <!-- Detalles del Colaborador -->
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <h4 class="text-base font-bold text-slate-900 truncate">{{ $name }}</h4>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                    {{ $statusLabel }}
                </span>
            </div>
            <p class="text-xs text-slate-500 truncate mt-0.5">{{ $email }}</p>
            @if ($department || $employeeNumber)
                <div class="flex items-center gap-2 text-xs text-slate-400 mt-1">
                    @if ($employeeNumber)
                        <span class="font-mono bg-slate-50 px-1.5 py-0.5 rounded text-slate-600 border border-slate-100">Nº {{ $employeeNumber }}</span>
                    @endif
                    @if ($department)
                        <span>&bull; {{ $department }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Dropdown de Acciones (Alpine.js) -->
    <div x-data="{ open: false }" @click.outside="open = false" class="relative self-end sm:self-center">
        <button @click="open = !open" type="button"
            class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-colors"
            aria-haspopup="true" :aria-expanded="open">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
        </button>

        <!-- Menú Dropdown -->
        <div x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 z-20 mt-2 w-48 rounded-2xl bg-white p-1.5 shadow-lg border border-slate-100 focus:outline-none"
            style="display: none;">
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @else
                <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 rounded-xl transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Ver detalles
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 rounded-xl transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Editar usuario
                </a>
            @endif
        </div>
    </div>
</div>
