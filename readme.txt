=== Migrate WP Cron to Action Scheduler ===
Contributors: Infosatech
Tags: scheduler, cron
Requires at least: 5.1
Tested up to: 5.7
Stable tag: 1.0.0
Requires PHP: 5.3
Donate link: https://www.paypal.me/iamsayan/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

The Migrate WP Cron to Action Scheduler plugin helps to easily migrate Native WordPress Cron to the Action Scheduler Library.

== Description ==

Action Scheduler is a scalable, traceable job queue for background processing large sets of actions in WordPress. It's specially designed to be distributed in WordPress plugins.

Action Scheduler works by triggering an action hook to run at some time in the future. Each hook can be scheduled with unique data, to allow callbacks to perform operations on that data. The hook can also be scheduled to run on one or more occassions.

Think of it like an extension to `do_action()` which adds the ability to delay and repeat a hook.

## Battle-Tested Background Processing

Every month, Action Scheduler processes millions of payments for [Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/), webhooks for [WooCommerce](https://wordpress.org/plugins/woocommerce/), as well as emails and other events for a range of other plugins.

It's been seen on live sites processing queues in excess of 50,000 jobs and doing resource intensive operations, like processing payments and creating orders, at a sustained rate of over 10,000 / hour without negatively impacting normal site operations.

## Credits

Action Scheduler is developed and maintained by [Automattic](http://automattic.com/) with significant early development completed by [Flightless](https://flightless.us/).

Collaboration is cool. We'd love to work with you to improve Action Scheduler. [Pull Requests](https://github.com/woocommerce/action-scheduler/pulls) welcome.