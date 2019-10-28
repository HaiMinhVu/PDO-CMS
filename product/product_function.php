<?php

/************* get total record for fetching ***********/
function get_total_all_records($readdb, $totalrecord){
    $statement = $readdb->prepare($totalrecord);
    $statement->execute();
    return $statement->rowCount();
}

/************* SKU list to check duplicated when adding new product ***********/
function product_list_select_option($cms_connect){
	$output = array();
	$sql = "SELECT id, sku FROM product";
	$result = $cms_connect->query($sql);
    foreach ($result as $row) {
		$output .='<option value = "'.$row['id'].'">'.$row['sku'].'</option>';
    }
	return $output;
}

/************* SKU list to check duplicated when adding new product ***********/
function sku_list($readdb){
	$output = array();
	$sql = "SELECT sku FROM product WHERE sku <> ''";
	$stmt = $readdb->prepare($sql);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$output[] = $row['sku'];
	}
	return $output;
}

/************* Manufacture list***********/
function manufacture_list($readdb){
    $sql = "SELECT id, name FROM list_manufacture WHERE manufacture_active = 1 ORDER BY id";
    $stmt = $readdb->prepare($sql);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $output .='<option value = "'.$row['id'].'">'.utf8_encode($row['name']).'</option>';
    }
    return $output;
}

/************* site category list***********/
function category_list($readdb){
    $sql = "SELECT id, label FROM list_product_category ORDER BY id";
    $stmt = $readdb->prepare($sql);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $output .='<option value = "'.$row['id'].'">'.$row['label'].'</option>';
    }
    return $output;
}

/************* Product Battery list***********/
function battery_list($readdb){
    $sql = "SELECT id, type FROM list_product_battery ORDER BY type";
    $stmt = $readdb->prepare($sql);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $output .='<option value = "'.$row['id'].'">'.$row['type'].'</option>';
    }
    return $output;
}

/************* Product Battery selected***********/
function battery_id_selected($readdb, $selected){
    $sql = "SELECT id, type FROM list_product_battery ORDER BY type";
    $stmt = $readdb->prepare($sql);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if($row['id'] == $selected){
            $output .='<option value = "'.$row['id'].'" selected>'.$row['type'].'</option>';
        }
        else{
            $output .='<option value = "'.$row['id'].'">'.$row['type'].'</option>';
        }
        
    }
    return $output;
}

/************* get product general info ***********/
function product_info_by_id($readdb, $productid){
	$sql = "SELECT * FROM product WHERE id = ?";
	$stmt = $readdb->prepare($sql);
	$stmt->execute([$productid]);
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

/************* get product feature ***********/
function product_feature_by_id($readdb, $productid){
	$feature = array();
	$sql = "SELECT id, feat_item FROM product_feature WHERE product_id = ? ORDER BY feature_order";
	$stmt = $readdb->prepare($sql);
	$stmt->execute([$productid]);
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$feature[] = $row['feat_item'];	
	}
	return $feature;
}

/************* get product included ***********/
function product_included_by_id($readdb, $productid){
	$included = array();
	$sql = "SELECT id, included_items FROM product_included WHERE product_id = ? ORDER BY included_order";
	$stmt = $readdb->prepare($sql);
	$stmt->execute([$productid]);
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$included[] = $row['included_items'];	
	}
	return $included;
}

/************* get product battery ***********/
function product_battery_by_id($readdb, $productid){
	$battery = array();
	$sql = "SELECT id, battery_id FROM product_battery WHERE product_id = ?";
	$stmt = $readdb->prepare($sql);
	$stmt->execute([$productid]);
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$battery[] = $row['battery_id'];	
	}
	return $battery;
}

/************* get product related ***********/
function product_related_by_id($readdb, $productid){
	$related = array();
	$sql = "SELECT id, related_product_id, related_product_order FROM list_related_product WHERE product_id = ? ORDER BY related_product_order";
	$stmt = $readdb->prepare($sql);
	$stmt->execute([$productid]);
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		array_push($related, $row['related_product_id']);
		//$related[] = $rrow['related_product_id'];	
	}
	return $related;
}

// product sku, name, main image for related product selection
function product_list_by_brand($readdb, $brand){
	$sql = 'SELECT p.sku, p.id, m.site, fm.file_name, p.feature_name
			FROM product p LEFT JOIN file_manager fm ON fm.ID = p.main_img_id
                            LEFT JOIN list_manufacture m ON m.id = p.manufacture
                            WHERE p.manufacture = ?';
	$output = '';
	$stmt = $readdb->prepare($sql);
	$stmt->execute([$brand]);
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$src = $row['site'].'/images/'.$row['file_name'];
		//$output .= '<option value="'.$row['id'].'" data-img_src="'.$src.'">'.$row['sku'].' - '.$row['feature_name'].'</option>';
		$output .='<option data-content="<img height=\'50\' src=\''.$src.'\'> '.$row['sku'].' - '.$row['feature_name'].'" value = "'.$row['id'].'"></option>';
    }
	
	return $output;
}

