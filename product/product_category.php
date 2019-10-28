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
$category_tree = json_encode(treeview_category_array($cms_connect, $productid));

include('subheader.php');
?>

<div class="tab-content">
	<div class="tab-pane active"> 
		<form method="POST" id="product_category_form" enctype="multipart/form-data">
			<div class="row form-group">
        		<div class="offset-md-2 col-md-7">
        			<input type="text" class="form-control" placeholder="Search in tree" id="search" />
        		</div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-info" id="btn_search">Search</button>
                </div>
    		</div>
        	<div class="row form-group">
        		<div class="offset-md-2 col-md-8">
        			<div id="cat_tree"></div>
        		</div>
    		</div>
    		<input type="hidden" name="associatedCatID" id="associatedCatID">
            <input type="hidden" name="prim_cat" id="prim_cat">
    	</form>
    	<div style="text-align:center"> <!-- button submit form -->
	    	<button type="submit" class="btn btn-outline-info" title="Save" form="product_category_form"><i class="fa fa-floppy-o"></i>&nbsp; Save</button>
	    	<button type="button" class="btn btn-outline-warning" title="Reset" onClick="window.location.reload()"><i class="fa fa-refresh"></i>&nbsp; Reset</button>
	    </div>
	</div> 
</div> <!-- end tab content-->
                

<script type="text/javascript">

$(document).ready(function(){
	$('#product_category').addClass('active');

	$(window).on("load", function(){
        if(localStorage.getItem('update_category_result') != null){
            $('#alert_action').fadeIn().html('<div class="alert alert-info">'+localStorage.getItem('update_category_result')+'</div>');
            localStorage.removeItem('update_category_result');
        }
	});

	/**************************** initial load ****************************/
	// treeview for category tab  
    var treedata = <?php echo $category_tree; ?>;
    $('#cat_tree').jstree({
        'core': {
            'data': treedata,
            'themes': {
		    	'icons': false,
		    },
        },
        'plugins': ["checkbox", "grid", "search"],
        'grid': {
            'columns': [
                {width: '90%', header: "Categoriy List"},
                {width: '10%', header: "Primary", value: "radio"}
            ],
        },
        'search': {
		    'show_only_matches': true,
		    'show_only_matches_children': true
		},
		
    });
    // tree view on change 
    $('#cat_tree').on('changed.jstree', function (e, data) {
    	$('#associatedCatID').val(data.selected);
    	var prim = $("input[name='prim_category']:checked").val();
        $("#prim_cat").val(prim);
    	$('#cat_tree').jstree("select_node", prim);
  	});

    // search in category tree
    $('#btn_search').click(function(){
        var search = $('#search').val();
        $('#cat_tree').jstree(true).search(search)
    });


    $('input[name=prim_category]').change(function(){
        var value = $( 'input[name=prim_category]:checked' ).val();
        alert(value);
    });


	/***************************** end of initial load *****************************/
    

  	/********************** submit CATEGORY form ***************************/
  	$('#product_category_form').submit(function(e){
    	event.preventDefault();
        var data = new FormData(this);
        data.append("action", "update_category");
        data.append("productid", "<?php echo $productid?>");
        $.ajax({
            type:"post",
            url:"product_action.php",
            data:data,
        	contentType: false,
            cache: false,
            processData:false,
            success: function(mess){
            	localStorage.setItem("update_category_result", mess);
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