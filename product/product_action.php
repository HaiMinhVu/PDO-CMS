<?php
include('../../../dbconnect.php');
include('product_function.php');

/******** submit product add form *******/
if($_POST['action'] == 'add_product'){

	$product_name 					= $_POST['product_name'];
	$product_sku 					= $_POST['product_sku'];
	$product_nsid 					= $_POST['product_nsid'];
	$product_upc 					= $_POST['product_upc'];
	$product_manufacture 			= $_POST['product_manufacture'];
	$product_feature_name 			= $_POST['product_feature_name'];
	$product_nsn 					= $_POST['product_nsn'];
	$product_store_description 		= $_POST['product_store_description'];
	$product_feature_description 	= $_POST['product_feature_description'];
	$product_category 				= $_POST['product_category'];
	$product_keywords 				= $_POST['product_keywords'];
	$product_start_date 			= $_POST['product_start_date'] == '' ? '1969-12-31' : $_POST['product_start_date'];

	$user_id = $_SESSION['uid'];

	$new_product_id = $add_result = '';

	$insertsql = "INSERT INTO product (Name, sku, nsid, UPC, manufacture, feature_name, nsn, store_desc, feature_desc, product_category_id, keywords, start_date, uid_created, uid_modified) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$stmt = $writedb->prepare($insertsql);
	if($stmt->execute([$product_name, $product_sku, $product_nsid, $product_upc, $product_manufacture, $product_feature_name, $product_nsn, $product_store_description, $product_feature_description, $product_category, $product_keywords, $product_start_date, $user_id, $user_id])){
		$add_result = "Product Added";
		$new_product_id = $writedb->lastInsertId();
	}
	else{
		$add_result =  $writedb->errorInfo();
	}
	
	$output = array();
	$output['add_result'] = $add_result;
	$output['new_product_id'] = $new_product_id;
	echo json_encode($output);
}

if($_POST['action'] == 'delete_product'){
	$product_id = $_POST['productID'];
	$user_id = $_SESSION['uid'];
	$sql = "UPDATE product set status = 0, uid_modified = $user_id WHERE id = $product_id";
	if($cms_connect->query($sql)){
		echo "Product Deleted";
	}
	else{
		echo "Failed".$sql;
	}
}

/******** submit product general form *******/
if($_POST['action'] == 'update_general'){

	$product_id 					= $_POST['productid'];
	$product_name 					= $_POST['product_name'];
	$product_sku 					= $_POST['product_sku'];
	$product_nsid 					= $_POST['product_nsid'];
	$product_upc 					= $_POST['product_upc'];
	$product_manufacture 			= $_POST['product_manufacture'];
	$product_feature_name 			= $_POST['product_feature_name'];
	$product_nsn 					= $_POST['product_nsn'];
	$product_store_description 		= $_POST['product_store_description'];
	$product_feature_description 	= $_POST['product_feature_description'];
	$product_ns_category 				= $_POST['product_ns_category'];
	$product_keywords 				= $_POST['product_keywords'];
	$product_start_date 			= $_POST['product_start_date'] == '' ? '1969-12-31' : $_POST['product_start_date'];

	$user_id = $_SESSION['uid'];

	$updatesql = "UPDATE product SET Name = ?, sku = ?, nsid = ?, UPC = ?, manufacture = ?, feature_name = ?, nsn = ?, store_desc = ?, feature_desc = ?, product_category_id = ?, keywords = ?, start_date = ?, uid_modified = ? WHERE id = ?";
	$stmt = $cms_connect->prepare($updatesql);
	$stmt->bind_param("ssisissssissii", $product_name, $product_sku, $product_nsid, $product_upc, $product_manufacture, $product_feature_name, $product_nsn, $product_store_description, $product_feature_description, $product_ns_category, $product_keywords, $product_start_date, $user_id, $product_id);
	if($stmt->execute()){
		echo "Product Updated";
	}
	else{
		echo $cms_connect->errorInfo();
	}
	$stmt->close();

}

