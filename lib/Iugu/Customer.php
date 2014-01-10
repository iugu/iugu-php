<?php

class Iugu_Customer extends APIResource {

  public static function create($attributes=Array()) { return self::createAPI($attributes); }
  public static function fetch($id)                  { return self::fetchAPI($id); }
  public        function save()                      { return $this->saveAPI(); }
  public        function delete()                    { return $this->deleteAPI(); }

  public        function refresh()                   { return $this->refreshAPI(); }
  public static function search($options=Array(),
                                &$totalRecords=null )
                                                     { return self::searchAPI($options, $totalRecords); }
}

?>
