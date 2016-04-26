<?php
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	include("functions.inc.php");
	$db = new PDO($dsn, $dbuser,  $dbpass);
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	$dt=date('Ymd');
	$rate=3;//This is for available time range. class count/rate x 7 + days +(31days for missed class)
	echo "<b>莫圣瑜伽</b> 已选课程包列表：";
	if( $_POST ){ 
		 $choices = $_POST['t1']; 
		 $mid = $_POST['mid'];
		 $discount=$_POST['discount'];
		 $where=" WHERE package_id='0' ";
		 
		 
		 for($i=0;$i<count($choices);$i++){
			 $where=$where." or package_id='{$choices[$i]}'";
			   
	     }
		$where=$where.";";
	    $sqlviewsub ="SELECT * FROM `yoga_lu`.`package_show`".$where;		
		$db->query('set names UTF8'); 
		$resviewsub = $db->query($sqlviewsub);
        
		
		?>

		<table width="99%" border="0" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
		<thead><tr style="border:1px solid #f7900f;background-color:#fff1cc;height:25px;">
		<td style="border:1px solid #f7900f;background-color:#fff1cc;height:25px;">课程名</td>
		<td style="border:1px solid #f7900f;background-color:#fff1cc;height:25px;">价格</td>
		</tr>
		</thead>
		<tbody>

<?php
				$total=0;
		$total1=0;
		$total2=0;
		$totalcount=0;
		$totalcount1=0;
		$totalcount2=0;
		$rate=10;
		$rowviewsubs=$resviewsub->fetchAll();
		foreach($rowviewsubs as $rowviewsub) {		
				
			echo "<tr style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>"; 
			echo "<td style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'><b>{$rowviewsub['package_name']}</b>";
			//echo substr($rowviewsub['package_id'],0,7);
			
			echo "</td>";
			echo "<td style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>{$rowviewsub['package_price']}</td>";
			echo "</tr > ";
			
			if((substr($rowviewsub['package_id'],0,7)=='package') or (substr($rowviewsub['package_id'],0,5)=='suite')){
			$totalcount1=$totalcount1+$rowviewsub['package_course_count'];
			$total1=$total1+$rowviewsub['package_price'];
			
			}
			else{
			$totalcount2=$totalcount2+$rowviewsub['package_course_count'];
			$total2=$total2+$rowviewsub['package_price'];
				
			}
			
		}
		$total=$total1+$total2;
		
	$final=$total*$discount;
	$discountP=100*$discount;
		
        echo "<tr style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>";
		echo "<td style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>课时(课程包部分)</td><td>{$totalcount1}</td>";
			echo "</tr > ";
		echo "<tr style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>";
		echo "<td style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>有效期天数(课程包部分)</td><td>";
		echo ceil($totalcount1/$rate*30.5+($totalcount1/$rate)*4);
		echo "</td>";
			echo "</tr > ";
		echo "<tr style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>";
		echo "<td style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>课时(非课程包部分)</td><td>{$totalcount2}</td>";
		echo "</tr > ";
		echo "<tr style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>";
		echo "<td style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>总价</td><td>{$total}</td>";
			echo "</tr > ";
 		echo "<tr style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>";
		echo "<td style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>折扣</td><td>{$discountP}%</td>";
		    echo "</tr > ";
 		echo "<tr style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>";
		echo "<td style='border:1px solid #f7900f;background-color:#fff1cc;height:25px;'>实际价格</td><td>{$final}</td>";
		
		echo "</tr > ";
 		
		
	
		 
	}
	else {echo "no choice";}
	 
?>

</tbody></table>