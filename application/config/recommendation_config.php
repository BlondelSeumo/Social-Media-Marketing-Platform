<?php 
$config['page_title_recommendation'] = 'Title is the heading of the webpage. The sentence or string enclosed between html title tag (<title></title>) is the title of your website. Search engines searches for the title of your website and displays title along with your website address on search result. Title is the most important element for both SEO and social sharing. Title should be less than 50 to 60 characters because search engine typically displays this length of string or sentence on search result. A good title can consist the primary keyword, secondary keyword and brand name. For example a fictitious gaming information providing sites title may be like "the future of gaming information is here".  A webpage title should contain a proper glimpse of the website. title is important element as an identification of your website for user experience, SEO and social sharing. So have a nice and catching title. <br/> <a href="https://moz.com/learn/seo/title-tag" target="_BLANK"> <i class="fa fa-hand-o-right"></i> Learn more</a>';


$config['description_recommendation']="Description is the full interpretation of your website content and features. Most often it is a short paragraph that describe what are features and information provided by the website to its visitors. You may consider it a advertising of your website. Although not important for search engine ranking but very important for hits or visits through search engine results. Description should be less than 150 character because search engine shows this length of paragraph on search result. And every page of website should contain an unique description to avoid description duplication. Description is the definition of your website for user experience so form it as complete but short and precise illustration of your website.";


$config['meta_keyword_recommendation']="Meta keywords are keywords inside Meta tags. Meta keywords are not likely to be used for search engine ranking. the words of title and description can be used as meta keywords. it is a good idea for SEO other than search engine ranking.";


$config['keyword_usage_recommendation']="Keyword usage is the using of your keywords inside Meta tags and contents of your website. Use keywords that describes your site properly for precise search engine result of your website.";


$config['unique_stop_words_recommendation']="Unique words are uncommon words that reflects your site features and informations. Search engine metrics are not intended to use unique words as ranking factor but it is still useful to get a proper picture of your site contents. Using positive unique words like complete, perfect, shiny, is a good idea user experience.<br/><br/>
Stop words are common words like all the preposition, some generic words like download, click me, offer, win etc. since most used keyword may be a slight factor for visitors you are encouraged to use more unique words and less stop words.";


$config['heading_recommendation']="h1 status is the existence of any content inside h1 tag. Although not important like Meta titles and descriptions for search engine ranking but still a good way to describe your contents in search engine result.<br/><br/>
h2 status less important but should be used for proper understanding of your website for visitor.";


$config['robot_recommendation']='robots.txt is text file that reside on website root directory and contains the instruction for various robots (mainly search engine robots) for how to crawl and indexing your website for their webpage. robots.txt contains the search bots or others bots name, directory list allowed or disallowed to be indexing and crawling for bots, time delay for bots to crawl and indexing and even the sitemap url. A full access or a full restriction or customized access or restriction can be imposed through robots.txt.<br><br>
robots.txt is very important for SEO. Your website directories will be crawled and indexed on search engine according to robots.txt instructions. So add a robots.txt file in your website root directory. Write it properly including your content enriched pages and other public pages and exclude any pages which contain sensitive information. Remember robots.txt instruction to restrict access to your sensitive information of your page is not formidable on web page security ground. So do not use it on security purpose.
<br/> <a href="http://www.robotstxt.org/robotstxt.html" target="_BLANK"> <i class="fa fa-hand-o-right"></i> Learn more</a>';