// treeview with pre-selected data
function treeview_category_array($readdb, $productid){	
	// get all associated and primary category
	$associatedArr = array();
	$prim = 0;
	$sql = "SELECT category_id, pca_primary FROM product_category_association WHERE product_id = ?";
	$stmt = $readdb->prepare($sql);
	$stmt->execute([$productid]);
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$associatedArr[] = $row['category_id'];
		if($row['pca_primary'] == 1){
			$prim = $row['category_id'];
		}
	}

	// get all parent cat id
	$sql = 'SELECT id, label, parent FROM product_category  WHERE status = 1';
	$parentArray = array();

	$parentresult = $readdb->prepare($sql);
	$parentresult->execute();
	while($parent = $parentresult->fetch(PDO::FETCH_ASSOC)){
		if(!in_array($parent['parent'], $parentArray)){
			$parentArray[] = $parent['parent'];
		}
	}

	// tree output
	$output = array();
	$result = $readdb->prepare($sql);
	$result->execute();
	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		$tmp = array();
		$tmp['id'] = $row['id'];
		$tmp['parent'] = $row['parent'] == 0 ? '#' : $row['parent'];
		$tmp['text'] = $row['label'];
		if(in_array($row['id'], $associatedArr)){
			$tmp['state'] = array('selected' => 'true');
		}
		if(!in_array($row['id'], $parentArray)){
			if($row['id'] == $prim){
				$tmp['data'] = array('radio' => '<input type="radio" name="prim_category" value="'.$row['id'].'" checked>');
			}
			else{
				$tmp['data'] = array('radio' => '<input type="radio" name="prim_category" value="'.$row['id'].'">');
			}
		}
		$output[] = $tmp;
	}
	return $output;
}


/*********************************************************************************/
/******************************* PRODUCT IMAGE ***********************************/
/*********************************************************************************/

/************* Image select option ***********/
function all_image_select_option($cms_connect){
    $sql = "SELECT DISTINCT fm.ID, concat(sl.url, ml.description, fm.file_name) as url, fm.file_name
			FROM file_manager fm LEFT JOIN site_list sl ON sl.id = fm.site_id
			                	LEFT JOIN master_list ml ON ml.id = fm.site_folder_id
			WHERE fm.file_type_id = 10 ";
    $result = $cms_connect->query($sql);
    $output = '';

    foreach ($result as $row){
    	$url = str_replace(' ', '%20', $row['url']);
	    //$output .='<option data-content="<img width=\'50\' height=\'50\' src=\''.$url.'\'> '.$row['file_name'].'" value = "'.$row['ID'].'"></option>';
	    $output .='<option value = "'.$row['ID'].'">'.$row['file_name'].'</option>';	    
    }

    return $output;
}

/************* get image info by id and product id ***********/
function image_info_by_id($cms_connect, $imageid, $productid){
	$output = array();
	$sql = "SELECT concat(sl.url, ml.description, fm.file_name) as url, fm.file_name, pi.*
			FROM file_manager fm LEFT JOIN site_list sl ON sl.id = fm.site_id
			                	LEFT JOIN master_list ml ON ml.id = fm.site_folder_id
			                	LEFT JOIN product_img pi ON pi.file_id = fm.ID
			WHERE fm.file_type_id = 10 AND fm.ID = $imageid AND pi.product_id = $productid";
    $result = $cms_connect->query($sql);
    return $result->fetch_assoc();
}

/************* get all associated images id by product id ***********/
function associated_imgs_by_product_id($cms_connect, $productid){
	$sql = "SELECT file_id, img_order FROM product_img Where active = 1 AND product_id = $productid ORDER BY img_order";
	$result = $cms_connect->query($sql);
    $output = array();
	foreach ($result as $row) {
		array_push($output, $row['file_id']);
	}
    return $output;
}



/************* get all product spec in select option  without recursive***********/
/*function product_spec_select_option($cms_connect){
	$sql = "SELECT id, name FROM list_product_spec";
	$result = $cms_connect->query($sql);
    $output = '';
    foreach ($result as $row){
        $output .='<option value = "'.$row['id'].'">'.$row['name'].'</option>';
    }
    return $output;
}*/

