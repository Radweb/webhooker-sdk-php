<?php

namespace WebHooker\Exceptions;

class ExpiredException extends Exception
{
    protected $message = 'Team has expired. Please visit the web app to re-activate.';
}