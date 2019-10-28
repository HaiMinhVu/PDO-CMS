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
include('subheader.php');

$product_availability = product_availability($cms_connect, $productid);

?>


<div class="tab-content">
	<form method="POST" id="product_availability_form" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<table class="table table-hover">
					<tr>
						<td>Status (active)</td>
						<td><input type="checkbox" name="status" <?php echo $product_availability['status'] == "1" ? "checked" : ""?>></td>
					</tr>
					<tr>
						<td>Company</td>
						<td><input type="checkbox" name="company" <?php echo $product_availability['company_res'] == "1" ? "checked" : ""?>></td>
					</tr>
					<tr>
						<td>Dealer</td>
						<td><input type="checkbox" name="dealer" <?php echo $product_availability['dealer_res'] == "1" ? "checked" : ""?>></td>
					</tr>
					<tr>
						<td>Consumer</td>
						<td><input type="checkbox" name="consumer" <?php echo $product_availability['consumer_res'] == "1" ? "checked" : ""?>></td>
					</tr>
					<tr>
						<td>Vendor</td>
						<td><input type="checkbox" name="vendor" <?php echo $product_availability['vendor_res'] == "1" ? "checked" : ""?>></td>
					</tr>
					<tr>
						<td>NS Status</td>
						<td><input type="checkbox" name="nsstatus" <?php echo $product_availability['ns_active'] == "1" ? "checked" : ""?> disabled></td>
					</tr>
					<tr>
						<td>NS Product Feed</td>
						<td><input type="checkbox" name="nsproductfeed" <?php  echo $product_availability['ns_inactive'] == "1" ?"checked" : ""?> disabled></td>
					</tr>
					<tr>
						<td>NS Display</td>
						<td><input type="checkbox" name="nsdisplay" <?php echo $product_availability['ns_webstore_active'] == "1" ? "checked" : ""?> disabled></td>
					</tr>
					<tr>
						<td>Is New (NS)</td>
						<td><input type="checkbox" name="isnewns" <?php echo $product_availability['ns_isnew'] == "1" ? "checked" : ""?> disabled></td>
					</tr>
					<tr>
						<td>Is New (CMS)</td>
						<td><input type="checkbox" name="isnewcms" <?php echo $product_availability['cms_isnew'] == "1" ? "checked" : ""?> disabled></td>
					</tr>
					<tr>
						<td>MSRP</td>
						<td>$<?php echo $product_availability['msrp']?></td>
					</tr>
					<tr>
						<td>Online Price</td>
						<td>$<?php echo $product_availability['online_price']?></td>
					</tr>
					<tr>
						<td>Savings Message</td>
						<td><?php echo $product_availability['savings'] == "1" ? "".number_format($product_availability['savings_pct']+0.5,0)."% OFF!" : ""?></td>
					</tr>
					<tr>
						<td>Quantity on Hand</td>
						<td><?php echo $product_availability['quantity'] ?></td>
					</tr>
				</table>

				<div style="text-align:center"> <!-- button submit form -->
			    	<button type="submit" class="btn btn-outline-info" title="Save" ><i class="fa fa-floppy-o"></i>&nbsp; Save</button>
			    </div>
			</div>
		</div>
	</form>
</div>




<script type="text/javascript">
$(document).ready(function(){
	$('#product_availability').addClass('active');

	$(window).on("load", function(){
        if(localStorage.getItem('update_availability_result') != null){
            $('#alert_action').fadeIn().html('<div class="alert alert-info">'+localStorage.getItem('update_availability_result')+'</div>');
            localStorage.removeItem('update_availability_result');
        }
	});

	$('#product_availability_form').submit(function(e){
    	event.preventDefault();
        var data = new FormData(this);
        data.append("action", "update_availability");
        data.append("productid", "<?php echo $productid?>");
        $.ajax({
            type:"post",
            url:"product_action.php",
            data:data,
        	contentType: false,
            cache: false,
            processData:false,
            success: function(mess){
            	localStorage.setItem("update_availability_result", mess);
                window.location.reload(); 
            }
        });
    });

});
</script>

<?php
include('subfooter.php');
include('../footer.php');
?>