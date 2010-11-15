<p>  
	<b>PDF Creation Station</b> can add links in pages or posts allowing users to download PDF files of those pages. Here you can adjust settings for those links and PDF files.</p>
    <p>If you are looking for the utility to create PDF files from multiple pages and posts, you can find it under the Tools menu to the left.</p>
    
    <ul>
    	<li>
        	<b>Insert HTML:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
            You may customize many aspects of the generated PDF files by inserting text or HTML snippets into the first six text fields on this page. The HTML will be rendered and saved into the PDF file. If you do not know HTML, you can simply leave the default HTML as is, which will give you a standard title header with the post title, author, date and link.
            <br/>&nbsp;&nbsp;&nbsp;&nbsp;
            A list of allowable HTML tags can be found in the Shortcodes section below.
            <br/>&nbsp;&nbsp;&nbsp;&nbsp;
            If you do not want a Title or Final page, you may leave those fields blank and the page will not be created. If you would like a blank Title or Final page, enter a spacebar character.
            <br/>&nbsp;&nbsp;&nbsp;&nbsp;<b>Note:</b> Please use double quotes (") in HTML attributes such as font size or href, due to a bug with single quotes.
        </li>
        <br/>
        <li>
        	<b>Options:</b>
             <ol>
            	<li><b>Header Title and Header Sub Title:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                This is the text that will display as the header at the top of every page within your generated PDF files. Shortcodes (explained below) are allowed here, but HTML is not.
                
                </li>
                <li><b>Link Text:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                This is the text that will be used to link to the PDF document. HTML may be used here, for example, if you wish to link using an image. For more customization there are fields allowing you to insert HTML before and after the link.
                </li>
                <li><b>Include Images:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                You can choose whether or not to include images in the PDF. This is turned off by default because there are bugs with displaying images. If you turn this on, be sure to check that your images are displayed properly.
                </li>
                <li><b>Content Font Size:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Set the font size for the main content of the PDF files. You MUST enter a number into this field. You may use font tags to override this in inserted HTML.
                </li>
                <li><b>Default Link Placement:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Choose where on the blog page you would like the PDF link displayed: top, bottom, or not at all. This setting can be overridden on the individual page/post edit pages, allowing you to create PDF files for some pages and not for others. For pages/posts saved before the installation of PDF Creation Station, the links will be displayed according to the default you set here.
                </li>
                <li><b>Minimum Post Character Count</b><br />&nbsp;&nbsp;&nbsp;&nbsp;
                Set the minimum number of characters a page/post can have before it receives a PDF link. Note: This feature is not perfect. For the sake of efficiency, the script does not discriminate between HTML and actual text, so a YouTube video, for example, may count as several hundred characters. Settings on the individual page/post edit screen will override this value if it becomes an issue on certain pages.
                </li>
                
                
                <li><b>Use post slug for PDF filename</b><br />&nbsp;&nbsp;&nbsp;&nbsp;
                Check this to use post slugs for filenames instead of ID numbers. I was originally worried this would create conflicts, but it appears as though WordPress forces all slugs to be unique.
                </li>
                
                <li><b>Show on home, category and tag pages</b><br />&nbsp;&nbsp;&nbsp;&nbsp;
                Check this only if you are using the entire post content on all these pages. If you use excerpts, the link will still be applied but will be stripped of it's HTML, making the excerpt rather ugly. Your theme normally handles the loop on these pages so I can't figure out a way to allow a plugin to have reliable control over the display on these pages. However, if you know a little about themes and PHP, it should be relatively easy to hack your theme to show the link on these pages. I wrote a blog post explaining how to do this.
                </li>
                
                
                <li><b>Save Settings:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Remember to click this button to save all your settings.
                </li>
                <li><b>Reset Defaults:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                Reset all your form values back to the originals that shipped with the plugin.
                </li>
            </ol>
            
      </li>
        <br/>
        <li>
        	<b>Shortcodes:</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
           Shortcodes are snippets of text starting with "[" and ending with "]" that will be automatically replaced with the appropriate information. You may insert these codes into any fields on this page to be replaced with information like blog name, post title, author, date, etc.<br/>&nbsp;&nbsp;&nbsp;&nbsp;
           <b>Note:</b> these shortcodes are not entered into the main WordPress shortcode system, so they will only work within the PDF Creation Station plugin.
        </li>
        
        <br/>
        <li>
        <b>Clean up Database entries</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
        At the bottom of this page is a little checkbox labeled "Upon plugin deactivation clean up all database entries." This applies to this page, the tool page and the page/post meta. Un-check this only if you plan on deactivating this plugin and want to be able to turn it back on later without all the settings reverting to their defaults.
        </li>
        
        <br/>
        <li>
        <b>Hard-code link into theme</b><br/>&nbsp;&nbsp;&nbsp;&nbsp;
        If you would like to add the PDF link directly into your theme, follow the instructions in <a href="http://kalinbooks.com/pdf-creation-station-hard-code-link/">this blog post.</a> This will give you more flexibility on where in the page to display the link and will also use fewer server resources. Requires some HTML knowledge (naturally you need to be able to understand your theme) and requires one very minor change to a plugin file.
        </li>
        
        
        <br/>
        <li>
        	<b>Notes about Caching:</b><br/>
        	&nbsp;&nbsp;&nbsp;&nbsp;
        	To minimize server CPU usage, PDF files are cached within uploads/kalins-pdf/singles/. When a user clicks on a link to a PDF file, the script will first check if the file exists, and if so, it will redirect the user. If there is no file, a brand new PDF will be generated. This means the first person to click the link will experience a slightly longer delay. Individual cached PDF files will be automatically deleted whenever a page or post is updated, ensuring that the files are always up-to-date. All the cached files will be deleted whenever you update or reset the settings on this page.
        </li>
    </ul>
</p>