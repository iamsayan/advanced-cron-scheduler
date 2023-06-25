# Changelog

If you like Advanced Cron Scheduler plugin, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/migrate-wp-cron-to-action-scheduler/reviews/?filter=5#new-post). It helps to keep development and support going strong. Thank you!

All notable changes to this project will be documented in this file.

## 1.1.0
Release Date: 25th June, 2023

* Added: Option to disable check for past-due actions.
* Tweak: Changed `init` hook to `action_scheduler_init`.
* Updated: Action Scheduler library to v3.6.1.
* Tested up to WordPress 6.3.

## 1.0.9
Release Date: 15th November, 2022

* Updated: Action Scheduler library to v3.5.3.
* Tested with WordPress 6.1.

## 1.0.8
Release Date: 28th August, 2022

* Added: Settings to toggle settings under Settings > General.
* Updated: Action Scheduler library to v3.5.0.
* Improved: Cron Logic.

## 1.0.7
Release Date: 24th June, 2022

* Added: Admin Bar Link.
* Removed: Unused code.
* Fixed: Typo.

## 1.0.6
Release Date: 24th June, 2022

* Updated: Action Scheduler library to v3.4.2.
* Fixed: Various bugs and PHP errors.
* Minimum WordPress supported version is now 5.2.0.
* Tested with WordPress 6.0.

## 1.0.5
Release Date: 3rd December, 2021

* Fixed: An errors which occurs if cron is creating early before initialization of Action scheduler library.

## 1.0.4
Release Date: 2nd December, 2021

* Added: A check to prevent creating duplicate actions.
* Updated: Action Scheduler library to v3.4.0.
* Fixed: Date format error.

## 1.0.3
Release Date: 5th June, 2021

* Updated: Action Scheduler library to v3.2.0.

## 1.0.2
Release Date: 7th March, 2021

* Added: Ability to Auto Delete pending tasks from Action Scheduler upon Deactivation.

## 1.0.1
Release Date: 4th March, 2021

* Added: Ability to Auto delete the Store Schema on PLugin Activation and Deactivation to avoid Missing Database error on some installations.
* Added: Ability to Purge Cache on plugin Deactivation.

## 1.0.0
Release Date: 2nd March, 2021

* Initial Release.