<?php

namespace App\Enums;

enum JourneyEventType: string
{
    case ROUTE_REQUESTED = 'ROUTE_REQUESTED';
    case ROUTE_CREATED = 'ROUTE_CREATED';
    case ROUTE_NOT_FOUND = 'ROUTE_NOT_FOUND';
    case ROUTE_ADAPTED = 'ROUTE_ADAPTED';
    case AI_INTERPRETATION_FAILED = 'AI_INTERPRETATION_FAILED';
}