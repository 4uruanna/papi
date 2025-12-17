<?php

namespace Papi\enumerator;

enum HttpMethod: string
{
    case DELETE = "delete";

    case GET = "get";

    case PATCH = "patch";

    case POST = "post";

    case PUT = "put";
}