/******** add more battery to table *******/
if($_POST['action'] == 'add_more_battery'){
	//echo $_POST['action'];
    $output = "";
    $output .= "<tr>";
    $output .= "<td><select name='battery_id[]' class='form-control' required><option value=''>--- Select One ---</option>".battery_list($cms_connect)."</select></td>";
    $output .= "<td><input type='number' name='take_battery[]' min='0' value='0' class='form-control'></td>";
    $output .= "<td><input type='number' name='qty_included[]' min='0' value='0' class='form-control'></td>";
	$output .= "<td><input type='number' name='list_order[]' min='0' value='0' class='form-control'></td>";
    $output .= "<td><button type='button' id='remove_battery' class='btn btn-outline-danger btn-sm delete' title='Remove Battery'><i class='fa fa-minus'></i></button></td>";
    $output .= "</tr>";
    echo $output;
}


/******** display product with image for related product section *******/
if($_POST['action'] == 'add_related_product'){
	$productid = $_POST['productid'];
	$selected_related_productArr = $_POST['productIDs'];
	$existingRelatedProductOrderArr = product_related_by_id($cms_connect, $productid);

	$output = "";

	// add to non order array if new spec selected
	$non_existingRelatedProductOrder = array();
	foreach ($selected_related_productArr as $productID){
		if(!in_array($productID, $existingRelatedProductOrderArr)){
			$non_existingRelatedProductOrder[] = $productID;
		}
	}

	// remove spec that exist in the order array
	$lostRelatedIdArr = array_diff($existingRelatedProductOrderArr, $selected_related_productArr);
	foreach($lostRelatedIdArr as $lost){
		$key = array_search($lost, $existingRelatedProductOrderArr);
		unset($existingRelatedProductOrderArr[$key]);
	}
	
	$final_RelatedArr = array_merge($existingRelatedProductOrderArr, $non_existingRelatedProductOrder);
	foreach ($final_RelatedArr as $id){
		$sql = "SELECT p.sku, p.id, m.site, fm.file_name, p.feature_name
				FROM product p LEFT JOIN file_manager fm ON fm.ID = p.main_img_id
								LEFT JOIN list_manufacture m ON m.id = p.manufacture
                WHERE p.id = $id";
        $result = $cms_connect->query($sql);
        $row = $result->fetch_assoc();
        $src = $row['site'].'/images/'.$row['file_name'];
		$output .= "<tr>";
	    $output .= "<td><a href='".$src."' data-toggle='lightbox' data-max-height='600'><img src='".$src."' height='80'></a></td>";
	    $output .= "<td>".$row['sku']."</td>";
		$output .= "<td>".$row['feature_name']."</td>";
		$output .= "<td><input type='hidden' name='related_products[]' value='".$id."'></td>";
	    $output .= "<td><button type='button' name='delete' id='".$id."'' class='btn btn-outline-danger btn-sm remove_product' title='Remove Related Product'><i class='fa fa-minus'></i></button></td>";
	    $output .= "</tr>";
	}
	echo $output;
}

