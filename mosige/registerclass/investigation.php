<?php
session_start();
//$code = mt_rand(0,1000000);
//$_SESSION['code'] = $code;

if(!$_SESSION["usercell"])
{
	header("location:do_login.php");
}
include("header.inc.php");

?>
<b>亲爱的小伙伴，</br>为了我们能够完善学习规划，更加科学有效地给每一个人定制课时和进度，麻烦花1分钟填选下面的的调查问卷：</b>
</br></br>
<form method="post" action="do_investigation.php" data-ajax="false">
  <fieldset data-role="controlgroup" data-inline="false">
    <legend>请谨慎选择<b>所有</b>您可以上课的时间，这将成为排课的重要参考（多选）:</legend>
    
      	<label for="1030">10:30:00(工作日)</label>
      <input type="checkbox" name="favtime[1030]" id="1030" value="10:30:00">
      
       	<label for="1030w">10:30:00(周末)</label>
      <input type="checkbox" name="favtimew[1030]" id="1030w" value="10:30:00">
      
    	 <label for="1200">12:00:00(工作日)</label>
       <input type="checkbox" name="favtime[1030]" id="1200" value="10:30:00">
       
     	<label for="1200w">12:00:00(周末)</label>
      <input type="checkbox" name="favtimew[1200]" id="1200w" value="12:00:00">
      
     	 <label for="1400">14:00:00(工作日)</label>
       <input type="checkbox" name="favtime[1200]" id="1400" value="14:00:00">
       
      	<label for="1400w">14:00:00(周末)</label>
      <input type="checkbox" name="favtimew[1400]" id="1400w" value="14:00:00">
      
      	<label for="1730">17:30:00(工作日)</label>
       <input type="checkbox" name="favtime[1730]" id="1730" value="17:30:00">
       
     	 <label for="1730w">17:30:00(周末)</label>
      <input type="checkbox" name="favtimew[1730]" id="1730w" value="17:30:00"> 
      
      	<label for="1845">18:45:00(工作日)</label>
      <input type="checkbox" name="favtime[1730]" id="1845" value="18:45:00"> 
      
     	 <label for="1845w">18:45:00(周末)</label>
      <input type="checkbox" name="favtimew[1845]" id="1845w" value="18:45:00"> 
      
       <label for="1945">19:45:00(工作日)</label>
      <input type="checkbox" name="favtime[1945]" id="1945" value="19:45:00"> 
    
          <label for="1945w">19:45:00(周末)</label>
      <input type="checkbox" name="favtimew[1945]" id="1945w" value="19:45:00"> 
       <label for="2000">20:00:00(工作日)</label>
      <input type="checkbox" name="favtime[2000]" id="2000" value="20:00:00"> 
    
          <label for="2000w">20:00:00(周末)</label>
      <input type="checkbox" name="favtimew[2000]" id="2000w" value="20:00:00"> 
      <label for="0000">以上全部不合适</label>
      <input type="checkbox" name="favtime[0000]" id="0000" value="00:00:00"> 
   </fieldset>  
   倾向于白天（上午中午）还是晚上上课呢？
  <fieldset data-role="controlgroup" data-inline="true">
  <label for="selfday">白天</label>
    <input type="checkbox" name="selftime[selfday]" id="selfday" value ="day" > 
  <label for="selfnight">晚上</label>
       <input type="checkbox" name="selftime[selfnight]" id="selfnight"  value ="night"> 
    </fieldset> 
   
   如果上述时间都不合适，请在下面的输入框填写你自己期望的上课时间要求，越清晰明确越好~(例如：只能白天/周一到周四上午周五晚上/周末任何时候): 
       <textarea name="selftime[self1]" id="self1" placeholder=""></textarea>
  
       <textarea name="selftime[self2]" id="self2" placeholder=""></textarea>
     
      <textarea name="selftime[self3]" id="self3" placeholder=""></textarea>
      
      
      
      
      
      <input type="submit" name="favcolor" id="sub" value="提交">
  
</form>

</body>

</html>