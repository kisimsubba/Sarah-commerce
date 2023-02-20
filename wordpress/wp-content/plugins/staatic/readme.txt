=== Staatic - Static Site Generator ===
Contributors: staatic
Tags: performance, seo, security, optimization, static site, fast, speed, cache, caching, cdn
Stable tag: 1.4.0
Tested up to: 6.1.1
Requires at least: 5.0
Requires PHP: 7.0
License: BSD-3-Clause

Staatic allows you to generate and deploy an optimized static version of your WordPress site.

== Description ==

Staatic allows you to generate and deploy an optimized static version of your WordPress site, improving performance, SEO and security all at the same time.

Features of Staatic include:

* Powerful Crawler to transform your WordPress site quickly.
* Supports multiple deployment methods, e.g. Netlify, AWS (Amazon Web Services) S3 or S3-compatible providers + CloudFront integration, or even your local server (dedicated or shared hosting).
* Very flexible out of the box (allows for additional urls, paths, redirects, exclude rules).
* Supports HTTP (301, 302, 307, 308) redirects, custom “404 not found” page and other HTTP headers.
* CLI command to publish from the command line.
* Compatible with WordPress MultiSite installations.
* Compatible with HTTP basic auth protected WordPress installations.
* Various integrations to improve compatibility with popular WordPress plugins.

Depending on the chosen deployment method, additional features may be available.

== Installation ==

Installing Staatic is simple!

### Install from within WordPress

1. Visit the plugins page within your WordPress Admin dashboard and select ‘Add New’;
2. Search for ‘Staatic’;
3. Activate ‘Staatic’ from your Plugins page;
4. Go to ‘After activation’ below.

### Install manually

1. Upload the ‘staatic’ folder to the `/wp-content/plugins/` directory;
2. Activate the ‘Staatic’ plugin through the ‘Plugins’ menu in WordPress;
3. Go to ‘After activation’ below.

### After activation

1. Click on the ‘Staatic’ menu item on the left side navigation menu;
2. On the settings page, provide the relevant Build & Deployment settings;
3. Start publishing to your static site!

== Frequently Asked Questions ==

= How will Staatic improve the performance of my site? =

Staatic will convert your dynamic WordPress site into a static site consisting of HTML assets, images, scripts and other assets. By removing WordPress (and even PHP) from the equation, requested pages from your site can be served instantly, instead of having to be generated on the fly.

= Why not use a caching plugin? =

Caching plugins are great to improve the performance of your site as well, however they (usually) don’t remove WordPress itself from the stack, which adds additional latency.

Also by using Staatic, you are free to host your site anywhere. You could for example choose a very fast cloud provider or content delivery network, providing even more performance.

= Will the appearance of my site change? =

