<?php

class ChildWp_Handler
{
  public function add($cacheid, $type, $lat, $lon, $desc)
  {
    sql("INSERT INTO coordinates(type, subtype, latitude, longitude, cache_id, description) VALUES(&1, &2, &3, &4, &5, '&6')", Coordinate_Type::ChildWaypoint, $type, $lat, $lon, $cacheid, $desc);
  }
}

?>
