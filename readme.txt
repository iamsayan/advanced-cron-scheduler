=== WP Auto Republish ===
Contributors: Infosatech
Tags: republish, republishing, old posts, old post, repost, old post promoter, post promoter, promotion, SEO, rss, plugin, posts
Requires at least: 4.7
Tested up to: 5.7
Stable tag: 1.2.3
Requires PHP: 5.6
Donate link: https://www.paypal.me/iamsayan/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Republish your old posts automatically by resetting the date to the current date. Revive old posts to users that haven't seen them.

== Description ==

The WP Auto Republish plugin helps revive old posts by resetting the published date to the current date. This will push old posts to your front page, the top of archive pages, and back into RSS feeds. Ideal for sites with a large repository of evergreen content.

Like WP Auto Republish plugin? Consider leaving a [5 star review](https://wordpress.org/support/plugin/wp-auto-republish/reviews/#new-post).

== Why would you want to do this? Here are a few reasons: ==

1. New visitors to your site haven't seen your old content. <strong>This will help them discover it.</strong>
2. Old content won't show up in date-based searches on search engines, but resetting the date can make them <strong>look fresh again</strong>.
3. People <strong>like to share and link to new content, </strong>and they determine that by looking at the publication date.
4. It will highlight older posts by moving them back to <strong>front page and in the RSS feed</strong>.
5. WP Auto Republish will improve your <strong>blog visibility, traffic and SEO</strong>!
6. And also <strong>Google likes updated content</strong> if it’s done right.

== What does this plugin do? ==

This plugin helps revive old posts by resetting the published date to the current date and push old posts to your front page, the top of archive pages, and back into RSS feeds.

> <strong>Note</strong>: All basic functionality is FREE. Features such as single post republishing, auto social share, repeated republishing & triggering publish events are available in the <Strong>[Premium Edition](https://wpautorepublish.com)</strong>.

### Key Features

* Automatically republish your all posts.
* Set minimum republish interval and randomness interval.
* Display original publication date Before/After post.
* Exclude or include posts by category or tags.
* Force exclude/include posts by their ID.
* Can select post in ASC / DESC order.
* Compatible with any timezone.
* Automatically purge site cache (limited) after republishing.

### Premium Features

* Supports all free version features.
* **Automatic Social Media Share**.
* Custom post types support.
* Custom taxonomies support.
* **Individual post republishing (also supports repeated)**.
* Scheduled post republishing.
* Date & time based republishing.
* Custom post republish interval.
* Custom title for each republish event.
* Trigger publish event at the time of republish.
* Automatic Site or Single Post Cache Purge Support (supports most of the cache plugins and hosting platforms)
* Custom date range for republishing.
* Can use dates in post permalinks.
* Change Post Status after Last Republish.
* One click instant republish.
* Show all republished history in logs.
* Can use dates in post permalinks.
* Can change the post name on every republish.
* Shows all single upcoming republication in a dashboard widget.
* Shows single republication info in a admin column.
* Can hide last original published info from frontend.

<strong>[Upgrade to WP Auto Republish Premium](https://wpautorepublish.com) now. You can also upgrade to Premium Version directly from your dashboard.</strong>

### Free and Premium Support

Support for the WP Auto Republish plugin on the WordPress forums is free.

Premium world-class support is available via email to all [WP Auto Republish Premium](https://wpautorepublish.com) customers.

> <strong>Note</strong>: Paid customers support is always given priority over free support. Paid customers support is provided via one-to-one email. [Upgrade to Premium](https://wpautorepublish.com) to benefit from priority support.

= Compatibility =

* This plugin is tested with W3 Total Cache, WP Super Cache, WP Rocket, WP Fastest Cache, Cachify, Comet Cache, Zen Cache, LiteSpeed Cache, SG Optimizer, HyperCache, Cache Enabler, Swift Performance Lite, Nginx Cache, Proxy Cache, Nginx Helper Cache, Autoptimize, Breeze (Cloudways), Godaddy Managed WordPress Hosting and WP Engine and fully compatible with WordPress Version 4.7 and beyond and also compatible with any WordPress theme.

= Support =

* Community support via the [support forums](https://wordpress.org/support/plugin/wp-auto-republish) at WordPress.org.

= Contribute =
* Active development of this plugin is handled [on GitHub](https://github.com/iamsayan/wp-auto-republish/).
* Feel free to [fork the project on GitHub](https://github.com/iamsayan/wp-auto-republish/) and submit your contributions via pull request.

= Translations =

* Simplified Chinese (zh_CN) by [Changmeng Hu](https://profiles.wordpress.org/cmhello)

== Installation ==

1. Visit 'Plugins > Add New'
2. Search for 'WP Auto Republish' and install it.
3. Or you can upload the `wp-auto-republish` folder to the `/wp-content/plugins/` directory manually.
4. Activate WP Auto Republish from your Plugins page.
5. After activation go to 'Settings > WP Auto Republish'.
6. Configure settings according to your need and save changes.

6. Configure plugins settings according to your need and save changes.

== Frequently Asked Questions ==

= How to customize original post publication date format on frontend? =

To customize original post publication date, you need to add this following snippet to the end of your active theme's functions.php file:

`function wpar_override_time_format() {
    return 'F jS, Y \a\t h:i a';
}
add_filter( 'wpar/published_date_format', 'wpar_override_time_format' );`

= Will it work with my theme? =

Yes, our plugins work independently of themes you are using. As long as your website is running on WordPress, it will work.

= Are posts duplicated? =

By default, no. But you can configure it from plugin settings. The date on posts is updated to the current date making a post appear new. URLs don't change and comments continue to display with the post.

= Doesn't changing the timestamp affect permalinks that include dates?  =

Yes, permalinks with dates would be affected only in free version. This plugin shouldn't be used if your permalinks include dates since those dates will change when a post is republished. But in Premium version it is possible to use dates in permalinks.

== Screenshots ==

1. WP Auto Republish - General Tab.
2. WP Auto Republish - Post Options Tab.

== Changelog ==

If you like WP Auto Republish, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/wp-auto-republish/reviews/#new-post). It helps to keep development and support going strong. Thank you!

= 1.2.3 =
Release Date: 28th February, 2021

* Fixed: Database Table not found error.

= 1.2.2 =
Release Date: 23rd February, 2021

= Premium Version =
* Added: Social Media Share Feature is added.
* Added: Compatibility with Jetpack auto social share.
* Fixed: PHP error in Dashboard Widget if this plugin is installed on Wordpress version < 5.3.0.
* Fixed: Wrong date showing if Weekly republish is set on post edit screen.
* Tweak: Added some CSS imporvements.
* Tweak: Moved Single Republish Settings to a new seperate tab.
* Other Free version & Misc improvements and fixes.

= Free Version =
* Added: Action Schedular to Handle Post republishing more efficiently.
* Added: A Health Check mechanism to properly handle the missed schedule issue.
* Added: A Documentation and Faqs link as Plugin Tab.
* Properly handled the plugin uninstall actions to reduce the server load.
* Remove filter for 5 star rating from review link.
* Other improvements and fixes.
* Tested with WordPress v5.7.
* Freemius SDK update.

= 1.2.1 =
Release Date: 11th Novemober, 2020

* Fixed: Mising variable bug and date calculation.
* Freemius SDK update.

= 1.2.0 / 1.2.0.1 =
Release Date: 9th October, 2020

= Premium Version =
* Added: Option to set custom republish interval.
* Added: Option to disable republish randomness interval.
* Added: Option to remove post age and to set custom post age.
* Fixed: Orderby query is not working if Random Selection was selected.
* Added: Ability to last hours to get most recent published posts.
* Fixed: Taxonomies were showing wrong in plugin dropdown settings.
* Added: Option to enable/disable post row action republish links.
* Added: Scheduled republishing feature. Please check tahp tab for more details.
* Added: Advanced republish log feature to track every event more efficiently.
* Added: An option to view pending republish posts from All Posts page.
* Fixed: Admin column was not showing proper republish infomation.
* Fixed: High memory usage and freeze issue if republish start date is very far than current date.
* Tweak: Single republishing with 1 to 4 minutes interval is removed by default. But it can be enabled by filter.
* Added: Option to clear post metas for debug purposes and to copy/paste plugin settings.
* Preparing for upcoming Features to handle Post Republish more efficiently. And Social Share is coming soon in the Premium Version :)

= Free Version =
* Added: A guide to help users to configure the plugin easily.
* Tweak: It is now designed in a way to easily work with any server enviroment.
* Optimized codes to handle republish process more efficiently to avoid event missing issue.
* Remove filter for 5 star rating from review link.
* Other improvements and fixes.

= Other Versions =

* View the <a href="https://plugins.svn.wordpress.org/wp-auto-republish/trunk/changelog.txt" target="_blank">Changelog</a> file.

== Upgrade Notice ==

= 1.2.0 =
In this release, we make some major changes. Please re-configure your plugin settings, if it stops working.