/*********************************************************************************/
/********************************* PRODUCT SPECT *********************************/
/*********************************************************************************/

/************* get all product spec in select option with recursive parent ***********/
function product_spec_select_option($dbconnect, $pid = 0, $indent = ''){
    $sql = "SELECT id, name FROM list_product_spec WHERE pid = $pid";
	$result = $dbconnect->query($sql);

    while($row = $result->fetch_assoc()){
        echo '<option value = "'.$row['id'].'">'.$indent.utf8_encode($row['name']).'</option>';
        product_spec_select_option($dbconnect, $row['id'], $indent.$row['name'].' -> ');
    }    
}

/************* product spec info by product id and spec id ***********/
function spec_info_by_ids($cms_connect, $specid, $productid){
	$sql = "SELECT * FROM product_spec ps LEFT JOIN list_product_spec lps ON lps.id = ps.spec_id WHERE ps.spec_id = $specid AND ps.product_id = $productid";
    $result = $cms_connect->query($sql);
	return $result->fetch_assoc();
}

/************* get spec name by spec id ***********/
function spec_name_by_ids($cms_connect, $specid){
	$sql = "SELECT name FROM list_product_spec WHERE id = $specid";
    $result = $cms_connect->query($sql);
	$row = $result->fetch_assoc();
	return $row['name'];
}

/************* get all existing SPEC with product id ***********/
function spec_id_by_product_id($cms_connect, $productid){
	$sql = "SELECT spec_id, prod_order FROM product_spec WHERE product_id = $productid ORDER BY prod_order";
    $result = $cms_connect->query($sql);
	$output = array();
	foreach ($result as $row) {
		array_push($output, $row['spec_id']);
	}
    return $output;
}



/*********************************************************************************/
/******************************* PRODUCT RETICLE *********************************/
/*********************************************************************************/

/************* get all associated RETICLE id by product id ***********/
function associated_reticle_by_product_id($cms_connect, $productid){
	$sql = "SELECT file_id, reticle_order FROM product_reticle Where product_id = $productid ORDER BY reticle_order";
	$result = $cms_connect->query($sql);
    $output = array();
	foreach ($result as $row) {
		array_push($output, $row['file_id']);
	}
    return $output;
}

/************* get RETICLE info by id and product id ***********/
function reticle_info_by_id($cms_connect, $imageid, $productid){
	$output = array();
	$sql = "SELECT concat(sl.url, ml.description, fm.file_name) as url, fm.file_name, pr.*
			FROM file_manager fm LEFT JOIN site_list sl ON sl.id = fm.site_id
			                	LEFT JOIN master_list ml ON ml.id = fm.site_folder_id
			                	LEFT JOIN product_reticle pr ON pr.file_id = fm.ID
			WHERE fm.file_type_id = 10 AND fm.ID = $imageid AND pr.product_id = $productid";
    $result = $cms_connect->query($sql);
    return $result->fetch_assoc();
}


/*********************************************************************************/
/********************************* FILE MANAGER **********************************/
/*********************************************************************************/

/************* get file info by id (including url, file name) ***********/
function file_info_by_id($cms_connect, $fileid){
	$output = array();
	$sql = "SELECT concat(sl.url, ml.description, fm.file_name) as url, fm.file_name
			FROM file_manager fm LEFT JOIN site_list sl ON sl.id = fm.site_id
			                	LEFT JOIN master_list ml ON ml.id = fm.site_folder_id
			WHERE fm.ID = $fileid";
    $result = $cms_connect->query($sql);
    return $result->fetch_assoc();
}

/*********************************************************************************/
/******************************* PRODUCT MANUAL **********************************/
/*********************************************************************************/

/************* all manual to select option ***********/
function all_manual_select_option($cms_connect){
    $sql = "SELECT * FROM file_manager WHERE file_type_id = 11 ORDER BY file_name ";
    $result = $cms_connect->query($sql);
    $output = '';
    foreach ($result as $row){
	    $output .='<option value = "'.$row['ID'].'">'.$row['file_name'].'</option>';	    
    }
    return $output;
}

/************* get all associated MANUAL id by product id ***********/
function associated_manual_by_product_id($cms_connect, $productid){
	$sql = "SELECT file_id FROM manuals WHERE product_id = $productid ORDER BY manual_order";
	$result = $cms_connect->query($sql);
    $output = array();
	foreach ($result as $row) {
		array_push($output, $row['file_id']);
	}
    return $output;
}

