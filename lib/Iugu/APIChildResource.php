<?php

class APIChildResource {
  private $_parentKeys;
  private $_fabricator;
  
  function __construct($parentKeys=Array(), $className) {
    $this->_fabricator = $className;
    $this->_parentKeys = $parentKeys;
  }

  function mergeParams( $attributes ) {
    return array_merge( $attributes, $this->_parentKeys );
  }

  private function configureParentKeys($object) {
    foreach ($this->_parentKeys as $key => $value)  {
      $object[$key] = $value;
    }
    return $object;
  }

  public function create($attributes=Array()) {
    $result = call_user_func_array($this->_fabricator . '::create', array( $this->mergeparams($attributes), $this->_parentKeys ));
    if ($result) $this->configureParentKeys( $result );
    return $result;
  }

  public function search($options=Array()) {
    $results = call_user_func_array($this->_fabricator . '::search', Array( $this->mergeParams($options), $this->_parentKeys ));
    if ($results && $results->total()) {
      $modifiedResults = $results->results();
      for ($i=0;$i<count($modifiedResults);$i++) {
        $modifiedResults[$i] = $this->configureParentKeys( $modifiedResults[$i] );
      }
      $results->set($modifiedResults, $results->total());
    }
    return $results;
  }

  public function fetch($key=Array()) {
    if (is_string($key)) {
      $key = Array( "id" => $key );
    }

    print_r(Array( $this->mergeParams($key), $this->_parentKeys ));

    $result = call_user_func_array($this->_fabricator . '::fetch', Array( $this->mergeParams($key), $this->_parentKeys ));
    if ($result) $this->configureParentKeys( $result );
    return $result;
  }
}
