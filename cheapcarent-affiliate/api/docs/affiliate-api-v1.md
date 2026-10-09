# CheapCarRent affiliate API v1

This API is for customer referrals by approved publishers. It is separate from rental-company supplier accounts and from the inbound Localrent feed.

## Authentication and approval

`GET /api/v1/affiliate/cabinet` and `POST /api/v1/affiliate/apply` use the existing customer `auth:api` session. Only an approved affiliate can call `GET /api/v1/affiliate/tools` or `GET /api/v1/affiliate/report`. An unapproved or missing application receives 404 from these endpoints. The application review remains in Orchid.

## Own vehicle catalog

`GET /api/v1/affiliate/catalog?city=Tallinn&duration_days=3&page=1` is available only to approved affiliates. It reads listed car records from CheapCarRent's `products` table and returns at most 24 records per page. It does **not** call Localrent, Avis, or any other connected supplier feed. The response is an explicit whitelist of vehicle ID, brand, model, year, customer daily price, sale daily price, currency, CheapCarRent-hosted photos and `availability_unchecked`. It contains no rental-company or supplier identity. Availability must be checked by the normal booking flow for chosen dates; a catalog record is not a bookable quote.

## Links and clicks

`GET /api/v1/affiliate/tools?to=/en/cars&sub_id=article_1` returns `{ "url": "..." }`. `to` must be an internal path beginning with one `/`; full external URLs, protocol-relative URLs, backslashes and fragments are rejected. `sub_id` is an optional publisher marker with letters, numbers, underscore or hyphen. The public link redirects through `GET /api/v1/affiliate/visit/{code}` and records one click before redirecting to `https://cheapcarent.com` with a UUID `affiliate_click_id` query parameter. Only approved partners' links work.

The Nuxt frontend preserves a valid click ID for the current browser tab and sends it with the core `/api/v1/order/create` request. The API accepts an attribution only when the click belongs to a currently approved partner and is at most 30 days old. Invalid or expired IDs do not change the booking outcome. The booking endpoint's idempotency handling prevents a repeated request from creating another attribution.

The visit endpoint is rate limited and deduplicates the same partner, destination, IP address and user agent within a 30-minute window. Only a keyed digest is stored, not the raw IP or user agent. This reduces accidental/repeated clicks but does not establish human traffic or prevent distributed bot traffic; clicks are informational and cannot directly accrue commission.

## Reports

`GET /api/v1/affiliate/report` returns 30-day aggregate clicks, total attributed orders, paid orders and completed orders. The response contains no customer information, rental-company name, supplier ID, supplier link, vehicle detail, or raw order row. Earnings and payouts remain unavailable until commercial terms and settlement rules are approved. No affiliate commission is calculated or accrued by this version.

## Coverage and release boundary

Order attribution currently covers the core `/api/v1/order/create` checkout only. Separate supplier booking endpoints require their own server-side attribution hooks before their orders can appear in reports. The dashboard's reporting/tools panels also need a frontend connection to these endpoints. Run the migration, feature tests, and browser checkout verification in an isolated release candidate before production deployment. Do not advertise full affiliate tracking or payouts before that work is done.
