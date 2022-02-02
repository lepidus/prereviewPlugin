<?php

/**
 * @file plugins/generic/prereview/PrereviewPluginDAO.inc.php
 *
 * @class PrereviewPluginDAO
 * @ingroup plugins.generic.prereview
 *
 * @brief Operations to add save settings for each item.
 */


class PrereviewPluginDAO extends DAO {

	/** @var $_result ADORecordSet */
	var $_result;

	/** @var $_loadId string */
	var $_loadId;

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();

		$this->_result = false;
		$this->_loadId = null;
	}
	function insert($id, $name, $value) {
		$this->update(
			'INSERT INTO prereview_settings
				(publication_id, setting_name, setting_value)
				VALUES
				(?, ?, ?)',
			array(
				$id,
				$name,
				$value,
			)
		);
		return true;
	}
	function updateObject($id, $name, $value) {
		
		$this->update(
			'UPDATE	prereview_settings
			SET	setting_value = ?
			WHERE publication_id = ? AND setting_name = ?',
			array(
				$value,
				$id,
				$name,				
			)
		);
		return true;
	}

	function _getPrereviewData($id, $name) {
		$result = $this->retrieve(
			'SELECT setting_value
			FROM prereview_settings WHERE publication_id = ? AND setting_name = ?
			GROUP BY setting_value',
			array((int) $id, $name) 
		);
		$returner = $result->fields[0];
		$result->Close();
		return $returner;
	}

	function getDataPrereview($id) {
		$result = $this->retrieve(
			'SELECT setting_value
			FROM prereview_settings WHERE publication_id = ?',
			array((int) $id) 
		);
		$returner = $result->current();
		return $returner;
	}
	
	
}

