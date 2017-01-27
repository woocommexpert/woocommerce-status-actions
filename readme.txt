=== WooCommerce Order Status & Actions Manager ===

Author: actualityextensions
Tags: woocommerce, orders, status, action, statuses, actions, status icons, action icons, custom order status, custom order action, email notification, action triggers, order status manager, order, manager
Requires at least: 4.6
Tested up to: 4.6
Stable tag: 2.1.1
Requires WooCommerce at least: 2.6
Tested WooCommerce up to: 2.6.7

Allows users to manage WooCommerce order statuses, create the action button that triggers the status and set up what happens to the order when the status is applied.

== Description ==

= Status Style =
Having custom statuses that can be displayed in different styles: colours, icons and variations allows you to manage your orders more effectively.

= Payment Complete Status =
Define the status for orders upon successful payment. This uses the feedback that is sent by gateways to trigger the status set from the Settings page.

= Font Icons =
Choose out of the default WooCommerce icon library as well as FontAwesome, Dashicons and the included IcoMoon 2000+ font icon library that comes with our plugin.

= Action Buttons =
Define which action buttons are displayed when the status is configured. You can also select your custom font icon to be displayed in the action button.

= Default Statuses =
Change the colour, text and settings for default statuses. This includes whether they are displayed in Bulk Actions and if they allow item editing when the order is set to the status.

= Email Notification =
Select the recipient for which the email should be sent to when the status is triggered onto the order. This includes the customer, admin and/or a custom email address.

