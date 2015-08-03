
<?php
include ("main.php");
include ("constants.php");
$page_no=0;
$iquery      	      = $_POST['initial_query'];
$user_query_string	  = $_POST['search'];// user query
$selected_brand_name  = $_POST['brand_name'];
$selected_os_name	  = $_POST['os_name'];
$selected_price_range = $_POST['price_range'];
$selected_ram_range   = $_POST['ram_range'];
if(!isset($user_query_string)) // if search is not perform
{
	$page_no       	  = $_POST['page'];
}
$es_client 		 =  new SearchElastic();
$ret_array 		 =  $es_client->perform_es($user_query_string, $selected_brand_name, $iquery, $selected_price_range, $page_no, $selected_os_name, $selected_ram_range);
if(!$ret_array[0][0]){ header("Location: http://localhost/flipkart/html/error_page.html");exit();}
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<!-- ###############################   search box ############################################################################## -->
<form action="query_result.php" method="post">
Search:<br>
<input type="text" name="search">
<br>
<input type="submit" value="Submit">
</form>

<!-- ########################################################################################################################### -->
<div style="float:left; width:50%;">
<table>
  <tr>
    <th>name</th>
    <th>brand</th>
    <th>Price</th>
    <th>image</th>
    <th>view_details</th>
  </tr>
  <?php for ($i=0; ($ret_array[0][$i])&&($i<count($ret_array[0])); $i=$i+1){?>
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
</div>
<!-- #############################################################  display filter in checkbox format ####################################################### -->
<div style="float:left; width:13%;">
<form  name="my_form" form id = "brand-fliter" action ="query_result.php" method="post"  >
	<H1>Brands</H1>
<?php for ($i = 0; $i < count($ret_array[1]); $i = $i+1)
    { ?>
    <input type="checkbox" name="brand_name[]"  onchange="change()" value="<?php echo $ret_array[1][$i]; ?>"<?php if(in_array($ret_array[1][$i],$selected_brand_name)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?>><?php echo $ret_array[1][$i]; ?><br>

        <?php
    }?>
</div>
<div style="float:left; width:13%;">
    <H1>Price Range</H1>
    <?php for ($i = 0; $i < count($ret_array[2]); $i = $i+1)
    { ?>
        <input type="checkbox" name="price_range[]" onchange="change()" value="<?php echo $ret_array[2][$i]; ?>"<?php if(in_array($ret_array[2][$i],$selected_price_range)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?>><?php if($ret_array[2][$i]=="50000 - 1550000"){echo "50000 and Above";} else {echo $ret_array[2][$i];} ?><br>

        <?php
    }?>
</div>
<div style="float:left; width:13%;">
    <H1>OS</H1>
    <?php for ($i = 0; $i < count($ret_array[3]); $i = $i+1)
    { ?>
    <input type="checkbox" name="os_name[]"  onchange="change()" value="<?php echo $ret_array[3][$i]; ?>"<?php if(in_array($ret_array[3][$i],$selected_os_name)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?>><?php echo $ret_array[3][$i]; ?><br>

        <?php
    }?>
    </div>
<div style="float:left; width:13%;">
    <H1>Ram</H1>
    <?php for ($i = 0; $i < count($ret_array[4]); $i = $i+1)
    { ?>
        <input type="checkbox" name="ram_range[]" onchange="change()" value="<?php echo $ret_array[4][$i]; ?>"<?php if(in_array($ret_array[4][$i],$selected_ram_range)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?>><?php if($ret_array[4][$i]=="4 - 15"){echo "4 GB and Above";} else {echo $ret_array[4][$i]." GB";} ?><br>

        <?php
    }?>
    <input type="hidden"   name="initial_query" value="<?php echo $es_client->initial_user_query;?>">
    <input type="hidden"    id="page_id" 	name="page" value="<?php echo $page_no; ?>">

    <input type="submit" value="submit" class="buttons"  />
    <input type="submit" value="next" class="buttons" onclick="next_page()" />
    <?php if($page_no)echo '<input type="submit" value="prev" class="buttons" onclick="prev_page()" />';?>

    
</form>
    </div>

<script type="text/javascript">
	var flag=0;
	function change () 
	{
		flag=1;
		var elem   = document.getElementById("page_id");
			elem.value=0;		
	}
	function next_page() 
	{
		if(!flag)
		{
			var elem   = document.getElementById("page_id");
			elem.value = parseInt(elem.value)+1;
			
		}
	}
	function prev_page () 
	{
		if(!flag)
		{
			var elem   = document.getElementById("page_id");
			elem.value = parseInt(elem.value)-1;
			
		}
	}


</script>
</body>
</html>

</body>
</html>