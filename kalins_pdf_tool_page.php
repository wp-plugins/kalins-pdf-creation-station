<?php
	
	if ( !function_exists( 'add_action' ) ) {
		echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
		exit;
	}
	
	$create_nonce = wp_create_nonce( 'kalins_pdf_tool_create' );
	$delete_nonce = wp_create_nonce( 'kalins_pdf_tool_delete' );
	$reset_nonce = wp_create_nonce( 'kalins_pdf_tool_reset' );
	
	$adminOptions = kalins_pdf_get_tool_options();
	$pageList = get_pages();
	$postList = get_posts('numberposts=-1');

	$pdfList = array();
	$count = 0;
	
	$uploads = wp_upload_dir();
	//$pdfDir = $uploads['basedir'].'/kalin-pdf/';
	$pdfDir = WP_PLUGIN_DIR . '/kalins-pdf-creation-station/pdf/';
	//if ($handle = opendir(get_bloginfo('wpurl') .'/wp-content/plugins/kalins-pdf-creation-station/pdf/')) {//open pdf directory//get_bloginfo('wpurl') .'/wp-content/plugins/kalins-pdf-creation-station/pdf/'
	if ($handle = opendir($pdfDir)) {

		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && substr($file, stripos($file, ".")+1, 3) == "pdf") {//loop to find all relevant files 
				$fileObj = new stdClass();
				$fileObj->fileName = $file;
				$fileObj->date = date("Y-m-d H:i:s", filemtime($pdfDir .$file));
				$pdfList[$count] = $fileObj;//compile array of file information simply to pass to javascript
				$count++;
			}
		}
		closedir($handle);
	}
?>


<script type='text/javascript'>

