<?php

namespace Deepstreamhub\Exceptions;

class NoBatchInstanceException extends \Exception
{
    protected $message = 'There is no batch instance';
}