/******** submit product extra form *******/
if($_POST['action'] == 'update_extra'){

	// initial data
	$product_id 					= $_POST['productid'];
	$product_feature 				= $_POST['product_feature'];
	$product_included 				= $_POST['product_included'];
	$battery_idArr 					= $_POST['battery_id'];
	$take_batteryArr 				= $_POST['take_battery'];
	$qty_includedArr 				= $_POST['qty_included'];
	$list_orderArr 					= $_POST['list_order'];
	$product_relatedArr 			= $_POST['product_related'];
	$related_productArr 			= $_POST['related_products'];
	
	$user_id = $_SESSION['uid'];


	$feature_result = $included_result = $battery_result = $related_result = '';

	/************* product feature *************/
	$old_featureArr = product_feature_by_id($cms_connect, $product_id);
	$new_featureArr = explode(',', $product_feature);

	if(count(array_diff($new_featureArr, $old_featureArr)) != 0 || $old_featureArr !== $new_featureArr){
		$fstmt = $cms_connect->prepare("DELETE FROM product_feature WHERE product_id = ?");
		$fstmt->bind_param("i",  $product_id);
		$fstmt->execute();
		$fstmt->close();
		$order = 10;
		foreach($new_featureArr as $feature){
			$f_insertsql = "INSERT INTO product_feature (product_id, feat_item, feature_order, uid_created, uid_modified) VALUES (?, ?, ?, ?, ?)";
			$fstmt = $cms_connect->prepare($f_insertsql);
			$fstmt->bind_param("isiii", $product_id, $feature, $order, $user_id, $user_id);
			if($fstmt->execute()){
				$feature_result = "Product Feature Updated";
				$order += 10;
			}
			else{
				$feature_result = $cms_connect->errorInfo();
			}
			$fstmt->close();
		}
	}

	/************* product included *************/
	$old_includedArr = product_included_by_id($cms_connect, $product_id);
	$new_includedArr = explode(',', $product_included);

	if(count(array_diff($new_includedArr, $old_includedArr)) != 0 || $new_includedArr !== $old_includedArr){
		$istmt = $cms_connect->prepare("DELETE FROM product_included WHERE product_id = ?");
		$istmt->bind_param("i",  $product_id);
		$istmt->execute();
		$istmt->close();
		$order = 10;
		foreach($new_includedArr as $included){
			$i_insertsql = "INSERT INTO product_included (product_id, included_items, included_order, uid_created, uid_modified) VALUES (?, ?, ?, ?, ?)";
			$istmt = $cms_connect->prepare($i_insertsql);
			$istmt->bind_param("isiii", $product_id, $included, $order, $user_id, $user_id);
			if($istmt->execute()){
				$included_result = "Product Included Updated";
				$order += 10;

			}
			else{
				$included_result = $cms_connect->errorInfo();
			}
			$istmt->close();
		}
	}

	/************* product battery *************/
	$old_batteryArr = product_battery_by_id($cms_connect, $product_id);
	$new_batteryArr = $battery_idArr;

	if(count(array_diff($new_batteryArr, $old_batteryArr)) != 0 || $new_batteryArr !== $old_batteryArr){
		$bstmt = $cms_connect->prepare("DELETE FROM product_battery WHERE product_id = ?");
		$bstmt->bind_param("i",  $product_id);
		$bstmt->execute();
		$bstmt->close();

		for($i = 0; $i < count($battery_idArr); $i++){
			$b_insertsql = "INSERT INTO product_battery (product_id, battery_id, battery_order, battery_qty, included, uid_created, uid_modified) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$bstmt = $cms_connect->prepare($b_insertsql);
			$bstmt->bind_param("iiiiiii", $product_id, $battery_idArr[$i], $list_orderArr[$i], $take_batteryArr[$i], $qty_includedArr[$i], $user_id, $user_id);
			if($bstmt->execute()){
				$battery_result = "Product Battery Updated";
			}
			else{
				$battery_result = $cms_connect->errorInfo();
			}
			$bstmt->close();
		}
	}

	/************* product related *************/
	$old_relatedArr = product_related_by_id($cms_connect, $product_id);
	$new_relatedArr = $related_productArr;

	if(count(array_diff($new_relatedArr, $old_relatedArr)) != 0 || $old_relatedArr !== $new_relatedArr){
		$rstmt = $cms_connect->prepare("DELETE FROM list_related_product WHERE product_id = ?");
		$rstmt->bind_param("i",  $product_id);
		$rstmt->execute();
		$rstmt->close();
		$order = 10;
		foreach($new_relatedArr as $related){
			$related_tags = '';
			$r_insertsql = "INSERT INTO list_related_product (product_id, related_product_id, related_product_order, uid_created, uid_modified) VALUES (?, ?, ?, ?, ?)";
			$rstmt = $cms_connect->prepare($r_insertsql);
			$rstmt->bind_param("iiiii", $product_id, $related, $order, $user_id, $user_id);
			if($rstmt->execute()){
				$related_result = "Related Product Updated";
				$order += 10;
			}
			else{
				$related_result = $cms_connect->errorInfo();
			}
			$rstmt->close();
		}
	}

	$output = array();
	$output['feature_result'] = $feature_result;
	$output['included_result'] = $included_result;
	$output['battery_result'] = $battery_result;
	$output['related_result'] = $related_result;
	echo json_encode($output);
}