$config['sitemap_recommendation']='Sitemap is a xml file which contain full list of your website urls. It is used to include directories of your websites for crawling and indexing for search engine and access for users. it can help search engine robots for indexing your website more fast and deeply.  It is roughly an opposite of robots.txt
You can create a sitemap.xml by various free and paid service or you can write it with proper way (read about how write a sitemap). <br><br>
<b>Also keep these things in mind:</b> <br/>
1) Sitemap must be less than 10 MB (10,485,760 bytes) and can contain maximum 50,000 urls. if you have more uls than this create multiple sitemap files and use a sitemap index file.<br/>
2) Put your sitemap in website root directory and add the url of your sitemap in robots.txt.<br/>
3) sitemap.xml can be compressed using grip for faster loading.<br/><br/>
<b>Broken link:</b> a broken link is an inaccessible link or url of a website. a higher rate of broken links have a negative effect on search engine ranking due to reduced link equity. it also has a bad impact on user experience. There are several reasons for broken link. All are listed below.<br/>
1) An incorrect link entered by you. <br/>
2) The destination website removed the linked web page given by you. (A common 404 error).<br/>
3) The destination website is irreversibly moved or not exists anymore. (Changing domain or site blocked or dysfunctional).<br/>
4) User may behind some firewall or alike software or security mechanism that is blocking the access to the destination website.<br/>
5) You have provided a link to a site that is blocked by firewall or alike software for outside access.<br/>
<a href="http://www.sitemaps.org/protocol.html" target="_BLANK"> <i class="fa fa-hand-o-right"></i> Learn more</a> or <a href="http://webdesign.tutsplus.com/articles/all-you-need-to-know-about-xml-sitemaps--webdesign-9838" target="_BLANK"> <i class="fa fa-hand-o-right"></i> Learn more</a>';




$config['no_do_follow_recommendation']='<p>
  <strong>NoIndex : </strong>noindex directive is a meta tag value. noindex directive  is for not to show your website on search engine results. You must not set &lsquo;noindex&rsquo; as value in meta tags if you want to be your website on search engine result.</p>
<p>
  By default, a webpage is set to &ldquo;index.&rdquo; You should add a <code>&lt;meta name=&quot;robots&quot; content=&quot;noindex&quot; /&gt;</code> directive to a webpage in the &lt;head&gt; section of the HTML if you do not want search engines to crawl a given page and include it in the SERPs (Search Engine Results Pages).</p>
<p>
  <strong>DoFollow &amp; NoFollow : </strong>nofollow directive is a meta tag value. Nofollow directive  is for not to follow any links of your website by search engine bots. You must not set &lsquo;nofollow&rsquo; as value in meta tags if you want follow your link by search engine bots.</p>
<p>
  By default, links are set to &ldquo;follow.&rdquo; You would set a link to &ldquo;nofollow&rdquo; in this way: <code>&lt;a href=&quot;http://www.example.com/&quot; rel=&quot;nofollow&quot;&gt;Anchor Text&lt;/a&gt;</code> if you want to suggest to Google that the hyperlink should not pass any link equity/SEO value to the link target.</p>
<p>
  <a target="_BLANK" href="http://www.launchdigitalmarketing.com/seo-tips/difference-between-noindex-and-nofollow-meta-tags/"><i class="fa fa-hand-o-right"></i> Learn more</a></p>';



$config['seo_friendly_recommendation']='An SEO friendly link is roughly follows these rules. The url should contain dash as a separator, not to contain parameters and numbers and should be static urls.<br><br>
To resolve this use these techniques.<br>
1) Replace underscore or other separator by dash, clean url by deleting or replaceing number and parameters. <br>
2) Marge your www and non www urls.<br>
3) Do not use dynamic and related urls. Create an xml sitemap for proper indexing of search engine.<br>
4) Block unfriendly and irrelevant links through robots.txt.<br>
5) Endorse your canonical urls in canonical tag.<br/>
<a target="_BLANK" href="https://www.searchenginejournal.com/five-steps-to-seo-friendly-site-url-structure/"><i class="fa fa-hand-o-right"></i> Learn more</a>';



$config['img_alt_recommendation']='An alternate title for image. Alt attribute content to describe an image. It is necessary for notifying search engine spider and improve actability to your website. So put a suitable title for your image at least those are your website content not including the images for designing your website. To resolve this put a suitable title in your alt attributes.<br>
<a target="_BLANK" href="https://yoast.com/image-seo-alt-tag-and-title-tag-optimization/"><i class="fa fa-hand-o-right"></i>  Learn more</a>';


$config['depreciated_html_recommendation']="Older HTML tags and attributes that have been superseded by other more functional or flexible alternatives (whether as HTML or as CSS ) are declared as deprecated in HTML4 by the W3C - the consortium that sets the HTML standards. Browsers should continue to support deprecated tags and attributes, but eventually these tags are likely to become obsolete and so future support cannot be guaranteed.";


$config['inline_css_recommendation']="Inline css is the css code reside in html page under html tags not in external .css file. Inline css increases the loading time of your webpage which is an important search engine ranking factor. So try not to use inline css.";


