<?php

namespace WebHooker\Exceptions;

class UnauthorisedRequestException extends Exception
{
    protected $message = 'Unauthorised Request. Please check your API Key.';
}
