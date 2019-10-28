<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$root = 'http://'.$_SERVER['HTTP_HOST'].'/cmsnew';
if(!isset($_SESSION['user'])){
	header("Location:".$root."/login.php");
}

?>
<!DOCTYPE html>
<html lang="en">
  	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	    <title>Sellmark Content Management</title>

	<!-- All CSS -->
		<!-- general -->
	    <link rel="stylesheet" href="<?php echo $root?>/assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	    <link rel="stylesheet" href="<?php echo $root?>/assets/css/jquery-ui.css">
        <link rel="stylesheet" href="<?php echo $root?>/assets/css/bootstrap-select.min.css">
        <link rel="stylesheet" href="<?php echo $root?>/assets/css/nav/style.css">

        <!-- datatable -->
        <link rel="stylesheet" href="<?php echo $root?>/assets/css/dataTables.bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo $root?>/assets/css/dataTables.responsive.css" />

        <!-- webdatarocks for reporting tool-->
        <link href="https://cdn.webdatarocks.com/latest/webdatarocks.min.css" rel="stylesheet" />

        <!-- date range picker -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
		
		<!-- dialog pop up -->
		<link rel="stylesheet" type="text/css" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<!-- selectize -->
		<link rel="stylesheet" href="<?php echo $root?>/assets/css/selectize.default.css" />

		<!-- JS tree -->
		<link rel="stylesheet" href="<?php echo $root?>/assets/css/jstree/style.min.css" />

		<!----------- filepond upload images --------->
        <link rel='stylesheet' href="<?php echo $root?>/assets/css/filepond-plugin-image-preview.min.css">
        <link rel='stylesheet' href="<?php echo $root?>/assets/css/filepond.min.css">
        <link rel="stylesheet" href="<?php echo $root?>/assets/css/filepond.css">

	<!-- All JS -->
	    <!-- general -->
	    <script src="<?php echo $root?>/assets/js/jquery.min.js"></script>
	    <script src="<?php echo $root?>/assets/js/popper.min.js"></script>
	  	<script src="<?php echo $root?>/assets/js/bootstrap.min.js"></script>
	    <script src="<?php echo $root?>/assets/js/cookies.js"></script> <!-- save cookies and scroll down point -->

	    <!-- checkbox with yes/no
	    <script src="<?php echo $root?>/assets/js/bootstrap-checkbox.min.js"></script>
		-->
	    <!-- data table -->
        <script src="<?php echo $root?>/assets/js/dataTables.jquery.min.js"></script>
        <script src="<?php echo $root?>/assets/js/dataTables.bootstrap.min.js"></script>
        <script src="<?php echo $root?>/assets/js/dataTables.responsive.js"></script>

        <!-- export csv, excel, pdf datatable -->
	    <script type="text/javascript" src="<?php echo $root?>/assets/js/dataTables.buttons.min.js"></script>
		<script type="text/javascript" src="<?php echo $root?>/assets/js/pdfmake.min.js"></script>
		<script type="text/javascript" src="<?php echo $root?>/assets/js/vfs_fonts.js"></script>
		<script type="text/javascript" src="<?php echo $root?>/assets/js/buttons.html5.min.js"></script>

	    <!-- bootstrap selectpicker -->
        <script src="<?php echo $root?>/assets/js/bootstrap-select.min.js"></script>

        <!-- webdatarocks for reporting tool-->
        <script src="<?php echo $root?>/assets/js/webdatarocks.toolbar.min.js"></script>
		<script src="<?php echo $root?>/assets/js/webdatarocks.js"></script>	

		<!-- date range picker -->
		<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

		<!-- dialog pop up -->
        <script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <!--<script type="text/javascript" src="<?php echo $root?>/assets/js/jquery-ui.js"></script>-->

        <!-- selectize -->
        <script src="<?php echo $root?>/assets/js/selectize.min.js"></script>

        <!-- JS tree -->
        <script src="<?php echo $root?>/assets/js/jstree.min.js"></script>
        <script src="<?php echo $root?>/assets/js/jstreegrid.js"></script>

        <!----------- WYSIWYG --------->
		<script src="https://cdn.ckeditor.com/4.8.0/full-all/ckeditor.js"></script>

	    <!-- navigation -->
	    <script src="<?php echo $root?>/assets/js/nav/main.js"></script>

	    
		
		<!-- filepond to upload images and documents-->
        <script type="text/javascript" src="<?php echo $root?>/assets/js/filepond-plugin-file-encode.min.js"></script>
        <script type="text/javascript" src="<?php echo $root?>/assets/js/filepond-plugin-file-validate-size.min.js"></script>
        <script type="text/javascript" src="<?php echo $root?>/assets/js/filepond-plugin-image-exif-orientation.min.js"></script>
        <script type="text/javascript" src="<?php echo $root?>/assets/js/filepond-plugin-image-preview.min.js"></script>
        <script type="text/javascript" src="<?php echo $root?>/assets/js/filepond-plugin-file-validate-type.js"></script>
        <script type="text/javascript" src="<?php echo $root?>/assets/js/filepond.min.js"></script>
        <script type="text/javascript" src="<?php echo $root?>/assets/js/filepond.js"></script>

	</head>
	<body onload="loadScroll()" onunload="saveScroll()"> <!-- function stored in cookies.js-->
		<aside id="left-panel" class="left-panel">
		    <nav class="navbar navbar-expand-sm navbar-default">

		        <div class="navbar-header">
		            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
		                <i class="fa fa-bars"></i>
		            </button>
		            <a class="navbar-brand" ><img src="<?php echo $root?>/assets/images/sellmarklogo.png" alt="Logo"></a>
		        </div>

		        <div id="main-menu" class="main-menu collapse navbar-collapse" >
		            <ul class="nav navbar-nav">
		            	<li>
		                    <a href="<?php echo $root?>"><i class="menu-icon fa fa-dashboard"></i>Dashboard</a>
		                </li>
		                <h3 class="menu-title">Site Management</h3>
		                    <li class="menu-item-has-children dropdown">
		                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-laptop"></i>CMS</a>
		                        <ul class="sub-menu children dropdown-menu">
		                            <li><i class="fa fa-home"></i><a href="product/product.php">Product</a></li>
		                            <li><i class="fa fa-share-alt"></i><a href="element/navigation.php">Site Elements</a></li>
		                            <li><i class="fa fa-sitemap"></i><a href="manage/category.php">Manager</a></li>
		                        </ul>
		                    </li>
		                    
		                <h3 class="menu-title">Administrator</h3>
		                    <li class="menu-item-has-children dropdown">
		                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="menu-icon fa fa-users"></i>Users</a>
		                        <ul class="sub-menu children dropdown-menu">
		                            <li><i class="menu-icon fa fa-id-card"></i><a href="admin/users.php">User Manager</a></li>
		                            
		                        </ul>
		                    </li>

		            </ul>
		        </div><!-- /.navbar-collapse -->
		    </nav>
		</aside><!-- /#left-panel -->
		<!-- Left Panel -->


		<!-- Right Panel -->
		<div id="right-panel" class="right-panel">
		    <header id="header" class="header"> <!-- Begining of Right Panel Header-->
		        <div class="header-menu">
		            <div class="col-sm-7">
		                <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
		                <div class="page-header float-left">
		                    <div class="page-title">
		                        <ol class="breadcrumb text-left">
		                        	<?php
		                        	//echo breadcrumbs();
		                        	?>
		                            
		                        </ol>
		                    </div>
		                </div>
		            </div>
		            
		            <div class="col-sm-5">
	                    <div class="user-area dropdown float-right">
	                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                            <i class="menu-icon fa fa-user"></i> <?php echo $_SESSION['user'].' - '.$_SESSION['role']?>
	                        </a>
	                        <div class="user-menu dropdown-menu">
		                        <a class="nav-link" href="#"><i class="fa fa-eye"></i> My Profile</a>
		                        <a class="nav-link" href="<?php echo $root?>/logout.php"><i class="fa fa-power-off"></i> Logout</a>
		                    </div>
	                    </div>
	                </div>
		        </div>

		    </header> <!-- End of Right Panel Header-->
		    

		    <div class="content mt-3"> <!-- begin of main content section -->
		    	<div id="pagetop"></div>