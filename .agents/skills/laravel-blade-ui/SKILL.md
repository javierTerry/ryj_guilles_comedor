---
name: laravel-blade-ui
description: Diseña y estructura componentes reutilizables en Laravel Blade con Tailwind CSS y JavaScript modular (o Alpine.js), asegurando accesibilidad, diseño responsivo y atributos dinámicos.
---

# Generador de Componentes UI con Blade y Tailwind CSS

## Objetivo

Construir componentes de interfaz modulares, accesibles y altamente reutilizables utilizando Laravel Blade Components, clases utilitarias de Tailwind CSS y JavaScript sin dependencias pesadas.

---

## 1. Estándares de Componentes Blade

- **Ubicación:** Componentes en `resources/views/components/` organizados por dominio (ej. `ui/`, `forms/`, `layouts/`).
- **Manejo de Atributos:** Utiliza `$attributes->twMerge()` o `$attributes->class([...])` para permitir clases personalizadas sin romper los estilos base.
- **Props Explícitas:** Declara todas las propiedades mediante la directiva `@props([...])`.
- **Slots:** Define slots con nombre (`<x-slot:title>`) cuando el componente requiera múltiples secciones de contenido.

---

## 2. Estándares de Tailwind CSS

- **Diseño Mobile-First:** Diseña primero la vista móvil y escala con prefijos (`sm:`, `md:`, `lg:`).
- **Semántica y Accesibilidad:** Usa colores consistentes con modo oscuro si aplica (`dark:`), estados interactivos claros (`hover:`, `focus-visible:`, `active:`, `disabled:`).
- **Evitar CSS inline:** Toda la estilización debe resolverse con clases utilitarias de Tailwind o `@apply` en archivos CSS dedicados si se repite en exceso.

---

## 3. Estándares de JavaScript

- **Modularidad:** Si se requiere interactividad local, prioriza **Alpine.js** (`x-data`, `x-show`, `x-transition`) o scripts encapsulados vía Vite en `resources/js/components/`.
- **Eventos:** Usa despacho de eventos (`$dispatch` o `CustomEvent`) para comunicar componentes entre sí.

---

## 4. Plantillas de Referencia

### A. Componente de Botón Reutilizable (`resources/views/components/ui/button.blade.php`)

```blade
@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'icon' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus-visible:ring-indigo-500',
    'secondary' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 focus-visible:ring-slate-400 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700',
    'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus-visible:ring-rose-500',
    'outline' => 'border border-slate-300 text-slate-700 hover:bg-slate-50 focus-visible:ring-slate-400 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'md' => 'px-4 py-2 text-sm gap-2',
    'lg' => 'px-5 py-2.5 text-base gap-2.5',
];

$classes =$baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $attributes->class([$classes]) }}>
    @if ($icon)
        <span class="shrink-0">{{ $icon }}</span>
    @endif
    {{ $slot }}
</button>
```