jQuery(document).ready(function($){
	var pdfList = <?php echo json_encode($pdfList);//hand over the objects and vars that javascript will need?>;
	var pageList = <?php echo json_encode($pageList);?>;
	var postList = <?php echo json_encode($postList); ?>;
	var createNonce = '<?php echo $create_nonce; //pass a different nonce security string for each possible ajax action?>'
	var deleteNonce = '<?php echo $delete_nonce; ?>';
	var resetNonce = '<?php echo $reset_nonce; ?>';
	
	function buildFileTable(){//build the file table - we build it all in javascript so we can simply rebuild it whenever an entry is added through ajax
		if(pdfList.length == 0){
			$('#pdfListDiv').html("You do not have any custom PDF files.");
			return;
		}
		
		function tc(str){
			return "<td>" + str + "</td>";
		}
		
		var tableHTML = "<table width='%100' border='1' cellspacing='1' cellpadding='3'><tr><th scope='col'>#</th><th scope='col'>File Name</th><th scope='col'>Creation Date</th><th scope='col'>Delete&nbsp;&nbsp;<button name='btnDeleteAll' id='btnDeleteAll'>Delete All</button></th></tr>";
			
		var l = pdfList.length;
		for(var i=0; i<l; i++){
			var fileLink = tc("<a href='<?php echo WP_PLUGIN_URL; ?>/kalins-pdf-creation-station/pdf/" + pdfList[i].fileName + "' target='_blank'>" + pdfList[i].fileName + "</a>");
			tableHTML += "<tr>" + tc(i) + fileLink + tc(pdfList[i].date) + tc("<button name='btnDelete_" + i + "' id='btnDelete_" + i + "'>Delete</button>") + "</tr>";
		}
	
		tableHTML += "</table>";
		$('#pdfListDiv').html(tableHTML);
		
		for(i=0; i<l; i++){
			$('#btnDelete_' + i).click(function(){
				var fileIndex = parseInt($(this).attr('name').substr(10));							
				if(confirm("Are you sure you want to delete " + pdfList[fileIndex].fileName + "?")){							
					deleteFile(pdfList[fileIndex].fileName, fileIndex);
				}
			});
		}
		
		$('#btnDeleteAll').click(function(){
			if(confirm("Are you sure you want to delete all your custom created PDF files?")){
				deleteFile("all");
			}
		});
	}
	
	var selectAllPageState = true;
	var selectAllPostState = true;
	
	$('#btnSelectAllPages').click(function() {
		var l = pageList.length;
		for(var i=0; i<l; i++){
			$('#chk' + pageList[i]['ID']).attr('checked', selectAllPageState);	
		}
		selectAllPageState = !selectAllPageState;
	});
	
	$('#btnSelectAllPosts').click(function() {
		var l = postList.length; 
		for(var i=0; i<l; i++){
			$('#chk' + postList[i]['ID']).attr('checked', selectAllPostState);
		}
		selectAllPostState = !selectAllPostState;
	});
	
	function deleteFile(fileName, indexToDelete){//takes a single fileName or "all"

		var data = {action: 'kalins_pdf_tool_delete', filename: fileName, _ajax_nonce : deleteNonce};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			//alert('Got this from the server: ' + response.substr(0, response.lastIndexOf("}") + 1));
			var newFileData = JSON.parse(response.substr(0, response.lastIndexOf("}") + 1));//parse response while removing strange trailing 0 from the response (anyone know why that 0 is being added by jquery or wordpress?)
			if(newFileData.status == "success"){
				if(fileName == "all"){
					pdfList = new Array();
					$('#createStatus').html("Files deleted successfully");
				}else{
					$('#createStatus').html("File deleted successfully");
					pdfList.splice(indexToDelete, 1);
				}
				buildFileTable();
			}else{
				//if(newFileData.status == "exists"){
				$('#createStatus').html(newFileData.status);
				//}
			}
		});
	}
	
	
	$('#btnCreate').click(function() {
		$('#sortDialog').dialog('close');
								   
		var sortString = $("#sortable").sortable('toArray').join(",");
		
		createDocument(sortString);
	});
	
	$('#createNow').click(function() {
		
		var sortString = '';
		var pageCount = 0;
		var l = pageList.length;		   
		for(var i=0; i<l; i++){
			if($('#chk' + pageList[i]['ID']).is(':checked')){
				//pageIDList += "," + pageList[i].ID;
				sortString += 'pg_' + pageList[i]['ID'] + ",";
				pageCount++;
			}
		}

		var l = postList.length;		   
		for(var i=0; i<l; i++){
			if($('#chk' + postList[i]['ID']).is(':checked')){
				sortString += 'po_' + postList[i]['ID'] + ",";
				pageCount++;
			}
		}
		
		if(pageCount == 0){
			$('#createStatus').html("Error: you must select at least one page or post to create a PDF.");
			return;
		}
		
		sortString = sortString.substr(0, sortString.length - 1);
		
		createDocument(sortString);
	
	});
	
	function createDocument(sortString){
		
		var data = { action: 'kalins_pdf_tool_create',
			pageIDs : sortString,
			_ajax_nonce : createNonce
		}

		data.titlePage = $("#txtTitlePage").val();
		data.beforePage = $("#txtBeforePage").val();
		data.beforePost = $("#txtBeforePost").val();
		data.afterPage = $("#txtAfterPage").val();
		data.afterPost = $("#txtAfterPost").val();
		data.fileNameCont = $("#txtFileName").val();
		data.includeImages = $("#chkIncludeImages").is(':checked');
		//data.includeTables = $("#chkIncludeTables").is(':checked');
		data.headerTitle = $("#txtHeaderTitle").val();
		data.headerSub = $("#txtHeaderSub").val();
		data.finalPage = $("#txtFinalPage").val();
		data.fontSize = $("#txtFontSize").val();
		
		$('#createStatus').html("Building PDF file. Wait time will depend on the length of the document, image complexity and current server load. Refreshing the page or navigating away will cancel the build.");

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			
			var startPosition = response.indexOf("{")
			var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
			
			var newFileData = JSON.parse(responseObjString);
			if(newFileData.status == "success"){
				$('#createStatus').html("File created successfully");
				pdfList.push(newFileData);
				buildFileTable();
			}else{
				
				$('#createStatus').html("Error: " + newFileData.status);
				//$('#createStatus').html(response);
			}
		});
	}
	
	$('#btnReset').click(function(){
		if(confirm("Are you sure you want to reset all of your field values? You will lose all the information you have entered into the form. (This will NOT delete or change your existing PDF documents.)")){
			var data = { action: 'kalins_pdf_tool_defaults', _ajax_nonce : resetNonce};
			
			jQuery.post(ajaxurl, data, function(response) {
				var newValues = JSON.parse(response.substr(0, response.lastIndexOf("}") + 1));
				$('#txtBeforePage').val(newValues["beforePage"]);
				$('#txtBeforePost').val(newValues["beforePost"]);
				$('#txtAfterPage').val(newValues["afterPage"]);
				$('#txtAfterPost').val(newValues["afterPost"]);
				$('#txtTitlePage').val(newValues["titlePage"]);
				$('#txtFinalPage').val(newValues["finalPage"]);
				$('#txtFontSize').val(newValues["fontSize"]);
				$('#txtHeaderTitle').val(newValues["headerTitle"]);
				$('#txtHeaderSub').val(newValues["headerSub"]);
				$('#txtFileName').val(newValues["filename"]);
				
				if(newValues["includeImages"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
					$('#chkIncludeImages').attr('checked', true);
				}else{
					$('#chkIncludeImages').attr('checked', false);
				}
			});
		}
	});
	
	function toggleWidgets() {//make menus collapsible
		$('.collapse').addClass('plus');

		$('.collapse').click(function() {
			$(this).toggleClass('plus').toggleClass('minus').next().toggle(180);
		});
	}
	
	$('#btnCreateCancel').click(function(){
		$('#sortDialog').dialog('close');									 
	});
	
	$(function() {
		$('#sortDialog').dialog({
			autoOpen: false,
			show: 'blind',
			hide: 'explode',
			width: 370,
			resizable:false,
			modal: true
		});
		
		$('#btnOpenDialog').click(function() {
				
			
			var sortHTML = '<ul id="sortable">';
			var pageCount = 0;
			var l = pageList.length;		   
			for(var i=0; i<l; i++){
				if($('#chk' + pageList[i]['ID']).is(':checked')){
					sortHTML += '<li class="ui-state-default" id="pg_' + pageList[i]['ID'] + '"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' + pageList[i].post_title + '</li>';
					pageCount++;
				}
			}
	
			var l = postList.length;		   
			for(var i=0; i<l; i++){
				if($('#chk' + postList[i]['ID']).is(':checked')){
					sortHTML += '<li class="ui-state-default" id="po_' + postList[i]['ID'] + '"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' + postList[i].post_title + '</li>';
					pageCount++;
				}
			}
			
			if(pageCount == 0){
				$('#createStatus').html("Error: you must select at least one page or post to create a PDF.");
				return;
			}
			
			sortHTML += '</ul>';
			$('#sortHolder').html(sortHTML);
			
			$(function() {//set the div as sortable every time we open the dialog (doing this earlier and just calling refresh didn't work)
				$("#sortable").sortable();
				$("#sortable").disableSelection();
			});
		
			$('#sortDialog').dialog('open');
			return false;
		});
	});

	buildFileTable();
	toggleWidgets();
});
	
