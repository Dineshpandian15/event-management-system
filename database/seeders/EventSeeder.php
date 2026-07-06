<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::where('email', 'organizer@example.com')->first();

        if (! $organizer) {
            return;
        }

        $events = [
            [
                'title' => 'Annual Tech Conference 2026',
                'description' => 'A full-day conference featuring talks on Laravel, cloud infrastructure, and modern PHP.',
                'location' => 'Chennai Convention Center',
                'event_date' => now()->addMonths(2)->setTime(9, 0),
                'participants' => [
                    ['name' => 'Alice Johnson', 'email' => 'alice@example.com', 'phone' => '+919876543210'],
                    ['name' => 'Bob Smith', 'email' => 'bob@example.com', 'phone' => '+919876543211'],
                    ['name' => 'Carol Williams', 'email' => 'carol@example.com', 'phone' => null],
                ],
            ],
            [
                'title' => 'Product Launch Webinar',
                'description' => 'Live demo and Q&A for the new event management features.',
                'location' => 'Online (Zoom)',
                'event_date' => now()->addWeeks(3)->setTime(15, 30),
                'participants' => [
                    ['name' => 'David Lee', 'email' => 'david@example.com', 'phone' => '+919876543212'],
                    ['name' => 'Eva Martinez', 'email' => 'eva@example.com', 'phone' => null],
                ],
            ],
            [
                'title' => 'Team Building Workshop',
                'description' => 'Half-day workshop focused on collaboration and agile practices.',
                'location' => 'Mallow Technologies Office',
                'event_date' => now()->addMonth()->setTime(10, 0),
                'participants' => [
                    ['name' => 'Frank Brown', 'email' => 'frank@example.com', 'phone' => '+919876543213'],
                ],
            ],
        ];

        foreach ($events as $eventData) {
            $participants = $eventData['participants'];
            unset($eventData['participants']);

            $event = $organizer->events()->create($eventData);

            foreach ($participants as $participant) {
                $event->participants()->create($participant);
            }
        }

        Event::factory()
            ->count(2)
            ->for($organizer)
            ->has(Participant::factory()->count(3))
            ->create();
    }
}
