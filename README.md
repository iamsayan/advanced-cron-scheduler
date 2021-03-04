# Migrate WP Cron to Action Scheduler

The Migrate WP Cron to Action Scheduler plugin helps to easily migrate Native WordPress Cron to the Action Scheduler Library. 

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

The Migrate WP Cron to Action Scheduler plugin does alter the way that WordPress core runs cron events using the Action Scheduler Library.

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

* Active development of this plugin is handled [on GitHub](https://github.com/iamsayan/migrate-wp-cron-to-action-scheduler/).
* Feel free to [fork the project on GitHub](https://github.com/iamsayan/migrate-wp-cron-to-action-scheduler/) and submit your contributions via pull request.

## Credits

Action Scheduler is developed and maintained by [Automattic](http://automattic.com/).

## Changelog ##
[View Changelog](CHANGELOG.md)