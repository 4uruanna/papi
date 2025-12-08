<?php

namespace Papi\enumerator;

class HttpMethods
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const PATCH = 'PATCH';
    public const DELETE = 'DELETE';

    public const CALLBACK_MAP = [
        self::GET => 'get',
        self::POST => 'post',
        self::PUT => 'put',
        self::PATCH => 'patch',
        self::DELETE => 'delete',
    ];
}
