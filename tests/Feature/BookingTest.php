<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Resource;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_book_the_same_resource_in_the_same_time_slot()
    {
        // 1. Preparazione dati (Arrange)
        $user = User::factory()->create();
        $resource = Resource::create(['name' => 'Sala Meeting A']);

        // creo una prenotazione esistente dalle 10 alle 12
        Booking::create([
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'start_time' => now()->addDay()->setTime(10, 0, 0),
            'end_time' => now()->addDay()->setTime(12, 0, 0),
        ]);

        // 2. Esecuzione (Act)
        // provo a prenotare dalle 11 alle 13
        // dovrebbe fallire perché si sovrappone ad una già esistente
        $response = $this->postJson('/api/bookings', [
            'user_id' => $user->id,
            'resource_id' => $resource->id,
            'start_time' => now()->addDay()->setTime(11, 0, 0),
            'end_time' => now()->addDay()->setTime(13, 0, 0),
        ]);

        // 3. Verifica (Assert)
        $response->assertStatus(422);
        $response->assertJsonFragment(['error' => 'La risorsa è già occupata per questo intervallo temporale.']);
        
        // verifico che nel db ci sia solo 1 prenotazione
        $this->assertEquals(1, Booking::count());
    }
}