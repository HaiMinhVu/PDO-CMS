<script type="text/javascript">
$(document).ready(function(){
	$('#switch_product').selectpicker('val', "<?php echo $productinfo['id']?>");

	$('#switch_product').change(function(){
		var id = $(this).val();
		var url = window.location.href;
		var tmp = url.substring(0, url.indexOf('='));
		var newurl = tmp+'='+id;
		window.location.href = newurl;
	});



});
</script>


                </div>
            </div>
        </div>
    </div>
</div> <!-- end of display content -->

