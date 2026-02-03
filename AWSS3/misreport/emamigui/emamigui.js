function get_category_data(category){
	//alert(category);
	if(category == 'HOME')
		var location_href = 'emamigui.php';
	else if(category == 'EMPLOYEE')
		var location_href = 'emami_empwise.php';
	else if(category == 'STATE')
		var location_href = 'emami_statewise.php';
	else if(category == 'DEPOT')
		var location_href = 'emami_depotwise.php';
	else if(category == 'PLANT')
		var location_href = 'emami_plantwise.php';
	else if(category == 'ZONE')
		var location_href = 'emami_zonewise.php';
		
	window.location.href = ''+location_href+'';
}

function emp_scale_wise(emp_scale){
	var location_href = 'emami_empwise.php?level_short='+emp_scale+'';
	window.location.href = ''+location_href+'';
}