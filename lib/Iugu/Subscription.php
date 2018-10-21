<?php

class Iugu_Subscription extends APIResource
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

    public function add_credits($quantity)
    {
        if ($this->is_new()) {
            return false;
        }

        try {
            $response = self::API()->request(
        'PUT',
        static::url($this).'/add_credits',
        ['quantity' => $quantity]
      );
            if (isset($response->errors)) {
                return false;
            }
            $new_object = self::createFromResponse($response);
            $this->copy($new_object);
            $this->resetStates();

            return $new_object;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function remove_credits($quantity)
    {
        if ($this->is_new()) {
            return false;
        }

        try {
            $response = self::API()->request(
        'PUT',
        static::url($this).'/remove_credits',
        ['quantity' => $quantity]
      );
            if (isset($response->errors)) {
                return false;
            }
            $new_object = self::createFromResponse($response);
            $this->copy($new_object);
            $this->resetStates();

            return $new_object;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function suspend()
    {
        if ($this->is_new()) {
            return false;
        }

        try {
            $response = self::API()->request(
        'POST',
        static::url($this).'/suspend'
      );

            if (isset($response->errors)) {
                return false;
            }
            $new_object = self::createFromResponse($response);

            $new_flag = $new_object->suspended;
            $old_flag = $this->suspended;
            $this->copy($new_object);
            $this->resetStates();

            return $old_flag != $new_flag;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function activate()
    {
        if ($this->is_new()) {
            return false;
        }

        try {
            $response = self::API()->request(
        'POST',
        static::url($this).'/activate'
      );
            if (isset($response->errors)) {
                return false;
            }
            $new_object = self::createFromResponse($response);

            $new_flag = $new_object->active;
            $old_flag = $this->active;
            $this->copy($new_object);
            $this->resetStates();

            return $old_flag != $new_flag;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function change_plan_simulation($identifier = null)
    {
        if ($this->is_new()) {
            return false;
        }

        if ($identifier == null) {
            return false;
        }

        try {
            $response = self::API()->request(
                'GET',
                static::url($this).'/change_plan_simulation/'.$identifier
            );

            if (isset($response->errors)) {
                return false;
            }

            $new_object = self::createFromResponse($response);

            return $new_object;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function change_plan($identifier = null)
    {
        if ($this->is_new()) {
            return false;
        }
        if ($identifier == null) {
            return false;
        }

        try {
            $response = self::API()->request(
        'POST',
        static::url($this).'/change_plan/'.$identifier
      );
            if (isset($response->errors)) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function customer()
    {
        if (!isset($this->customer_id)) {
            return false;
        }
        if (!$this->customer_id) {
            return false;
        }

        return Iugu_Customer::fetch($this->customer_id);
    }
}
