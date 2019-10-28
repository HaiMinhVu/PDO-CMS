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
    //$productid = "";
    header('location:product.php');
}

$productinfo = product_info_by_id($cms_connect, $productid);
$productList = product_list_by_brand($cms_connect, $productinfo['manufacture']);
// feature product
$feature = '';
$featureArr = product_feature_by_id($cms_connect, $productid);
foreach($featureArr as $item){
	$feature .= $item.',';	
}
$feature = rtrim($feature,',');

// included product
$included = "";
$includedArr = product_included_by_id($cms_connect, $productid);
foreach($includedArr as $item){
	$included .= $item.',';	
}
$included = rtrim($included,',');

// related product
$related = '';
$relatedArr = product_related_by_id($cms_connect, $productid);
foreach($relatedArr as $item){
	$related .= $item.',';	
}
$related = rtrim($related,',');

include('subheader.php');
?>


<div class="tab-content">
	<div class="tab-pane active"> 
		<form method="POST" id="product_extra_form" enctype="multipart/form-data">
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Product Feature</div>
        		<div class="col-md-8"><input type="text" name="product_feature" id="product_feature" value="<?php echo $feature;?>"></div>
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Product Included</div>
        		<div class="col-md-8"><input type="text" name="product_included" id="product_included" value="<?php echo $included;?>" ></div>
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Product Battery</div>
        		<div class="col-md-8">

        			<table class="table" id="battery_table">
	                    <thead><tr>
	                            <th width=40%>Battery Type</th>
	                            <th width=20%>Takes Battery</th>
	                            <th width=20%>Qty Included</th>
	                            <th width=20%>List Order</th>
	                            <th width=5%><button type="button" id="add_battery" class="btn btn-outline-info btn-sm" title="Add Battery"><i class="fa fa-plus"></i></button></th>
	                    </tr></thead>
	                    <tbody>
	                    <?php
                        $batterysql = "SELECT * FROM product_battery WHERE product_id = $productid";
                        $batterystmt = $cms_connect->query($batterysql);
                        while ($brow = $batterystmt->fetch_assoc()) {
                        	$tmp_battery_id = $brow['battery_id'];
	                    ?>
	                        <tr>
	                            <td><select name="battery_id[]" class="form-control" required>
	                            	<option value="">--- Select One ---</option>
	                                <?php echo battery_id_selected($cms_connect, $tmp_battery_id);?>
	                                </select></td>
	                            <td><input type="number" name="take_battery[]" value="<?php echo $brow['battery_qty'];?>" class="form-control" /></td>
	                            <td><input type="number" name="qty_included[]" value="<?php echo $brow['included'];?>" class="form-control" /></td>
	                            <td><input type="number" name="list_order[]" value="<?php echo $brow['battery_order'];?>" class="form-control" /></td>
	                            <td><button type="button" id="remove_battery" class="btn btn-outline-danger btn-sm delete" title="Remove Battery"><i class="fa fa-minus"></i></button></td>
	                        </tr>
	                    <?php
	                    }
	                    ?>
	                    </tbody>
	                </table>

        		</div>
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Related Product</div>
        		<div class="col-md-8">
        			<select name="product_related[]" id="product_related" class="selectpicker form-control show-tick" data-live-search="true" multiple data-style="btn-primary" data-actions-box="true" data-selected-text-format="count" data-size="15">
                		<?php echo product_list_by_brand($cms_connect, $productinfo['manufacture']);?>
            		</select>
        		</div>
        	</div>
            <div class="row form-group">
                <div class="col-md-2" align="right"></div>
                <div class="col-md-8">
                    <table class="table" id="related_product_table">
                        <thead><tr>
                                <th width=30%>Image</th>
                                <th width=20%>SKU</th>
                                <th width=40%>Name</th>
                                <th width=10%>Remove</th>
                        </tr></thead>
                        <tbody id="related_product_table_body"></tbody>
                    </table>
                </div>
            </div>
    	</form>

    	<div style="text-align:center"> <!-- button submit form -->
	    	<button type="submit" class="btn btn-outline-info" title="Save" form="product_extra_form"><i class="fa fa-floppy-o"></i>&nbsp; Save</button>
	    	<button type="button" class="btn btn-outline-warning" title="Reset" onClick="window.location.reload()"><i class="fa fa-refresh"></i>&nbsp; Reset</button>
	    </div>
	</div> 
