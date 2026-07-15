<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ReservationDataTable;
use App\Events\ReservationStatusUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;


class ReservationController extends Controller
{
    function index(ReservationDataTable $dataTable) : View|JsonResponse
    {
        return $dataTable->render('admin.reservation.index');
    }

    function update(Request $request) : Response {
        $request->validate([
            'id' => ['required', 'exists:reservations,id'],
            'status' => ['required', 'in:pending,approved,complete,cancel'],
        ]);

        $reservation = Reservation::findOrFail($request->id);
        $reservation->status = $request->status;
        $reservation->save();

        try {
            broadcast(new ReservationStatusUpdatedEvent($reservation))->toOthers();
        } catch (Throwable $exception) {
            Log::warning('Reservation status updated, but realtime broadcast failed.', [
                'reservation_id' => $reservation->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return response(['status' => 'success', 'message' => 'updated successfully!']);
    }

    function destroy(string $id) : Response {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->delete();
            return response(['status' => 'success', 'message' => 'Deleted Successfully!']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'something went wrong!']);
        }
    }
}
