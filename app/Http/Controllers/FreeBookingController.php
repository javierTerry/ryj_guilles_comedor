<?php

namespace App\Http\Controllers;

use App\Models\FreeBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FreeBookingController extends Controller
{
    /**
     * Muestra la tabla de seguimiento y gestión de Reservas Libres.
     */
    public function index(Request $request): View|JsonResponse
    {
        $bookings = FreeBooking::with('createdBy')
            ->orderBy('booking_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        if ($request->wantsJson() && !$request->hasHeader('X-Inertia')) {
            return response()->json([
                'success' => true,
                'data' => $bookings,
            ]);
        }

        return view('reservas.libres', compact('bookings'));
    }

    /**
     * Almacena una nueva Reserva Libre para habilitar fechas de reservación anticipada.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
        ], [
            'booking_date.required' => 'La fecha de la reserva libre es obligatoria.',
            'booking_date.date_format' => 'El formato de fecha debe ser AAAA-MM-DD.',
            'booking_date.after_or_equal' => 'La fecha debe ser posterior o igual a la fecha actual.',
        ]);

        $bookingDate = $validated['booking_date'];

        // Regla de negocio: No permitir duplicados de booking_date con estatus activo
        $existingActive = FreeBooking::where('booking_date', $bookingDate)
            ->where('status', FreeBooking::STATUS_ACTIVO)
            ->first();

        if ($existingActive) {
            Log::channel('reservas_libres')->warning('Intento de crear reserva libre duplicada para fecha activa', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()?->email,
                'booking_date' => $bookingDate,
                'existing_id' => $existingActive->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ya existe una reserva libre activa para la fecha seleccionada (' . date('d/m/Y', strtotime($bookingDate)) . ').',
            ], 422);
        }

        $freeBooking = FreeBooking::create([
            'booking_date' => $bookingDate,
            'created_by' => Auth::id(),
            'status' => FreeBooking::STATUS_ACTIVO,
        ]);

        $freeBooking->load('createdBy');

        Log::channel('reservas_libres')->info('Nueva reserva libre creada exitosamente', [
            'id' => $freeBooking->id,
            'booking_date' => $freeBooking->booking_date->format('Y-m-d'),
            'created_by' => $freeBooking->created_by,
            'creator_name' => Auth::user()?->name,
            'status' => $freeBooking->status,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reserva libre para el ' . $freeBooking->booking_date->format('d/m/Y') . ' habilitada exitosamente.',
            'data' => [
                'id' => $freeBooking->id,
                'booking_date' => $freeBooking->booking_date->format('d/m/Y'),
                'created_by' => $freeBooking->createdBy?->name ?? 'Usuario',
                'status' => $freeBooking->status,
            ],
        ], 201);
    }

    /**
     * Actualiza el estatus de una Reserva Libre (cancelar o reactivar).
     */
    public function updateStatus(Request $request, FreeBooking $freeBooking): JsonResponse
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in([FreeBooking::STATUS_ACTIVO, FreeBooking::STATUS_CANCELADO]),
            ],
        ], [
            'status.required' => 'El estatus es requerido.',
            'status.in' => 'El estatus solo puede ser modificado a activo o cancelado.',
        ]);

        $newStatus = $validated['status'];
        $previousStatus = $freeBooking->status;

        // Bloquear operación si el registro actual ya está en estatus 'aplicado'
        if ($freeBooking->isAplicado()) {
            Log::channel('reservas_libres')->warning('Intento de modificar una reserva libre en estado aplicado', [
                'id' => $freeBooking->id,
                'booking_date' => $freeBooking->booking_date->format('Y-m-d'),
                'attempted_by' => Auth::id(),
                'attempted_status' => $newStatus,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No es posible modificar el estado de una reserva libre que ya ha sido aplicada.',
            ], 422);
        }

        // Si se va a reactivar a activo, validar que no exista otra reserva libre activa para esa misma fecha
        if ($newStatus === FreeBooking::STATUS_ACTIVO) {
            $otherActive = FreeBooking::where('booking_date', $freeBooking->booking_date->format('Y-m-d'))
                ->where('status', FreeBooking::STATUS_ACTIVO)
                ->where('id', '!=', $freeBooking->id)
                ->first();

            if ($otherActive) {
                Log::channel('reservas_libres')->warning('Intento de reactivar reserva libre existiendo otra activa para la fecha', [
                    'id' => $freeBooking->id,
                    'conflict_id' => $otherActive->id,
                    'booking_date' => $freeBooking->booking_date->format('Y-m-d'),
                    'user_id' => Auth::id(),
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'No se puede reactivar. Ya existe otra reserva libre activa para esta fecha.',
                ], 422);
            }
        }

        $freeBooking->status = $newStatus;
        $freeBooking->save();

        Log::channel('reservas_libres')->info('Estado de reserva libre modificado exitosamente', [
            'id' => $freeBooking->id,
            'booking_date' => $freeBooking->booking_date->format('Y-m-d'),
            'previous_status' => $previousStatus,
            'new_status' => $freeBooking->status,
            'updated_by' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'ip' => $request->ip(),
        ]);

        $accion = $newStatus === FreeBooking::STATUS_ACTIVO ? 'reactivada' : 'cancelada';

        return response()->json([
            'success' => true,
            'message' => "La reserva libre fue {$accion} correctamente.",
            'data' => [
                'id' => $freeBooking->id,
                'booking_date' => $freeBooking->booking_date->format('d/m/Y'),
                'created_by' => $freeBooking->createdBy?->name ?? 'Usuario',
                'status' => $freeBooking->status,
            ],
        ]);
    }
}
