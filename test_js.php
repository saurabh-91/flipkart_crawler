<?php  include ("class_query.php");?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<table>
  <tr>
    <th>name</th>
    <th>brand</th>       
    <th>Price</th>
    <th>image</th>
    <th>view_details</th>
  </tr>
  <?php for ($i=0;$i<count($ret_array[0]);$i=$i+1){?>
      <tr> 
        <td><?php echo $ret_array[0][$i][_source][name]?></td>
        <td><?php echo $ret_array[0][$i][_source][brand]?></td>
        <td><?php echo $ret_array[0][$i][_source][price]?></td>      
        <td><img src="<?php echo $ret_array[0][$i][_source][i_link];?>" style="width:100px;height:228px"> </td>
        <?php $id =$ret_array[0][$i][_id]; ?>
    
        <td><a href="details_page.php?id=<?php echo $id ?>">click here</a> </td> 
        
      </tr>
      <?php } ?>
</table>



<!-- #############################################################  display filter in checkbox format ######################################################### -->
<form  name="my_form" form id = "brand-fliter" action ="test_js.php" method="get" >

<?php while (current($ret_array[1]))
    { ?>
    <input type="checkbox" name="brand_name[]" class="brand_id" value="<?php echo key($ret_array[1]); ?>"><?php echo key($ret_array[1])." = ".current($ret_array[1]); ?><br>
       
        <?php next($ret_array[1]);
    }?>
<!--
    <input type="hidden"   name="selecte[q]" id ="sel_brand" value="<?php echo $s_brand?>">
    <input type="hidden"   name="selecte[d]" id="sel_range"  value="<?php echo $s_range?>"> 
    -->

   

   <?php foreach($s_brand as $value)
{
  echo '<input type="hidden" name="selected_brand[]" value="'. $value. '">';
}?>
<?php foreach($s_range as $value)
{
  echo '<input type="hidden" name="selected_range[]" value="'. $value. '">';
}
?>-->
    <input type="hidden"   name="initial_query" value="<?php echo $ini_query?>">
    <input class="range_id" type="checkbox" name="price_range[]" value="8888" >0-10000(<?php echo $ret_array[2][0];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="18888" >10000-20000 (<?php echo $ret_array[2][1];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="28888" >20000-35000 (<?php echo $ret_array[2][2];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="38888" >35000-50000 (<?php echo $ret_array[2][3];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="48888" >50000 and above (<?php echo $ret_array[2][4];?>)<br>
    <input class="range_id" type="checkbox" name="price_range[]" value="-5" >price not listed (<?php echo $ret_array[2][5];?>)<br>
    <input class="range_id" type="submit" value="submit" class="buttons"/>
    
</form>
 <!--<button onmouseover ="myFunction()">Try it</button>

<p id="demo"></p>
<p id ="t"></p>-->
<!--
<script >
function myFunction() {
	var checkedValue = []; 
	var x=null;
var inputElements = document.getElementsByClassName('brand_id');
for(var i=0; inputElements[i]; ++i){
      if(inputElements[i].checked){
           x = inputElements[i].value;
           checkedValue.push(x);
      }
}
document.my_form.selecte.value = checkedValue;
document.getElementById("t").innerHTML = checkedValue;
checkedValue = []; 
inputElements = document.getElementsByClassName('range_id');
for(var i=0; inputElements[i]; ++i){
      if(inputElements[i].checked){
           x = inputElements[i].value;
           checkedValue.push(x);
      }
}
//checkedValue.toString();
document.getElementById("demo").innerHTML = checkedValue;
}
</script>

-->

<!-- ##########################################################################################################################################  -->


</body>
</html>

</body>
</html>



<!--<!DOCTYPE html>
<html>
<body>

Checkbox: <input type="checkbox" id="myCheck" value="myvalu">


<input type="checkbox" name="brand_name" id="check" value="yeh"><br>

    <input type="hidden"   name="selected[r]"  id= "t" value="<?php echo $s_range?>"> 

<p>Click the "Try it" button to display the value of the value attribute of the checkbox.</p>

<button onclick="myFunction()">Try it</button>

<p id="demo"></p>
<p id ="t"></p>

<script>
function myFunction() {
    var x = document.getElementById("check").value;
    document.getElementById("t").innerHTML = x;
}
</script>

</body>
</html>-->
