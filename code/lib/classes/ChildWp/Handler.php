<?php

class ChildWp_Handler
{
  public function add($cacheid, $type, $lat, $lon, $desc)
  {
    sql("INSERT INTO coordinates(type, subtype, latitude, longitude, cache_id, description) VALUES(&1, &2, &3, &4, &5, '&6')", Coordinate_Type::ChildWaypoint, $type, $lat, $lon, $cacheid, $desc);
  }

  public function update($childid, $type, $lat, $lon, $desc)
  {
    sql("UPDATE coordinates SET subtype = &1, latitude = &2, longitude = &3, description = '&4' WHERE id = &5", $type, $lat, $lon, $desc, $childid);
  }

  public function getChildWp($childid)
  {
    $rs = sql("SELECT cache_id AS cacheid, subtype AS type, latitude, longitude, description FROM coordinates WHERE id = &1", $childid);

    return sql_fetch_array($rs);
  }
}

?>
