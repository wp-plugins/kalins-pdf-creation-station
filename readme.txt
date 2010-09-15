=== Kalin's PDF Creation Station ===
Contributors: kalinbooks
Donate link: http://kalinbooks.com/pdf-creation-station/
Tags: PDF, document, export, print, pdf, creation
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 0.7

Build highly customizable PDF documents from any combination of pages and posts, or add a link to any page to download a PDF of that post. Beta release. Please let me know if you find bugs.

== Description ==
<p>
Build highly customizable PDF documents from any combination of pages and posts, or add a link to any page to download a PDF of that post. Beta release. Please let me know if you find bugs.                  
</p>
<p>
Kalin's PDF Creation Station will add two menus to your WordPress admin. One under tools and one under settings. 
</p>
<p>In the tools menu you will be able to build PDF documents from any combination of pages and posts. Select any or all pages and posts from your site, then add a custom title page, end page and custom headers. Adjust font sizes, file names, or insert information such as timestamps, excerpts and urls through the use of shortcodes. Finally, adjust page order through a simple drag and drop interface. All created PDF files will display in a convenient list for you to delete, download or link to.
</p>
<p>
In the settings menu you will be able to setup options for a link that can be automatically added to some or all pages and posts. This link will point to an automatically generated PDF version of that page. Most of the same customization options are available here that are available in the creation tool, like title page and font size, as well as the option to fully customize the link itself. On individual page/post edit pages you will be able to override the default link placement so you can show links on some pages and not on others. PDF files are saved to your server so they only need to be created once, reducing server load compared to other PDF generation plugins that create a new PDF every time the link is clicked. The PDF file is automatically deleted when a page or post is edited, so the PDF always matches the page.
</p>
<p>
Plugin by Kalin Ringkvist at http://kalinbooks.com/
</p>

== Installation ==

1. Unzip `kalins-pdf-creation-station.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
1. Find the PDF Creation Station menu under 'tools' and begin creating custom PDF documents of your website. Or go into the PDF Creaction Station menu under 'settings' and begin setting up the options for automatic individual page generation.

== Frequently Asked Questions ==

= Where do I find instructions and help? =

In both the settings and tool pages you can find help in the built-in wordpress help dropdown menu in the upper right side of the screen. If you continue to have problems, feel free to make a comment at http://kalinbooks.com/pdf-creation-station/. Try to include as much specific information as you can, especially if you think you've found a bug.

= Font, href or align tags don't work in inserted HTML. =

Make sure to use double quotes instead of single quotes when inserting arbitrary HTML attributes because of a bug with the core PDF creation engine (TCPDF).

== Screenshots ==

1. A portion of the creator tool that creates custom PDF documents for large portions of your website.
2. A different shot of the creator tool with a couple sub-menus expanded.
3. Settings for PDF creation for individual posts. Note the link customization options.
4. A shot of the box that is added to the page/post edit page to control link placement.

== Changelog ==

= 0.7 =
* First version. Beta. Includes basic functionality for tool menu and settings menu including page order, title page, include images, font size, ajaxified interface, shortcodes, etc.

== Upgrade Notice ==

= 0.7 =
First version. Beta. Use with Caution.

== About ==

If you find this plugin useful please pay it forward to the community, or visit http://kalinbooks.com/ and check out some of my science fiction or political writings.
Thanks to Marcos Rezende's Blog as PDF and Aleksander Stacherski's AS-PDF plugins which provided a good starting point.

