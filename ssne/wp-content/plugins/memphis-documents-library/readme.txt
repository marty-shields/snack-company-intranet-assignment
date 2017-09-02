=== Memphis Documents Library ===
Contributors: bhaldie
Donate link: http://www.kingofnothing.net/
Tags: plugin,documents,memphis,bhaldie,WordPress,library,repository,files,versions, import, export
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.5
Tested up to: 4.4
Stable tag: 3.1.1

A documents library for WordPress.

== Description ==

Memphis Documents Library (mDocs) is a  documents library for WordPress with a robust feature set.  It is a great tool for the organization and distribution of files.

= What's New With Version 3.1.1 =

* *New* - Now you have the choice to use Google Document Preview or Box View.
* *Update* - Removed a redundant css file.
* *Bug* - Fixed Page Builder bug.

= Memphis Documents Library Features =

* Document preview and thumbnails available for most file types.
* Batch Upload of files into the system
* Upload media files that match WordPress's white-list. This white-list is configurable from the WordPress menus.
* Download tracking of media
* Posts created for each new media upload, showing information of the specific file.
* Version control, allows you to view and revise to older version of your file.
* The ability to share you files on other websites.
* Social media buttons for each document.
* Referential file methodology. This allows for the updating of a file while the link remains the same.
* Importing of document libraries into your current library, or just migrating to another website.
* Exporting you documents libraries for safe backup and store, migration to another website or sharing with someone else.
* The ability to create, edit and delete categories and subcategories.
* Search for files using the WordPress search.
* Customization of download button

== Frequently Asked Questions ==

= Preview not Working =

First thing to try is go to Setting > Disable Third Party Includes: > Bootstrap and check the checkbox.  Try the preview again.  If that doesn't work the second most like issue is your themes css if conflicting with mDocs functionality.  The second problem is hard to fix it requires you to debug you site and check and change values in order to get the preview to show.  If you need assistance on the css to edit please feel free to contact me.

= 404 Error when trying to access document page =

If you get a 404 error when trying to access your Memphis documents pages try going to Setting>Permalinks and pressing Save.  This may solve the issue, if it doesn't please contact me for more support.

= Memphis Documents Library look wrong in IE =

Add the following code to your theme right under the `<head>` tag this will turn off compatibility mode for IE.
`<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7; IE=EDGE" />`

= Importing Into Memphis Documents Library =

There are two type of imports you can choose from.

**Keep Existing Saved Variables**

* Is the safest way to import.  This option keeps all your current files and only imports new ones. 
If a file that is being imported matches one on the current system, the one on the current system will be left untouched,
and you will have to manually import these files.

**Overwrite Saved Variables**

* Is a good when you have a empty documents library or you at looking to refresh your current library.  
This method deletes all files, posted and version on the current system. After the method has completed you will
get a list of all the conflicts that have occurred make note of them.
Please take great care in using this method as there is little to no return.

= Exporting Out of Memphis Documents Library =

When you click the export button the document library will create a ZIP files for you to save to your computer.
This compressed data, will contain your documents, saved variables, media and posts tied to each document.
Once you've saved the download file, you can use the Import function in another WordPress installation to import the content from this site.

= Uninstalling Memphis Documents Library =

When you uninstall the documents library make sure you export all your important files. **All data will be removed on completion of uninstall, this includes files, directories, posts, and media.**

== Installation ==

From the WordPress plugin menu click on Add New and search for Memphis Documents Library

Instead of searching for a plugin you can directly upload the plugin zip file by clicking on Upload:

Use the browse button to select the plugin zip file that was downloaded, then click on Install Now. The plugin will be uploaded to your blog and installed. It can then be activated.

Once uploaded the configuration menu is located in either the "Memphis" menu with the heading of "Documents" in the Dashboard or in the "Memphis Docs" menu. 

== Screenshots ==

1. screenshot-1.png 
2. screenshot-2.png
3. screenshot-3.png
4. screenshot-4.png
5. screenshot-5.png
6. screenshot-6.png
7. screenshot-7.png

