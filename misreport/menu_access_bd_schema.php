<?php
/*-------> Schema and rules for the BD menu restriction list (menu_access_bd) <-------
 * A row means: emp_code is NOT PERMITTED to see not_accessible_menu.
 * Keys are either a survey_sub_menu name, or "Tech_Meet_<row_id>" for a Technical Meet type.
 * No row means permitted: a ticked box restricts a type, an unticked box allows it, so an
 * employee with nothing ticked sees every Type / Technical Meet Type.
 */

if(!function_exists('ensure_menu_access_bd_schema'))
{
	function ensure_menu_access_bd_schema($link)
	{
		$sql_create_table = "CREATE TABLE IF NOT EXISTS menu_access_bd (
			emp_code VARCHAR(50) NOT NULL,
			not_accessible_menu VARCHAR(100) NOT NULL,
			PRIMARY KEY (emp_code, not_accessible_menu)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		mysqli_query($link, $sql_create_table);

		/* A previous build stored the inverse (accessible_menu). Those rows now mean the
		   opposite of what they say, so clear them instead of carrying them over. */
		$res_old_column = mysqli_query($link, "SHOW COLUMNS FROM menu_access_bd LIKE 'accessible_menu'");
		if($res_old_column && mysqli_num_rows($res_old_column) > 0)
		{
			mysqli_query($link, "TRUNCATE TABLE menu_access_bd");
			mysqli_query($link, "ALTER TABLE menu_access_bd CHANGE accessible_menu not_accessible_menu VARCHAR(100) NOT NULL");
		}
	}

	function get_restricted_bd_menus($link, $emp_code)
	{
		$restricted_menus = array();
		$sql_restricted_menu = "SELECT not_accessible_menu FROM menu_access_bd WHERE emp_code = '".mysqli_real_escape_string($link, $emp_code)."' ORDER BY not_accessible_menu ASC";
		$res_restricted_menu = mysqli_query($link, $sql_restricted_menu);
		if($res_restricted_menu)
		{
			while($row_restricted_menu = mysqli_fetch_assoc($res_restricted_menu))
			{
				$restricted_menus[] = $row_restricted_menu['not_accessible_menu'];
			}
		}
		return $restricted_menus;
	}

	function is_tech_meet_bd_menu($menu_key)
	{
		return (strpos($menu_key, 'Tech_Meet_') === 0);
	}

	/* Ticked (a stored row) means restricted, unticked means allowed. */
	function bd_menu_is_restricted($menu_key, $restricted_menus)
	{
		return in_array($menu_key, $restricted_menus);
	}
}
?>
