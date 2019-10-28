<?php
require_once('../../../dbconnect.php');
require_once('../header.php');
require_once('product_function.php');

$skuArr = sku_list($readdb);

?>

<div class="content mt-3"> <!-- begining of display content -->
<span id="alert_action"></span>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <strong class="card-title">Add New Product</strong>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" align="right">
                        <button type="button" class="btn btn-outline-info btn-sm" title="Go Back" onclick="window.location.href='product.php'"><i class="fa fa-arrow-left"></i>&nbsp; Back</button>
                    </div>
                </div>
                <div class="card-body">
                	<form method="POST" id="product_add_form" enctype="multipart/form-data">
                		<div class="row form-group">
	                		<div class="col-md-3" align="right">Product Name <span style="color:red">*</span></div>
	                		<div class="col-md-6"><input type="text" name="product_name" id="product_name" class="form-control" required></div>
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">SKU <span style="color:red">*</span></div>
	                		<div class="col-md-6"><input type="text" name="product_sku" id="product_sku" class="form-control" required></div>
	                		<div class="col-md-3"><span id="hintsku"></span></div>
	                		
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">NSID&nbsp;&nbsp;&nbsp;</div>
	                		<div class="col-md-6"><input type="number" name="product_nsid" min="0" step="1" class="form-control"></div>
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">UPC&nbsp;&nbsp;&nbsp;</div>
	                		<div class="col-md-6"><input type="text" name="product_upc" class="form-control"></div>
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">Manufacture <span style="color:red">*</span></div>
	                		<div class="col-md-6">
	                			<select name="product_manufacture" id="product_manufacture" class="form-control" required>
                            		<option value="">--- Select One ---</option>
                            		<?php echo manufacture_list($readdb);?>
                        		</select>
                    		</div>
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">Product Sales Description&nbsp;&nbsp;&nbsp;</div>
	                		<div class="col-md-6"><input type="text" name="product_feature_name" class="form-control"></div>
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">NSN&nbsp;&nbsp;&nbsp;</div>
	                		<div class="col-md-6"><input type="text" name="product_nsn" class="form-control" ></div>
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">Short Description&nbsp;&nbsp;&nbsp;</div>
	                		<div class="col-md-6"><textarea rows="5" name="store_description" id="product_store_description"></textarea></div>
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">Long Description&nbsp;&nbsp;&nbsp;</div>
	                		<div class="col-md-6"><textarea rows="5" name="feature_description" id="product_feature_description"></textarea></div>
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">Netsuit Product Category <span style="color:red">*</span></div>
	                		<div class="col-md-6">
	                			<select name="product_category" id="product_category" class="form-control" required>
                            		<option value="">--- Select One ---</option>
                            		<?php echo category_list($readdb);?>
                        		</select>
                    		</div>
	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">Keywords&nbsp;&nbsp;&nbsp;</div>
	                		<div class="col-md-6"><input type="text" name="product_keywords" id="product_keywords" /></div>

	                	</div>
	                	<div class="row form-group">
	                		<div class="col-md-3" align="right">Realease Date&nbsp;&nbsp;&nbsp;</div>
	                		<div class="col-md-6"><input type="date" name="product_start_date" class="form-control" /></div>
	                	</div>
                	</form>
                	<div style="text-align:center"> <!-- button submit form -->
				    	<button type="submit" class="btn btn-outline-info" title="Save" form="product_add_form"><i class="fa fa-floppy-o"></i>&nbsp; Add</button>
				    	<button type="button" class="btn btn-outline-warning" title="Reset" onClick="window.location.reload()"><i class="fa fa-refresh"></i>&nbsp; Reset</button>
				    </div>
                	
                </div>
            </div>
        </div>
    </div>
        
    

       

</div> <!-- end of display content -->


<script type="text/javascript">

$(document).ready(function(){
	$(window).on("load", function(){

	});

	/******************************* initial load ********************************/
	// wysisyg for short and long description
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

	
	/************************ check for duplicated SKU *************************/
	var skus = <?php echo json_encode($skuArr)?>;
	$('#product_sku').on('keyup change', function(){
    	var product_sku = $('#product_sku').val();
    	if (jQuery.inArray(product_sku, skus)!='-1'){
        	$('#hintsku').fadeIn().html('<div><span style="color:red">SKU already exist</spam></div>');
        }
    	else{
        	$('#hintsku').fadeOut(100);
        }
    });

	/********************** submit ADD form ***************************/
    $('#product_add_form').submit(function(e){
    	event.preventDefault();
        var product_store_description = (CKEDITOR.instances['product_store_description']).getData();
        var product_feature_description = (CKEDITOR.instances['product_feature_description']).getData();

        var data = new FormData(this);
        data.append("action", "add_product");
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
            	var result = JSON.parse(mess);
            	localStorage.setItem("add_product_result", result.add_result);
            	window.open("product.php", "_self");

            	// open general after successfully added
            	//var productid = result.new_product_id;
            	//window.open("product_general.php?id="+productid, "_self");
                //window.location.reload(); 
            }
        });
    });

});
</script>

<?php
include('../footer.php');
?>