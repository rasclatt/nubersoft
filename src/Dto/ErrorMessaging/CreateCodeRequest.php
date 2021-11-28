<?php
namespace Nubersoft\Dto\ErrorMessaging;

class CreateCodeRequest extends \SmartDto\Dto
{
    public $code;
    public $message;
    public $locale = 'us';
    public $lang = 'en';
}