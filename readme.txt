=== News Match Donation Shortcode ===
Contributors: innlabs
Tags: donation, shortcode
Requires at least: 4.0
Tested up to: 4.8.2
Stable tag: 0.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin implements a shortcode that allows News Revenue Hub clients to collect donations.

== Description ==

The News Match Donation Shortcode plugin creates a donation form compatible with the [News Revenue Hub](https://fundjournalism.org/) and [News Match](https://newsmatch.org) donation systems. The plugin allows you to configure donation levels to match your organization's existing membership program, and it implements a shortcode that creates a donation form with either buttons or drop-down styled membership level selections. The shortcode can be used multiple times on a single page.

The plugin was developed thanks to an investment from Knight Foundation as part of the national [News Match](https://www.newsmatch.org/) campaign.

== Settings ==

The plugin's settings can be found in the WordPress Dashboard. In the left sidebar, look for Settings > News Match Shortcode.

These settings allow you to configure:
- your organization's name
- whether to accept donations through NewsMatch.org or FundJournalism.org
- your organization's News Match ID with that provider
- the default donation amount
- the live and testing donation form URLs
- which donation form to use
- the Salesforce campaign ID associated with this form

The settings page also allows you to configure up to four donation levels with minimum and maximum donation amounts and custom names for the tiers. Configure the donation levels to match your organization's existing membership programs.

== Donation Shortcode Examples ==

Donations can occur once or reoccur on a monthly or yearly basis. ("One Time", "Per Month" or "Per Year".) The donation shortcode comes in two forms: one with buttons and one with a drop-down to choose the donation frequency. The default shortcode uses buttons:

Add donation form with no Salesforce campaign id and no default donation amount specified:

	[newsmatch_donation_form]

Add donation form with no Salesforce campaign id and $50.00 as the default donation amount:

	[newsmatch_donation_form amount="50"]

Add a donation form with a Salesforce campaign id of `foo` and $25.00 as the default donation amount:

	[newsmatch_donation_form sf_campaign_id="foo" amount="25"]

Add a donation form with a Salesforce campaign id of `foo` and do not specify a default donation amount:

	[newsmatch_donation_form sf_campaign_id="foo"]

You may wish to use a drop-down instead of buttons; to do that add `type="select"` to the shortcode:

	[newsmatch_donation_form type="select"]

	[newsmatch_donation_form amount="50" type="select"]

	[newsmatch_donation_form sf_campaign_id="foo" amount="25" type="select"]

	[newsmatch_donation_form sf_campaign_id="foo" type="select"]

If you have set the plugin to use NewsMatch.org to handle donations instead of FundJournalism.org, the donation frequency options will not be available to readers, as News Match only allows one-time donations.

== Frequently Asked Questions ==

= Who provides support for this plugin? =

If you have questions about this plugin and integrating it with your WordPress site, contact support@inn.org.

If you have questions about the News Revenue Hub, visit [their contact page](https://fundjournalism.org/contact/).

If you have questions about the News Match program, visit their website for [donor](https://www.newsmatch.org/info/donors), [nonprofit](https://www.newsmatch.org/info/nonprofits) and [funding partner](https://www.newsmatch.org/info/funders) information.

= How do I change the style of this form? =

This plugin comes with a default stylesheet, `assets/css/donation.css`, which is output on pages that have the shortcode.

If you do not want this stylesheet enqueued on pages where the donation shortcode is displayed, hook a filter on `newsmatch_donate_plugin_css_enqueue` that returns `False`. Within your filter function, you may want to enqueue your own stylesheet. Alternately, just put the styles in your theme's stylesheet.

The structure of the buttons and `<select>`-based dropdown markup can be examined through your browser's inspector, or by [viewing the source code of the views](https://github.com/INN/news-match-donation-shortcode/tree/master/views).

If you wish to augment the plugin's existing styles, examine the `donation.css` file that comes with this plugin. You may want to:

- configure fonts:
	- `label.donation-frequency` for the donation buttons
	- `.donation-level-message` for the text that appears under the buttons
	- `.newsmatch-donation-form button[type="submit"]` for the submit button
	- `.newsmatch-donation-form` for the form in general
- configure colors:
	- `label.donation-frequency` for the donation buttons
	- `label.donation-frequency:hover` for the hovered donation button
	- `label.donation-frequency.selected` for the active donation button
	- `.newsmatch-donation-form button[type="submit"]` for the submit button
	- `.newsmatch-donation-form button[type="submit"]:hover` for the hovered submit button

= Do I have to be a News Revenue Hub client to use this plugin? =

No; If your organization has a News Match donation page on newsmatch.org, you can use that instead of a donation page at the News Revenue Hub's fundjournalism.org. If you elect to use newsmatch.org, donation options are limited to one-time donations and donation frequency options will not present themselves to your website's readers.

However, this plugin is also compatible with any donation system that can accept queries in this format:

- Setting up a recurring donation: `http://live.example.org/memberform?org_id=organizatino&amount=25.00&installmentPeriod=<yearly|monthly>&campaign=<salesforce campaign ID>`
- Single donation: `http://live.example.org/donateform?org_id=organization&amount=50.00`

It should have live and staging URLs.

= Does my organization have to use this plugin if we are a News Match member? =

No, but we do recommend it if you are a News Revenue Hub client and wish to simplify your News Match donation pipeline.

== Changelog ==

= 0.1.2 =

- Fixes an activation error on older PHP versions
- Adds the ability to use NewsMatch.org donation page URLs.

= 0.1.1 =

Initial public release
