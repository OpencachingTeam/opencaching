<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *   get/set has to be commited with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/logic/rowEditor.class.php');
require_once($opt['rootpath'] . 'lib2/logic/const.inc.php');

class podcast
{
	var $nPodcastId = 0;
	var $rePodcast;
	var $sFileExtension = '';
	var $bFilenamesSet = false;

	static function podcastIdFromUUID($uuid)
	{
		$podcastid = sql_value("SELECT `id` FROM `mp3` WHERE `uuid`='&1'", 0, $uuid);
		return $podcastid;
	}

	static function fromUUID($uuid)
	{
		$podcastid = podcast::podcastIdFromUUID($uuid);
		if ($podcastid == 0)
			return null;

		return new podcast($podcastid);
	}

	function __construct($nNewPodcastId=ID_NEW)
	{
		global $opt;

		$this->rePodcast = new rowEditor('mp3');
		$this->rePodcast->addPKInt('id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->rePodcast->addString('uuid', '', false);
		$this->rePodcast->addInt('node', 0, false);
		$this->rePodcast->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->rePodcast->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->rePodcast->addString('url', '', false);
		$this->rePodcast->addString('title', '', false);
		$this->rePodcast->addDate('last_url_check', 0, true);
		$this->rePodcast->addInt('object_id', null, false);
		$this->rePodcast->addInt('object_type', null, false);
		$this->rePodcast->addInt('local', 0, false);
		$this->rePodcast->addInt('unknown_format', 0, false);
		$this->rePodcast->addInt('display', 1, false);

		$this->nPodcastId = $nNewPodcastId+0;

		if ($nNewPodcastId == ID_NEW)
		{         	
			$this->rePodcast->addNew(null);

			$sUUID = mb_strtoupper(sql_value("SELECT UUID()", ''));
			$this->rePodcast->setValue('uuid', $sUUID);
			$this->rePodcast->setValue('node', $opt['logic']['node']['id']);
		}
		else
		{
			$this->rePodcast->load($this->nPodcastId);

			$sFilename = $this->getFilename();
			$fna = mb_split('\\.', $sFilename);
			$this->sFileExtension = mb_strtolower($fna[count($fna) - 1]);

			$this->bFilenamesSet = true;
		}
	}

	function exist()
	{
		return $this->rePodcast->exist();
	}

	static function allowedExtension($sFilename)
	{
		global $opt;

		if (strpos($sFilename, ';') !== false)
			return false;
		if (strpos($sFilename, '.') === false)
			return false;

		$sExtension = mb_strtolower(substr($sFilename, strrpos($sFilename, '.') + 1));
		
		if (strpos(';' . $opt['logic']['podcasts']['extensions'] . ';', ';' . $sExtension . ';') !== false)
			return true;
		else
			return false;
	}

	function setFilenames($sFilename)
	{
		global $opt;

		if ($this->bFilenamesSet == true)
			return;
		if (strpos($sFilename, '.') === false)
			return;
		$sExtension = mb_strtolower(substr($sFilename, strrpos($sFilename, '.') + 1));

		$sUUID = $this->getUUID();

		$this->sFileExtension = $sExtension;
		$this->setUrl($opt['logic']['podcasts']['url'] . $sUUID . '.' . $sExtension);
		$this->bFilenamesSet = true;
	}

	function getPodcastId()
	{
		return $this->nPodcastId;
	}

	function delete()
	{
		global $opt;

		// delete record and file
		@unlink($this->getFilename());

		sql("DELETE FROM `mp3` WHERE `id`='&1'", $this->nPodcastId);

		return true;
	}

	function getUrl()
	{
		return $this->rePodcast->getValue('url');
	}
	function setUrl($value)
	{
		return $this->rePodcast->setValue('url', $value);
	}
	function getTitle()
	{
		return $this->rePodcast->getValue('title');
	}
	function setTitle($value)
	{
		if ($value != '')
			return $this->rePodcast->setValue('title', $value);
		else
			return false;
	}
	function getLocal()
	{
		return $this->rePodcast->getValue('local')!=0;
	}
	function setLocal($value)
	{
		return $this->rePodcast->setValue('local', $value ? 1 : 0);
	}
	function getDisplay()
	{
		return $this->rePodcast->getValue('display')!=0;
	}
	function setDisplay($value)
	{
		return $this->rePodcast->setValue('display', $value ? 1 : 0);
	}
	function getFilename()
	{
		global $opt;

		if (mb_substr($opt['logic']['podcasts']['dir'], -1, 1) != '/')
			$opt['logic']['podcasts']['dir'] .= '/';

		$uuid = $this->getUUID();
		$url = $this->getUrl();
		$fna = mb_split('\\.', $url);
		$extension = mb_strtolower($fna[count($fna) - 1]);
		
		return $opt['logic']['podcasts']['dir'] . $uuid . '.' . $extension;
	}
	function getLogId()
	{
		if ($this->getObjectType() == OBJECT_CACHELOG)
			return $this->getObjectId();
		else
			return false;
	}
	function getCacheId()
	{
		if ($this->getObjectType() == OBJECT_CACHELOG)
			return sql_value("SELECT `cache_id` FROM `cache_logs` WHERE `id`='&1'", false, $this->getObjectId());
		else if ($this->getObjectType() == OBJECT_CACHE)
			return $this->getObjectId();
		else
			return false;
	}
	function getObjectId()
	{
		return $this->rePodcast->getValue('object_id');
	}
	function setObjectId($value)
	{
		return $this->rePodcast->setValue('object_id', $value+0);
	}
	function getObjectType()
	{
		return $this->rePodcast->getValue('object_type');
	}
	function setObjectType($value)
	{
		return $this->rePodcast->setValue('object_type', $value+0);
	}
	function getUserId()
	{
		if ($this->getObjectType() == OBJECT_CACHE)
			return sql_value("SELECT `caches`.`user_id` FROM `caches` WHERE `caches`.`cache_id`='&1'", false, $this->getObjectId());
		else if ($this->getObjectType() == OBJECT_CACHELOG)
			return sql_value("SELECT `cache_logs`.`user_id` FROM `cache_logs` WHERE `cache_logs`.`id`='&1'", false, $this->getObjectId());
		else
			return false;
	}

	function getNode()
	{
		return $this->rePodcast->getValue('node');
	}
	function setNode($value)
	{
		return $this->rePodcast->setValue('node', $value);
	}
	function getUUID()
	{
		return $this->rePodcast->getValue('uuid');
	}
	function getLastModified()
	{
		return $this->rePodcast->getValue('last_modified');
	}
	function getDateCreated()
	{
		return $this->rePodcast->getValue('date_created');
	}
	function getAnyChanged()
	{
		return $this->rePodcast->getAnyChanged();
	}

	// return if successfull (with insert)
	function save()
	{
		if ($this->bFilenamesSet == false)
			return false;

		$bRetVal = $this->rePodcast->save();

		if ($bRetVal)
			sql_slave_exclude();

		return $bRetVal;
	}

	function allowEdit()
	{
		global $login;

		$login->verify();

		if (sql_value("SELECT COUNT(*) FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&1') AND `caches`.`cache_id`='&2'", 0, $login->userid, $this->getCacheId()) == 0)
			return false;
		else if ($this->getUserId() == $login->userid)
			return true;

		return false;
	}
}
?>