/******** submit product category form *******/
if($_POST['action'] == 'update_category'){

	$product_id = $_POST['productid'];
	$associatedCatIDArr = explode(',', $_POST['associatedCatID']);
	$prim_category = $_POST['prim_cat'];
	$user_id = $_SESSION['uid'];
	
	// delete all association
	$cat_delete_stmt = $cms_connect->prepare("DELETE FROM product_category_association WHERE product_id = ?");
	$cat_delete_stmt->bind_param("i",  $product_id);
	$cat_delete_stmt->execute();

	// insert new association
	foreach($associatedCatIDArr as $associatedCatID){
		$prim = ($associatedCatID == $prim_category ? 1 : 0);
		$insertsql = "INSERT INTO product_category_association (product_id, category_id, pca_primary, uid_created, uid_modified) VALUES (?,?,?,?,?)";
		$insert_cat_stmt = $cms_connect->prepare($insertsql);
		$insert_cat_stmt->bind_param("iiiii", $product_id, $associatedCatID, $prim, $user_id, $user_id);
		if($insert_cat_stmt->execute()){
			$result = 'Category Association Updated';
		}
		else{
			$result = 'Failed';
		}
	}
	echo $result;
}

/******** add selected images to table *******/
if($_POST['action'] == 'add_associated_image'){
	$productid = $_POST['productid'];
	$selected_imageArr = $_POST['imageids'];
	$existingImageOrderArr = associated_imgs_by_product_id($cms_connect, $productid);
	$output = "";

	// add to non order array if new spec selected
	$non_existingImageOrder = array();
	foreach ($selected_imageArr as $imgId){
		if(!in_array($imgId, $existingImageOrderArr)){
			$non_existingImageOrder[] = $imgId;
		}
	}

	// remove spec that exist in the order array
	$lostImageIdArr = array_diff($existingImageOrderArr, $selected_imageArr);
	foreach($lostImageIdArr as $lost){
		$key = array_search($lost, $existingImageOrderArr);
		unset($existingImageOrderArr[$key]);
	}

	$final_imageArr = array_merge($existingImageOrderArr, $non_existingImageOrder);

	foreach ($final_imageArr as $imageid){
		$img_url_name = file_info_by_id($cms_connect, $imageid);  // get image info by image id
		$imgname = $img_url_name['file_name'];
		$imgurl = $img_url_name['url'];

		$imgdescription = '';
		$imginfo = image_info_by_id($cms_connect, $imageid, $productid);  // get image description from product_img table
		if(count($imginfo) != 0){
			$imgdescription = $imginfo['alt_description'];
		}

		$output .= "<tr>";
	    $output .= "<td><a href='".$imgurl."' data-toggle='lightbox' data-max-height='600'><img src='".$imgurl."' height=80></a></td>";
	    $output .= "<td>".$imgname."</td>";
	    $output .= "<td><input type='text' name='description[]' value='".$imgdescription."' class='form-control'></td>";
		$output .= "<td><input type='radio' name='primary_image' value='".$imageid."' required></td>";
	    $output .= "<td><button type='button' name='delete' id='".$imageid."'' class='btn btn-outline-danger btn-sm disassociate' title='Disassociate Image'><i class='fa fa-minus'></i></button></td>";
	    $output .= "<input type='hidden' value='".$imageid."' name='associatedImages[]'>";
	    $output .= "</tr>";
	}
	echo $output;
}

/******** submit product image form *******/
if($_POST['action'] == 'update_image'){
	$product_id = $_POST['productid'];
	$associatedImgArr = $_POST['associatedImages'];
	$descArr = $_POST['description'];
	$primary = $_POST['primary_image'];
	$user_id = $_SESSION['uid'];

	
	
	$update_prim = $cms_connect->prepare("UPDATE product SET main_img_id = ?, uid_modified = ? WHERE id = ?");
	$update_prim->bind_param("iii",  $primary, $user_id, $product_id);
	$update_prim->execute();
	$update_prim->close();

	// delete all existing associated
	$delete_stmt = $cms_connect->prepare("DELETE FROM product_img WHERE product_id = ?");
	$delete_stmt->bind_param("i",  $product_id);
	$delete_stmt->execute();
	$delete_stmt->close();

	// insert new images
	$order = 1;
	for($i = 0; $i < count($associatedImgArr); $i++){
		$image_insertsql = "INSERT INTO product_img (product_id, alt_description, file_id, img_order, uid_created, uid_modified) VALUES (?, ?, ?, ?, ?, ?)";
		$image_stmt = $cms_connect->prepare($image_insertsql);
		$image_stmt->bind_param("isiiii", $product_id, $descArr[$i], $associatedImgArr[$i], $order, $user_id, $user_id);
		if($image_stmt->execute()){
			$result = "Associated Image Updated";
			$order++;
		}
		else{
			$result = $cms_connect->errorInfo();
		}
		$image_stmt->close();
	}
	echo $result;
}