</script>


<h2>PDF Creation Station</h2>

<h3>by Kalin Ringkvist - kalinbooks.com</h3>

<p>Create custom PDF files for any combination of posts and pages.</p> 

<div class='collapse'><b>Select Pages and Posts</b></div>
<div class="wideHolder">
    <div class='formDiv'>
    <button id="btnSelectAllPages">Select All</button><br/><br/>
    <?php
        $l = count($pageList);
        $previousIndent = '';
        $previousID = 0;
        for($i=0; $i<$l; $i++){//build our list of pages with checkboxes
            $pageID = $pageList[$i]->ID;
            $parent = $pageList[$i]->post_parent;
            
            if($parent == 0){//if this is a top level page, don't indent
                $indent = '';
            }else{
                if($parent == $previousID){//if the parent is the previous page, add another three spaces of indentation (if pages are not returned by wordpress in proper order, indentation will fail)"
                    $indent = $previousIndent .'&nbsp;&nbsp;&nbsp;';
                }
            }
            $previousID = $pageID;
            $previousIndent = $indent;
            echo($indent .'<input type=checkbox id="chk' .$pageID .'" name="chk' .$pageID .'"></ input> ' .$pageList[$i]->post_title .'<br />');//create each checkbox and label
        }
    ?>
    </div>

    <div class="formDiv">
    	<button id="btnSelectAllPosts">Select All</button><br/><br/>	
        <?php
            $l = count($postList);
            for($i=0; $i<$l; $i++){//build our list of posts with checkboxes
                $pageID = $postList[$i]->ID;
                //echo $postList[$i]->post_parent;
                echo('<input type=checkbox id="chk' .$pageID .'" name="chk' .$pageID .'"></ input> ' .$postList[$i]->post_title .'<br />');
            }
        ?>
    </div>
	