/************* all languages to select option ***********/
function all_languages_select_option($cms_connect, $languageid){
    $sql = "SELECT * FROM master_list WHERE pid = 1 ";
    $langArr = explode('/', $languageid);
    $result = $cms_connect->query($sql);
    $output = '';
    foreach ($result as $row){
    	if(in_array($row['id'], $langArr)){
    		$output .='<option value = "'.$row['id'].'" selected>'.$row['description'].'</option>';	    
    	}
    	else{
    		$output .='<option value = "'.$row['id'].'">'.$row['description'].'</option>';	 
    	}
	       
    }
    return $output;
}

/************* get languages by manual id ***********/
function manual_language_info($cms_connect, $productid, $fileid){
    $sql = "SELECT m.*, 
    		(SELECT GROUP_CONCAT(ml.description SEPARATOR '/') FROM master_list ml LEFT JOIN manual_language l ON l.language_id = ml.id WHERE l.manual_id = m.id) AS languages,
    		(SELECT GROUP_CONCAT(ml.ID SEPARATOR '/') FROM master_list ml LEFT JOIN manual_language l ON l.language_id = ml.id WHERE l.manual_id = m.id) AS languageIDs
    		FROM manuals m 
    		WHERE product_id = $productid AND file_id = $fileid";
    $result = $cms_connect->query($sql);
    return $result->fetch_assoc();
}


/*********************************************************************************/
/***************************** PRODUCT SPEC SHEET ********************************/
/*********************************************************************************/

/************* all spec sheet to select option ***********/
function all_spec_sheet_select_option($cms_connect){
    $sql = "SELECT * FROM file_manager WHERE file_type_id = 12 ORDER BY file_name ";
    $result = $cms_connect->query($sql);
    $output = '';
    foreach ($result as $row){
	    $output .='<option value = "'.$row['ID'].'">'.$row['file_name'].'</option>';	    
    }
    return $output;
}

/************* get all associated MANUAL id by product id ***********/
function associated_specsheet_by_product_id($cms_connect, $productid){
	$sql = "SELECT file_id FROM spec_sheet WHERE product_id = $productid ORDER BY spec_sheet_order";
	$result = $cms_connect->query($sql);
    $output = array();
	foreach ($result as $row) {
		array_push($output, $row['file_id']);
	}
    return $output;
}



/*********************************************************************************/
/***************************** PRODUCT AVAILABILITY ******************************/
/*********************************************************************************/
function product_availability($cms_connect, $productid){
	$sql = "
		SELECT p.id, p.sku, p.feature_name, p.status,
		p.company_res, p.dealer_res, p.consumer_res, p.vendor_res,
		(nsp.inactive!='No') as ns_inactive,
		(nsp.active_in_webstore!='No') as ns_webstore_active,
		nswp.active as ns_active, 
		FLOOR((UNIX_TIMESTAMP(STR_TO_DATE(nsp.startdate, '%m/%d/%Y'))-UNIX_TIMESTAMP())/(-182 * 24 * 60 * 60)) as ns_timeblock, 
		(((UNIX_TIMESTAMP(STR_TO_DATE(nsp.enddate, '%m/%d/%Y')))-(UNIX_TIMESTAMP()))>0) as ns_isdiscontinued, 
		(((UNIX_TIMESTAMP(STR_TO_DATE(nsp.startdate, '%m/%d/%Y')))-(UNIX_TIMESTAMP() - (182 * 24 * 60 * 60)))>0) as ns_isnew, 
		FLOOR((UNIX_TIMESTAMP(p.start_date)-UNIX_TIMESTAMP())/(-182 * 24 * 60 * 60)) as cms_timeblock, 
		(((UNIX_TIMESTAMP(p.start_date))-(UNIX_TIMESTAMP() - (182 * 24 * 60 * 60)))>0) as cms_isnew, 
		nswp.quantity as quantity, 
		FLOOR((UNIX_TIMESTAMP(nswp.lastQtyUpdate)-UNIX_TIMESTAMP())/(-182 * 24 * 60 * 60)) as quantity_stale, 
		nsp.msrp as msrp, 
		nsp.online_price as online_price, 
		if(nsp.msrp > nsp.online_price,((1-(nsp.online_price/nsp.msrp))*100)/1,0) as savings_pct, 
		(nsp.msrp > nsp.online_price) as savings 
		FROM product p 
		left join netsuite_products nsp on (p.nsid=nsp.nsid) 
		left join netsuite_WSDK_products nswp on (p.nsid=nswp.nsid) 
		WHERE id = $productid
		ORDER BY nswp.lastQtyUpdate
	";
	$stmt = $cms_connect->query($sql);
	return $stmt->fetch_assoc();
}

?>
