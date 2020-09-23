# Dynamic Sitemap Generators

Snippets to generate dynamic sitemaps for Express/Node.js and PHP projects

## Node.js Sitemap

-   Runs as middleware in an Express setup
-   Specify static pages to index
-   Pull dynamic pages from db objects
-   By default, caches sitemap for 24 hours

## PHP Sitemap

-   PHP script to format and print a sitemap with dynamic content
-   Relies on rewritten url from `.htaccess` file
-   Configured to work with WordPress query for basic blog setup

## Format

We're generating these sitemaps based on the Google standard:

```
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xhtml="http://www.w3.org/1999/xhtml" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9&#xA; http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd&#xA;&#x9;&#x9;&#x9;http://www.w3.org/1999/xhtml&#xA;    &#x9;&#x9;http://www.w3.org/2002/08/xhtml/xhtml1-strict.xsd">
  <url>
    <loc>http://localhost:3000/</loc>
    <xhtml:link rel="alternate" hreflang="en-US" href="http://localhost:3000/"/>
    <xhtml:link rel="alternate" hreflang="en" href="http://localhost:3000/en/"/>
    <lastmod>2020-09-22T20:45:20.139Z</lastmod>
    <priority>1.00</priority>
  </url>
</urlset>
```
