=== weclapp ===
Contributors: Lukas Kröger
Tags: webinar, weclapp, campaign, Kampagne 
Requires at least: 4.0
Tested up to: 4.2.3
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin integrates weclapp functionality into wordpress CMS

== Description ==

<h4> Campaign Management </h4>

Adds the possibility to display campaigns and allow automatic webinar registration. It integrates the following two elements into your webpage:

* a list of upcoming campaings of a chosen type scheduled in your weclapp account under CRM -> campaigns
* a display of a submit form enabling visitors of your website to participate in one or more campaigns with one single registration (this feature can be deactivated using the Shortcode parameter displayformular="No")

If a user is not in your contact, lead, or customer list in weclapp then then the participant will be added to your contacts (contact type can be set in the Plugin Settings under "Create new persons as").

Participants trying to register repeatedly will be informed about their already existing participation. Such messages can be configured under the plugin settings.

Here is a sample how the usage of the plugin looks like: https://www.weclapp.com/de/webinare/

<h4> More functionality will be added in the future </h4>

<h4> Supported languages: </h4>

* German
* English
* Turkish
* Polsky

<h4> Example Usage </h4>


To use this feature, just place the Shortcode <code>[weclapp]</code> into a chosen post. 

By default webinar is the campaign type. If you like to use another campaign type, use the Shortcode parameter "type". It is also possible to disable the formular using the Shortcode parameter <code> displayformular="No" </code>. 

For instance, if you like to list all upcoming events without a formular:

<code> [weclapp type="Event" displayformular="No"] </code>

Use the following names for the campaign types:

* Event
* Webinar
* Exposition 
* Publicrelation
* Advertisement
* Bulkmail
* Email
* Telemarketing
* Other

== Installation ==

1. Install plugin under Plugins -> Add new.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Setup your plugin (API data in weclapp can be found under the user settings):
  * Enter your API Token
  * Enter your domain name e.g. your_domain_name.weclapp.com (without "https://")


== Screenshots ==

For an impression visit https://www.weclapp.com/de/webinare/

1. Plugin settings

2. List of all campaigns with type webinar with one opened description

3. List of all campaigns with type webinar including a formular

== Changelog ==

1.0 Initial version with campaign support only

1.1 Shortcode parameters added and settings modified

== Upgrade Notice ==

1.1 Update to this version to use all kinds of campaigns
