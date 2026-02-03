<?php
class Function_include{
	function return_no_days($val1,$val2)
	{
		if($val2=='M')
		{
			$countday=0;
			for($i=1;$i<=date('d');$i++)
			{
				$no_of_date=date('Y').'/'.date('m').'/'.$i;
				$week_day=date('l', strtotime($no_of_date));
				if($week_day!='Sunday')
				{
					$countday++;
				}
					
			}
		}
		if($val2=='Y')
		{
			$countday=0;
			for($i=1;$i<=$val1;$i++)
			{
				$start_date = strtotime("2012/06/16");
				$date = strtotime(date("Y/m/d", strtotime($start_date)) . " +$i day");
				$date=date("Y/m/d",$date);
				$week_day=date('l', strtotime($date));
				if($week_day!='Sunday')
				{
					$countday++;
				}
			}
		}
		return $countday;
	}
}
?>