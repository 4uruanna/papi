<?php

namespace Papi\enumerator;

enum EventType: int
{
    case START = 0;

    case CONFIGURE_DEFINITIONS = 100;

    case CONFIGURE_MIDDLEWARES = 200;

    case CONFIGURE_ACTIONS = 300;

    case END = 400;
}