No. At least, it should not. If the static version of your site does differ, it is probably because of invalid HTML in your original WordPress site, which could not be converted correctly. In that case you can verify the validity of your HTML using a validator service like [W3C Markup Validation Service](https://validator.w3.org/).

= How will Staatic improve the security of my site? =

Since your site is converted into static HTML pages, the attack surface is greatly reduced. That means less need to worry about keeping WordPress, plugins and themes up-to-date.

= Is Staatic compatible with all plugins? =

Unfortunately not. Because your site is converted into a static site, dynamic server side functions are not available. Plugins that require this, for example to process forms, retrieve data externally etc., do not work out of the box, or are not supported at all.

You will need to make modifications to make such features work, or you can choose Staatic Premium which adds such functionality automatically. For more information, please visit [staatic.com](https://staatic.com/wordpress/).

= Will it work on shared or (heavily) restricted servers? =

Staatic has been optimized to work in most environments. The major requirements are that the plugin is able to write to the work directory and connect to your WordPress installation.

= Where can I get help? =

If you have any questions or problems, please have a look at our [documentation](https://staatic.com/wordpress/documentation/) and [FAQ](https://staatic.com/wordpress/faq/) first.

If you cannot find an answer there, feel free to open a topic on our [Support Forums](https://wordpress.org/support/plugin/staatic/).

Want to get in touch directly? Please feel free to [contact us](https://staatic.com/wordpress/contact/). We will get back to you as soon as possible

== Screenshots ==

1. Use your WordPress installation as a private staging environment and make all of the modifications you need. Then publish these changes to your highly optimized and consumer facing static site with the click of a button.
2. Monitor the status of your publications while they happen and review details of past publications to easily troubleshoot any issues.
3. Configure and fine tune the way Staatic processes your site to suit your specific needs.

== Changelog ==

= 1.4.0 =

Release date: January 30th, 2023.

**Features**

* Adds “HTML DOM parser” crawler setting to allow you to change the HTML DOM parser used while crawling.
* Adds “Process page not found resources” crawler setting, which by default is set to disabled.

**Improvements**

* Improves performance and reliability of publication task processing.
* Adds filter hooks `staatic_crawl_batch_size` and `staatic_deploy_batch_size` to allow fine-tuning publication performance.
* Adds support for AWS regions `ap-south-2`, `eu-central-2`, `eu-south-2` and `me-central-1`.
* Prevents PHP warnings when supplying invalid regular expressions in exclude URLs setting.
* Adds support for database migrations with beta releases.
* Increases PHP time limit during database migrations and publications.
* Ensures that the WordPress debug log is excluded when it is within a configured additional path.
* Updates external dependencies.

**Fixes**

* Uses the correct region while deploying to an Amazon S3 (or compatible) bucket.
* Corrects display of numbers and sizes in certain languages.

= 1.3.4 =

Release date: December 22nd, 2022.

**Improvements**

* Adds support for regular expressions in excluded URLs setting.
* Adds validation to filesystem target directory setting to prevent accidential data loss.

**Fixes**

* Utilizes internal URL normalizer, correcting issues with partially secure WordPress sites (http/https).
* Utilizes `STAATIC_KEY` constant in `wp-config.php` while encrypting/decrypting secrets when available.

= 1.3.3 =

Release date: November 4th, 2022.

**Improvements**

* Adds URL transformation support for XSLT assets.
* Adds configuration test to verify homepage accessibility.
* Displays detailed error information in case a publication fails.
* Improves description of configuration test failures.
* Improves overall compatibility with Elementor page builder plugin.
* Improves support for questionable URLs with duplicate slashes in its path segment.
* Improves excluded URL matching algorithm.
* Updates external dependencies.

**Fixes**

* Fixes undefined array key warning when `HTTP_USER_AGENT` is not defined.

= 1.3.2 =

Release date: October 25th, 2022.

**Improvements**

* Improves support for AWS authentication using IAM security credentials from AWS EC2 instances and AWS ECS containers.
* Tests AWS connectivity before starting publications using the AWS deployment method.
* Improves compatibility with plugins using `http_request_args` filter hook without passing a valid URL.

**Fixes**

* Fixes PHP 7.0 downgrade issue in external HTTP library causing AWS deployment failures in rare cases.

= 1.3.1 =

Release date: October 1st, 2022.

**Features**

* Adds WP-CLI command “staatic migrate” to manually upgrade or downgrade the database.

**Improvements**

* Improves database upgrades with new versions and adds the ability to retry failed upgrades.
* Improves handling of encrypted settings in combination with invalid/modified encryption key.
* Updates external dependencies.

**Fixes**

* Restores compatibility with PHP 7.0-7.1.

= 1.3.0 =

Release date: August 30th, 2022.

**Features**

* Adds support for alternative S3-compatible providers by accepting a custom endpoint in the S3 deployment method.
* Allows the maximum number of invalidation paths to be adjusted when invalidating the CloudFront cache.
* Allows the path to invalidate everything from the CloudFront cache to be adjusted.
* Adds the ability to apply a canned ACL to uploaded files in the S3 deployment method.
* Stores sensitive setting values (passwords, keys and tokens) in encrypted form.

**Improvements**

* Improves overall compatibility with Elementor page builder plugin.
* Skips transformation of fragment-only links while processing HTML files, resolving an issue with Elementor Popups.
* Increases maximum length of supported URLs from 255 to 2083 characters.
* Updates external dependencies.

**Fixes**

* Fixes handling of HTML entities while extracting links from HTML documents, resolving issues with obfuscated mailto-links and SVG data URLs.

= Earlier releases =

For the changelog of earlier releases, please refer to [the changelog on staatic.com](https://staatic.com/wordpress/changelog/).

== Upgrade Notice ==

= 1.4.0 =
This update changes the default HTML DOM parser from Simple Html Dom Parser to PHP DOM Wrapper, because the first appeared to suffer from a memory leak. Different HTML DOM parsers may produce slightly different HTML output.

== Staatic Premium ==

In order to support ongoing development of Staatic, please consider going Premium. In addition to helping the authors maintain Staatic, Staatic Premium adds additional functionality.

For more information visit [Staatic](https://staatic.com/wordpress/).
