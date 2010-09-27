<?php


$outputVar = new stdClass();

$isSingle = isset($_GET["singlepost"]);

try{
	if($isSingle){//guess I don't know enough about PHP to understand why this page thinks its in a different location in relation to wp-config depending on how its called... but somehow always knows how to get tcpdf
		require_once("../../../wp-config.php");
	}
	
	require_once('tcpdf/config/lang/eng.php');
	require_once('tcpdf/tcpdf.php');
} catch (Exception $e) {
	$outputVar->status = "problem loading wp-config or TCPDF library.";
	echo json_encode($outputVar);
}

createPDFDir();

/*
try{
	ob_end_clean();
} catch (Exception $e) {
	$outputVar->status = "problem with ob_end_clean.";
	echo json_encode($outputVar);
	return;
}
*/

$uploads = wp_upload_dir();
$uploadDir = $uploads['basedir'];
$uploadURL = $uploads['baseurl'];

$adminOptions = kalins_pdf_get_admin_options();

if($isSingle){
	$singleID = substr($_GET["singlepost"], 3);
	$pdfDir = $uploadDir .'/kalins-pdf/singles/';//not sure why we need both a directory and a url - it seems like the two should evaluate to the exact same string
	$pdfURL = $uploadURL .'/kalins-pdf/singles/';
	$fileName = $singleID .'.pdf';
	
	if(file_exists($pdfDir .$fileName)){//if the file already exists, simply redirect to that file and we're done
		header("Location: " .$pdfURL .$fileName);//for some reason pdfDir doesn't work here so we use pdfURL
		return;
	}else{
		$outputVar->fileName = $fileName;
		$outputVar->date = date("Y-m-d H:i:s", time());
		
		$pageIDs = $_GET["singlepost"];
		
		$adminOptions = kalins_pdf_get_admin_options();//for individual pages/posts we grab all the PDF options from the options page instead of the POST
		
		$titlePage = $adminOptions["titlePage"];
		$finalPage = $adminOptions["finalPage"];
		$beforePage = $adminOptions["beforePage"];
		$beforePost = $adminOptions["beforePost"];
		$afterPage = $adminOptions["afterPage"];
		$afterPost = $adminOptions["afterPost"];
		$headerTitle = $adminOptions["headerTitle"];
		$headerSub = $adminOptions["headerSub"];
		$includeImages = $adminOptions["includeImages"];
		//$includeTables = $adminOptions["includeTables"];
		$fontSize = $adminOptions["fontSize"];
	}
}else{
	try{
		$pdfDir = $uploadDir .'/kalins-pdf/';
		
		if($_POST["fileNameCont"] != ""){
			$fileName = kalins_pdf_global_shortcode_replace($_POST["fileNameCont"]) .".pdf";
		}else{
			$fileName = time() .".pdf";
		}
		
		$pageIDs = stripslashes($_POST["pageIDs"]);
		$titlePage = stripslashes($_POST['titlePage']);
		$finalPage = stripslashes($_POST['finalPage']);
		$beforePage = stripslashes($_POST['beforePage']);
		$beforePost = stripslashes($_POST['beforePost']);
		$afterPage = stripslashes($_POST['afterPage']);
		$afterPost = stripslashes($_POST['afterPost']);
		$headerTitle = stripslashes($_POST['headerTitle']);
		$headerSub = stripslashes($_POST['headerSub']);
		$includeImages = stripslashes($_POST['includeImages']);
		//$includeTables = stripslashes($_POST['includeTables']);
		$fontSize = (int) $_POST['fontSize'];
		
		$kalinsPDFToolOptions = array();//collect our passed in values so we can save them for next time
			
		$kalinsPDFToolOptions["headerTitle"] = $headerTitle;
		$kalinsPDFToolOptions["headerSub"] = $headerSub;
		$kalinsPDFToolOptions["filename"] = $_POST["fileNameCont"];
		$kalinsPDFToolOptions["includeImages"] = $includeImages;
		//$kalinsPDFToolOptions["includeTables"] = $includeTables;
		$kalinsPDFToolOptions["beforePage"] = $beforePage;
		$kalinsPDFToolOptions["beforePost"] = $beforePost;
		$kalinsPDFToolOptions["afterPage"] = $afterPage;
		$kalinsPDFToolOptions["afterPost"] = $afterPost;
		$kalinsPDFToolOptions["titlePage"] = $titlePage;
		$kalinsPDFToolOptions["finalPage"] = $finalPage;
		$kalinsPDFToolOptions["fontSize"] = $fontSize;
		
		update_option(KALINS_PDF_TOOL_OPTIONS_NAME, $kalinsPDFToolOptions);//save options to database
	} catch (Exception $e) {
		$outputVar->status = "problem setting options. Be sure the text you have entered is compatible or try resetting to defaults.";
		echo json_encode($outputVar);
	}	
	
	if(file_exists($pdfDir .$fileName)){//if the file already exists, echo an error and quit
		$outputVar->status = "file already exists.";
		echo json_encode($outputVar);
		return;
	}else{
		$outputVar->fileName = $fileName;
		$outputVar->date = date("Y-m-d H:i:s", time());
	}
}

