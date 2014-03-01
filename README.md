=== Firefox OS Bookmark ===  
Contributors: Mte90  
Donate link: http://mte90.net  
Tags: mobile, bookmark, firefox os, firefox  
Requires at least: 3.8  
Tested up to: 3.8  
Stable tag: 1.0.0  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

Plugin for create the manifest.webapp file for install your site as an hosted app on Firefox OS/Firefox/Firefox for Android!  
You can add your site in the Firefox Marketplace!

== Features ==

* Basic manifest.webapp (domain.tld/manifest.webapp) file
* Icons support
* MultiSite support (read the section MultiSite)
* Manifest support for multilanguage
* Popup installation (settings)

== Roadmap ==

* New magic unknown features!

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Firefox OS Bookmark'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

==MultiSite==  
To enable in the multisite you need to add this rule in the .htaccess file:
```
RewriteRule manifest.webapp$ wp-content/plugins/firefox-os-bookmark/manifest.php [L]
```

== Changelog ==

= 1.0 =
* First Release
