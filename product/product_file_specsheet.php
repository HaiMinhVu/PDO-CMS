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
$associatedSpecSheetArr = associated_specsheet_by_product_id($cms_connect, $productid);

include('subheader.php');
?>


<div class="tab-content">
	<form method="POST" id="product_specsheet_form" enctype="multipart/form-data">
		<div class="row form-group">
    		<div class="col-md-2" align="right">Spec Sheet List</div>
    		<div class="col-md-8">
        		<select id="fileIDs" class="selectpicker form-control show-tick" data-live-search="true" multiple data-style="btn-primary" data-actions-box="true" data-selected-text-format="count">
			        <?php echo all_spec_sheet_select_option($cms_connect)?>
			    </select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-2" align="right">Associated Spec Sheets</div>
    		<div class="col-md-8">
    			<table class="table" id="specsheet_table">
                    <thead><tr>
                            <th width=90%>Name</th>
                            <th width=10%></th>
                    </tr></thead>
                    <tbody id="specsheet_table_body"></tbody>
                </table>
    		</div>
    	</div>
    	
	</form>
	<div style="text-align:center"> <!-- button submit form -->
    	<button type="submit" class="btn btn-outline-info" title="Save" form="product_specsheet_form"><i class="fa fa-floppy-o"></i>&nbsp; Save</button>
    	<button type="button" class="btn btn-outline-warning" title="Reset" onClick="window.location.reload()"><i class="fa fa-refresh"></i>&nbsp; Reset</button>
    </div>


</div> <!-- end tab content-->
	                
                


<script type="text/javascript">

$(document).ready(function(){	
	$('#product_file').addClass('active');

	$(window).on("load", function(){
		$('#page_title').val("abc");

		if(localStorage.getItem('specsheet_result') != null){
            $('#alert_action').fadeIn().html('<div class="alert alert-info">'+localStorage.getItem('specsheet_result')+'</div>');
            localStorage.removeItem('specsheet_result');
        }


	});

	/******************* initial load ********************/
	$("#fileIDs").selectpicker('val', <?php echo json_encode($associatedSpecSheetArr);?>);
    load_associated_specsheet_table($("#fileIDs").val());

	/********************** on change images ***************************/
  	function load_associated_specsheet_table(fileIDs){
  		if(fileIDs.length == 0){
            $('#specsheet_table_body').html('');
        }
        else{
	  		var productid = "<?php echo $productid?>";
	  		var action = "add_associated_specsheet";
	    	$.ajax({
	            type:"post",
	            url:"product_action.php",
	            data:{
	                fileIDs:fileIDs,action:action,productid:productid
	            },
	            success: function(mess){
	            	//console.log(mess);
	            	$('#specsheet_table_body').html('');
	                $('#specsheet_table_body').append(mess);
	  				$('#specsheet_table_body').sortable();	  				
	            }
	        });
	    }
  	}
	$("#fileIDs").change(function(){
		var fileIDs = $("#fileIDs").val();
    	load_associated_specsheet_table(fileIDs);
    });

	/********************** remove associate manual ***************************/
	$(document).on('click', '.disassociate', function(){
		var specsheets = $("#fileIDs").val();
        var deletedspecsheet = $(this).attr("id");
        for( var i = 0; i < specsheets.length; i++){ 
		   	if(specsheets[i] == deletedspecsheet){
		    	specsheets.splice(i, 1); // to remove associated image
		   	}
		}
		$("#fileIDs").selectpicker('val', specsheets);
		load_associated_specsheet_table(specsheets);
    });

	/********************** submit manual form ***************************/
  	$('#product_specsheet_form').submit(function(e){
    	event.preventDefault();
        var data = new FormData(this);
        data.append("action", "update_specsheet");
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
            	localStorage.setItem("specsheet_result", mess);
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