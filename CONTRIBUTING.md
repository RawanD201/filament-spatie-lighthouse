# Contributing to Filament Spatie Lighthouse

Thank you for considering contributing! Every improvement — whether a bug fix, new feature, documentation update, or test — is welcome and appreciated.

---

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Running Tests](#running-tests)
- [Submitting Changes](#submitting-changes)
- [Coding Standards](#coding-standards)
- [Reporting Bugs](#reporting-bugs)
- [Suggesting Features](#suggesting-features)

---

## Code of Conduct

Be respectful. Constructive criticism is welcome; personal attacks are not. This project follows the [Contributor Covenant](https://www.contributor-covenant.org/version/2/1/code_of_conduct/).

---

## Getting Started

1. Fork the repository on GitHub: `https://github.com/Rawand201/filament-spatie-lighthouse`
2. Clone your fork locally:

```bash
git clone https://github.com/YOUR_USERNAME/filament-spatie-lighthouse.git
cd filament-spatie-lighthouse
```

3. Create a feature branch:

```bash
git checkout -b feat/your-feature-name
# or
git checkout -b fix/your-bug-description
```

---

## Development Setup

**Requirements:**

- PHP 8.2+
- Composer
- Node.js + Chrome/Chromium (for running actual Lighthouse audits in tests)

**Install dependencies:**

```bash
composer install
```

The package uses [Orchestra Testbench](https://github.com/orchestral/testbench) to run tests inside a minimal Laravel application — no separate Laravel project is needed.

---

## Running Tests

Tests are written with [Pest PHP](https://pestphp.com).

```bash
# Run all tests
./vendor/bin/pest

# Run with coverage
./vendor/bin/pest --coverage

# Run a specific test file
./vendor/bin/pest tests/Feature/ServiceProviderTest.php

# Run tests matching a description
./vendor/bin/pest --filter "binds ResultStore"
```

All tests must pass before submitting a pull request. If you add a new feature, please add tests for it.

---

## Submitting Changes

1. Ensure all tests pass: `./vendor/bin/pest`
2. Follow the [coding standards](#coding-standards) below
3. Write a clear commit message describing **what** and **why**:
   ```
   feat: add filesystem storage driver for raw audit results
   fix: resolve raw_results null when driver is filesystem
   docs: document raw_results_driver config option
   ```
4. Push your branch and open a Pull Request against `main`
5. Fill in the PR template — describe the change, why it is needed, and how to test it

### Pull Request checklist

- [ ] Tests added or updated
- [ ] `README.md` updated if behaviour changed
- [ ] `CHANGELOG.md` entry added under `[Unreleased]`
- [ ] No unrelated changes in the PR

---

## Coding Standards

This package follows [PSR-12](https://www.php-fig.org/psr/psr-12/) and Laravel conventions.

- Use `declare(strict_types=1)` where appropriate
- Prefer named arguments for clarity on multi-parameter calls
- Keep methods small and focused
- Public API should have docblocks with `@param` / `@return` types where PHPDoc adds value beyond type hints
- Avoid adding dependencies unless strictly necessary

---

## Reporting Bugs

Open a GitHub Issue at `https://github.com/Rawand201/filament-spatie-lighthouse/issues` and include:

- Your environment: PHP version, Laravel version, Filament version, OS
- Steps to reproduce
- Expected behaviour vs actual behaviour
- Any relevant stack trace or log output

---

## Suggesting Features

Open a GitHub Issue with the `enhancement` label. Describe:

- The problem you are trying to solve
- Your proposed solution
- Any alternative approaches you considered

Large changes should be discussed in an issue before a PR is opened, to avoid wasted effort if the direction does not align with the project's goals.

---

## Credits

See [CREDITS.md](CREDITS.md).
