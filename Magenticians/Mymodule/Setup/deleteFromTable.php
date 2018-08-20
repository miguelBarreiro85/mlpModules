<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 10-07-2018
 * Time: 20:18
 */
$con=mysqli_connect("127.0.0.1","magento","magento","magento", "8580");
// Check connection
if (mysqli_connect_errno())
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Perform queries
$attributes = include ("attributes.php");
foreach ($attributes as $attribute){
    print_r($attribute['label']);
    mysqli_query($con,"DELETE FROM `eav_attribute_set` WHERE `attribute_set_name`=".$attribute['label']);
}
mysqli_close($con);
exit();
?>