/******** add spec to form on change of selection *******/
if($_POST['action'] == 'product_spec_change'){
	$productid = $_POST['productid'];
	$selected_specArr = $_POST['specids'];
	$existingSpecOrderArr = spec_id_by_product_id($cms_connect, $productid);
	$output = "";
	
	// add to non order array if new spec selected
	$non_existingSpecOrder = array();
	foreach ($selected_specArr as $specId){
		if(!in_array($specId, $existingSpecOrderArr)){
			$non_existingSpecOrder[] = $specId;
		}
	}

	// remove spec that exist in the order array
	$lostSpecIdArr = array_diff($existingSpecOrderArr, $selected_specArr);
	foreach($lostSpecIdArr as $lost){
		$key = array_search($lost, $existingSpecOrderArr);
		unset($existingSpecOrderArr[$key]);
	}

	$final_specArr = array_merge($existingSpecOrderArr, $non_existingSpecOrder);

	foreach ($final_specArr as $specId){
		$specInfo = spec_info_by_ids($cms_connect, $specId, $productid);
		$specname = $specdescription = $specsuffix = '';
		if(count($specInfo) != 0){
			$specname = utf8_encode($specInfo['name']);
			$specdescription = $specInfo['description'];
			$specsuffix = $specInfo['suffix'];
		}
		else{
			$specname = spec_name_by_ids($cms_connect, $specId);
		}
		$output .= "<tr>";
	    $output .= "<td>".$specname."</td>";
	    $output .= "<td><input type='text' name='description[]' value='".$specdescription."' class='form-control'></td>";
	    $output .= "<td><input type='text' name='suffix[]' value='".$specsuffix."' class='form-control'></td>";
		$output .= "<td><button type='button' name='removespec' id='".$specId."'' class='btn btn-outline-danger btn-sm removespec' title='Remove Spec'><i class='fa fa-minus'></i></button></td>";
	    $output .= "<input type='hidden' value='".$specId."' name='specid[]'>";
	    $output .= "</tr>";

	}
	echo $output;
}

/******** product specs like other products *******/
if($_POST['action'] == 'product_spec_like'){
	$output = '';
	$productID = $_POST['productID'];
	$sql = "SELECT ps.*, lps.name FROM product_spec ps LEFT JOIN list_product_spec lps ON lps.id = ps.spec_id WHERE product_id = $productID";
	$stmt = $cms_connect->query($sql);
	while($row = $stmt->fetch_assoc()){
		$output .= "<tr>";
	    $output .= "<td>".$row['name']."</td>";
	    $output .= "<td><input type='text' name='description[]' value='".$row['description']."' class='form-control'></td>";
	    $output .= "<td><input type='text' name='suffix[]' value='".$row['suffix']."' class='form-control'></td>";
		$output .= "<td><button type='button' name='removespec' id='".$row['spec_id']."'' class='btn btn-outline-danger btn-sm removespec' title='Remove Spec'><i class='fa fa-minus'></i></button></td>";
	    $output .= "<input type='hidden' value='".$row['spec_id']."' name='specid[]'>";
	    $output .= "</tr>";
	}
	echo $output;
}


