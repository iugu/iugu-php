<?php

class APIChildResource
{
    private $_parentKeys;
    private $_fabricator;

    public function __construct($parentKeys, $className)
    {
        $this->_fabricator = $className;
        $this->_parentKeys = $parentKeys;
    }

    public function mergeParams($attributes)
    {
        return array_merge($attributes, $this->_parentKeys);
    }

    private function configureParentKeys($object)
    {
        foreach ($this->_parentKeys as $key => $value) {
            $object[$key] = $value;
        }

        return $object;
    }

    public function create($attributes = [])
    {
        $result = call_user_func_array($this->_fabricator.'::create', [$this->mergeparams($attributes), $this->_parentKeys]);
        if ($result) {
            $this->configureParentKeys($result);
        }

        return $result;
    }

    public function search($options = [])
    {
        $results = call_user_func_array($this->_fabricator.'::search', [$this->mergeParams($options), $this->_parentKeys]);
        if ($results && $results->total()) {
            $modifiedResults = $results->results();
            for ($i = 0; $i < count($modifiedResults); $i++) {
                $modifiedResults[$i] = $this->configureParentKeys($modifiedResults[$i]);
            }
            $results->set($modifiedResults, $results->total());
        }

        return $results;
    }

    public function fetch($key = [])
    {
        if (is_string($key)) {
            $key = ['id' => $key];
        }

        print_r([$this->mergeParams($key), $this->_parentKeys]);

        $result = call_user_func_array($this->_fabricator.'::fetch', [$this->mergeParams($key), $this->_parentKeys]);
        if ($result) {
            $this->configureParentKeys($result);
        }

        return $result;
    }
}