global $wpdb, $post;

$result = array ();

try{
	
	$pageArr = explode(",", $pageIDs);
	$le = count($pageArr);
	
	for($i = 0; $i < $le; $i++){
		if(substr($pageArr[$i], 0, 2) == "po"){
			array_push($result, get_post(substr($pageArr[$i], 3)));
		}else{
			array_push($result, get_page(substr($pageArr[$i], 3)));
		}
	}
} catch (Exception $e) {
	$outputVar->status = "problem getting pages and posts.";
	echo json_encode($outputVar);
	return;
}

try{
	// create new PDF document
	$objTcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
	// set document information
	$objTcpdf->SetCreator(PDF_CREATOR);
	
	if($isSingle){
		$objTcpdf->SetTitle( kalins_pdf_page_shortcode_replace($headerTitle, $result[0]) );// set default header data
		$objTcpdf->SetHeaderData(null, null, kalins_pdf_page_shortcode_replace($headerTitle, $result[0]), kalins_pdf_page_shortcode_replace($headerSub, $result[0]) );
	}else{
		$objTcpdf->SetTitle( kalins_pdf_global_shortcode_replace($headerTitle, $isSingle) );// set default header data
		$objTcpdf->SetHeaderData(null, null, kalins_pdf_global_shortcode_replace($headerTitle), kalins_pdf_global_shortcode_replace($headerSub) );
	}
	// set header and footer fonts
	$objTcpdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$objTcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	//set margins
	$objTcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$objTcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$objTcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	//set auto page breaks
	$objTcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	//set image scale factor
	$objTcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
	//set some language-dependent strings
	$objTcpdf->setLanguageArray($l); 
	//initialize document
	$objTcpdf->AliasNbPages();

} catch (Exception $e) {
	$outputVar->status = "problem setting TCPDF options. Double check header titles and font size";
	echo json_encode($outputVar);
	return;
}

try{
	if($titlePage != ""){
		$objTcpdf->AddPage();//create title page and start off our PDF file
		$objTcpdf->SetFont( PDF_FONT_NAME_MAIN, '', $fontSize );
		if($isSingle){
			$titlePage = kalins_pdf_page_shortcode_replace($titlePage, $result[0]);
		}else{
			$titlePage = kalins_pdf_global_shortcode_replace($titlePage);
		}
		$strHtml = wpautop($titlePage, true );
		$objTcpdf->writeHTML( $strHtml , true, 0, true, 0);
	}
} catch (Exception $e) {
	$outputVar->status = "problem creating title page.";
	echo json_encode($outputVar);
	return;
}

