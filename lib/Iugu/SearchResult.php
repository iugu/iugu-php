<?php

class Iugu_SearchResult
{
  protected $_totalResults;
  protected $_results;

  public function __construct($results,$totalResults) {
    $this->_totalResults = $totalResults;
    $this->_results = $results;
  }

  public function total() {
    return $this->_totalResults; 
  }

  public function results() {
   return $this->_results; 
  }

  public function set($results, $totalResults) {
    $this->_totalResults = $totalResults;
    $this->_results = $results;
  }
}