</div>
<div class='collapse'><b>Insert HTML before every page or post</b></div>
   <div class="txtfieldHolder">
        <div class="textAreaDiv">
            <b>HTML to insert before every page:</b><br />
            <textarea class="txtArea" name='txtBeforePage' id='txtBeforePage' rows='8'><?php echo $adminOptions["beforePage"]; ?></textarea>
        </div>
        <div class="textAreaDiv">
            <b>HTML to insert before every post:</b><br />
            <textarea class="txtArea" name='txtBeforePost' id='txtBeforePost' rows='8'><?php echo $adminOptions["beforePost"]; ?></textarea>
        </div>
    </div>
    <div class='collapse'><b>Insert HTML after every page or post</b></div>
    <div class="txtfieldHolder">
        <div class="textAreaDiv">
            <b>HTML to insert after every page:</b><br />
            <textarea class="txtArea" name='txtAfterPage' id='txtAfterPage' rows='8'><?php echo $adminOptions["afterPage"]; ?></textarea>
        </div>
        <div class="textAreaDiv">
            <b>HTML to insert after every post:</b><br />
            <textarea class="txtArea" name='txtAfterPost' id='txtAfterPost' rows='8'><?php echo $adminOptions["afterPost"]; ?></textarea>
        </div>
    </div>
    <div class='collapse'><b>Insert HTML for title and final pages</b></div>
    <div class="txtfieldHolder">
        <div class="textAreaDiv">
            <b>HTML to insert for title page:</b><br />
            <textarea class="txtArea" name='txtTitlePage' id='txtTitlePage' rows='8'><?php echo $adminOptions["titlePage"]; ?></textarea>
        </div>
        <div class="textAreaDiv">
            <b>HTML to insert for final page:</b><br />
            <textarea class="txtArea" name='txtFinalPage' id='txtFinalPage' rows='8' ><?php echo $adminOptions["finalPage"]; ?></textarea>
        </div>
    </div>
    <div class='collapse'><b>CREATE PDF!</b></div>
    <div class="generalHolder">
        <p>Header title: <input type='text' name='txtHeaderTitle' id='txtHeaderTitle' class='txtHeader' value='<?php echo $adminOptions["headerTitle"]; ?>'></input></p>
        <p>Header sub title: <input type='text' name='txtHeaderSub' id='txtHeaderSub' class='txtHeader' value='<?php echo $adminOptions["headerSub"]; ?>'></input></p><br/>
        
        <p><input type='checkbox' id='chkIncludeImages' name='chkIncludeImages' <?php if($adminOptions["includeImages"] == 'true'){echo "checked='yes' ";} ?>></input> Include Images<!--&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<input type='checkbox' id='chkIncludeTables' name='chkIncludeTables' if($adminOptions["includeTables"] == 'true'){echo "checked='yes' ";} ></input> Include Tables -->&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Content Font Size: <input type="text" id="txtFontSize" size="2" maxlength="3" value='<?php echo $adminOptions["fontSize"]; ?>' />
