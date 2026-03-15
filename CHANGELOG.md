# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.8] - 2026-03-16

### Fixed
- Fix MySQL "Out of sort memory" error: table query now selects only lightweight columns, excluding the large `raw_results` JSON column from the sort operation

## [1.0.7] - 2026-03-15

### Fixed
- Fix font override: use `@theme inline` so plugin CSS does not set `--font-sans` on `:root`
- Fix page layout: override `.container` max-width so Filament's page is not squeezed
- Fix responsive grid: add breakpoints and shadow values to `@theme inline`

## [1.0.6] - 2026-03-15

### Fixed
- Add Noto Naskh Arabic font for proper Kurdish (Sorani) and Arabic script rendering

## [1.0.5] - 2026-03-15

### Added
- Central Kurdish (Sorani / ckb) translation

## [1.0.4] - 2026-03-15

### Fixed
- Build and ship compiled CSS so styles are applied correctly

## [1.0.3] - 2026-03-15

### Fixed
- Check all configured auth guards to support any Filament panel guard on report routes

## [1.0.2] - 2026-03-15

### Fixed
- Replace `auth` middleware with guard-agnostic check to avoid missing `login` route error

## [1.0.1] - 2026-03-15

### Fixed
- Suppress "Stylesheet not found" error when the CSS asset has not been built

## [1.0.0] - 2025-03-10

### Added
- Filament page showing Lighthouse audit results with color-coded scores
- Run audits synchronously or via queue from the Filament UI
- Support for all four Lighthouse categories: Performance, Accessibility, Best Practices, SEO
- Configurable form factor (Desktop / Mobile)
- Custom HTTP headers and user agent per audit
- CPU and network throttling options
- `DatabaseResultStore` for persistent history with automatic pruning
- `CacheResultStore` for lightweight, stateless deployments
- `ResultStore` interface for custom store implementations
- Artisan commands: `lighthouse:audit`, `lighthouse:schedule`, `lighthouse:list`
- `lighthouse:list` supports `table`, `json`, and `csv` output formats
- Scheduled audits via config (`scheduling.urls`)
- Email and Slack notifications on audit completion and failure
- Bulk delete and bulk CSV / JSON export of audit results
- Per-record HTML report view and download
- REST API endpoints (opt-in, token-protected)
- `AuditStartingEvent`, `AuditEndedEvent`, `AuditFailedEvent` for custom listeners
- Configurable score and metric thresholds
- Expandable performance metrics cards (FCP, LCP, TBT, TTI, SI, CLS, page size)
- Failed audit listing with show-more / show-less toggle
- Audit history section per URL
- Full i18n support via `resources/lang/en/lighthouse.php`
