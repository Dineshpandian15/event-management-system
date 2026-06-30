<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'participants' => ['nullable', 'array'],
            'participants.*.name' => ['required', 'string', 'max:255'],
            'participants.*.email' => ['required', 'email', 'max:255'],
            'participants.*.phone' => ['nullable', 'string', 'max:20'],
        ]);

        $event = $request->user()->events()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'event_date' => $validated['event_date'],
        ]);

        if (! empty($validated['participants'])) {
            $event->participants()->createMany($validated['participants']);
        }

        $event->load('participants');

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event,
        ], 201);
    }

    public function addParticipants(Request $request, Event $event): JsonResponse
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'participants' => ['required', 'array', 'min:1'],
            'participants.*.name' => ['required', 'string', 'max:255'],
            'participants.*.email' => ['required', 'email', 'max:255'],
            'participants.*.phone' => ['nullable', 'string', 'max:20'],
        ]);

        $event->participants()->createMany($validated['participants']);
        $event->load('participants');

        return response()->json([
            'message' => 'Participants added successfully',
            'event' => $event,
        ], 201);
    }
}