== Changelog ==

= 3.1.1 =

* *New* - Now you have the choice to use Google Document Preview or Box View.
* *Update* - Removed a redundant css file.
* *Bug* - Fixed Page Builder bug.

= 3.1 =

* *New* - Added thumbnail image for PDFs using Imagick.
* *Update* - Removed Box view and reverted back to Google doc view.
* *Update* - Added another server compatibility check, to see if WordPress upload directory is accessible.
* *Update* - Added another server compatibility check, to test if ZipArchive is installed.
* *Update* - Added another server compatibility check, to see if Imagick is installed.
* *Update* - Minor changes and updates.
* *Bug* - Fixed localization bug which didn't allow uploading files when using translations.

= 3.0.18 =

* *Update* - Tested up to version 4.4.
* *Bug* - Data check added, to fix minor issue with contributors.
* *Bug* - Fixed capitalization bug when using import from one system to another.

= 3.0.17 =

* *Update* - Changed the color of the 'Add Main Folder' Button.
* *Bug* - More fixes to folder issues
* *Bug* - Minor bug fixes.

= 3.0.16 =

* *New* - Fontawesome can now be turn off as a third party applications.
* *Bug* - More fixes to folder navigation.
* *Bug* - Changes made to file management.

= 3.0.15 =

* *New* - There is a new setting that will allow you to disable Memphis Documents Library's third party applications.
* *New* - Added Cyrillic to Latin file name conversion, this convert Cyrillic to a Latin format.
* *New* - Added a setting to turn off the "No files found in this folder." statement.
* *Bug* - Changes some code around to try and address the folder linking issues.

= 3.0.14 =

* *Update* - Removed some debugging code.

= 3.0.13 =

* *Update* - Change the way modals open, now using pure java script.
* *Bug* - Fixed a bug when adding and removing folders.
* *Bug* - Small fix to the batch uploader.
* *Bug* - Fixed issue with contributors not being adding when uploading a new document.
* *Bug* - Fixed some permission issues.

= 3.0.12 =

* *New* - Added an new test to server compatibility
* *Bug* - Fixed Fatal error issue dealing with date method.
* *Bug* - Fixed naming issue using batch upload.
* *Bug* - Fixed folder issue when using shortcode and having multiple shortcodes on one page.

= 3.0.11 =

* *New* - Added a server compatibility module, to see if you have all required elements for mDocs to work properly.
* *Bug* - More fixes to import and export.
* *Bug* - Minor fixes to the social media buttons.
* *Bug* - Fixed date issue.

= 3.0.10 =

* *Bug* - File size bug fix.
* *Bug* - Improvements to the import export processes.

= 3.0.9 =

* *New* - Now can disable the ability for users to sort documents
* *New* - A setting is available to have the mDocs Posts visible from the dashboard.
* *Update* - Now can run Preview and Thumbnail updater at any time.
* *Bug* - Fixed Allowed file types bug.
* *Bug* - Fixed the date issue when adding and updating documents.
* *Bug* - Fixed some style issues.
* *Bug* - Fixed some other small bugs.

= 3.0.8 =

* *Bug* - More fixes to the folder editor.

= 3.0.7 =

* *New* - Added a file finder, to help retrieve lost files.
* *New* - Added a tag editor to the uploaded for new/updated documents.
* *Update* - More changes made to the "mdocs-modals" class style.
* *Update* - Language update.
* *Update* - Small changes to the main style sheets.

= 3.0.6 =

* *Update* - Changed some rights to see certain buttons.
* *Update* - Added a mDocs tag to the body for theme style issue fixes.
* *Update* - Added a class to all modals called "mdocs-modals".

= 3.0.5 =

* *Bug* - Fix to specific dropdown menu issue, made by Cameron Barrett
* *Bug* - Fixes to imports and exports.
* *Bug* - Fixes to File System Cleanup
* *Bug* - Fixes to Restore to Defaults