$config['internal_css_recommendation']="Internal css is the css codes which resides on html page inside style tag. Internal css is increases loading time since no page caching is possible for internal css. Try to put your css code in external file.";


$config['html_page_size_recommendation']='HTML page size is the one of the main factors of webpage loading time. It should be less than 100 KB according to google recommendation. Note that, this size not including external css, js or images files. So small page size less loading time.<br><br>
To reduce your page size do this steps<br>
1) Move all your css and js code to external file.<br>
2) make sure your text content be on top of the page so that it can displayed before full page loading.<br>
3) Reduce or compress all the image, flash media file etc. will be better if these files are less than 100 KB<br>
<a target="_BLANK" href="https://www.searchenginejournal.com/seo-recommended-page-size/10273/"><i class="fa fa-hand-o-right"></i>  Learn more</a>';

$config['gzip_recommendation']="GZIP is a generic compressor that can be applied to any stream of bytes: under the hood it remembers some of the previously seen content and attempts to find and replace duplicate data fragments in an efficient way - for the curious, great low-level explanation of GZIP. However, in practice, GZIP performs best on text-based content, often achieving compression rates of as high as 70-90% for larger files, whereas running GZIP on assets that are already compressed via alternative algorithms (e.g. most image formats) yields little to no improvement. It is also recommended that, GZIP compressed size should be <=33 KB";


$config['doc_type_recommendation']='doc type is not SEO factor but it is checked for validating your web page. So set a doctype at your html page.<br> <a target="_BLANK" href="http://www.pitstopmedia.com/sem/doctype-tag-seo"><i class="fa fa-hand-o-right"></i> Learn more</a>';


$config['micro_data_recommendation']='Micro data  is the information underlying a html string or paragraph. Consider a string “Avatar”, it could refer a profile picture on forum, blog or social networking site or may it refer to a highly successful 3D movie. Microdot is used to specify the reference or underlying information about an html string. Microdata gives chances to search engine and other application for better understanding of your content and better display significantly on search result.
<br> <a target="_BLANK" href="https://schema.org/docs/gs.html"><i class="fa fa-hand-o-right"></i> Learn more</a>';


$config['ip_canonicalization_recommendation']='If multiple domain name is registered under single ip address the search bots can label other sites as duplicates of one sites. This is ip canonicalization. Little bit like url canonicalizaion. To solve this use redirects.
<br> <a target="_BLANK" href="http://www.phriskweb.com.au/DIY-SEO/ip-canonicalization"><i class="fa fa-hand-o-right"></i> Learn more</a>';



$config['url_canonicalization_recommendation']='Canonical tags make your all urls those lead to a single address or webpage into a single url. Like : <br>
<code>&lt;link rel="canonical" href="https://mywebsite.com/home" /&gt;</code><br>
<code>&lt;link rel="canonical" href="https://www.mywebsite.com/home" /&gt;</code><br>
Both refer to the link mywebsite.com/home. So all the different url with same content or page now comes under the link or url mywebsite.com/home. Which will boost up your search engine ranking by eliminating content duplication.
Use canonical tag for all the same urls.<br> <a target="_BLANK" href="https://audisto.com/insights/guides/28/"><i class="fa fa-hand-o-right"></i> Learn more</a>';


$config['plain_email_recommendation']='Plain text email address is vulnerable to email scrapping agents. An email scrapping agent crawls your website and collects every Email address which written in plain text. So existence of plain text email address in your website can help spammers in email Harvesting. This could be a bad sign for search engine.<br/><br/>
<b>To fight this you can obfuscate your email addresses in several ways:</b> <br/>
1) CSS pseudo classes.<br/>
2) Writing backward your email address.<br/>
3) Turn of display using css.<br/>
4) Obfuscate your email address using javascript.<br/>
5) Using wordpress and php (wordpress site only).<br/>
<a target="_BLANK" href="http://www.labnol.org/internet/hide-email-address-web-pages/28364/"><i class="fa fa-hand-o-right"></i> Learn more</a>';



$config['text_to_html_ratio_recommendation']="The ideal page's ratio of text to HTML code must be lie between 20 to 60%.
Because if it is come less than 20% it means you need to write more text in your web page while in case of more than 60% your page might be considered as spam.";