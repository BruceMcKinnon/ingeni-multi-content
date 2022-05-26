=== Ingeni Multi Content ===

Contributors: Bruce McKinnon
Tags: content
Requires at least: 5.0
Tested up to: 5.7
Stable tag: 2022.03

A custom post type that allows you to store four discrete blocks of content in a single post.

Originally designed for a recipe page, where the design require discrete areas of content to have their own individual treatments.




== Description ==

* - Super-simple, add up to four blocks of content to a simple post.

* - Display the content using a theme template file, or use the shortcode to display selected content blocks.



== Installation ==

1. Upload 'ingeni-multi-content' folder to the '/wp-content/plugins/' directory.

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. Add new Multi-Content posts.

4. Display the content using either the shortcode or directly with custom templates




== Frequently Asked Questions ==



== How do I use the shortcode? ===

Multi Content can be displayed from pages and posts using the [ingeni-multi-block] shortcode.

The following parameters are available:

id = ID of the Multi Content post. Required.

content_id = The content block you wish to display. Supports values between 1 and 4. Defaults to 1.

show_title = Show the title of the content block. Default is 1.

show_content = Show the content of the content block. Default is 1.

class = A wrapper class. Defaults is 'imc_wrapper'. If a blank class is provided, then no wrapping div is applied to the returned HTML.

data-id = A 'data-id' attribute that can be used by JS.


For example:

[ingeni-multi-block id=123 content_id=3 show_title=0 show_content=1 class="my_content_3_no_title"]





== Implementing a tabbed panel ==

Using JS and the 'data-id' attribute, you can implement tabbed panels that use Ingeni Multi Content.


HTML:

<!-- This is block describes the tabs -->
<div class="titles">
<h4><div class="ico-new" data-id=101></div>[ingeni-multi-block id=263 content_id=1 show_title=1 show_content=0 class="mba_title" data-id=101]</h4>
<h4><div class="ico-edit" data-id=102></div>[ingeni-multi-block id=263 content_id=2 show_title=1 show_content=0 class="mba_title" data-id=102]</h4>
</div>

<!-- This block describes the panel content -->
<div class="panels">
[ingeni-multi-block id=263 content_id=1 show_title=0 show_content=1 class="mba_content_panel" data-id=101]
[ingeni-multi-block id=263 content_id=2 show_title=0 show_content=1 class="mba_content_panel" data-id=102]
</div>


CSS:

.panels .mba_content_panel {
  display: none;
  font-size: 20px;
  margin: 7px 0;
}

.panels .mba_content_panel.open {
  display: block;
}


JS:

function multiblockVerticalAccordion() {
  jQuery(".mba_title").mouseenter(function() {
    toggleVerticalAccordionPanel(jQuery( this ).data("id"));
  })
  .mouseleave(function() {
   toggleVerticalAccordionPanel(jQuery( this ).data("id"));
  });

  jQuery("[class^=ico-]").mouseenter(function() {
    toggleVerticalAccordionPanel(jQuery( this ).data("id"));
  })
  .mouseleave(function() {
    toggleVerticalAccordionPanel(jQuery( this ).data("id"));
  });
}

function toggleVerticalAccordionPanel(dataId) {
  if (dataId) {
    jQuery('.mba_content_panel[data-id="'+dataId+'"]').toggleClass('open');
  }
}






== Changelog ==

2021.01 - 12 April 2021 - Initial version

2021.02 - 27 Apr 2021 - Added support for Content Block #4
	- Re-factored some of the load/save functions
	- Added support for Content #1 title
	- Added support for specifying a data-id attrib

2022.01 - 2 May 2022 - Test existence of imc_content2_nonce before verifying it.
	- Removed call to uninstall() hook.

2022.02 - 25 May 2022 - imc_content_save() - Test if post_type is set within $POST

2022.03 - 27 May 2022 - imc_content_save() - More checking of $POST fields before attempting to save.

