<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Helper global para confirmaciones
                window.confirmAction = function(event, message) {
                    event.preventDefault();
                    const element = event.currentTarget || event.target;
                    const form = element.closest('form');
                    Swal.fire({
                        title: '¿Confirmar acción?',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#ef4444',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                };

                // Alertas Flash automáticas (excepto en la pantalla de comedor para no interrumpir el flujo del lector)
                @if (!request()->routeIs('comedor.*'))
                    @if (session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 3000
                        });
                    @endif

                    @if (session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: "{{ session('error') }}",
                            confirmButtonColor: '#4f46e5'
                        });
                    @endif

                    @if ($errors->any())
                        Swal.fire({
                            icon: 'error',
                            title: 'Errores de Validación',
                            html: `<ul style="text-align: left; list-style-type: disc; padding-left: 20px; margin: 0;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>`,
                            confirmButtonColor: '#4f46e5'
                        });
                    @endif
                @endif
            });
        </script>
    </body>
</html>
