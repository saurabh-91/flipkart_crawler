<!DOCTYPE html>
<!-- ####################################################### some of html BC #######################################################-->
<html>
<head>
    <title></title>
</head>
<body>


<!-- #################################################### partial end  of html BC  ################################################-->




<!-- #################################################### start of main php program ##############################################-->
<?php



    
//#################################################   some global veriables ##########################################################
    require 'vendor/autoload.php';
    $ini_query="";// initial search query which will be maintained after applying filter
    $ret_array=array(); // global array for storing  arrays of results

//#################################################################################################################################





//############################################# function for home page search   ###################################################
     if (isset($_POST['search']))
    {
        $query=$_POST["search"];
        $ini_query=$query;
        $searchParams=search($query); 
        $ret_array=common($searchParams);

    }
    else
    {
        $bname=array();
        $ini_query=$_GET['initial_query'];
         foreach($_GET['brand_name'] as $temp)
        {
            array_push($bname,$temp);
        }
        $range_array=array();
        $lower_limit=0;
        $upper_limit=10000;
        $i=0;
        foreach ($_GET['price_range'] as $temp) 
        {

            $filter=array();
            //var_dump($temp);

            /*if($temp>0)
            {

                if($i==0)
                {
                    $filter["range"]['price']['gte']=$lower_limit;
                    $filter["range"]['price']['lt']=$upper_limit;
                    //var_dump($filter);
                    //echo "filter";
                    array_push($range_array, $filter);
                }else
                {
                    $filter["range"]['price']['gte']=10000;
                    $filter["range"]['price']['lt']=100000;
                    array_push($range_array, $filter);
                }
            }*/
            switch ($temp) {
                case ($temp>=0&&$temp<10000):
                    $filter["range"]['price']['gte']=$lower_limit;
                    $filter["range"]['price']['lt']=$upper_limit;
                    break;
                
                default:
                     $filter["range"]['price']['gte']=10000;
                    $filter["range"]['price']['lt']=100000;
                    break;
            }
            array_push($range_array, $filter);
            //$lower_limit+=10000;
            //$upper_limit+=10000;
            $i+=1;
        }
        //echo "start";
        //var_dump($range_array);
        //echo "dumped";
        $searchParams=filter_function($bname,$ini_query,$range_array);
        $ret_array=common($searchParams); 
    }


    function search($query) 
    {
        $searchParams = array();
        $searchParams['index'] = "flipkart1";
        $searchParams['type']  = "mobile";
        $searchParams['body']['query']['match']['name'] = $query;
        $searchParams['size']=2000;
        return $searchParams;        
    }

//##################################################################################################################################

//####################################################   filter function  ############################################################
    function filter_function($bname,$ini_query,$range_array)
    {
        $searchParams = array();
        $searchParams['index'] = "flipkart1";
        $searchParams['type']  = "mobile";
        $query=array();
        $query['terms']['brand']=$bname;
        $filter=array();
        $filter['bool']['should']=$range_array;
        $must_array=array();
        array_push($must_array,$query);
        array_push($must_array,$filter);
        $searchParams['body']['query']['filtered']['filter']['bool']['must']=$must_array;
        $searchParams['body']['query']['filtered']['query']['match']['name']=$ini_query;
        $searchParams['body']['size']=2000;

        var_dump(json_encode($searchParams));
        //die(3);
        return $searchParams;
    }
//####################################################################################################################################




//###################################### common function used by both search and filter function #####################################  
    function common($searchParams)
    {
        $client = new Elasticsearch\Client(); // create a elastcsearch client
        $retDoc = $client->search($searchParams);//echo json_encode($retDoc);
        $retDoc=$retDoc[hits][hits];
        $price=array();
        $price['index']="flipkart1";
        $price['type']="mobile";
        for ($i=0;$i<10;$i=$i+1)
        {
            $table_array[$i]=$retDoc[$i];
        }
        for ($i=0;$i<count($retDoc);$i=$i+1)
        {    
            $temp=$retDoc[$i][_source][brand];
            $temp1=$retDoc[$i][_source][price];
            if(!array_key_exists($temp, $brand_list_count))
            {
                $brand_list_count[$temp]=0;
            }
            switch ($temp1) {
                case ($temp1<10000):
                    $price_range[0]+=1;
                    break;
                
                default:
                    $price_range[1]+=1;
                    break;
            }
            $brand_list_count[$temp]+=1;
        }
        return array($table_array,$brand_list_count,$price_range);
    }

//#########################################################################################################################################


?>


<!--##################################### end of php and start of html tables and checkboxes ############################################# -->





<!-- ###########################################   print results in tabular format ##################################################################-->

<table>
  <tr>
    <th>name</th>
    <th>brand</th>       
    <th>Price</th>
    <th>image</th>
    <th>view_details</th>
  </tr>
  <?php for ($i=0;$i<=count($ret_array[0]);$i=$i+1){?>
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
<form form id = "brand-fliter" action ="query.php" method="get">

<?php while (current($ret_array[1]))
    { ?>
    <input type="checkbox" name="brand_name[]" value="<?php echo key($ret_array[1]); ?>"><?php echo key($ret_array[1])." = ".current($ret_array[1]); ?><br>
       
        <?php next($ret_array[1]);
    }?>
    <input type="hidden" name="initial_query" value="<?php echo $ini_query?>">
    <input type="checkbox" name="price_range[]" value="<?php echo $ret_array[2][0];?>" >0-10000(<?php echo $ret_array[2][0];?>)<br>
    <input type="checkbox" name="price_range[]" value="<?php echo $ret_array[2][1];?>" >100000 and above (<?php echo $ret_array[2][1];?>)
    <!--<input type="checkbox" name="price_range[]" >20000-30000(<?php echo $ret_array[2][0];?>)<br>
    <input type="checkbox" name="price_range[]" >30000-40000(<?php echo $ret_array[2][1];?>)
    <input type="checkbox" name="price_range[]" >40000-50000(<?php echo $ret_array[2][0];?>)<br>
    <input type="checkbox" name="price_range[]" >10000-100000(<?php echo $ret_array[2][1];?>)-->
    <input type="submit" value="submit" class="buttons"/>
    
</form>

<!-- ##########################################################################################################################################  -->


</body>
</html>