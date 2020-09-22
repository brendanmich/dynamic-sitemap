// MIT LICENSE

/**
 * Requires
 */

// Modules
const fs = require('fs');
const xml2js = require('xml2js');
const { AVAILABLE_LOCALES } = require('./Constants');

/**
 * Cache
 */

let cacheSitemap = null;
let cacheDate = new Date();

/**
 * Constants
 */

const sitemapStaticPaths = [
	{ path: '/', priority: '1.00' },
];

/**
 * Helpers
 */

const createAlternativeLinks = (path, hreflang, locale) => {
	const localePath = (locale === '') ? '' : `/${locale}`;
	return {
		$: {
			rel: 'alternate',
			hreflang,
			href: `${process.env.APP_URL}${localePath}${path}`
		}
	};
};

/**
 * Handler
 */

exports.createDynamicSitemap = async (req, res) => {

	// Check for existing cached sitemap
	if (!cacheSitemap || ((new Date()) - cacheDate) > (24 * 60 * 60 * 1000)) { // Cache expires after 24 hours

		// Get sitemap template file
		const sitemap = fs.readFileSync(`${__dirname}/sitemap.xml`, 'utf8');

		// Parse xml sitemap
		const parser = new xml2js.Parser();
		const result = await parser.parseStringPromise(sitemap);

		// Add static urls
		result.urlset.url = [];
		sitemapStaticPaths.forEach((url) => {

			// Create alternatives
			const alternatives = [createAlternativeLinks(url.path, 'en-US', '')];
			AVAILABLE_LOCALES.forEach((locale) => {
				alternatives.push(createAlternativeLinks(url.path, locale, locale));
			});

			// Create url set
			result.urlset.url.push({
				loc: [`${process.env.APP_URL}${url.path}`],
				'xhtml:link': alternatives,
				lastmod: [(new Date().toISOString())],
				priority: [url.priority]
			});
		});

		// MARK: - Add db query and build dynamic pages

		// Build xml sitemap
		const builder = new xml2js.Builder();
		cacheSitemap = builder.buildObject(result);
		cacheDate = new Date();
	}

	// Return xml sitemap content
	res.type('application/xml');
	res.send(cacheSitemap);
};
