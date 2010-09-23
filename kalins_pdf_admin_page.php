<?php

	if ( !function_exists( 'add_action' ) ) {
		echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
		exit;
	}
	
	$save_nonce = wp_create_nonce( 'kalins_pdf_admin_save' );
	$reset_nonce = wp_create_nonce( 'kalins_pdf_admin_reset' );
	
	$adminOptions = kalins_pdf_get_admin_options();
	
?>


<script type='text/javascript'>

jQuery(document).ready(function($){
	
	var saveNonce = '<?php echo $save_nonce; //pass a different nonce security string for each possible ajax action?>'
	var resetNonce = '<?php echo $reset_nonce; ?>'

	
	$('#btnReset').click(function(){
		if(confirm("Are you sure you want to reset all of your field values? You will lose all the information you have entered into the form.")){
			var data = { action: 'kalins_pdf_reset_admin_defaults', _ajax_nonce : resetNonce};
			
			jQuery.post(ajaxurl, data, function(response) {
				
				var newValues = JSON.parse(response.substr(0, response.lastIndexOf("}") + 1));
				
				$('#txtBeforePage').val(newValues["beforePage"]);
				$('#txtBeforePost').val(newValues["beforePost"]);
				$('#txtAfterPage').val(newValues["afterPage"]);
				$('#txtAfterPost').val(newValues["afterPost"]);
				$('#txtTitlePage').val(newValues["titlePage"]);
				$('#txtFinalPage').val(newValues["finalPage"]);
				
				$('#txtHeaderTitle').val(newValues["headerTitle"]);
				$('#txtHeaderSub').val(newValues["headerSub"]);
				
				$('#txtLinkText').val(newValues["linkText"]);
				$('#txtBeforeLink').val(newValues["beforeLink"]);
				$('#txtAfterLink').val(newValues["afterLink"]);
				
				$('#txtFontSize').val(newValues["fontSize"]);
				$('#txtFilename').val(newValues["filename"]);
				$('#txtCharCount').val(newValues["charCount"]);
				
				if(newValues["includeImages"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
					$('#chkIncludeImages').attr('checked', true);
				}else{
					$('#chkIncludeImages').attr('checked', false);
				}
				
				$("input[name='kalinsPDFLink']").val(newValues["showLink"]);//set link radio button option
				$("#opt_" + newValues["showLink"]).attr("checked", "checked"); 
				
				if(newValues["doCleanup"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
					$('#chkDoCleanup').attr('checked', true);
				}else{
					$('#chkDoCleanup').attr('checked', false);
				}
			});
		}
	});
	
	$('#btnSave').click(function(){
		
		var data = { action: 'kalins_pdf_admin_save',
			_ajax_nonce : saveNonce
		}

		data.beforePage = $("#txtBeforePage").val();
		data.beforePost = $("#txtBeforePost").val();
		data.afterPage = $("#txtAfterPage").val();
		data.afterPost = $("#txtAfterPost").val();
		data.titlePage = $("#txtTitlePage").val();
		data.finalPage = $("#txtFinalPage").val();
		data.headerTitle = $("#txtHeaderTitle").val();
		data.headerSub = $("#txtHeaderSub").val();
		data.linkText = $("#txtLinkText").val();
		data.beforeLink = $("#txtBeforeLink").val();
		data.afterLink = $("#txtAfterLink").val();
		data.fontSize = $("#txtFontSize").val();
		data.includeImages = $("#chkIncludeImages").is(':checked');
		//data.includeTables = $("#chkIncludeTables").is(':checked');
		data.showLink = $("input[name='kalinsPDFLink']:checked").val();
		data.charCount = $("#txtCharCount").val();
		data.doCleanup =  $("#chkDoCleanup").is(':checked');
		
		$('#createStatus').html("Saving settings...");

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			var startPosition = response.indexOf("{");
			var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
			
			var newFileData = JSON.parse(responseObjString);
			if(newFileData.status == "success"){
				$('#createStatus').html("Settings saved successfully.");
			}else{
				$('#createStatus').html(response);
			}
		});
	});
	
	function toggleWidgets() {//make menus collapsible
		$('.collapse').addClass('plus');

		$('.collapse').click(function() {
			$(this).toggleClass('plus').toggleClass('minus').next().toggle(180);
		});
	}
	
	toggleWidgets();
});
	
</script>


<h2>PDF Creation Station</h2>

<h3>by Kalin Ringkvist - kalinbooks.com</h3>

<p>Settings for creating PDF files on individual pages and posts. For more information, click the help button to the right.</p>


