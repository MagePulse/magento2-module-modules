# MagePulse Modules Magento 2 Module

[![License][ico-license]][link-license]
[![Total Downloads][ico-downloads]][link-downloads]
[![Latest Stable Version][ico-version]][link-version]

This module adds the full list of installed Magento modules with version, status, and composer metadata — plus all non-Magento composer dependencies — to the MagePulse Collector payload.

[ico-license]: https://poser.pugx.org/magepulse/magento2-module-modules/license
[ico-downloads]: https://poser.pugx.org/magepulse/magento2-module-modules/downloads
[ico-version]: https://poser.pugx.org/magepulse/magento2-module-modules/v/stable

[link-license]: ./LICENSE.md
[link-downloads]: https://packagist.org/packages/magepulse/magento2-module-modules
[link-version]: https://packagist.org/packages/magepulse/magento2-module-modules

---

## Collector Payload

This module contributes two keys to the MagePulse Collector payload.

### `modules`

An array of every Magento module registered in the application, regardless of enabled state. Sourced from each module's own `composer.json` and `module.xml`.

```json
"modules": [
  {
    "name": "Magento_Store",
    "composer_name": "magento/module-store",
    "composer_version": "102.0.0",
    "module_version": "2.0.0",
    "enabled": "Enabled",
    "license": "proprietary",
    "support": "N/A"
  },
  {
    "name": "Vendor_CustomModule",
    "composer_name": "vendor/module-custom",
    "composer_version": "1.3.2",
    "module_version": "1.3.2",
    "enabled": "Disabled",
    "license": "MIT",
    "support": "https://vendor.com/support"
  }
]
```

| Field | Source | Description |
|---|---|---|
| `name` | Magento module registry | Magento module code (e.g. `Vendor_Name`) |
| `composer_name` | Module `composer.json` → `name` | Composer package name |
| `composer_version` | Module `composer.json` → `version` | Version recorded in the module's composer.json |
| `module_version` | Module `module.xml` → `setup_version` | Database schema version |
| `enabled` | Magento module manager | `"Enabled"` or `"Disabled"` |
| `license` | Module `composer.json` → `license` | SPDX license identifier or `"N/A"` |
| `support` | Module `composer.json` → `support.url` | Vendor support URL or `"N/A"` |

---

### `dependencies`

An array of all non-Magento packages from the root `composer.lock`. Magento modules and themes (`magento2-module`, `magento2-theme`) are excluded here as they are already reported with full metadata under `modules`. This data is intended for server-side CVE database lookups.

```json
"dependencies": [
  {
    "name": "guzzlehttp/guzzle",
    "version": "7.4.5",
    "type": "library"
  },
  {
    "name": "symfony/console",
    "version": "v5.4.21",
    "type": "library"
  },
  {
    "name": "league/flysystem",
    "version": "3.12.0",
    "type": "library"
  }
]
```

| Field | Source | Description |
|---|---|---|
| `name` | `composer.lock` → `packages[].name` | Composer package name |
| `version` | `composer.lock` → `packages[].version` | Exact installed version |
| `type` | `composer.lock` → `packages[].type` | Composer package type; defaults to `"library"` if absent |

**Notes:**
- Only `packages` are included; `packages-dev` (dev dependencies) are omitted as they are not present in production installs.
- If `composer.lock` is absent or unreadable, this key will be an empty array and will not affect the rest of the payload.
