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

include('subheader.php');
?>


<div class="tab-content">

	<form method="POST" id="product_manual_form" enctype="multipart/form-data">
		<div class="row form-group">
    		<div class="col-md-2" align="right">Manual List</div>
    		<div class="col-md-8">
        		<select id="fileIDs" class="selectpicker form-control show-tick" data-live-search="true" multiple data-style="btn-primary" data-actions-box="true" data-selected-text-format="count">
			        <?php echo all_manual_select_option($cms_connect)?>
			    </select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-2" align="right">Associated Manual</div>
    		<div class="col-md-8">
    			<table class="table" id="manual_table">
                    <thead><tr>
                            <th width=40%>Name</th>
                            <th width=30%>Languages</th>
                            <th width=25%>Change</th>
                            <th width=5%></th>
                    </tr></thead>
                    <tbody id="manual_table_body"></tbody>
                </table>
    		</div>
    	</div>
    	
	</form>
	<div style="text-align:center"> <!-- button submit form -->
    	<button type="submit" class="btn btn-outline-info" title="Save" form="product_manual_form"><i class="fa fa-floppy-o"></i>&nbsp; Save</button>
    	<button type="button" class="btn btn-outline-warning" title="Reset" onClick="window.location.reload()"><i class="fa fa-refresh"></i>&nbsp; Reset</button>
    </div>


</div> <!-- end tab content-->
	                



<script type="text/javascript">

$(document).ready(function(){	
	$('#product_file').addClass('active');

	$(window).on("load", function(){
		if(localStorage.getItem('manual_result') != null){
            $('#alert_action').fadeIn().html('<div class="alert alert-info">'+localStorage.getItem('manual_result')+'</div>');
            localStorage.removeItem('manual_result');
        }
	});

	/******************* initial load ********************/
	$("#fileIDs").selectpicker('val', <?php echo json_encode($associatedManualArr);?>);
    load_associated_manual_table($("#fileIDs").val());

	/********************** on change images ***************************/
  	function load_associated_manual_table(fileIDs){
  		if(fileIDs.length == 0){
            $('#manual_table_body').html('');
        }
        else{
	  		var productid = "<?php echo $productid?>";
	  		var action = "add_associated_manual";
	    	$.ajax({
	            type:"post",
	            url:"product_action.php",
	            data:{
	                fileIDs:fileIDs,action:action,productid:productid
	            },
	            success: function(mess){
	            	//console.log(mess);
	            	$('#manual_table_body').html('');
	                $('#manual_table_body').append(mess);
	  				$('#manual_table_body').sortable();
	  				$('#manual_table_body select[name*="languages"]').selectpicker('refresh');
	  				
	            }
	        });
	    }
  	}
	$("#fileIDs").change(function(){
		var fileIDs = $("#fileIDs").val();
    	load_associated_manual_table(fileIDs);
    });

	/********************** remove associate manual ***************************/
	$(document).on('click', '.disassociate', function(){
		var Manuals = $("#fileIDs").val();
        var deletedManual = $(this).attr("id");
        for( var i = 0; i < Manuals.length; i++){ 
		   	if(Manuals[i] == deletedManual){
		    	Manuals.splice(i, 1); // to remove associated image
		   	}
		}
		$("#fileIDs").selectpicker('val', Manuals);
		load_associated_manual_table(Manuals);
    });

	/********************** submit manual form ***************************/
  	$('#product_manual_form').submit(function(e){
    	event.preventDefault();
        var data = new FormData(this);
        data.append("action", "update_manual");
        data.append("productid", "<?php echo $productid?>");
        $.ajax({
            type:"post",
            url:"product_action.php",
            data:data,
        	contentType: false,
            cache: false,
            processData:false,
            success: function(mess){
            	//console.log(mess);
            	localStorage.setItem("manual_result", mess);
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