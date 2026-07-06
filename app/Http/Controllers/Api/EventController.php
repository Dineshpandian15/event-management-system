<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class EventController extends Controller
{
    #[OA\Post(
        path: '/events',
        operationId: 'createEvent',
        summary: 'Create a new event with optional participants',
        security: [['passport' => []]],
        tags: ['Events'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'event_date'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Annual Tech Conference 2026'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'A full-day conference featuring talks on Laravel.'),
                    new OA\Property(property: 'location', type: 'string', maxLength: 255, nullable: true, example: 'Chennai Convention Center'),
                    new OA\Property(property: 'event_date', type: 'string', format: 'date-time', example: '2026-09-15 09:00:00'),
                    new OA\Property(
                        property: 'participants',
                        type: 'array',
                        items: new OA\Items(ref: '#/components/schemas/ParticipantInput'),
                    ),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Event created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Event created successfully'),
                        new OA\Property(property: 'event', ref: '#/components/schemas/Event'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
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

    #[OA\Post(
        path: '/events/{event}/participants',
        operationId: 'addParticipants',
        summary: 'Add participants to an existing event',
        security: [['passport' => []]],
        tags: ['Events'],
        parameters: [
            new OA\Parameter(
                name: 'event',
                in: 'path',
                required: true,
                description: 'Event ID',
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['participants'],
                properties: [
                    new OA\Property(
                        property: 'participants',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(ref: '#/components/schemas/ParticipantInput'),
                    ),
                ],
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Participants added successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Participants added successfully'),
                        new OA\Property(property: 'event', ref: '#/components/schemas/Event'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden — event belongs to another user'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
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