</div> <!-- end tab content-->
                


<script type="text/javascript">

$(document).ready(function(){
	$('#product_extra').addClass('active');

	$(window).on("load", function(){
        if(localStorage.getItem('update_extra_result') != null){
            $('#alert_action').fadeIn().html('<div class="alert alert-info">'+localStorage.getItem('update_extra_result')+'</div>');
            localStorage.removeItem('update_extra_result');
        }
	});

	/**************************** initial load ****************************/
	// product feature, included selectize option
	$('#product_feature, #product_included').selectize({
    	plugins: ['remove_button','drag_drop','restore_on_backspace'],
        delimiter: ',',
        persist: false,
        create: function(input){
            return {
                value: input,
                text:  input
            }
        },
        render:{
            item: function(item, escape) {
                return '<div class="col-md-11 col-lg-11">'+item.text+'</div>';
            },
            option: function(item, escape) {
                return '<div class="col-md-11 col-lg-11">'+item.text+'</div>';
            }
        }
    });

    // initial load related product
    $("#product_related").selectpicker('val', <?php echo json_encode($relatedArr);?>);
    load_related_product_table($("#product_related").val());
	
	/***************************** end of initial load *****************************/


    /********************** add/remove battery type ***************************/
	$("#add_battery").click(function(){
    	var action = "add_more_battery";
    	$.ajax({
            type:"post",
            url:"product_action.php",
            data:{
                    action:action
            },
            success: function(mess){
                $('#battery_table').append(mess);
  				$('#battery_table').find('select').addClass('form-control');
            }
        });
    });
	$("#battery_table").on("click", "#remove_battery", function () {
    	if(confirm("Remove Cannot Be Undo. Are You Sure???")){
        	$(this).closest("tr").remove();     
        }
    });

    /********************** submit EXTRA form ***************************/
    $('#product_extra_form').submit(function(event){
    	event.preventDefault();
        var data = new FormData(this);
        data.append("action", "update_extra");
        data.append("productid", "<?php echo $productid?>");
        $.ajax({
            type:"post",
            url:"product_action.php",
            data:data,
        	contentType: false,
            cache: false,
            processData:false,
            success: function(mess){
            	var result = JSON.parse(mess);
            	var string = '';
            	if(result.feature_result == '' && result.included_result == '' && result.battery_result == '' && result.related_result == ''){
            		string = 'Nothing Updated';
            	}
            	else{
            		string = result.feature_result+' - '+result.included_result+' - '+result.battery_result+' - '+result.related_result;
            	}
            	localStorage.setItem("update_extra_result", string);
                window.location.reload();
            }
        });
    });

    /********************** load related product to view on table ***************************/
    function load_related_product_table(productIDs){
        if(productIDs.length == 0){
            $('#related_product_table_body').html('');
        }
        else{
            var productid = "<?php echo $productid?>";
            var action = "add_related_product";
            $.ajax({
                type:"post",
                url:"product_action.php",
                data:{
                    action:action,productIDs:productIDs,productid:productid
                },
                success: function(mess){
                    $('#related_product_table_body').html('');
                    $('#related_product_table_body').append(mess);
                    $('#related_product_table_body').sortable();
                }
            });
        }
        
        
    }
    $('#product_related').change(function(){
        var productIDs = $('#product_related').val();
        load_related_product_table(productIDs);
    });
    /********************** remove associate images ***************************/
    $(document).on('click', '.remove_product', function(){
        var productIDs = $("#product_related").val();
        var productID = $(this).attr("id");
        
        for( var i = 0; i < productIDs.length; i++){ 
            if(productIDs[i] == productID){
                productIDs.splice(i, 1); // to remove associated image
            }
        }
        $("#product_related").selectpicker('val', productIDs);
        load_related_product_table(productIDs);
    });
  	
    $(document).on("click", '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });

});
</script>

<?php
include('subfooter.php');
include('../footer.php');
?>