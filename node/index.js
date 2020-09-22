// MIT LICENSE

/**
 * Required
 */

// Environment
const path = require('path');
require('dotenv').config({ path: path.resolve(__dirname, './.env') });

// Modules
const express = require('express');
const http = require('http');
const Constants = require('./Constants');
const { createDynamicSitemap } = require('./sitemap');

/**
 * Configure app
 */

const app = express();
module.exports = app;

/**
 * Enable trust proxy (Heroku)
 */

app.enable('trust proxy');

/**
 * Cross-domain middleware to prevent unidentified calls
 */

app.use(async (req, res, next) => {
	if (process.env.ENABLE_ORIGIN_WHITELIST === 'true') {
		const { origin } = req.headers;
		if (origin && Constants.DOMAIN_WHITELIST.indexOf(origin) === -1) {
			res.status(403).end();
		} else {
			next();
		}
	} else {
		next();
	}
});

/**
 * Security upgrade middleware to redirect http calls to https
 */

if (process.env.ENV !== 'development') {
	app.use(async (req, res, next) => {
		if (req.get('X-Forwarded-Proto') === 'https' || req.hostname === 'localhost') {
			next();
		} else if (req.get('X-Forwarded-Proto') !== 'https' && req.get('X-Forwarded-Port') !== '443') {
			res.redirect(301, `https://${req.hostname}${req.url}`);
		}
	});
}

/**
 * Serve sitemap
 */

app.use('/sitemap.xml', createDynamicSitemap);

/**
 * Set up port and listener
 */

const port = process.env.PORT || 1337;
const httpServer = http.createServer(app);
httpServer.listen(port, () => {
	console.log(`dynamic-sitemap running on port ${port}.`);
});
