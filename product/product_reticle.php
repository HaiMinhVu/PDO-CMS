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
$associatedReticleArr = associated_reticle_by_product_id($cms_connect, $productid);

include('subheader.php');
?>

<div class="tab-content">
	<div class="tab-pane active"> 
		<form method="POST" id="product_reticle_form" enctype="multipart/form-data">
			<div class="row form-group">
        		<div class="col-md-2" align="right">File List</div>
        		<div class="col-md-8">
            		<select id="imageids" class="selectpicker form-control show-tick" data-live-search="true" multiple data-style="btn-primary" data-actions-box="true" data-selected-text-format="count">
				        <?php echo all_image_select_option($cms_connect)?>
				    </select>
				</div>
    		</div>
			<div class="row form-group">
    			<div class="col-md-2" align="right">Associated Reticles</div>
        		<div class="col-md-8">
        			<table class="table" id="image_table">
	                    <thead><tr>
	                            <th width=15%>Image</th>
	                            <th width=20%>Name</th>
	                            <th width=30%>Title</th>
	                            <th width=30%>Tool Tips</th>
	                            <th width=5%></th>
	                    </tr></thead>
	                    <tbody id="reticle_table_body"></tbody>
	                </table>
        		</div>
        	</div>
        	
    	</form>
    	<div style="text-align:center"> <!-- button submit form -->
	    	<button type="submit" class="btn btn-outline-info" title="Save" form="product_reticle_form"><i class="fa fa-floppy-o"></i>&nbsp; Save</button>
	    	<button type="button" class="btn btn-outline-warning" title="Reset" onClick="window.location.reload()"><i class="fa fa-refresh"></i>&nbsp; Reset</button>
	    </div>
	</div> 
	<!-- end image tab -->

</div> <!-- end tab content-->
                
    

       

</div> <!-- end of display content -->


<script type="text/javascript">

$(document).ready(function(){
	$('#product_reticle').addClass('active');

	/******************* initial load ********************/
	$(window).on("load", function(){
		
	    	    

  		
  		if(localStorage.getItem('reticle_result') != null){
            $('#alert_action').fadeIn().html('<div class="alert alert-info">'+localStorage.getItem('reticle_result')+'</div>');
            localStorage.removeItem('reticle_result');
        }
	});
	$("#imageids").selectpicker('val', <?php echo json_encode($associatedReticleArr);?>);
    load_associated_table($("#imageids").val());
    /******************* end of initial load ********************/


  	/********************** on change images ***************************/
  	function load_associated_table(imageids){
  		if(imageids.length == 0){
            $('#reticle_table_body').html('');
        }
        else{
	  		var productid = "<?php echo $productid?>";
	  		var action = "add_associated_reticle";
	    	$.ajax({
	            type:"post",
	            url:"product_action.php",
	            data:{
	                imageids:imageids,action:action,productid:productid
	            },
	            success: function(mess){
	            	//console.log(mess);
	            	$('#reticle_table_body').html('');
	                $('#reticle_table_body').append(mess);
	  				$('#reticle_table_body').sortable();
	            }
	        });
	    }
  	}
	$("#imageids").change(function(){
		var imageids = $("#imageids").val();
    	load_associated_table(imageids);
    });

    /********************** remove associate images ***************************/
	$(document).on('click', '.disassociate', function(){
		var imageids = $("#imageids").val();
        var imageid = $(this).attr("id");
        for( var i = 0; i < imageids.length; i++){ 
		   	if(imageids[i] == imageid){
		    	imageids.splice(i, 1); // to remove associated image
		   	}
		}
		$("#imageids").selectpicker('val', imageids);
		load_associated_table(imageids);
        
    });
    /********************** submit IMAGE form ***************************/
  	$('#product_reticle_form').submit(function(e){
    	event.preventDefault();
        var data = new FormData(this);
        data.append("action", "update_reticle");
        data.append("productid", "<?php echo $productid?>");
        $.ajax({
            type:"post",
            url:"product_action.php",
            data:data,
        	contentType: false,
            cache: false,
            processData:false,
            success: function(mess){
            	localStorage.setItem("reticle_result", mess);
                window.location.reload(); 
            }
        });
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