<?php
include('../../../dbconnect.php');
include('../header.php');
?>

<!------------ export csv ------------>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>


<span id="alert_action"></span>
<!---------------------- main table  ------------------------------>
	 <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                        <div class="row">
                            <strong class="card-title">Products</strong>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" align="right">
                        <button type="button" onclick="location.href='product_add.php'" class="btn btn-outline-info btn-sm" title="Add New"><i class="fa fa-plus"></i>&nbsp; New Product</button>    
                    </div>
                    <div style="clear:both"></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="right">
                            <input type="checkbox" id="inactive" > InActive
                        </div>
                    
                        <div class="col-lg-12 col-sm-12 table-responsive">
                            <table id="product_table" class="table table-bordered table-hover">
                                <thead class="thead-dark"><tr>
                                    <th>ID</th>
                                    <th>SKU</th>
                                    <th>Name</th>
                                    <th width=5%></th>
                                </tr></thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- end of display content -->






<?php
include '../footer.php';
?>

<script type="text/javascript">

$(document).ready(function(){

    $(window).on("load", function(){
        if(localStorage.getItem('add_product_result') != null){
            console.log(localStorage.getItem('add_product_result'));
            $('#alert_action').fadeIn().html('<div class="alert alert-info">'+localStorage.getItem('add_product_result')+'</div>');
            localStorage.removeItem('add_product_result');

        }
    });
    
    fetch_product();

    function fetch_product(inactive='false'){
        $('#product_table').DataTable().destroy();
        var action = "fetch_product";
        $('#product_table').DataTable({
            "processing":true,
            "serverSide":true,
            "stateSave": true,
            "order":[],
            "ajax":{
                url:"product_fetch.php",
                type:"POST",
                data:{
                    inactive:inactive, action:action
                }
            },
            "columnDefs":[{
                "targets":[1,2,3],
                "orderable":false,
                },
            ],
            'columns': [
                { 'data': 'id' },
                { 'data': 'sku' },
                { 'data': 'name' },
                { 'data': 'action' }
            ],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "dom": 'lBfrtip',
            "buttons": ['excel','csv','pdf'],
            "language": {"infoFiltered": ""},
        });
    }

    $('#inactive').change(function(){
        var inactive = $('#inactive').is(':checked');
        fetch_product(inactive);
    });

    $(document).on('click', '.delete', function(){
        var productID = $(this).attr("id");
        var action = 'delete_product';
        //console.log(productID);
        if(confirm("Delete CANNOT be undo!!!\nWant to Delete Product ?" )){
            $.ajax({
                url:"product_action.php",
                method:"POST",
                data:{productID:productID,action:action},
                success:function(data){
                    $('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
                    $('#product_table').DataTable().ajax.reload();
                }
            });
        }
        else{
            return false;
        }
    });

});
</script>