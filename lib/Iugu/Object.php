<?php

//ooooooooooooooooooooooooooooooooooooooooooooo
// Iugu_Object manages the Object State
// Values that changed, values that need to be saved
//ooooooooooooooooooooooooooooooooooooooooooooo
class Iugu_Object implements ArrayAccess
{
  protected $_attributes;
  protected $_unsavedAttributes;

  public function __construct($attributes=Array()) {
    $this->_attributes  = Array();
    $this->_unsavedAttributes = Array();  

    print_r($attributes);
    foreach ($attributes as $key=>$value) {
     $this->_attributes[$key] = $value;
    }
  }

  public function __set($key, $value) {
  
  }

  public function __isset($key) {
  
  }

  public function __unset($key) {
  
  }

  public function __get($key) {
  
  }

  public function offsetSet($key, $value) {
    $this->$key = $value; 
  }

  public function offsetExists($k) {
  
  }

  public function offsetUnset($key) {
    unset($this->$key) ;
  }

  public function offsetGet($key) {
    return array_key_exists($key, $this->_attributes) ? $this->_attributes[$key] : null; 
  }

  public function keys() {
    return array_keys($this->_attributes); 
  }

  public function modifiedValues() {
  
  }

  public function resetStates() {
    $this->$_unsavedAttributes = Array();
  }
}

?>
