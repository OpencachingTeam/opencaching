<?php

class ChildWp_Handler
{
  private $childWpTypes = array();
  private $translator;

  public function __construct()
  {
    $this->translator = new Language_Translator();

    require($_SERVER['DOCUMENT_ROOT'] . '/config2/childwp.inc.php');

    foreach ($childWpTypes as $type)
    {
      $this->childWpTypes[$type->getId()] = $type;
    }
  }

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
    $rs = sql("SELECT id, cache_id, subtype, latitude, longitude, description FROM coordinates WHERE id = &1", $childid);
    $ret = $this->recordToArray(sql_fetch_array($rs));
    mysql_free_result($rs);

    return $ret;
  }

  public function getChildWps($cacheid)
  {
    $rs = sql("SELECT id, cache_id, subtype, latitude, longitude, description FROM coordinates WHERE cache_id = &1 AND type = &2", $cacheid, Coordinate_Type::ChildWaypoint);
    $ret = array();

    while ($r = sql_fetch_array($rs))
    {
      $ret[] = $this->recordToArray($r);
    }

    mysql_free_result($rs);

    return $ret;
  }

  public function getChildWpIdAndNames()
  {
    $idAndNames = array();

    foreach ($this->childWpTypes as $type)
    {
      $idAndNames[$type->getId()] = $this->translator->translate($type->getName());
    }

    return $idAndNames;
  }

  private function recordToArray($r)
  {
    $ret = array();

    $ret['cacheid'] = $r['cache_id'];
    $ret['childid'] = $r['id'];
    $ret['type'] = $r['subtype'];
    $ret['latitude'] = $r['latitude'];
    $ret['longitude'] = $r['longitude'];
    $ret['coordinate'] = new Coordinate_Coordinate($ret['latitude'], $ret['longitude']);
    $ret['description'] = $r['description'];

    $type = $this->childWpTypes[$ret['type']];

    if ($type)
    {
      $ret['name'] = $this->translator->translate($type->getName());
      $ret['image'] = $type->getImage();
    }

    return $ret;
  }

  public function delete($childid)
  {
    sql("DELETE FROM coordinates WHERE id = &1", $childid);
  }
}

?>
