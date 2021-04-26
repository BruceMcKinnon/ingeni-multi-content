=== Ingeni Multi Content ===

Contributors: Bruce McKinnon
Tags: content, receipties
Requires at least: 5.0
Tested up to: 5.7
Stable tag: 2021.01

A custom post type that allows you to store four discrete blocks of content in a single post.

Originally designed for a recipe page, where the design require discrete arears of content to have their own individual treatments.




== Description ==

* - Super-simple, add up to four blocks of content to a simple post.

* - Display the content using a theme template file, or use the shortcode to display selected content blocks.



== Installation ==

1. Upload 'ingeni-multi-content' folder to the '/wp-content/plugins/' directory.

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. Add new Multi-Content posts.

4. Display the content using either the shortcode or directly with custom templates




== Frequently Asked Questions ==



= How do I use the shortcode? =

Multi Content can be displayed from pages and posts using the [ingeni-multi-block] shortcode.

The following parameters are available:

id = ID of the Multi Content post. Required.

content_id = The content block you wish to display. Supports values between 1 and 4. Defaults to 1.

show_title = Show the title of the content block. Default is 1.

show_content = Show the content of the content block. Default is 1.

class = A wrapper class. Defaults is 'imc_wrapper'.



For example:

[ingeni-multi-block id=123 content=3 show_title=0 show_content=1 class="my_content_3_no_title"]




== Changelog ==

2021.01 - 12 April 2021 - Initial version
