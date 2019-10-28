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
$product_manufacture = $productinfo['manufacture'];
$product_category = $productinfo['product_category_id'];

include('subheader.php');
?>

<!----------- WYSIWYG --------->
<script src="https://cdn.ckeditor.com/4.8.0/full-all/ckeditor.js"></script>

<div class="tab-content">
	<div class="tab-pane active">
		<form method="POST" id="product_update_form" enctype="multipart/form-data">
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Product Name <span style="color:red">*</span></div>
        		<div class="col-md-8"><input type="text" name="product_name" value="<?php echo $productinfo['Name'];?>" class="form-control" required></div>
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">SKU <span style="color:red">*</span></div>
        		<div class="col-md-8"><input type="text" name="product_sku" value="<?php echo $productinfo['sku'];?>" class="form-control" required></div>
        		<label  style="font-size:11px;opacity:0.6;float:right;">NS-SKU: <a href="https://system.na1.netsuite.com/app/common/item/item.nl?id=<?php echo $productinfo['sku'];?>" target="_blank"><?php echo $productinfo['sku'];?></a></label> 
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">NSID&nbsp;&nbsp;&nbsp;</div>
        		<div class="col-md-8"><input type="number" name="product_nsid" value="<?php echo $productinfo['nsid'];?>"min="0" step="1" class="form-control"></div>
        		<label  style="font-size:11px;opacity:0.6;float:right;">NS-NSID: <a href="https://system.na1.netsuite.com/app/common/item/item.nl?id=<?php echo $productinfo['nsid'];?>" target="_blank"><?php echo $productinfo['nsid'];?></a></label> 
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">UPC&nbsp;&nbsp;&nbsp;</div>
        		<div class="col-md-8"><input type="text" name="product_upc" value="<?php echo $productinfo['UPC'];?>" class="form-control"></div>
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Manufacture <span style="color:red">*</span></div>
        		<div class="col-md-8">
        			<select name="product_manufacture" id="product_manufacture" class="selectpicker form-control" required>
                		<option value="">--- Select One ---</option>
                		<?php echo manufacture_list($cms_connect);?>
            		</select>
        		</div>
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Product Sales Description&nbsp;&nbsp;&nbsp;</div>
        		<div class="col-md-8"><input type="text" name="product_feature_name" value="<?php echo $productinfo['feature_name'];?>" class="form-control"></div>
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">NSN&nbsp;&nbsp;&nbsp;</div>
        		<div class="col-md-8"><input type="text" name="product_nsn" value="<?php echo $productinfo['nsn'];?>" class="form-control" ></div>
        	</div>
            <div class="row form-group">
                <div class="col-md-2" align="right">NetSuite Product Category <span style="color:red">*</span></div>
                <div class="col-md-8">
                    <select name="product_ns_category" id="product_ns_category" class="selectpicker form-control" data-live-search="true" required>
                        <option value="">--- Select One ---</option>
                        <?php echo category_list($cms_connect);?>
                    </select>
                </div>
            </div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Short Description&nbsp;&nbsp;&nbsp;</div>
        		<div class="col-md-8"><textarea rows="5" name="store_description" id="product_store_description"><?php echo $productinfo['store_desc']?></textarea></div>
        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Long Description&nbsp;&nbsp;&nbsp;</div>
        		<div class="col-md-8"><textarea rows="10" name="feature_description" id="product_feature_description"><?php echo $productinfo['feature_desc']?></textarea></div>
        	</div>
        	
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Keywords&nbsp;&nbsp;&nbsp;</div>
        		<div class="col-md-8"><input type="text" name="product_keywords" id="product_keywords" value="<?php echo $productinfo['keywords'];?>" /></div>

        	</div>
        	<div class="row form-group">
        		<div class="col-md-2" align="right">Realease Date&nbsp;&nbsp;&nbsp;</div>
        		<div class="col-md-8"><input type="date" name="product_start_date" class="form-control" value="<?php echo ($productinfo['start_date'] != '1969-12-31') ? $productinfo['start_date'] : "" ; ?>" /></div>
        	</div>
    	</form>
    	<div style="text-align:center"> <!-- button submit form -->
	    	<button type="submit" class="btn btn-outline-info" title="Save" form="product_update_form"><i class="fa fa-floppy-o"></i>&nbsp; Save</button>
	    	<button type="button" class="btn btn-outline-warning" title="Reset" onClick="window.location.reload()"><i class="fa fa-refresh"></i>&nbsp; Reset</button>
	    </div>
	</div> 
</div>
                


<script type="text/javascript">

$(document).ready(function(){	
	$('#product_general').addClass('active');

	$(window).on("load", function(){
		if(localStorage.getItem('update_general_result') != null){
            $('#alert_action').fadeIn().html('<div class="alert alert-info">'+localStorage.getItem('update_general_result')+'</div>');
            localStorage.removeItem('update_general_result');
        }
	});

	/**************************** initial load ****************************/
    $('#product_ns_category').val('<?php echo $product_category;?>');
	$('#product_manufacture').selectpicker('val', '<?php echo $product_manufacture;?>');
    
	// wysiwyg for short and long description
	var wysiwyg_config = {
		skin: 'moono',
		enterMode: CKEDITOR.ENTER_BR,
		shiftEnterMode:CKEDITOR.ENTER_P,
		toolbar: [{ name: 'basicstyles', groups: [ 'basicstyles' ], items: [ 'Bold', 'Italic', 'Underline', "-", 'TextColor', 'BGColor' ] },
				{ name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
				{ name: 'scripts', items: [ 'Subscript', 'Superscript' ] },
				{ name: 'justify', groups: [ 'blocks', 'align' ], items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
				{ name: 'paragraph', groups: [ 'list', 'indent' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
				{ name: 'links', items: [ 'Link', 'Unlink' ] },
				{ name: 'insert', items: [ 'Image'] },
				{ name: 'spell', items: [ 'jQuerySpellChecker' ] },
				{ name: 'table', items: [ 'Table' ] }
				],
	}
	CKEDITOR.replace('product_store_description', wysiwyg_config);
	CKEDITOR.replace('product_feature_description', wysiwyg_config);

	// product keywords selectize option
	$('#product_keywords').selectize({
    	plugins: ['remove_button','drag_drop','restore_on_backspace'],
        delimiter: ',',
        persist: false,
        create: function(input){
            return {
                value: input,
                text:  input
            }
        }
    });
	/***************************** end of initial load *****************************/

    /********************** submit GENERAL form ***************************/
    $('#product_update_form').submit(function(e){
    	event.preventDefault();
    	var product_store_description = (CKEDITOR.instances['product_store_description']).getData();
        var product_feature_description = (CKEDITOR.instances['product_feature_description']).getData();
        var data = new FormData(this);
        data.append("action", "update_general");
        data.append("productid", "<?php echo $productid?>");
        data.append("product_store_description", product_store_description);
        data.append("product_feature_description", product_feature_description);
        $.ajax({
            type:"post",
            url:"product_action.php",
            data:data,
        	contentType: false,
            cache: false,
            processData:false,
            success: function(mess){
                //console.log(mess);
            	localStorage.setItem("update_general_result", mess);
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