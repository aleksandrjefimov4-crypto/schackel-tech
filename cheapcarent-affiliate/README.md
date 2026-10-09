# CheapCarRent affiliate program code

This directory contains only the partner-program source used in the r457 frontend and r218 API releases. It is an integration package for the existing private Nuxt/Laravel application, not a standalone application or deployable release. Build artifacts, dependencies, environment files, credentials, database dumps and supplier integrations are excluded.

## Contents

- `front/app/pages/partners/`: affiliate program landing page and sign-in page.
- `front/app/pages/profile/affiliate-cabinet.vue`: application, links and reporting UI.
- `front/app/plugins/affiliate-attribution.client.js`: stores a referral click ID for the browser tab.
- `front/public/partners/affiliate-logo-realistic-b.png`: program logo.
- `api/app/Http/Controllers/Affiliate*`: partner cabinet, own-inventory catalog and tracked visits.
- `api/app/Models/AffiliatePartner.php`, migrations and Orchid screen: partner records, review and tracking tables.
- `api/docs/affiliate-api-v1.md`: API contract and launch limitations.
- `patches/`: narrow changes to the host application's order endpoint, API routes and Localrent regression test. Apply only after reviewing against the target revision.

## Host application integration

At order creation in `front/app/pages/checkout.vue`, include the stored referral ID in the request body:

```js
affiliate_click_id: (() => {
  try { return window.sessionStorage.getItem('cheapcar_affiliate_click_id') || undefined }
  catch { return undefined }
})(),
```

The production API catalog lists only CheapCarRent's own `products` inventory and returns a field allowlist. Supplier names and Localrent data are not part of the partner catalog. Links, reports and catalog access require approved partner status. Attribution is stored only for valid, unexpired click IDs. Payouts remain disabled until commercial terms and settlement are implemented.

Before using this package in another application revision, run the included focused tests and verify guest, other-partner and rejected-partner access; click deduplication; order attribution; and absence of supplier identifiers in all serialized responses.