= 3.0.4 =

* *Bug* - More fixes to downloads.
* *Bug* -  fixed settings checkboxes for non member downloads and show social apps.

= 3.0.3 =

* *New* - Change the font size of the documents list.
* *New* - Hide/Show sub folders when using shortcodes.
* *New* - Added to new setting for changing font size and hiding and showing sub folders.
* *Update* - Fixed Bootstrap navbar issue.
* *Update* - Remove some unused Bootstrap functionality.
* *Update* - Added a mDocs class to a Bootstrap dropdown menu
* *Update* - Removed unused Jquery UI java script and css
* *Bug* - Fixed php error on rights page.
* *Bug* - Fixed a null session error when uploading a file.

= 3.0.2 =
* *New* - Added a new button for files to refresh document preview
* *Update* - Added a catch for versions that check if file exists.
* *Update* - Added a catch for file upload errors using php 5.3 and higher.
* *Update* - Fixed Box View preview window.
* *Update* - Many other small updates
* *Bug* - Fixed issue when you delete a file then tried to add another file.
* *Bug* - Fixed many other warning and noticed.

= 3.0.1 =
* *Bug* - Fixed, admin menu issue
* *Bug* - Fixed, null reference to 'mdocs-view-private'

= 3.0 =
* *New* - Now other user types can upload files to mDocs, with the ability to add other contributors to the files they own.
* *New* - Look and feel has been updated.
* *New* - Added more safety check for lost of data.
* *New* - Added Finnish language support, thanks to *sloworks* for their hard work.
* *New* - Interface improvements
* *New* - Added a Dutch Translation thanks to DK for all the hard work.
* *New* - Thumbnails of most documents now on the description page of each file.
* *New* - Google Doc View has been replaced with Box Viewer API, this allows for some extra functionality not available with Google Docs.
* *New* - The ability to change the Last Modified category of a file.
* *New* - Added an option in the Settings to change the date format.
* *New* - The ability to allow/deny user types access to Private Posts. 
* *New* - In the setting menu you can now choose the allowed file types.
* *New* - A restore defaults option has been added this will restore Memphis Documents Library to its factory state, *WARNING all files and post will be deleted*.
* *Update* - Updated Font Awesome to version 4.3.0
* *Update* - Updated localization files.
* *Update* - Uninstall will not remove all saved variables , posts, files, categories, and directories for a single WordPress Site and also WordPress Multisite.
* *Update* - Change the way date modified is handle, was using an array value now using file date modified attribute.
* *Update* - Updated localization files.
* *Update* - Change $autoload functionality from yes to no for mdocs-list and mdocs-cats database entries.
* *Update* - Removed the choice of size of list.  Large size document list caused performance issues and had to bee removed.
* *Update* - Added a slug name to the custom post recreating function.
* *Update* - Added the ability to see document previews when logged in.
* *Update* - Changed the Google docs link to Google drive.
* *Bug* - Fixed the XSS (Cross Site Scripting) issues root cause was using $_REQUEST inside a form.
* *Bug* - Fixed the security vulnerabilities known as LFI/RFI, which stands for Local or Remote File Inclusion.
* *Bug* - Fixed mime type bug, where mime types where not being removed properly.
* *Bug* - Fixed issue with post always showing mDocs at the top of the post.  Now it behaves as expected.
* *Bug* - Fixed bug which didn't allow for viewing sub categories when using mdocs short codes.  This short code currently only works on main categories you can't target a subcategory to display.
* *Bug* - Fixed security issues using $_REQUEST inside a form.
* *Bug* - Fixed security issue Local or Remote File Inclusion.
* *Bug* - Fixed Batch Upload naming issue.
* *Bug* - Added a missing div tag to list.
* *Bug* - Fixed a bug when creating categories a null category would be created that could not be delete.
* *Bug* - Error with java script loading, if using WordPress Multisite network admin.
* *Bug* - Fixed issue where Post Status was not displaying any statuses.
* *Bug* - Batch upload was cutting of file names with dots in them.
* *Bug* - Fixed bug causing new installs to produce errors, these errors would correct themselves but very annoying for users to see.
* *Bug* - Removed extra label tag in sort box which was cause issues in Firefox.
* *Bug* - Fixed, Chrome bug, where file types that are allowed in WordPress are being blocked by Memphis Documents Library.
* *Bug* - Fixed, when there are multiple categories on a page the get request fails to recognize each individual category.
* *Rejected* - Short-code to add a download link to a post or page.
= 2.6.1 =
* *Update* - Change the way date modified is handle, was using an array value now using file date modified attribute.
* *Update* - Updated localization files.
* *Bug* - Fixed Chrome bug, where file types that are allowed in WordPress are being blocked by Memphis Documents Library.
= 2.6 =
* *New* - The ability to allow/deny user types access to Private Posts. 
* *New* - In the setting menu you can now choose the allowed file types.
* *New* - A restore defaults option has been added this will restore Memphis Documents Library to its factory state, *WARNING all files and post will be deleted*.
* *Update* - Updated localization files.
* *Update* - Uninstall will not remove all saved variables , posts, files, categories, and directories for a single WordPress Site and also WordPress Multisite.
* *Bug* - Error with java script loading, if using WordPress Multisite network admin.
* *Bug* - Fixed issue where Post Status was not displaying any statuses.
* *Bug* - Batch upload was cutting of file names with dots in them.
* *Bug* - Fixed bug causing new installs to produce errors, these errors would correct themselves but very annoying for users to see.
* *Bug* - Removed extra label tag in sort box which was cause issues in Firefox.
= 2.5.1.2 =
* *Bug* - Fixed loop bug, when a Memphis Documents post does not have the category mdocs-media.  Now the result will be an output of the short-code only.
* *Bug* - Permalink setting fixed. Sub categories where not working when set to default WordPress permalink setting.
* *Bug* - JavaScript error with FireFox and IE.  A undefined `event.preventDefault();` was causing Add Main Category to no function.  Removing this line fixed the issue.
= 2.5.1.1 =
* *Hot-Fix* - Added the style.css file to the admin page. Now the page will display the correct style.
= 2.5.1 =
* *Fix* - Removed style.php and replaced it with style.css and used the WordPress function `wp_add_inline_style` to handle custom stylesheet changes.
* *Fix* - Disabled the ability to view a private post if the user does not have the capabilities to.
* *Fix* - Updated large list to reflect the addition of sub categories.
* *Fix* - Removed unnecessary padding from the category tabs.
* *Update* - Updated localization.
* *Update* - Removed the sub folder on the right side of the documents list, seems unneeded.
= 2.5 =
* *New* - You have the ability to create sub categories.
* *New* - Three new widgets have been added, you can now display, Most Downloaded, Highest Rated and Recently Updated documents.
* *Fix* -  Issue with file upload on Windows platform, now is resolved.  Batch upload still remains in beta.
* *Fix* - Minor style changes
* *Bug* - small bug fixes and updates.
= 2.4.1 =
* fixed short code, not showing categories.
* special character changes.
* lots of bug fixes
* optimization of code
= 2.4 =
* Removed IE Compatibility mode fix, this was causing too many header errors.  If you want to this functionality add this line to your theme header file, right under the `<head>` tag
 * `<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7; IE=EDGE" />`
