<div class="content mt-3"> <!-- begining of display content -->
<span id="alert_action"></span>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-md-2">
                        <strong class="card-title">View & Update Product:</strong>
                    </div>

                    <div class="col-md-5">
                    	<select id="switch_product" class="selectpicker form-control" data-live-search="true">
                    		<option>--- Switch Product ---</option>
                    		<?php echo product_list_select_option($cms_connect);?>
                    	</select>
                    </div>
                    <div class="col-md-5" align="right">
                        <button type="button" class="btn btn-outline-info btn-sm" title="Go Back" onclick="window.location.href='product.php'"><i class="fa fa-arrow-left"></i>&nbsp; Back</button>
                    </div>
                </div>
                <div class="card-body">
                	<ul class="nav nav-tabs nav-justified">
	                    <li class="nav-item">
	                        <a class="nav-link " id="product_general" href="product_general.php?id=<?php echo $productid?>" >General</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link " id="product_extra" href="product_extra.php?id=<?php echo $productid?>" >Extra</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link " id="product_category" href="product_category.php?id=<?php echo $productid?>" >Category</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link " id="product_image" href="product_image.php?id=<?php echo $productid?>" >Image</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link " id="product_spec" href="product_spec.php?id=<?php echo $productid?>" >Specs</a>
	                    </li>
	                    <li class="nav-item">
	                        <a class="nav-link " id="product_reticle" href="product_reticle.php?id=<?php echo $productid?>" >Reticles</a>
	                    </li>
	                    <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="product_file" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Files <span class="caret"></span></a>
                            <div class="dropdown-menu">
                                <a class="nav-link" href="product_file_manual.php?id=<?php echo $productid?>" >Manual</a>
                                <a class="nav-link" href="product_file_specsheet.php?id=<?php echo $productid?>" >Spec Sheets</a>
                                <a class="nav-link" href="product_file_download.php?id=<?php echo $productid?>" >Download</a>
                            </div>
                        </li>
	                    <li class="nav-item">
	                        <a class="nav-link " id="product_availability" href="product_availability.php?id=<?php echo $productid?>" >Availability</a>
	                    </li>
	                </ul>
	               	<br>