# Changelog

## 2.0.7 - 2025-05-20

### Added
- Add LinkedIn source.

## 2.0.6 - 2025-05-01

### Fixed
- Fix an error with fetching post image when provider is not set.
- Fix an error with Twitter/X source.
- Fix status indicator for various UI elements.

## 2.0.5 - 2025-03-04

### Changed
- Update support for Twitter to X.
- Improve accessibility of some items for posts.

## 2.0.4 - 2025-02-05

### Changed
- Update Facebook source to use `business_management` scope.

## 2.0.3 - 2024-09-14

### Removed
- Remove the ability to create feeds to a Facebook Group (no longer possible via Facebook’s API).

## 2.0.2 - 2024-09-07

### Added
- Add Craft Teams support for permissions.

## 2.0.1 - 2024-05-29

### Added
- Add support for `headlessMode` redirect URIs.
- Add `business_management` scope to Instagram.

### Changed
- Update English translations.
- Update `graphApiVersion` for Instagram.

## 2.0.0 - 2024-05-13

### Added
- Add improved session-handling for authorization and callback methods, to improve failed sessions in some cases.
- Improve error feedback for feeds preview.

### Changed
- Now requires PHP `8.2.0+`.
- Now requires Craft `5.0.0+`.

### Fixed
- Fix an error when uninstalling the plugin.
- Fix Facebook photos not returning all photos.
- Update `symfony/property-access` dependency.
- Fix an error when uninstalling.

## 1.0.12 - 2025-05-20

### Added
- Add LinkedIn source.

## 1.0.11 - 2025-05-01

### Fixed
- Fix an error with fetching post image when provider is not set.
- Fix an error with Twitter/X source.

## 1.0.10 - 2025-03-04

### Changed
- Update support for Twitter to X.
- Improve accessibility of some items for posts.

## 1.0.9 - 2024-09-14

### Removed
- Remove the ability to create feeds to a Facebook Group (no longer possible via Facebook’s API).

## 1.0.8 - 2024-05-29

### Changed
- Update English translations.

## 1.0.7 - 2024-04-29

### Added
- Add support for `headlessMode` redirect URIs.

### Changed
- Update English translations.

## 1.0.6 - 2024-04-18

### Added
- Add `business_management` scope to Instagram.

### Changed
- Update `graphApiVersion` for Instagram.

## 1.0.5 - 2024-04-05

### Added
- Add improved session-handling for authorization and callback methods, to improve failed sessions in some cases.
- Improve error feedback for feeds preview.

### Fixed
- Fix an error when uninstalling the plugin.
- Fix Facebook photos not returning all photos.

## 1.0.4 - 2024-02-11

### Fixed
- Fix Facebook source not connecting.

## 1.0.3 - 2023-12-08

### Fixed
- Fix YouTube offline access type.

## 1.0.2 - 2023-09-26

### Added
- Add ability to set `scopes` and `authorizationOptions` for accounts in config files.
- Add current site support to Redirect URI for multi-sites.

## 1.0.1 - 2023-03-05

### Fixed
- Fix incorrect unique index for posts database table.
- Fix an issue when fetching posts when saving, when existing cache data exists.
- Fix an error when creating a source.
- Fix an error when saving a feed with no sources.
- Fix Redirect URI not working correctly for multi-sites.

## 1.0.0 - 2023-02-01

### Added
- Initial release