* Add the ability to change the colour of the download button.
* Fixed the rss feed bug
* Fixed a look an feel issue with the sort box
* More small fixes and updates
= 2.3.2 =
* possible hot-fix to header issues
* fix of Google docs issues
* privacy and protection updates
* still working on child categories
= 2.3.1 =
* htaccess update
* htaccess file editor in settings menu
* fixed a file not found error
= 2.3 =
* Batch file upload beta
* List of available short-codes
* Document page options added
** Default Content (Preview or Description)
** Show/Hide (Preview and Description)
= 2.2.2 =
* Minor bug fixes
* Small look and feel changes.
* Moved the language files into there own folder
= 2.2.1 =
* Changed the way preview works, added a preview button
* Added default sort options
= 2.2 =
* Added the ability to preview documents instead of a description.
* hot fix on the uninstall issue.  I hope this will solve the problem.
= 2.1.1 =
* fixed some header already sent messages.
= 2.1 =
* added a rating system
* code cleanup
* browser capability fixes
* updated the language file
= 2.0.2 =
* ie compatibility mode fix.
= 2.0.1 =
* Minor html fixes.  Thanks for the reports thibodeaux and ghalusa.
= 2.0 =
* Added a new or updated banner.
* Can now run a filesystem check to clean up and unwanted files or data.
* Can now sort files by any of the categories this sort option is saved for the session of the user.
* Restricted access to the file directory, now only Memphis Documents has access to the files.  Directory link to the files is denied.
* Added a setting menu with the following options
 * Change size of file list on both the site and dashboard
 * Hide our show certain fields of the file.
 * Hide/Show all files from everybody or just non-members
 * Hide/Show all post from everybody or just non-members
 * Hide/Show new and updated banner
 * Determine the length in days to display the new or update banner
