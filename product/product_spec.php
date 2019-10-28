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
$specArr = spec_id_by_product_id($cms_connect, $productid);

include('subheader.php');
?>

<div class="tab-content">
	<div class="tab-pane active"> 
		<form method="POST" id="product_spec_form" enctype="multipart/form-data">
			<div class="row form-group">
                <div class="col-md-2" align="right">Select Spec</div>
                <div class="col-md-4">
                    <select id="specids" class="selectpicker form-control show-tick" data-live-search="true" multiple data-style="btn-primary" data-actions-box="true" data-selected-text-format="count" data-size="15">
                        <?php echo product_spec_select_option($cms_connect)?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="product_spec_like" id="product_spec_like" class="selectpicker form-control show-tick" data-live-search="true" data-style="btn-primary" data-size="15">
                        <option value="">--- Select One ---</option>
                        <?php echo product_list_by_brand($cms_connect, $productinfo['manufacture']);?>
                    </select>
                </div>
                <div class="col-md-2" >Spec Like</div>
            </div>
            <div class="row form-group">
                <div class="col-md-2" align="right">Selected Specs</div>
                <div class="col-md-8">
                    <table class="table" id="spec_table">
                        <thead><tr>
                                <th width=20%>Name</th>
                                <th width=50%>Value</th>
                                <th width=20%>Suffix</th>
                                <th width=10%>Remove</th>
                        </tr></thead>
                        <tbody id="spec_table_body"></tbody>
                    </table>
                </div>
            </div>
    	</form>
    	<div style="text-align:center"> <!-- button submit form -->
	    	<button type="submit" class="btn btn-outline-info" title="Save" form="product_spec_form"><i class="fa fa-floppy-o"></i>&nbsp; Save</button>
	    	<button type="button" class="btn btn-outline-warning" title="Reset" onClick="window.location.reload()"><i class="fa fa-refresh"></i>&nbsp; Reset</button>
	    </div>
	</div> 
</div> <!-- end tab content-->
                


<script type="text/javascript">

$(document).ready(function(){
	$('#product_spec').addClass('active');

    /******************* initial load ********************/
	$(window).on("load", function(){
        if(localStorage.getItem('spec_result') != null){
            $('#alert_action').fadeIn().html('<div class="alert alert-info">'+localStorage.getItem('spec_result')+'</div>');
            localStorage.removeItem('spec_result');
        }
	});
    $("#specids").selectpicker('val', <?php echo json_encode($specArr);?>);
    load_spec_table($("#specids").val());
    /******************* end of initial load ********************/
    
    
    /********************** on change spec selected ***************************/
    function load_spec_table(specids){
        if(specids.length == 0){
            $('#spec_table_body').html('');
        }
        else{
            var action = "product_spec_change";
            var productid = "<?php echo $productid?>";
            $.ajax({
                type:"post",
                url:"product_action.php",
                data:{
                    specids:specids,action:action, productid:productid
                },
                success: function(mess){
                    //console.log(mess)
                    $('#spec_table_body').html('');
                    $('#spec_table_body').append(mess);
                    $('#spec_table_body').sortable();
                }
            });
        }
    }
    $("#specids").change(function(){
        var specids = $("#specids").val();
        load_spec_table(specids);
    });


    $('#product_spec_like').change(function(){
        var productID = $('#product_spec_like').val();
        var action = 'product_spec_like';
        $.ajax({
            type:"post",
            url:"product_action.php",
            data:{
                action:action,productID:productID
            },
            success: function(mess){
                //console.log(mess)
                $('#spec_table_body').html('');
                $('#spec_table_body').append(mess);
                $('#spec_table_body').sortable();
            }
        });
        //console.log(productID);
    });

    /********************** remove selected spec ***************************/
    $(document).on('click', '.removespec', function(){
        var specids = $("#specids").val();
        var specid = $(this).attr("id");
        for( var i = 0; i < specids.length; i++){ 
            if(specids[i] == specid){
                specids.splice(i, 1); // to remove associated image
            }
        }
        $("#specids").selectpicker('val', specids);
        load_spec_table(specids);
        
    });
  	/********************** submit SPECS form ***************************/
  	$('#product_spec_form').submit(function(e){
        event.preventDefault();
        var data = new FormData(this);
        data.append("action", "update_spec");
        data.append("productid", "<?php echo $productid?>");
        $.ajax({
            type:"post",
            url:"product_action.php",
            data:data,
            contentType: false,
            cache: false,
            processData:false,
            success: function(mess){
                localStorage.setItem("spec_result", mess);
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