= Custom Email Messages =
Include shortcodes from the order as well as integrations from [WooCommerce Shipment Tracking](https://www.woothemes.com/products/shipment-tracking/), [Advanced Custom Fields](https://en-gb.wordpress.org/plugins/advanced-custom-fields/) and the [WooCommerce Checkout Field Editor](https://www.woothemes.com/products/woocommerce-checkout-field-editor/) plugin.

= Customer Options =
Choose what customers can do when the status is set. This includes leaving a review for the products purchased in the order, cancelling the order or triggering another status from their My Account page.

= Product Options =
Choose how the products purchased are affected when the custom status is set. This includes permitting downloads for virtual products, increase / decrease of stock and enabling item editing of the order.

= Action Settings =
Decide which default actions are displayed when the status is set. You can also set a custom font icon for the action button. You can also hide the action from the Bulk Actions menu as well as set an automatic trigger after an interval of time to trigger another status.

= Documentation =
You can find the documentation to [here](http://actualityextensions.com/documentation/woocommerce-order-status-actions-manager/). If there is anything missing from the documentation that you would like help on, please fill in our contact [form](http://actualityextensions.com/contact/).

= Bugs =
Should you find a bug, please do not hesitate to contact us through our support form found [here](http://actualityextensions.com/contact/).

== Installation ==
1. Upload the entire 'woocommerce-status-actions' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==
= 2.1.2 - 2016.11.11 =
* Tweak to order layouts.

= 2.1.1 - 2016.11.10 =
* Fix bug with updater.

= 2.1.0 - 2016.11.03 =
* Fix bug with order statuses duplicating when existing slug used.

= 2.0.9 - 2016.11.02 =
* Tweak status style with text filled and lined.

= 2.0.8 - 2016.09.21 =
* Feature added to enable / disable prompt for button on My Account page.

= 2.0.7 - 2016.09.13 =
* Fix bug with trigger period not saving.
* Tweak to prompt shown when status is cancelled from My Account page.

= 2.0.6 - 2016.07.29 =
* Fix bug with statuses not changing.
* Fix bug with emails not sent when using bulk actions.

= 2.0.5 - 2016.07.15 =
* Fix bug with custom email addresses not receiving status updates.

= 2.0.4 - 2016.07.02 =
* Fix bug with emails not sending to customer and administrator at the same time.
* Fix bug with blank fields not being ignored in the emails.
* Feature added to show the Pay button on the My Account page.

= 2.0.3 - 2016.06.23 =
* Change icons on the settings page to match new WooCommerce icons.
* Tweak to the font smoothing being applied to fill text statuses.
* Tweak to the dashboard icons.

= 2.0.2 - 2016.06.09 =
* Fix bug with updates causing errors with existing status.

= 2.0.1 - 2016.06.07 =
* Fix bug with title being replaced.
* Fix bug with text fill status types.

= 2.0.0 - 2016.06.06 =
* Refactor entire plugin to handle high number of orders and reduce loading.

= 1.7.9.1 - 2016.05.16 =
* Fix bug with Bank Transfer orders disappearing on Orders page.

= 1.7.9 - 2016.05.04 =
* Fix bug with Bulk Actions not displaying custom statuses in right order.
* Fix bug with case switch error appearing.

= 1.7.8.1 - 2016.05.01 =
* Fix bug where featured images could not be set.

= 1.7.8 - 2016.04.22 =
* Feature to add custom note when triggering action.
* Feature to set default status per gateway.

= 1.7.7 - 2016.04.21 =
* Feature with sorting order statuses.

= 1.7.6 - 2016.04.12 =
* Feature plugin updater.

= 1.7.5 - 2016.03.14 =
* Fix bug with dashboard icons not appearing correctly.
* Fix bug with icon selector not displaying on certain hosts.

= 1.7.4 - 2015.12.14 =
 * Fix bug appearing on the status table page.
 * Fix bug related to the shipment tracking plugin.
 * Tweak to the UX when setting status colour, real live change of styles.
 * Tweak to the UX when setting status icon, real live change of styles.
 
= 1.7.3 - 2015.12.11 =
 * Tweak to the font icon picker, now includes WooCommerce, Dashboard and Font Awesome icons.

= 1.7.2 - 2015.10.07 =
 * Fix bug with automatic triggers being applied even after the status is changed.
 * Feature added with payment method short code.
 * Feature added with shipping method short code.

= 1.7.1 - 2015.09.15 =
 * Tweak to required fields, more defined.
 * Tweak to settings field that use select2.
 * Fix bug with order status table showing incorrect item editable.
 * Name change to WooCommerce Order Status & Actions Manager

= 1.7.0 - 2015.08.28 =
 * Feature added where users can now set automatic status trigger after a period of time.
 * Removal of customer trigger from Order Statuses column.
 * Rename of customer trigger to My Account.
 * Tweak to Edit Status page.
 * Tweak to Order Statuses page.

= 1.6.10 - 2015.08.27 =
 * WooCommerce 2.4 compatibility.
 * Tweak to order status page layout by removing icons.
 * Fix bug on settings page after WooCommerce update.
 * Fix bug with Avada theme.

= 1.6.9 - 2015.06.17 =
 * Feature added for Advanced Custom Fields short codes.
 * Fix bug with missing bulk actions.

= 1.6.8 - 2015.06.03 =
 * Fix bug with some orders not coming through due to invalid colour string.
 * Feature added for Aftership integration thanks to mensmaximus.

= 1.6.7 - 2015.05.22 =
 * Feature added with the message now includes visual editor along with shortcode tool.
 * Feature added can now disable and enable item editing for default statuses.
 * Feature added for dashboard WooCommerce Status widget, new widget.
 * Tweak to action button visibility, can now select none if you do not want button.
 * Fix bug with missing tab status.

= 1.6.6 - 2015.04.24 =
 * WordPress 4.2 compatibility and fix XSS vulnerability.

= 1.6.5 - 2015.04.14 =
 * Fix bug with stock not being restored.
 * Feature added to allow users to set which status allows order to be edited.

= 1.6.4 - 2015.04.08 =
 * Fix bug with settings page loaded on some users.
 * Feature added where you can define whether product reviews can be left or not.
 * Tweak to localisation strings.

= 1.6.3 - 2015.03.26 =
 * Fix issue when editing old status - migration bug.

= 1.6.2 - 2015.03.12 =
 * Tweak to hooks, new added.
 * Updated translations.
 
= 1.6.1 - 2015.03.02 =
 * Tweak with status handling.
 * Tweak to notifications.

= 1.6 - 2015.02.17 =
 * WC 2.3 compatibility
 * Fix 838 error bug

= 1.5.5 - 2015.02.09 =
 * Feature to allow you to hide or show statuses in the bulk actions menu.
 * Feature to reset the default statuses (colour and name).
 * Tweak to the default status table added icon.
 * Tweak to the action visibility field.
 * Fix bug with customers not being able to trigger statuses.

= 1.5.4 - 2015.02.03 =
 * Feature where you can now choose what colour the icons are for the default WooCommerce order statuses.
 * Tweak to the edit status page for icons.

= 1.5.3 - 2015.01.26 =
 * Feature added where you can decide what happens when new order is placed with completed payment.
 * Tweak to the settings page.

= 1.5.2 - 2014.12.09 =
 * Fix bug with pagination.

= 1.5.1 - 2014.12.04 =
 * Fix bug with jQuery not being loaded, now included.

= 1.5.0 - 2014.11.30 =
 * Feature added where you can choose what happens to product stock levels when status is applied.
 * Feature added where you can allow customers to trigger status from My Account page.
 * Tweak to CSS of the status style.
 * Fix bug with undefined values.
 * Fix bug with reports page, refunded orders.
 * Fix bug with slug database.
 
= 1.4.9 - 2014.10.28 =
 * Feature added, multisite support.

= 1.4.8 - 2014.10.27 =
 * Fix bug with reports displaying error.
 * Fix bug on Orders page with statuses.
 * Feature added localisation.

= 1.4.7 - 2014.10.07 =
 * Fix bug with Action Visibility.

= 1.4.6 - 2014.10.07 =
 * Fix bug with downloads (Thanks to Rob from Bare Fiction).
 * Fix bug with missing styles on settings page.
 * Tweak to status icons on status & actions page.
 * Tweak to status styles.
 * Feature added to support permitting downloads on custom status.

= 1.4.5 - 2014.09.26 =
 * Fix bug when setting statuses via the Bulk Actions menu.

= 1.4.4 - 2014.09.23 =
 * Fix bug with Czech and Japanese sites.

= 1.4.3 - 2014.09.17 =
 * Fix bug when deleting statuses.
 * Fix bug when selecting the "Cancel Orders" option.

= 1.4.2 - 2014.09.14 =
 * Tweak to the Status slug being displayed in status column.

= 1.4.1 - 2014.09.12 =
 * Fix major bug when deleting status, no longer changes all orders status.

= 1.4 - 2014.09.10 =
 * Feature compatibility with WooCommerce 2.2.
 * Feature added when choosing a font icon.
 * Tweak to the status page.
 * Fix bug with Cyrillic characters.

= 1.3.2 - 2014.08.13 =
 * Fix bug not support Cyrillic characters.
 
= 1.3.1 - 2014.07.14 =
 * Fix bug of ghost status appearing after deleting the status in drop down.

= 1.3 - 2014.07.09 =
 * Feature included for multisite support.

= 1.2.1 - 2014.06.25 =
 * Fix the 'Customise Email' link page on editing and creating a status.

= 1.2 - 2014.06.12 =
 * Feature added where you can now include Checkout Field Editor shortcodes (offical WooThemes extension).
 * Feature added where you can now include Shipment Tracking shortcodes (official WooThemes extension).
 * Fix made on the status page when showing icons.

= 1.1.1 - 2014.04.22 =
 * Fixed a bug regarding the installation.

= 1.1 - 2014.03.17 =
 * Feature added where status shows in report.
 * Feature added where you can set custom email address to send to.
 * Removed support for WooCommerce 2.0.x
 * Refined the status icon styles.

= 1.0 - 2013.12.01 =
 * Initial release!
 
 == FAQ ==
= Where can I get support or talk to other users? =
If you come across a bug or a problem, please contact us [here](http://actualityextensions.com/contact/).

For queries on customisation and modifications to the plugin, please fill this [form](http://actualityextensions.com/contact/).

You can view comments on this plugin on [Envato](http://codecanyon.net/item/woocommerce-order-status-actions-manager/6392174/comments).

= Where can I find the documentation? =
You can find the documentation of our Point of Sale plugin on our [documentations page](http://actualityextensions.com/documentation/woocommerce-order-status-actions-manager/).