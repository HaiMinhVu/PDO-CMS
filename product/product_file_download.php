<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../../../newconnect.php');
include('header.php');
include('product_function.php');

$temp = $_GET['id'];
if(!preg_match('#[^0-9]#',$temp)){
    $productid = $temp;
}
else{
    header('location:product.php');
}

$productinfo = product_info_by_id($cms_connect, $productid);
$associatedManualArr = associated_manual_by_product_id($cms_connect, $productid);

?>

<div class="content mt-3"> <!-- begining of display content -->
	<h1>Coming Soon ...</h1>
</div> <!-- end of display content -->




<?php
include('../footer.php');
?>