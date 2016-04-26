<?php
//session_start();
//$code = mt_rand(0,1000000);
//$_SESSION['code'] = $code;

//if(!$_SESSION["usercell"])
//{
//	header("location:do_login.php");
//}
include("header.inc.php");

?>

<form method="post" action="do_investigation_weekend.php" data-ajax="false">
  <fieldset data-role="controlgroup">
    <legend>请选择您更倾向于工作日还是周末上课:</legend>
      <label for="workday">工作日</label>
      <input type="radio" name="favtime[workday]" id="workday" value="workday">
     <label for="weekend">周末</label>
      <input type="radio" name="favtime[weekend]" id="weekend" value="weekend">
       <label for="both">工作日和周末都可以上课</label>
      <input type="radio" name="favtime[both]" id="both" value="both"> 
   </fieldset>  
   
      
      <input type="submit" name="favcolor" id="sub" value="提交">
  
</form>

</body>

</html>