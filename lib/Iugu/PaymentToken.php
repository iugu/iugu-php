<?php

class Iugu_PaymentToken extends APIResource
{
    public static function create($attributes = [])
    {
        return self::createAPI($attributes);
    }
}
