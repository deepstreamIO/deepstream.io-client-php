<?php

namespace Deepstreamhub\Exceptions;

class NoDeepStreamClientException extends \Exception
{
    protected $message = 'There is no Deepstream Client';
}