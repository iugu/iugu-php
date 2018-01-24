<?php

class Iugu_Plan extends APIResource
{
    public static function create($attributes = [])
    {
        return self::createAPI($attributes);
    }

    public static function fetch($key)
    {
        return self::fetchAPI($key);
    }

    public function save()
    {
        return $this->saveAPI();
    }

    public function delete()
    {
        return $this->deleteAPI();
    }

    public function refresh()
    {
        return $this->refreshAPI();
    }

    public static function search($options = [])
    {
        return self::searchAPI($options);
    }

    public static function fetchByIdentifier($identifier) {
        try {
            $response = self::API()->request(
                "GET",
                static::url() . "/identifier/" . $identifier
            );
            if (isset($response->errors)) {
                return false;
            }
            $new_object = self::createFromResponse( $response );
            return $new_object;

        } catch (Exception $e) {
            return false;
        }
        return false;
    }
}
