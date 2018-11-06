<?php	
	if(!function_exists('week_name'))
	{
		function week_name($v)
		{
			$weekarray=array("日","一","二","三","四","五","六");
			
			return  "星期".$weekarray[date("w",strtotime($v))];
		}
		
		function week_name2($v)
		{
			$weekarray=array("日","一","二","三","四","五","六");
			
			return  $weekarray[date("w",strtotime($v))];
		}
		
		function is_work_day($date)
		{
			$jiejiari[] = array('m'=>1,'d'=>1);
			$jiejiari[] = array('m'=>1,'d'=>2);
			$jiejiari[] = array('m'=>1,'d'=>3);
			$jiejiari[] = array('m'=>2,'d'=>18);
			$jiejiari[] = array('m'=>2,'d'=>19);
			$jiejiari[] = array('m'=>2,'d'=>20);
			$jiejiari[] = array('m'=>2,'d'=>21);
			$jiejiari[] = array('m'=>2,'d'=>22);
			$jiejiari[] = array('m'=>2,'d'=>23);
			$jiejiari[] = array('m'=>2,'d'=>24);
			$jiejiari[] = array('m'=>4,'d'=>4);
			$jiejiari[] = array('m'=>4,'d'=>5);
			$jiejiari[] = array('m'=>4,'d'=>6);
			$jiejiari[] = array('m'=>5,'d'=>1);
			$jiejiari[] = array('m'=>5,'d'=>2);
			$jiejiari[] = array('m'=>5,'d'=>3);
			$jiejiari[] = array('m'=>6,'d'=>20);
			$jiejiari[] = array('m'=>6,'d'=>21);
			$jiejiari[] = array('m'=>6,'d'=>22);
			$jiejiari[] = array('m'=>9,'d'=>3);
			$jiejiari[] = array('m'=>9,'d'=>26);
			$jiejiari[] = array('m'=>9,'d'=>27);
			$jiejiari[] = array('m'=>10,'d'=>1);
			$jiejiari[] = array('m'=>10,'d'=>2);
			$jiejiari[] = array('m'=>10,'d'=>3);
			$jiejiari[] = array('m'=>10,'d'=>4);
			$jiejiari[] = array('m'=>10,'d'=>5);
			$jiejiari[] = array('m'=>10,'d'=>6);
			$jiejiari[] = array('m'=>10,'d'=>7);
			$result = true;
			foreach($jiejiari as $k=>$v)
			{
				if((intval(date("m",strtotime($date)))==intval($v['m'])) &&(intval(date("d",strtotime($date)))==intval($v['d'])))
				{
					$result = false;
					break;
				}
			}
			
			if(!$result) return false;
			return in_array(date("w",strtotime($date)),array(1,2,3,4,5));
		}
		
		function is_off_day($date)
		{
			return !in_array(date("w",strtotime($date)),array(1,2,3,4,5));
		}
	}
?>	