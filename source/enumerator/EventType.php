<?php

namespace Papi\enumerator;

enum EventType
{
    case START;

    case CONFIGURE_DEFINITIONS;

    case CONFIGURE_EVENTS;

    case CONFIGURE_MIDDLEWARES;

    case CONFIGURE_ACTIONS;

    case END;
}
