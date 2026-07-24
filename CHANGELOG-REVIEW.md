# Changelog Review

## 0.2.6

* Verified that the wizard header now exposes a dedicated `Hinweise` step via `?view=module-hints`.
* Verified that only installed modules from the wesanox setup catalog are shown as Readme hint tabs.
* Verified that selection and review tables no longer include the Readme hint column.

## 0.2.5

* Verified that `readmeHints` are display-only catalog metadata and do not change module installation input.
* Verified that the setup wizard escapes each Readme hint before rendering it in backend tables.
* Remaining risk: modules with empty or very short Readmes can only show limited setup guidance.

## 0.2.0

* Verified that `Download bereit` is no longer rendered for actionable modules.
* Verified that the compact `JETZT INSTALLIEREN` status button submits via POST to direct installation.

## 0.1.9

* Verified that downloadable modules render a direct `Jetzt installieren` submit action.
* Verified that the direct action keeps the CSRF-protected POST flow and lands in the review step.

## 0.1.8

* Verified that the first wizard step now contains the tabs container, tab links, and `WireTabs()` initialization script.
* Verified that both module and recipe checkboxes remain inside the tabbed selection view.

## 0.1.7

* Verified that the first wizard step renders two `WireTab` sections.
* Verified that module and recipe checkboxes are still present in the tabbed view.

## 0.1.6

* Verified direct module selection renders from the local installable module catalog.
* Verified Recipe selection and direct module selection share the same dependency resolution path.
* Verified the wizard admin page is moved from `Setup` to the admin root for main-menu visibility.
* Remaining risk: hiding the main-menu item depends on the setup result being successful; direct URL access remains possible for superusers.

## 0.1.5

* Verified that `wesanox-lottery` resolves its catalog dependencies before the target module.
* Verified that external requirements such as `FormBuilder` and `FieldtypeRepeaterMatrix` are detected separately.
* Verified that the wizard start view renders the new wesanox catalog recipes.
* Remaining risk: GitHub ZIP URLs point to the `main` branch and should be pinned to releases/tags for production-safe installs.

## 0.1.4

* Verified that the setup wizard is restricted to superusers.
* Added CSRF validation for all POST actions.
* Avoided free-form download URLs in the UI; module downloads can only come from predefined recipes.
* Preserved the existing public `downloadInstall()` method as a wrapper around the new installer.
* Verified the wizard module loads and renders its start view in a DDEV superuser context.
* Remaining risk: recipe module download URLs must be curated before using this wizard on projects where modules are not already present in `/site/modules/`.
