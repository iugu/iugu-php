<?php

class Iugu_Account extends APIResource
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

    public function configuration()
    {
        try {
            if ($this->is_new()) {
                return false;
            }
            $response = self::API()->request(
                'POST', static::url() . '/configuration', $this->modifiedAttributes()
            );
             $new_object = self::createFromResponse($response);
            $this->copy($new_object);
            $this->resetStates();
             if (isset($response->errors)) {
                throw new IuguException();
            }
        } catch (Exception $e) {
            return false;
        }
         return true;
    }
}
