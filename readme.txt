=== Plugin Name ===
Contributors: rscheink, zetaprints
Donate link: 
Tags: posts, e-cards, cards, invitations, campaigns, activism, image-generation, images, zetaprints
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: 2.12

Send virtial cards (e-cards) from a post. Use our designs or upload your own. Powered by ZetaPrints image generator.

== Description ==
Send virtial cards (e-cards) from a post. Use our designs or upload your own.

Based on [Zetaprints Image Generator](http://www.zetaprints.com/help/dynamic-image-generation-api/). Install the plugin. Activate.  [Find a design you like](http://zetaprints.com) on ZetaPrints.com or [create your own](http://www.zetaprints.com/help/). Paste the design ID to the config screen. Insert shortcode `[zp-e-cards]` into a post and bingo! The post gets a cute flash plugin with the selected designs that your visitors can modify and email to either a pre-defined list of recipients or enter a recipient of their own.

* embed in multiple posts using a shortcode
* one central config page for default values
* individual configuration section on each New/Edit post page
* search for templates/catalogs that fit your needs with the embedded search/retrieve widget
* images can be emailed to predefined recipients
* hide emails of the recipients, show names only
* control if users can enter email of the recipient
* optional sender's email address
* optional sender's email address validation
* use our designs
* upload your own designs
* control size of the Flash widget

This plugin was developed as an open source project and is hosted on [Google Code](http://code.google.com/p/e-cards-plugin/). You are welcome to poke around and generally do as you please, as it's under MIT license. 


== Installation ==

1. Upload `zpEcards.zip` through the plugin installation page in WordPress.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to `Ecards` menu at the bottom of the left navigation bar in WordPress admin.
1. Configure default values, read instructions on how to find designs and get design IDs.
1. Save default configuration.
1. Insert `[zp-e-cards]` shortcode into a post where you want the e-cards widget to appear.
1. Configure the widget for this post at E-cards section of the post (scroll down the page).
1. Save or publish the post.

== Frequently Asked Questions ==

= Can I hide email addresses of the recipients? =

Yes. Enter them like this: Senator 1, jo.bloggs@example.com
The dropdown will have only Senator 1 and not the email address. However, if the sender is required to enter his/her email address and the email bounces back for some reason, it will come back to the sender and disclose the email address of the recipient. Not your fault!

= Can I make sure the senders use real email addresses? =

Check on "Validate sender's email address" option in the configuration section and every sender will get a confirmation email with a link. The actual email with the card will be dispatched only of the link is clicked.
You do not have to do this. You can make it free and open with no sender's address required.

= What can I use this plugin for? =
Anywhere where you want to add a bit of creativity and emotion to the message. If all you are after is a plain message then send a plain text email instead. Invitations, get well cards, campaigns and activism are the most obvious applications. 

= What sort of designs can it handle? =
The plugin is agnostic to the type of design as such. It can be virtial cards or email signatures or internet banners or buttons for websites or badges, whatever your imagination can dream up.

== Screenshots ==

1. A sample post with the widget.
screenshot-1.jpg

2. A sample post an email form.
screenshot-2.jpg