</p><br/>
        
        File name: <input type="text" name='txtFileName' id='txtFileName' value='<?php echo $adminOptions["filename"]; ?>' ></input>.pdf
        </p>
        <p align="center"><br />
        <button id="btnOpenDialog">Create PDF!</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='btnReset'>Reset Defaults</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a name="createNow" id="createNow" href="javascript:void(0);" title="Use this if the 'Create PDF!' button won't properly show the popup. You won't be able to re-order your pages, but at least you can create a document.">create now!</a></p>
        <p align="center"><span id="createStatus">&nbsp;</span></p>
    </div>
    <p>WARNING: I have discovered a bug where PDF files created on this page are deleted upon plugin upgrade. Please be certain to save your created PDF files to your local machine and re-upload them somewhere outside of the kalins-pdf-creation-station directory before I release a new version of this plugin. I'm currently working on a permanent fix.</p>
    <div class='collapse'><b>Existing PDF Files</b></div>
    <div class="generalHolder" id="pdfListDiv"><p>List of compiled documents goes here</p></div>
    
    <div class='collapse'><b>Shortcodes</b></div>
    <div class="generalHolder">
    	<b>Blog shortcodes:</b> Use these codes anywhere in the above form to insert information about your blog.
    	<p><ul>
        <li><b>[current_time]</b> -  PDF creation date/time</li>
        <li><b>[blog_name]</b> -  the name of the blog</li>
        <li><b>[blog_description]</b> - description of the blog</li>
        <li><b>[blog_url]</b> - blog base url</li>
        </ul>
        </p><br />
        
        <b>Page/post Shortcodes:</b> Use these codes before and after posts and pages<br />
        <p>
        <ul>
        <li><b>[ID]</b> - the ID number of the page/post</li>
        <li><b>[post_author]</b> - author of the page/post</li>
        <li><b>[post_date]</b> - date page/post was created</li>
        <li><b>[post_date_gmt]</b> - date page/post was created in gmt time</li>
        <li><b>[post_title]</b> - page/post title</li>
        <li><b>[post_excerpt]</b> - page/post excerpt</li>
        <li><b>[post_name]</b> - page/post slug name</li>
        <li><b>[post_modified]</b> - date page/post was last modified</li>
        <li><b>[post_modified_gmt]</b> - date page/post was last modified in gmt time</li>
        <li><b>[guid]</b> - url of the page/post</li>
        <li><b>[comment_count]</b> - number of comments posted for this post/page</li>
        </ul></p>
        <p>Note: these shortcodes only work on this page.</p>
        
        <p><b>The following tags are supported wherever HTML is allowed (according to TCPDF documentation):</b><br /> a, b, blockquote, br, dd, del, div, dl, dt, em, font, h1, h2, h3, h4, h5, h6, hr, i, img, li, ol, p, pre, small, span, strong, sub, sup, table, tcpdf, td, th, thead, tr, tt, u, ul</p>
        <p>Please use double quotes (") in HTML attributes such as font size or href, due to a bug with single quotes.</p>
    
    </div>
    <div class='collapse'><b>About</b></div>
    <div class="generalHolder">
    
    	Thank you for using PDF Creation Station
                
        <?php 
		$versionNum = (int) substr(phpversion(), 0, 1);//check php version and possibly warn user
		if($versionNum < 5){//I have no idea what this thing will do at anything below 5.2.11 :)
			echo "<p>You are running PHP version "  .phpversion() .". This plugin was built with PHP version 5.2.11 and has NOT been tested with older versions. It likely requires at least PHP version 5.0.</p>";
		}
		?>
    	<p>PDF Creation Station was built with WordPress version 3.0. It has NOT been tested on older versions and will most likely fail.</p>
    </div>
    
    <div id="sortDialog" title="Adjust Order and Create"><div id="sortHolder" class="sortHolder"></div><p align="center"><br /><button id="btnCreateCancel">Cancel</button>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<button id="btnCreate">Create PDF!</button></p></div>
</html>