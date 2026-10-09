# M.A. Khan Insurance Brokers

Approved website: V27, 9 October 2026. Both approved films are included at 1080p. The layout, content, page navigation, logos and photographs are preserved from the approved HTML.

## Preview

Serve this directory with `python3 -m http.server 8000`, then open `http://localhost:8000`. The static `index.html` cannot send enquiries. The WordPress theme configures the live endpoint automatically.

## WordPress installation

Run `python3 scripts/package-theme.py`. Install `build/makhan-theme.zip` through Appearance → Themes → Add New → Upload Theme on a staging WordPress installation. The theme retains the approved hash-based page navigation; content is maintained in GitHub rather than the WordPress block editor. Upload limits may require installing the theme folder through the hosting file manager or SFTP because the videos make the package around 78 MB.

The active theme checks this public repository's tested releases using WordPress's native theme update mechanism. Future changes require a new semantic Version in style.css (for example, 1.0.1); the GitHub build checks the files and publishes a ZIP release. WordPress can then install that release through its Updates screen. This is a versioned update connection, not an immediate push-to-production deployment. It requires no hosting credentials or GitHub token on the website. Do not overwrite WordPress core, wp-config.php or wp-content/uploads. Deploy this theme only to wp-content/themes/makhan.

## Enquiry form

The theme posts JSON to `/wp-json/makhan/v1/enquiry` and sends plain-text enquiries to `info@makhaninsurance.com` with the visitor's address as Reply-To. It validates input, rejects header injection, limits request size, adds a honeypot and limits submissions per hashed connecting address. No visitor messages are saved to this public repository.

Configure authenticated outgoing mail on the actual host. Test a valid enquiry, invalid input, failed transport, and receipt in the destination mailbox before launch. WordPress mail acceptance does not guarantee delivery. The rate limit is a basic best-effort control; a reverse proxy and expected business traffic may require host-specific tuning. Keep credentials in host configuration or GitHub secrets, never source files.

## Launch checks

Back up the existing site and database. Verify all page routes, mobile navigation, all images and logos, both films, form receipt and reply handling on staging. Activate the new theme only after these checks. Retain the previous theme for rollback. Confirm host caching and large video delivery before switching production.

## Media

Photo sources and licences are recorded in comments in `site.html`. Client-supplied artwork and films remain the client's assets. Public repository visibility does not grant an additional licence to reuse the content.

`asset-manifest.json` records each media file's size and SHA-256 digest.

