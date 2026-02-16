<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    // public function createBooking(array $data)
    // {
    //     return DB::transaction(function() use ($data) {
        
    //         // verifica delle sovrapposizioni
    //         // la query controlla se esiste una prenotazione per la stessa risorsa che si sovrappone nello stesso intervallo di tempo
    //         $overlap = Booking::where('resource_id', $data['resource_id'])
    //         ->where(function($query) use ($data){
    //             $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
    //             ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
    //             ->orWhere(function($query) use ($data){
    //                 $query->where('start_time', '<', $data['start_time'])
    //                 ->where('end_time', '>', $data['end_time']);
    //             });
    //         })->exists();

    //         if($overlap)
    //             {
    //                 throw new \Exception('La risorsa è già prenotata in questo intervallo di tempo.');
    //             }

    //             return Booking::create($data);
    //     });
    // }

    public function createBooking(array $data)
    {
        return DB::transaction(function () use ($data) {
            $start = Carbon::parse($data['start_time']);
            $end = Carbon::parse($data['end_time']);

            $overlap = Booking::where('resource_id', $data['resource_id'])
                ->where(function ($query) use ($start, $end) {
                    $query->where('start_time', '<', $end)
                          ->where('end_time', '>', $start);
                })
                ->exists();

            if ($overlap) {
                // Lanciamo un'eccezione specifica
                throw new \Exception("La risorsa è già occupata per questo intervallo temporale.");
            }

            return Booking::create($data);
        });
    }
}