/******** submit product spec form *******/
if($_POST['action'] == 'update_spec'){
	$productid = $_POST['productid'];
	$specArr = $_POST['specid'];
	$descriptionArr = $_POST['description'];
	$suffixArr = $_POST['suffix'];
	$user_id = $_SESSION['uid'];

	$oldSpecArr = spec_id_by_product_id($cms_connect, $productid);

	// delete all existing associated
	$delete_stmt = $cms_connect->prepare("DELETE FROM product_spec WHERE product_id = ?");
	$delete_stmt->bind_param("i",  $productid);
	$delete_stmt->execute();
	$delete_stmt->close();

	// insert new images
	$order = 1;
	for($i = 0; $i < count($specArr); $i++){
		$spec_insertsql = "INSERT INTO product_spec (spec_id, product_id, description, prod_order, suffix, uid_created, uid_modified) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$spec_stmt = $cms_connect->prepare($spec_insertsql);
		$spec_stmt->bind_param("iisisii", $specArr[$i], $productid, $descriptionArr[$i], $order, $suffixArr[$i], $user_id, $user_id);
		if($spec_stmt->execute()){
			$result = "Product Specs Updated";
			$order++;
		}
		else{
			$result = $cms_connect->errorInfo();
		}
		$spec_stmt->close();
	}

	echo $result;
}


/******** add selected Reticle Image to table *******/
if($_POST['action'] == 'add_associated_reticle'){
	$productid = $_POST['productid'];
	$selected_ReticleArr = $_POST['imageids'];
	$existingReticleOrderArr = associated_reticle_by_product_id($cms_connect, $productid);
	$output = "";

	// add to non order array if new spec selected
	$non_existingReticleOrder = array();
	foreach ($selected_ReticleArr as $imgId){
		if(!in_array($imgId, $existingReticleOrderArr)){
			$non_existingReticleOrder[] = $imgId;
		}
	}

	// remove spec that exist in the order array
	$lostImageIdArr = array_diff($existingReticleOrderArr, $selected_ReticleArr);
	foreach($lostImageIdArr as $lost){
		$key = array_search($lost, $existingReticleOrderArr);
		unset($existingReticleOrderArr[$key]);
	}

	$final_reticleArr = array_merge($existingReticleOrderArr, $non_existingReticleOrder);

	foreach ($final_reticleArr as $imageid){
		$img_url_name = file_info_by_id($cms_connect, $imageid);  // get image info by image id
		$imgname = $img_url_name['file_name'];
		$imgurl = $img_url_name['url'];

		$reticleTooltip = $reticleTitle = '';
		$reticleinfo = reticle_info_by_id($cms_connect, $imageid, $productid);  // get reticle tool tip from product_reticle table
		if(count($reticleinfo) != 0){
			$reticleTooltip = $reticleinfo['tool_tips'];
			$reticleTitle = $reticleinfo['title'];
		}

		$output .= "<tr>";
	    $output .= "<td><a href='".$imgurl."' data-toggle='lightbox' data-max-height='600'><img src='".$imgurl."' height=80></a></td>";
	    $output .= "<td>".$imgname."</td>";
	    $output .= "<td><input type='text' name='title[]' value='".$reticleTitle."' class='form-control'></td>";
	    $output .= "<td><input type='text' name='tooltip[]' value='".$reticleTooltip."' class='form-control'></td>";
	    $output .= "<td><button type='button' name='delete' id='".$imageid."'' class='btn btn-outline-danger btn-sm disassociate' title='Disassociate Image'><i class='fa fa-minus'></i></button></td>";
	    $output .= "<input type='hidden' value='".$imageid."' name='associatedReticles[]'>";
	    $output .= "</tr>";
	}
	echo $output;
}

/******** submit product RETICLE form *******/
if($_POST['action'] == 'update_reticle'){
	$product_id = $_POST['productid'];
	$associatedReticlesArr = $_POST['associatedReticles'];
	$tooltipArr = $_POST['tooltip'];
	$title = $_POST['title'];
	$user_id = $_SESSION['uid'];

	// delete all existing associated
	$delete_stmt = $cms_connect->prepare("DELETE FROM product_reticle WHERE product_id = ?");
	$delete_stmt->bind_param("i",  $product_id);
	$delete_stmt->execute();
	$delete_stmt->close();

	// insert new images
	$order = 1;
	for($i = 0; $i < count($associatedReticlesArr); $i++){
		$image_insertsql = "INSERT INTO product_reticle (product_id, file_id, tool_tips, title, reticle_order, uid_created, uid_modified) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$image_stmt = $cms_connect->prepare($image_insertsql);
		$image_stmt->bind_param("iissiii", $product_id, $associatedReticlesArr[$i], $tooltipArr[$i], $title[$i], $order, $user_id, $user_id);
		if($image_stmt->execute()){
			$result = "Associated Reticle Updated";
			$order++;
		}
		else{
			$result = $cms_connect->errorInfo();
		}
		$image_stmt->close();
	}
	echo $result;
}