* Updated the translation file.
= 1.4 =
* Changed the why sharing works.  Now you share the page that the file is on not the file itself.
* minor bug fixes.
= 1.3.2 =
* fixed permalink bug where the default setting would cause errors when trying to move from one category to another.
= 1.3.1 =
* small bug fixes
= 1.3 =
* Added the ability to disable social apps
* Added the ability to only allow members to download file
* Now have the ability to change the status of a file post
* Have the ability to hide/show your file.
* Changed add update dashboard control panel.
* Update po file.
= 1.2.8 =
*  Fixed broken category issue.
= 1.2.7 =
* fixed download error where the ability to download a file was broken.  This error occurring with the latest WordPress update 3.6.1. The fix was to include a WordPress file ' wp-includes/pluggable.php' that was removed from the WordPress master include list.
= 1.2.6 =
* Stylesheet changes.
=1.2.5 =
* Fixed image links in description.
= 1.2.4 =
* Fixed a compatibility bug with Memphis Custom Login.
* Removed debugging text.
= 1.2.3 =
* Fixed a compatibility bug with Memphis Custom Login.
= 1.2.2 
* Updated robots list.
= 1.2.1 =
* correct path to the mdocs-robots.txt file
= 1.2 =
* Google Plus message updated
* Twitter messages updated.
* Bot list updated
= 1.1 =
* Bots are not counted towards downloads.
* Changed style of dashboard menu.
* Minor bug fixes.
= 1.0.2 =
* Download button fix.
= 1.0.1 =
* Download button fix.
= 1.0 =
* Initial Release of Memphis Documents Library

== Upgrade Notice ==
= 1.0 =
* Initial Release of Memphis Documents Library

== Feature Request ==

* *Feature* - Permissions in folders instead of each document independently.
* *Feature* - Add the ability to add a link back to the mDocs documents list.
* *Feature* - Add the ability to turn off "No files found in this folder" text.
* *Feature* - Editing security based on folders.
* *Feature* - Group file with different types together.  Eg if you have a PDF, e pub and mobi have then update the same document.
* *Feature* - Make all folders older than X years private to our users, but they should remain active for our admins.
* *Feature* - Upload a document, set it as "hidden" and then schedule a future date for it to be become "public".
* *Feature* - Bulk Delete/Hide/Move
* *Feature* - Search shows only files that the specific user role has access too.
* *Feature* - Added more level to categories.
* *Feature* - Add a tag editor to the add/update document page.
* *Feature* - In the Media tab find a way to display the category of the documents created by Memphis Documents.
* *Feature* - Connect to Cloud Services (DropBox, SkyDrive, MediaFire, etc.)
* *Feature* - Add the ability to change the path name and breadcrumb name.