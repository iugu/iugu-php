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

  public function create($attributes=Array()) {
    return call_user_func_array($this->_fabricator . '::create', array( $this->mergeparams($attributes) ));
  }

  public function search($options=Array()) {
    return call_user_func_array($this->_fabricator . '::search', Array( $this->mergeParams($options) ));
  }

  public function fetch($key=Array()) {
    return call_user_func_array($this->_fabricator . '::fetch', Array( $this->mergeParams($key) ));
  }
}

?>