/******** add selected Manual to table *******/
if($_POST['action'] == 'add_associated_manual'){
	$productid = $_POST['productid'];
	$selected_ManualArr = $_POST['fileIDs'];
	$existingManualOrderArr = associated_manual_by_product_id($cms_connect, $productid);
	//echo json_encode($selected_ManualArr);
	$output = "";

	// add to non order array if new spec selected
	$non_existingManualOrder = array();
	foreach ($selected_ManualArr as $fileid){
		if(!in_array($fileid, $existingManualOrderArr)){
			$non_existingManualOrder[] = $fileid;
		}
	}

	// remove spec that exist in the order array
	$lostManualIdArr = array_diff($existingManualOrderArr, $selected_ManualArr);
	foreach($lostManualIdArr as $lost){
		$key = array_search($lost, $existingManualOrderArr);
		unset($existingManualOrderArr[$key]);
	}

	$final_manualArr = array_merge($existingManualOrderArr, $non_existingManualOrder);

	foreach ($final_manualArr as $fileID){
		$fileInfo = file_info_by_id($cms_connect, $fileID);
		$fileName = $fileInfo['file_name'];
		$filelUrl = $fileInfo['url'];

		$language = $languageID = '';

		$manualInfo = manual_language_info($cms_connect, $productid, $fileID);  
		if(count($manualInfo) != 0){
			$language = $manualInfo['languages'];
			$languageID = $manualInfo['languageIDs'];
		}

		$output .= "<tr>";
	    $output .= "<td><a href='".$filelUrl."' target='_blank'>".$fileName."</td>";
	    $output .= "<td>".$language."</td>";
	    $output .= "<td>
	    				<select name='languages".$fileID."[]' class='form-control' data-live-search='true' multiple data-actions-box='true' data-selected-text-format='count'>
							".all_languages_select_option($cms_connect, $languageID)."
						</select>
					</td>";
	    $output .= "<td><button type='button' name='delete' id='".$fileID."'' class='btn btn-outline-danger btn-sm disassociate' title='Disassociate Image'><i class='fa fa-minus'></i></button></td>";
	    $output .= "<input type='hidden' value='".$fileID."' name='associatedManuals[]'>";
	    $output .= "</tr>";
	}
	echo $output;
}

/******** submit product MANUAL form *******/
if($_POST['action'] == 'update_manual'){
	$productid = $_POST['productid'];
	$associatedManualArr = $_POST['associatedManuals'];

	$user_id = $_SESSION['uid'];
	$order = 1;

	$deletemanual = "DELETE FROM manuals WHERE product_id = $productid";
	$cms_connect->query($deletemanual);

	foreach ($associatedManualArr as $id){
		$languageArr = $_POST['languages'.$id];

		// delete all manuals and languages
		$deletesql = "DELETE ml.* FROM manuals m LEFT JOIN manual_language ml ON ml.manual_id = m.id WHERE m.product_id = $productid AND file_id = $id";
		$cms_connect->query($deletesql);

		// insert new manuals and languages
		$insertmanual = "INSERT INTO manuals (product_id, file_id, manual_order, uid_created, uid_modified) VALUES (?,?,?,?,?)";
		$manualstmt = $cms_connect->prepare($insertmanual);
		$manualstmt->bind_param("iiiii", $productid, $id, $order, $user_id, $user_id);
		if($manualstmt->execute()){
			$lastManualID = $cms_connect->insert_id;
			foreach ($languageArr as $langID) {
				$insertlanguage = "INSERT INTO manual_language (manual_id, language_id, uid_created, uid_modified) VALUES (?,?,?,?)";
				$langstmt = $cms_connect->prepare($insertlanguage);
				$langstmt->bind_param("iiii", $lastManualID, $langID, $user_id, $user_id);
				$langstmt->execute();
				$langstmt->close();
			}

			$result = 'Product Manual Updated';
		}
		else{
			$result = 'Failed';
		}
		$manualstmt->close();
	
		$order++;
	}
	
	echo $result;
}



