=== Geneanet Embedded Individual ===
Contributors: alanpoulain
Tags: shortcode, embedded
Requires at least: 3.0.1
Tested up to: 4.8
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow to embed in a blog post main information about an individual from a Geneanet family tree.

== Description ==

The Geneanet Embedded Individual displays a box with main information about an individual from a family tree in your posts.

An embedded profile contains main information about an individual from a Geneanet family tree:
* picture (if available),
* last name and first name,
* Sosa/Ahnentafel number,
* date and place of birth,
* date and place of death,
* spouses and number of children,
* a link to the family tree or to the sheet of the individual on Geneanet.

There are two ways to use this plugin:
* By using the shortcode `geneanet-embed-individual` with the parameters `url`, `link_type` (`fiche` or `tree`) and `align` (`left`, `center` or `right`).
* By installing the plugin [Shortcake](https://wordpress.org/plugins/shortcode-ui/) which adds an user-friendly interface for adding a sheet to your post (click on Add Post Element).

= Features =
* Paste the URL of the profile page of an individual from a Geneanet family tree (it can be yours or any other family tree) and the plugin will take care of everything: if the individual is updated on Geneanet, the embedded profile will automatically be updated!
* Select the type of link you want to generate for the embedded profile. If you select `fiche`, the link will open the profile page of the individual on Geneanet. If you select `tree`, it will open the Geneanet family tree of the individual.
* The visual editor works with this plugin: preview in real time how your post will look like when it will be posted.
* Add any number of embedded profiles to your post.

This plugin uses the Geneanet API to retrieve the information about the individual.

When saving the post, the plugin saves some information about the individual: name, first name and gender. This allows to show it in the post before retrieving all data from Geneanet.

= Languages =
* Dutch
* English
* Finnish
* French
* German
* Italian
* Norwegian
* Portuguese
* Russian
* Spanish
* Swedish

= Credits =
* This plugin is part of the Labs from [Geneanet](http://www.geneanet.org), the #1 genealogy website in Europe. It's also based on the following projects: [WordPress Plugin Boilerplate](https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate) and [Beautiful Taxonomy Filters](https://github.com/jonathan-dejong/beautiful-taxonomy-filters).

== Installation ==

1. Search the plugin in the 'Plugins' menu in WordPress and install it or move manually the `geneanet-embedded-individual` folder to the `/wp-content/plugins/` folder.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. (Optional) Install the plugin [Shortcake](https://wordpress.org/plugins/shortcode-ui/).

== Frequently Asked Questions ==

= I have found a bug, how can I contact you? =

The best way to contact us is to post a message in the [Geneanet forum](http://en.geneanet.org/forum/Geneanet-Help-amp-Support-40).

== Screenshots ==

1. Example in a post.
2. Example in the visual editor.
3. Example in the text editor.
4. Options when using Shortcake.

== Changelog ==

= 1.1.1 =
* Use em instead of rem in CSS
= 1.1.0 =
* Add alignment option
= 1.0.1 =
* Fix box-sizing issue
* Fix loading in the visual editor when creating a new post
* Fix the changes done in the published post when modifying the shortcode in the visual editor
= 1.0.0 =
* First public version
