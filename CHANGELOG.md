# Changelog

## 0.2.6

* Moved module Readme hints from the selection/review tables into a dedicated `Hinweise` wizard step.
* Added module-name tabs for installed wesanox setup modules with their curated Readme hints.

## 0.2.5

* Added curated `readmeHints` metadata for wesanox setup modules.
* Rendered Readme hints in the setup wizard module selection and review tables.

## 0.2.0

* Replaced the separate direct install action with a compact `JETZT INSTALLIEREN` status button.
* Direct module status actions now submit to the installation flow immediately.

## 0.1.9

* Added per-module `Jetzt installieren` action buttons for installable/downloadable modules in the first wizard step.
* Direct module actions now enter the review step with dependencies resolved.

## 0.1.8

* Replaced passive `WireTab` field classes with fully rendered `JqueryWireTabs` markup and initialization.

## 0.1.7

* Changed the first wizard step to show module selection and recipe selection as tabs.

## 0.1.6

* Added direct per-module selection in the setup wizard.
* Added a visible three-step wizard header for selection, review, and installation.
* Moved the setup wizard admin page to the admin root so it appears in the main menu.
* Added automatic main-menu hiding after a successful setup run.
* Added optional external companion handling for non-catalog `installs` entries.

## 0.1.5

* Added `installable-modules.php` as the module-local catalog for wesanox install targets.
* Replaced demo recipes with wesanox recipes based on the GitHub module repositories.
* Added recursive dependency resolution for catalog modules.
* Added external dependency reporting for requirements that cannot be downloaded from the catalog.

## 0.1.4

* Added `ProcessWesanoxSetupWizard` as a backend setup routine under Setup.
* Added predefined setup recipes for site basics, development tools, and media fields.
* Extended `ModuleInstaller` with structured status checks and explicit module installation.
* Loaded ProcessWire's `ProcessModuleInstall` helper directly for download preflight checks.
* Registered the setup wizard as an installed companion module.