try{
	$le = count($result);
	
	for($i = 0; $i < $le; $i++){
		
		$objPost = $result[$i];
		
		$content = $objPost->post_content;
		
		if(preg_match('/\[caption +[^\]]*\]/', $content)){//remove all captions surrounding images and whatnot since tcpdf can't interpret them (but leave the images in place)
			$content = preg_replace('/\[caption +[^\]]*\]/', '', $content);//replace all instances of the opening caption tag
			$content = preg_replace('/\[\/caption\]/', '', $content);//replace all instances of the closing caption tag
		}
		
		if($includeImages != "true"){
			//remove all image tags if we don't want images
			if(preg_match('/<img[^>]+./', $content)){
				$content = preg_replace('/<img[^>]+./', '', $content);
			}
		}
		
		//echo "-------------" .$content ."-----------------";
		
		//echo $content;
		
		//if($includeTables != "true"){//TABLE REMOVAL STILL NEEDS TO BE TESTED... err actually coded I mean!!!!!!!
			/*$content = preg_replace("/^<table(?:.*?)>(.*?)</table>$/", '', $content);*/
			/*$content = preg_replace("/^<table.*?>^/", '', $content);*/
			/*$content = preg_replace('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/', '', $content);*/
			/*$content = preg_replace('/<table[^>]<\/table>/', '', $content);*/
			
			/*if(preg_match('/<table[^>]*./', $content)){
				echo "matched";
			
				
				$dom= new DOMDocument($content);
				//$dom->load($content);
				
				$dom->preserveWhiteSpace = false;
				$fullPage = $dom->documentElement;
				
				echo $dom->saveHTML();
				
				$domTable = $fullPage->getElementsByTagName("table");
				
				foreach ($domTable as $tables){
					echo DOMinnerHTML($tables);
					$fullPage = $fullPage->removeChild($tables);
				} 
				$content = (string) $fullPage;
				
			}else{
				echo "--";
			}
		}*/
		
		/*if($objPost->ID == 80){
			echo $content;
		}*/
		
		//$content = str_replace("\n", "", $content);//my attempt at replacing double line spaces between paragraphs with singles - damn tcpdf won't render a line break properly
		//$content = str_replace('\r', '', str_replace('\n', '', $content));
		//$content = ereg_replace("[\n\r]", "", $content);
		//$content = ereg_replace("\t\t+", "", $content);
		
		if($objPost->post_type == "page"){//insert appropriate html before and after every page and post
			$content = $beforePage .$content .$afterPage;
		}else{
			$content = $beforePost .$content .$afterPost;
		}
		
		$content = kalins_pdf_page_shortcode_replace($content, $objPost);
		
		/*$pos = strpos($content, '<table');//not sure what this is doing; looks like we skip the whole page if it contains a table
		if ($pos == true) continue;*/
		
		// add a page
		$objTcpdf->AddPage();
		
		// set font
		$objTcpdf->SetFont( PDF_FONT_NAME_MAIN, '', $fontSize );
	
		$strHtml = wpautop($content, true );
		
		// output the HTML content
		$objTcpdf->writeHTML( $strHtml , true, 0, true, 0);
	}
	
} catch (Exception $e) {
	$outputVar->status = "problem creating pages and posts. Perhaps there's a problem with one of the pages you've selected or with the before or after HTML.";
	echo json_encode($outputVar);
	return;
}

try{
	if($finalPage != ""){
		$objTcpdf->AddPage();//create final page in pdf
		$objTcpdf->SetFont( PDF_FONT_NAME_MAIN, '', $fontSize );
		//$finalPage = kalins_pdf_global_shortcode_replace($finalPage, $isSingle);
		
		if($isSingle){
			$finalPage = kalins_pdf_page_shortcode_replace($finalPage, $result[0]);
		}else{
			$finalPage = kalins_pdf_global_shortcode_replace($finalPage);
		}
		
		$strHtml = wpautop($finalPage, true );
		$objTcpdf->writeHTML( $strHtml , true, 0, true, 0);
	}
} catch (Exception $e) {
	$outputVar->status = "problem creating final page.";
	echo json_encode($outputVar);
	return;
}

try{
	//create and save the PDF document
	$objTcpdf->Output( $pdfDir .$fileName, 'F' );
} catch (Exception $e) {
	$outputVar->status = "problem outputting the final PDF file.";
	echo json_encode($outputVar);
	return;
}

$outputVar->status = "success";//set success status for output to AJAX


if($isSingle){//if this is called from a page/post we redirect so that user can download pdf directly
	header("Location: " .$pdfURL .$fileName);
}else{
	echo json_encode($outputVar);//if it's called from the creation station admin panel we output the result object to AJAX
}

?>