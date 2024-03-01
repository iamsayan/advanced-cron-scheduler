=== Advanced Cron Scheduler for WordPress ===
Contributors: infosatech
Tags: scheduler, cron, wp cron, debug, cron manager
Requires at least: 5.2
Tested up to: 6.4
Stable tag: 1.1.1
Requires PHP: 5.6
Donate link: https://www.paypal.me/iamsayan/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

The Advanced Cron Scheduler for WordPress plugin helps to easily replace or migrate Native WordPress Cron to the Action Scheduler Library.

== Description ==

The WP-Cron system in WordPress is not a "real" cron system, which means events may not run exactly according to their schedule because the system relies on regular traffic to the website in order to trigger scheduled events.

## Reasons WP-Cron events can miss their schedule

* Low traffic websites may not trigger the event runner often enough
* A fatal error caused by a plugin or theme may break the event runner
* A plugin or theme may intentionally or unintentionally break the event runner
* BasicAuth, a firewall, or other access restrictions may block the event runner
* A problem with your web hosting or web server may break the event runner
* The `DISABLE_WP_CRON` configuration constant is set but no alternative cron runner has been put in place
* Long-running events may temporarily block the event runner
* High traffic websites may suffer from sequential processing issues that block the event runner

The Advanced Cron Scheduler for WordPress plugin does alter the way that WordPress core runs cron events using the Action Scheduler Library.

Action Scheduler is a scalable, traceable job queue for background processing large sets of actions in WordPress. It's specially designed to be distributed in WordPress plugins.

Action Scheduler works by triggering an action hook to run at some time in the future. Each hook can be scheduled with unique data, to allow callbacks to perform operations on that data. The hook can also be scheduled to run on one or more occassions.

Think of it like an extension to `do_action()` which adds the ability to delay and repeat a hook.

## Battle-Tested Background Processing

Every month, Action Scheduler processes millions of payments for [Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/), webhooks for [WooCommerce](https://wordpress.org/plugins/woocommerce/), as well as emails and other events for a range of other plugins.

It's been seen on live sites processing queues in excess of 50,000 jobs and doing resource intensive operations, like processing payments and creating orders, at a sustained rate of over 10,000 / hour without negatively impacting normal site operations.

This is all on infrastructure and WordPress sites outside the control of the plugin author.

If your plugin needs background processing, especially of large sets of tasks, Action Scheduler can help.

## Learn More

To learn more about how to Action Scheduler works, and how to use it in your plugin, check out the docs on [ActionScheduler.org](https://actionscheduler.org).

There you will find:

* [Usage guide](https://actionscheduler.org/usage/): instructions on installing and using Action Scheduler
* [WP CLI guide](https://actionscheduler.org/wp-cli/): instructions on running Action Scheduler at scale via WP CLI
* [API Reference](https://actionscheduler.org/api/): complete reference guide for all API functions
* [Administration Guide](https://actionscheduler.org/admin/): guide to managing scheduled actions via the administration screen
* [Guide to Background Processing at Scale](https://actionscheduler.org/perf/): instructions for running Action Scheduler at scale via the default WP Cron queue runner

## Support

* Community support via the [support forums](https://wordpress.org/support/plugin/migrate-wp-cron-to-action-scheduler/) at WordPress.org.

## Contribute

* Active development of this plugin is handled [on GitHub](https://github.com/iamsayan/advanced-cron-scheduler/).
* Feel free to [fork the project on GitHub](https://github.com/iamsayan/advanced-cron-scheduler/) and submit your contributions via pull request.

## Credits

Action Scheduler is developed and maintained by [Automattic](http://automattic.com/).

== Installation ==

1. Visit 'Plugins > Add New'
2. Search for 'Advanced Cron Scheduler' and install it.
3. Or you can upload the `migrate-wp-cron-to-action-scheduler` folder to the `/wp-content/plugins/` directory manually.
4. Activate Advanced Cron Scheduler from your Plugins page.
5. After activation go to 'Tools > Scheduled Actions'.

== Frequently Asked Questions ==

= How this plugin works? =

To Avoid all limitations of WP Cron, this plugin automatically replaces all the Cron Events with Actions Scheduler Actions.

= Will it work with my theme? =

Yes, our plugins work independently of themes you are using. As long as your website is running on WordPress version 5.2 or later, it will work.

== Screenshots ==

1. Action Scheduler Tasks

== Changelog ==

If you like Advanced Cron Scheduler, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/migrate-wp-cron-to-action-scheduler/reviews/#new-post). It helps to keep development and support going strong. Thank you!

= 1.1.1 =
Release Date: 1st March, 2024

* Updated: Action Scheduler library to v3.7.21.
* Tested up to WordPress 6.4.

= 1.1.0 =
Release Date: 25th June, 2023

* Added: Option to disable check for past-due actions.
* Tweak: Changed `init` hook to `action_scheduler_init`.
* Updated: Action Scheduler library to v3.6.1.
* Tested up to WordPress 6.3.

= 1.0.9 =
Release Date: 15th November, 2022

* Updated: Action Scheduler library to v3.5.3.
* Tested with WordPress 6.1.

= 1.0.8 =
Release Date: 28th August, 2022

* Added: Settings to toggle settings under Settings > General.
* Updated: Action Scheduler library to v3.5.0.
* Improved: Cron Logic.

= 1.0.7 =
Release Date: 24th June, 2022

* Added: Admin Bar Link.
* Removed: Unused code.
* Fixed: Typo.

= 1.0.6 =
Release Date: 24th June, 2022

* Updated: Action Scheduler library to v3.4.2.
* Fixed: Various bugs and PHP errors.
* Minimum WordPress supported version is now 5.2.0.
* Tested with WordPress 6.0.

= 1.0.5 =
Release Date: 3rd December, 2021

* Fixed: An errors which occurs if cron is creating early before initialization of Action scheduler library.

= 1.0.4 =
Release Date: 2nd December, 2021

* Added: A check to prevent creating duplicate actions.
* Updated: Action Scheduler library to v3.4.0.
* Fixed: Date format error.

= 1.0.3 =
Release Date: 5th June, 2021

* Updated: Action Scheduler library to v3.2.0.

= 1.0.2 =
Release Date: 7th March, 2021

* Added: Ability to Auto Delete pending tasks from Action Scheduler upon Deactivation.

= 1.0.1 =
Release Date: 4th March, 2021

* Added: Ability to Auto delete the Store Schema on PLugin Activation and Deactivation to avoid Missing Database error on some installations.
* Added: Ability to Purge Cache on plugin Deactivation.

= 1.0.0 =
Release Date: 2nd March, 2021

* Initial Release.

== Upgrade Notice ==
Deactivate and Re-activate the plugin after Update process is completed if anything not works.