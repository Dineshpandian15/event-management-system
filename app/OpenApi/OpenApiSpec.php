<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Event Management API',
    description: 'REST API for managing events and participants with Laravel Passport authentication.',
)]
#[OA\Server(
    url: '/api',
    description: 'API base path',
)]
#[OA\SecurityScheme(
    securityScheme: 'passport',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Laravel Passport personal access token obtained from POST /login',
)]
#[OA\Schema(
    schema: 'User',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Event Organizer'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'organizer@example.com'),
        new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'Participant',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'event_id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Alice Johnson'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'alice@example.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '+919876543210'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'ParticipantInput',
    type: 'object',
    required: ['name', 'email'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Alice Johnson'),
        new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'alice@example.com'),
        new OA\Property(property: 'phone', type: 'string', maxLength: 20, nullable: true, example: '+919876543210'),
    ],
)]
#[OA\Schema(
    schema: 'Event',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Annual Tech Conference 2026'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'location', type: 'string', nullable: true, example: 'Chennai Convention Center'),
        new OA\Property(property: 'event_date', type: 'string', format: 'date-time'),
        new OA\Property(property: 'participants', type: 'array', items: new OA\Items(ref: '#/components/schemas/Participant')),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string'),
            ),
        ),
    ],
)]
class OpenApiSpec
{
}
