<p>  
	<b>PDF Creation Station</b> can create PDF files from any combination of posts and pages from this page.</p>
    <p>for more information, visit <a href="http://kalinbooks.com/pdf-creation-station">KalinBooks.com/pdf-creation-station</a></p>
    <p>If you are looking for the settings page to set up PDF creation for individual pages and posts, you can find it under the settings menu to the left.</p>
    
    
    <ul>
    	<li>
        	<b>Select Pages and Posts:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
        	Use the checkboxes and/or the Select All buttons to choose any combination of posts and pages. If you have custom post types, they will appear at the bottom of the posts list. You will have the opportunity to change the page order after clicking the Create PDF! button below.
        </li>
        <br/>
    	<li>
        	<b>Insert HTML:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
            You may customize many aspects of the generated PDF files by inserting text or HTML snippets into the first six text fields on this page. The HTML will be rendered and saved into the PDF file. If you do not know HTML, you can simply leave the default HTML as is, which will give you a standard title header with the post title, author, date and link as well as basic Title and Final pages.
            <br/>&nbsp;&nbsp;&nbsp;&nbsp;
            A list of allowable HTML tags can be found in the Shortcodes section below.
            <br/>&nbsp;&nbsp;&nbsp;&nbsp;
            If you do not want a Title or Final page, you may leave those fields blank and the page will not be created. If you would like a blank Title or Final page, enter a spacebar character.
            <br/>&nbsp;&nbsp;&nbsp;&nbsp;<b>Note:</b> Please use double quotes (") in HTML attributes such as font size or href, due to a bug with single quotes.
        </li>
        <br/>
        <li>
        	<b>CREATE PDF!:</b>
             <ol>
            	<li><b>Header Title and Header Sub Title:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                This is the text that will display as the header at the top of every page within your generated PDF files. Blog shortcodes (explained below) are allowed here, but due to PDF generation limitations, page/post shortcodes as well as HTML markup are not allowed.
                </li>
                <li><b>Include Images:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                You can choose whether or not to include images in the PDF. This is turned off by default because there are bugs with displaying images. If you turn this on, be sure to check that your images are displayed properly.
                </li>
                <li><b>Content Font Size:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Set the font size for the main content of the PDF files. You MUST enter a number into this field. You may use font tags to override this in inserted HTML.
                </li>
                <li><b>Run other plugin shortcodes:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Select whether or not you want other plugins to run their shortcode functions on the content before PDF generation. If not selected, shortcodes will be removed from the content. Not guaranteed to look pretty for all plugins.
                </li>
                 <li><b>Convert YouTube videos:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Select this to convert any YouTube videos in your pages/posts into a link to the YouTube page, since we can't embed a video in the PDF. If not selected, videos will be removed.
                </li>
                <li><b>File Name:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Enter the file name for the PDF you are about to create. No HTML. Blog shortcodes will work, but you may experience problems with the [current_time] shortcode. Page/post shortcodes will not work.
                </li>
                <li><b>Create PDF!:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Click this button when you are ready to create your PDF file. A pop-up window will allow you to drag and drop pages and posts to change their order before finally creating the file.
                </li>
                <li><b>Reset Defaults:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Reset all your form values back to the originals that shipped with the plugin. This will not change or delete any existing PDF files.
                </li>
                <li><b>create now!:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                This link is a hacky workaround for a bug that someone reported where the normal Create PDF! button was not properly showing the popup menu. Don't use this unless the Create PDF! button fails. This link will skip the popup and page-ordering and will simply create the PDF immediately. If you need this workaround, please help me out by posting your experience at <a href="http://kalinbooks.com/pdf-creation-station/known-bugs/">kalinbooks.com/pdf-creation-station/known-bugs/</a> because I'd like to find a way to reproduce this bug.
                </li>
            </ol>
            
        </li>
        <br/>
        <li>
        	<b>Shortcodes:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
           Shortcodes are snippets of text starting with "[" and ending with "]" that will be automatically replaced with the appropriate information. You may insert these codes into any fields on this page to be replaced with information like blog name, post title, author, date, etc.<br/>&nbsp;&nbsp;&nbsp;&nbsp;
           The date/time shortcodes all have an optional format parameter allowing you to format the date/time very specifically using tokens such as: m=month, M=text month, F=full text month, d=day, D=short text Day Y=4 digit year, y=2 digit year, H=hour, i=minute, s=seconds. For a full list of options look at the format parameters section of this page: <a href="http://php.net/manual/en/function.date.php" target="_blank">http://php.net/manual/en/function.date.php.</a><br/>&nbsp;&nbsp;&nbsp;&nbsp;
           <b>Note:</b> these shortcodes are not entered into the main WordPress shortcode system, so they will only work within the PDF Creation Station plugin.
        </li>
        <br/>
        
        <li>
        <b>Plugin efficiency and clean up procedure</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
        If you are not using the settings page that applies PDF links on individual pages and posts, you may want to deactivate this plugin after you have built all your PDF files. Doing this will give you a slight overall blog performance increase (due to WordPress' architecture, all plugins slow your blog down at least a little even if they aren't doing anything). However, if you want to retain your settings for next time you reactivate, go into the settings page (in the settings menu to the left) and scroll to the bottom to find the checkbox labeled "Upon plugin deactivation clean up all database entries". Un-check it, scroll back up and hit save.
        </li>
        <br/>
        <li>
        <b>File Locations</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
        PDF files are saved into the uploads directory in the kalins-pdf folder. You will need to delete this directory manually if you wish to remove it. To change this directory, refer to <a href="http://kalinbooks.com/2011/change-default-pdf-directory">this blog post.</a> (Requires a little PHP hacking.)
        </li>
        <br/>
        <li>
        	<b>wp-config in non-standard location?</b><br/>
        	&nbsp;&nbsp;&nbsp;&nbsp;
        	Refer to this <a href="http://kalinbooks.com/2010/creation-station-non-standard-location/">blog post</a> if your wp-config.php is in a non-standard location.
        </li>
        <br/>
        <li>
        	<b>Foreign Characters? Want to change a font?</b><br/>
        	&nbsp;&nbsp;&nbsp;&nbsp;
        	Refer to this <a href="http://kalinbooks.com/2010/foreign-characters-pdf-creation-station/">blog post</a> if you are having problems with foreign characters or otherwise wish to change the default font of the generated PDF files.
        </li>
        <br/>
        <li>
        	<b>BlockQuote customization</b><br/>
        	&nbsp;&nbsp;&nbsp;&nbsp;
        	Refer to this <a href="http://kalinbooks.com/2010/fix-blockquotes-in-tcpdf-and-adjust-blockquotes-in-pdf-creation-station/">blog post</a> if you would like to adjust the appearance of blockquotes within the generated PDF files.
        </li>
        
    </ul>
</p>