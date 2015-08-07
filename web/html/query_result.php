
<?php
// not need to include the file "constants.php" because it already included in "searchelastic.php"
include ("searchelastic.php");
$page_no                            = (empty($_POST['page'])) ? 0 : $_POST['page'];
$iquery      	                    = $_POST['initial_query'];
$user_query_string	                = $_POST['search'];// user query
$selected_brand_name                = $_POST['brand_name'];
$selected_os_name	                = $_POST['os_name'];
$selected_price_range               = $_POST['price_range'];
$selected_ram_range                 = $_POST['ram_range'];
$term_filter_input_array            = array();
$term_filter_input_array [BRAND]    = $selected_brand_name;
$term_filter_input_array [OS]       = $selected_os_name;
$range_filter_input_array           = array();
$range_filter_input_array [PRICE]   = $selected_price_range;
$range_filter_input_array [RAM]     = $selected_ram_range;
$es_client 		                    = new SearchElastic();
$ret_array 		                    = $es_client->perform_es($user_query_string, $term_filter_input_array, $range_filter_input_array, $iquery, $page_no);
if(!$ret_array[0][0]){ header(ERROR_PAGE_LOCATION);exit();} // if no result is found then redirect to this page
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
<!-- #############################################################  display filter in checkbox format ####################################################### -->
<div style="float:left; width:20%;">
<form  name="my_form" form id = "brand-fliter" action ="query_result.php" method="post"  >
	<H1>Brands</H1>
<?php for ($i = 0; $i < count($ret_array[1]); $i = $i+1)
    { ?>
    <input type="checkbox" name="brand_name[]"  onchange="change()" value="<?php echo $ret_array[1][$i]; ?>"<?php if(in_array($ret_array[1][$i],$selected_brand_name)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?>><?php echo $ret_array[1][$i]; ?><br>

        <?php
    }?>
    <H1>Price Range</H1>
    <?php for ($i = 0; $i < count($ret_array[2]); $i = $i+1)
    { $flag=$ret_array[2][$i][1];?>
        <?php if($flag)echo '<input type="checkbox" name="price_range[]" onchange="change()" value="';?><?php if($flag)echo $ret_array[2][$i][0]; ?><?php if($flag)echo'"'; if(in_array($ret_array[2][$i][0],$selected_price_range)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?><?php if($flag){echo '>';if($ret_array[2][$i][0]=="50000 - 1550000"){echo "50000 and Above";} else {echo $ret_array[2][$i][0];}} ?><?php if($flag)echo'<br>';?>

        <?php
    }?>
    <H1>OS</H1>
    <?php for ($i = 0; $i < count($ret_array[3]); $i = $i+1)
    { ?>
    <input type="checkbox" name="os_name[]"  onchange="change()" value="<?php echo $ret_array[3][$i]; ?>"<?php if(in_array($ret_array[3][$i],$selected_os_name)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?>><?php echo $ret_array[3][$i]; ?><br>

        <?php
    }?>
    <H1>Ram</H1>
    <?php for ($i = 0; $i < count($ret_array[4]); $i = $i+1)
    {  $flag=$ret_array[4][$i][1];?>
        <?php if($flag)echo '<input type="checkbox" name="ram_range[]" onchange="change()" value="';?><?php if($flag)echo $ret_array[4][$i][0]; ?><?php if($flag)echo'"'; if(in_array($ret_array[4][$i][0],$selected_ram_range)&&!(isset($_POST["search"]))) echo SHOWCHECKED ;?><?php if($flag){echo '>';if($ret_array[4][$i][0]=="4 - 15"){echo "4 GB and Above";} else {echo $ret_array[4][$i][0]." GB";}} ?><?php if($flag)echo'<br>';?>
        <?php
    }?>
    <input type="hidden"   name="initial_query" value="<?php echo $es_client->initial_user_query;?>">
    <input type="hidden"    id="page_id" 	name="page" value="<?php echo $page_no; ?>">

    <input type="submit" value="submit" class="buttons"  />
    <input type="submit" value="next" class="buttons" onclick="next_page()" />
    <?php if($page_no)echo '<input type="submit" value="prev" class="buttons" onclick="prev_page()" />';?>

    
</form>
    </div>




<div style="float:left; width:50%;">
<table>
<col width="1000">
  <col width="2500">
  <col width="200">
  <col width="200">
  <tr>
    <th>image</th>

    <th>name</th>
    
    <th>brand</th>
    
    <th>price</th>
    
    <th>view_details</th>
  </tr>
  <?php for ($i=0; ($ret_array[0][$i])&&($i<count($ret_array[0])); $i=$i+1){?>
      <tr>
      <td><img src="<?php echo $ret_array[0][$i][_source][I_LINK];?>" style="width:80px;height:150px"> </td>
        <?php $id =$ret_array[0][$i][_id]; ?>
        <td><?php echo $ret_array[0][$i][_source][NAME]?></td>
        <td><?php echo $ret_array[0][$i][_source][BRAND]?></td>
        <td><?php echo $ret_array[0][$i][_source][PRICE]?></td>
        

        <td><a href="details_page.php?id=<?php echo $id ?>">click here</a> </td>

      </tr>
      <?php } ?>
</table>
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