<?php

class APIResource extends Iugu_Object
{

    private static $_apiRequester = null;

    public static function convertClassToObjectType()
    {
        $object_type = str_replace('Iugu_', '', get_called_class());
        $object_type = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $object_type));

        return mb_strtolower($object_type, 'UTF-8');
    }

    public static function objectBaseURI()
    {
        $object_type = self::convertClassToObjectType();
        switch ($object_type) {
            // Add Exceptions as needed
        case 'charge':
            return $object_type;
        case 'payment_token':
            return $object_type;
        case 'bank_verification':
            return $object_type;
        case 'marketplace':
            return $object_type; // WORKAROUND MARKETPLACE
        default:
            return $object_type . 's';
        }
    }

    public static function API()
    {
        if (self::$_apiRequester == null) {
            self::$_apiRequester = new Iugu_APIRequest();
        }

        return self::$_apiRequester;
    }

    public static function endpointAPI($object = null, $uri_path = '')
    {
        $path = '';

        if (is_string($object)) {
            $path = '/' . $object;
        } elseif (is_object($object) && (isset($object['id']))) {
            $path = '/' . $object['id'];
        } elseif (is_object($object) && (isset($object['account_id']))) { // WORKAROUND MARKETPLACE/ACCOUNT
            $path = '/' . $object['account_id'];
        }
        if (isset($object['action'])) { // WORKAROUND MARKETPLACE/ACCOUNT
            if (isset($object['id']))
                $path .= '/' . $object['id'];
            elseif (isset($object['account_id']))
                $path .= '/' . $object['account_id'];
            $path .= '/' . $object['action'];
        }
        return Iugu::getBaseURI() . $uri_path . '/' . self::objectBaseURI() . $path;
    }

    public static function url($object = null)
    {
        return self::endpointAPI($object);
    }

    protected static function createFromResponse($response)
    {
        return Iugu_Factory::createFromResponse(
            self::convertClassToObjectType(), $response
        );
    }

    protected static function createAPI($attributes = [])
    {
        $response = self::createFromResponse(
            self::API()->request(
                'POST', static::url($attributes), $attributes
            )
        );

        return $response;
    }

    protected function deleteAPI()
    {
        if ($this['id'] == null) {
            return false;
        }

        try {
            $response = self::API()->request(
                'DELETE', static::url($this)
            );

            if (isset($response->errors)) {
                throw new IuguException();
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    protected static function searchAPI($options = [])
    {
        try {
            $response = self::API()->request(
                'GET', static::url($options), $options
            );

            return self::createFromResponse($response);
        } catch (Exception $e) {

        }

        return [];
    }

    protected static function fetchAPI($key)
    {
        try {
            $response = static::API()->request(
                'GET', static::url($key)
            );

            return self::createFromResponse($response);
        } catch (IuguObjectNotFound $e) {
            throw new IuguObjectNotFound(self::convertClassToObjectType(get_called_class()) . ':' . ' not found');
        }
    }

    protected function refreshAPI()
    {
        if ($this->is_new()) {
            return false;
        }

        try {
            $response = self::API()->request(
                'GET', static::url($this)
            );

            if (isset($response->errors)) {
                throw new IuguObjectNotFound();
            }

            $new_object = self::createFromResponse($response);
            $this->copy($new_object);
            $this->resetStates();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    protected function saveAPI()
    {
        try {
            $response = self::API()->request(
                $this->is_new() ? 'POST' : 'PUT', static::url($this), $this->modifiedAttributes()
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
