# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
