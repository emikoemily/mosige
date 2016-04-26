<?php
$prize_arr = array(
	'0' => array('id'=>1,'prize'=>'Mosige现金红包200元','v'=>10),
	'1' => array('id'=>2,'prize'=>'Mosige现金红包100元','v'=>35),
	'2' => array('id'=>3,'prize'=>'Mosige现金红包50元','v'=>55),

	 
);


foreach ($prize_arr as $key => $val) {
	$arr[$val['id']] = $val['v'];
}
//print_r($arr);

$rid = getRand($arr); //根据概率获取奖项id
$res['msg'] = ($rid==6)?0:1; 
$res['prize'] = $prize_arr[$rid-1]['prize']; //中奖项
echo json_encode($res);exit;


//计算概率
function getRand($proArr) {
	$result = '';

	//概率数组的总概率精度
	$proSum = array_sum($proArr);

	//概率数组循环
	foreach ($proArr as $key => $proCur) {
		$randNum = mt_rand(1, $proSum);
		if ($randNum <= $proCur) {
			$result = $key;
			break;
		} else {
			$proSum -= $proCur;
		}
	}
	unset ($proArr);

	return $result;
}
?>