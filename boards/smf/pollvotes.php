<?php
/**
 * MyBB 1.8 Merge System
 * Copyright 2014 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybb.com
 * License: http://www.mybb.com/download/merge-system/license/
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

class SMF_Converter_Module_Pollvotes extends Converter_Module_Pollvotes {

	var $settings = array(
		'friendly_name' => 'poll votes',
		'progress_column' => 'ID_POLL',
		'default_per_screen' => 1000,
	);

	function import()
	{
		global $import_session;

		$query = $this->old_db->simple_select("log_polls", "*", "", array('limit_start' => $this->trackers['start_pollvotes'], 'limit' => $import_session['pollvotes_per_screen']));
		while($pollvote = $this->old_db->fetch_array($query))
		{
			$this->insert($pollvote);
		}
	}

	function convert_data($data)
	{
		$insert_data = array();

		// SMF values
		$poll = $this->get_import->poll($data['ID_POLL']);

		$insert_data['uid'] = $this->get_import->uid($data['ID_MEMBER']);
		$insert_data['dateline'] = $poll['dateline'];
		$insert_data['voteoption'] = $data['ID_CHOICE']+1;
		$insert_data['pid'] = $poll['pid'];

		return $insert_data;
	}

	function fetch_total()
	{
		global $import_session;

		// Get number of poll votes
		if(!isset($import_session['total_pollvotes']))
		{
			$query = $this->old_db->simple_select("log_polls", "COUNT(*) as count");
			$import_session['total_pollvotes'] = $this->old_db->fetch_field($query, 'count');
			$this->old_db->free_result($query);
		}

		return $import_session['total_pollvotes'];
	}
}