<div class='collapse'><b>Insert HTML before page or post</b></div>
   <div class="txtfieldHolder">
        <div class="textAreaDiv">
            <b>HTML to insert before page:</b><br />
            <textarea class="txtArea" name='txtBeforePage' id='txtBeforePage' rows='8'><?php echo $adminOptions["beforePage"]; ?></textarea>
        </div>
        <div class="textAreaDiv">
            <b>HTML to insert before post:</b><br />
            <textarea class="txtArea" name='txtBeforePost' id='txtBeforePost' rows='8'><?php echo $adminOptions["beforePost"]; ?></textarea>
        </div>
    </div>
    <div class='collapse'><b>Insert HTML after page or post</b></div>
    <div class="txtfieldHolder">
        <div class="textAreaDiv">
            <b>HTML to insert after page:</b><br />
            <textarea class="txtArea" name='txtAfterPage' id='txtAfterPage' rows='8'><?php echo $adminOptions["afterPage"]; ?></textarea>
        </div>
        <div class="textAreaDiv">
            <b>HTML to insert after post:</b><br />
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
    <div class='collapse'><b>Options</b></div>
    <div class="generalHolder">
        <p>Header title: <input type='text' name='txtHeaderTitle' id='txtHeaderTitle' class='txtHeader' value='<?php echo $adminOptions["headerTitle"]; ?>'></input></p>
        <p>Header sub title: <input type='text' name='txtHeaderSub' id='txtHeaderSub' class='txtHeader' value='<?php echo $adminOptions["headerSub"]; ?>'></input></p>
        <br/>
        <p>Link text: <input type="text" id='txtLinkText' class='txtHeader' value='<?php echo $adminOptions["linkText"]; ?>' /></p>
        <p>Before Link: <input type="text" id='txtBeforeLink' class='txtHeader' value='<?php echo $adminOptions["beforeLink"]; ?>' /></p>
        <p>After Link: <input type="text" id='txtAfterLink' class='txtHeader' value='<?php echo $adminOptions["afterLink"]; ?>' /></p>
        <br/>
        <p><input type='checkbox' id='chkIncludeImages' name='chkIncludeImages' <?php if($adminOptions["includeImages"] == "true"){echo "checked='yes' ";} ?>></input> Include Images &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; Content Font Size: <input type="text" id="txtFontSize" size="2" maxlength="3" value='<?php echo $adminOptions["fontSize"]; ?>' /></p>
        <br/>
        
        <p>Default Link Placement (can be overwritten in page/post edit page):</p>
        
        <?php
		//KLUDGE I should probably replace this with some jquery that runs on page load to set the proper value of the option button rather than running through this switch statement just to check an option button
		switch($adminOptions["showLink"]){
			case "top":
				echo '<p><input type="radio" name="kalinsPDFLink" value="top" id="opt_top" checked /> Link at top of page<br /><input type="radio" name="kalinsPDFLink" value="bottom" id="opt_bottom" /> Link at bottom of page<br /><input type="radio" name="kalinsPDFLink" value="none" id="opt_none" /> Do not generate PDF</p>';
				break;
			case "bottom":
				echo '<p><input type="radio" name="kalinsPDFLink" value="top" id="opt_top" /> Link at top of page<br /><input type="radio" name="kalinsPDFLink" value="bottom" id="opt_bottom" checked /> Link at bottom of page<br /><input type="radio" name="kalinsPDFLink" value="none" id="opt_none" /> Do not generate PDF</p>';
				break;
        	case "none":
				echo '<p><input type="radio" name="kalinsPDFLink" value="top" id="opt_top" /> Link at top of page<br /><input type="radio" name="kalinsPDFLink" value="bottom" id="opt_bottom" /> Link at bottom of page<br /><input type="radio" name="kalinsPDFLink" value="none" id="opt_none" checked /> Do not generate PDF</p>';
				break;
		}
		?>
        <p>
        <input type="text" id="txtCharCount" size="3" maxlength="5" value='<?php echo $adminOptions["charCount"]; ?>' /> Minimum post character count
        </p>
        <p><!--&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<input type='checkbox' id='chkIncludeTables' name='chkIncludeTables' if($adminOptions["includeTables"] == 'true'){echo "checked='yes' ";} ></input> Include Tables --></p>
        
</p>
<p align="center"><br />
        <button id="btnSave">Save Settings</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='btnReset'>Reset Defaults</button></p>
        <p align="center"><span id="createStatus">&nbsp;</span></p>
    </div>
    
    <div class='collapse'><b>Shortcodes</b></div>
    <div class="generalHolder">
    	<b>Blog shortcodes:</b> Use these codes anywhere in the above form to insert information about your blog.
    	<p><ul>
        <li><b>[current_time]</b> -  PDF creation date/time</li>
        <li><b>[blog_name]</b> -  the name of the blog</li>
        <li><b>[blog_description]</b> - description of the blog</li>
        <li><b>[blog_url]</b> - blog base url</li>
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
        <p><input type='checkbox' id='chkDoCleanup' name='chkDoCleanup' <?php if($adminOptions["doCleanup"] == "true"){echo "checked='yes' ";} ?>></input> Upon plugin deactivation clean up all database entries</p>
    </div>
</html>