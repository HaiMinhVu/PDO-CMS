<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../../../newconnect.php');
include('header.php');
include('product_function.php');


?>

	<select id="imageids" class="selectpicker form-control show-tick" data-live-search="true" multiple data-style="btn-primary" data-actions-box="true" data-selected-text-format="count">
        <?php echo all_image_select_option($cms_connect)?>
    </select>



<script type="text/javascript">
$(document).ready(function(){

});
</script>
<?php
include('../footer.php');
?>