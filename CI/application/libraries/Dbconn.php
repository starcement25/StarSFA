<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dbconn{
		
	function create_db_conn($nick_name){
		$config['hostname'] = 'localhost';
		$config['username'] = 'acedns_dnsprod';
		$config['password'] = 'dnsprod1234#';
		$config['database'] = 'acedns_'.$nick_name.'';
		$config['dbdriver'] = 'mysqli';
		$config['dbprefix'] = '';
		$config['pconnect'] = FALSE;
		$config['db_debug'] = FALSE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = '';
		$config['char_set'] = 'utf8';
		$config['dbcollat'] = 'utf8_general_ci';
		
		return $config;
	}
}
?>