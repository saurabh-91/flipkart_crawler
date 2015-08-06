
<?php
// not need to include the file "constants.php" because it already included in "details_page_main.php"
include "details_page_main.php";
$product_id = $_GET['id'];
$client_redis= new RedisResult();
$details=$client_redis->get_detail_from_redis($product_id);
?>
<!DOCTYPE html>
<html><head></head>
<body>
<div style="float:left; width:30%;">
<img src="<?php echo $details[I_LINK];?>" style="width:180px;height:400px">
    </div>
<div style="float:left; width:70%;">
<h4>Name: </h4>
<?php echo $details[NAME];?><br>
    <h4>price: </h4>
    <?php echo $details[PRICE];?><br>
    <h4>brand: </h4>
    <?php echo $details[BRAND];?><br>
    <h4>operating system: </h4>
    <?php echo $details[OS];?><br>
    <h4>Ram: </h4>
    <?php echo $details[RAM]." GB";?><br>
    <a href="<?php echo $details[LINK] ?>"><H2>buy now</H2></a>
</div>
<br><br><br><br><br><br><br><br>

<B>GENERAL FEATURES  </B><br>
<?php echo nl2br($details['GENERAL FEATURES:']);?>
<br><br>


<B>CAMERA  </B><br>
<?php echo nl2br($details['Camera:']);?>
<br><br>


<B>Multimedia  </B><br>
<?php echo nl2br($details['Multimedia:']);?>
<br><br>


<B>Internet and connectivity  </B><br>
<?php echo nl2br($details['Internet & Connectivity:']);?>
<br><br>


<B>Others Features   </B><br>
<?php echo nl2br($details['Other Features:']);?>
<br><br>


<B>Display  </B><br>
<?php echo nl2br($details['Display:']);?>
<br><br>


<B>Dimensions </B><br>
<?php echo nl2br($details['Dimensions:']);?>
<br><br>


<B>Warranty </B><br>
<?php echo nl2br($details['Warranty:']);?>
<br><br>


<B>Battry </B><br>
<?php echo nl2br($details['Battery:']);?>
<br><br>


<B>MEMORY </B><br>
<?php echo nl2br($details['Memory and Storage:']);?>
<br><br>


<B>Platform </B><br>
<?php echo nl2br($details['Platform:']);?>
<br><br>
</body>
</html>