/******** add selected SPEC SHEET to table *******/
if($_POST['action'] == 'add_associated_specsheet'){
	$productid = $_POST['productid'];
	$selected_SpecSheetArr = $_POST['fileIDs'];
	$existingSpecSheetOrderArr = associated_specsheet_by_product_id($cms_connect, $productid);

	$output = "";

	// add to non order array if new spec selected
	$non_existingSpecSheetOrder = array();
	foreach ($selected_SpecSheetArr as $fileid){
		if(!in_array($fileid, $existingSpecSheetOrderArr)){
			$non_existingSpecSheetOrder[] = $fileid;
		}
	}

	// remove spec that exist in the order array
	$lostManualIdArr = array_diff($existingSpecSheetOrderArr, $selected_SpecSheetArr);
	foreach($lostManualIdArr as $lost){
		$key = array_search($lost, $existingSpecSheetOrderArr);
		unset($existingSpecSheetOrderArr[$key]);
	}

	$final_specsheetArr = array_merge($existingSpecSheetOrderArr, $non_existingSpecSheetOrder);

	foreach ($final_specsheetArr as $fileID){
		$fileInfo = file_info_by_id($cms_connect, $fileID);
		$fileName = $fileInfo['file_name'];
		$filelUrl = $fileInfo['url'];

		$output .= "<tr>";
	    $output .= "<td><a href='".$filelUrl."' target='_blank'>".$fileName."</td>";
	    $output .= "<td><button type='button' name='delete' id='".$fileID."'' class='btn btn-outline-danger btn-sm disassociate' title='Disassociate Image'><i class='fa fa-minus'></i></button></td>";
	    $output .= "<input type='hidden' value='".$fileID."' name='associatedSpecSheets[]'>";
	    $output .= "</tr>";
	}
	echo $output;
}

/******** submit product SPEC SHEET form *******/
if($_POST['action'] == 'update_specsheet'){
	$productid = $_POST['productid'];
	$associatedSpecSheetArr = $_POST['associatedSpecSheets'];
	$user_id = $_SESSION['uid'];
	$order = 1;

	$deletemanual = "DELETE FROM spec_sheet WHERE product_id = $productid";
	$cms_connect->query($deletemanual);

	foreach ($associatedSpecSheetArr as $id){
		$insertmanual = "INSERT INTO spec_sheet (product_id, file_id, spec_sheet_order, uid_created, uid_modified) VALUES (?,?,?,?,?)";
		$manualstmt = $cms_connect->prepare($insertmanual);
		$manualstmt->bind_param("iiiii", $productid, $id, $order, $user_id, $user_id);
		if($manualstmt->execute()){
			$result = 'Spec Sheet Updated';
		}
		else{
			$result = 'Failed';
		}
		$manualstmt->close();
		$order++;
	}
	echo $result;
}

/******** submit product AVAILABILITY form *******/
if($_POST['action'] == 'update_availability'){
	$productid = $_POST['productid'];
	$status = $_POST['status'] == 'on' ? 1 : 0;
	$company = $_POST['company'] == 'on' ? 1 : 0;
	$dealer = $_POST['dealer'] == 'on' ? 1 : 0;
	$consumer = $_POST['consumer'] == 'on' ? 1 : 0;
	$vendor = $_POST['vendor'] == 'on' ? 1 : 0;
	$user_id = $_SESSION['uid'];

	$sql = "UPDATE product SET status = ? , company_res = ?, dealer_res = ?, consumer_res = ?, vendor_res = ?, uid_modified = ? WHERE id = ?";
	$stmt = $cms_connect->prepare($sql);
	$stmt->bind_param("iiiiiii", $status, $company, $dealer, $consumer, $vendor, $user_id, $productid);
	if($stmt->execute()){
		$result = 'Availability Updated.';
	}
	else{
		$result = 'Failed';
	}
	echo $result;
}
?>