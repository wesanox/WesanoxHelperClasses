# Wesanox Helper Classes

A ProcessWire module that provides several helper classes to simplify common tasks in your projects.

## Features

This module includes the following helpers:

- **HeaderImageHelper** – Generate responsive header images.
- **HeadlineHelper** – Render styled and aligned headlines.
- **LinkHelper** – Render links from ProcessWire pages.
- **MatrixHelper** – Render `RepeaterMatrix` content with additional options.
- **SeparatorHelper** – Create responsive separators for layouts.
- **ModuleInstaller** – Download and install modules programmatically.

## Requirements

- ProcessWire `>= 3.0.210`
- PHP `>= 8.0.0`

## Installation

1. Copy the module into your `/site/modules/` directory.
2. Install it from the ProcessWire backend under **Modules > Refresh > Install**.
3. The helper classes are automatically loaded and ready to use.

## Usage Examples

### Header Image
```php
echo $modules->get('WesanoxHelperClasses')->getHeaderImage($image, $imageTablet, $imageMobile);
```
### Headline
```php
echo $modules->get('WesanoxHelperClasses')->getHeadline('My Headline', 'h2', 'headline-class', 'center');
```
### RepeaterMatrix Rendering
```php
$modules->get('WesanoxHelperClasses')->renderMatrix('myMatrix', $page->my_matrix_field, 'tags', '/files/dir/', '/files/path/');
```
### Link Rendering

```php
echo $modules->get('WesanoxHelperClasses')->renderLink($page);
```
### Separator

```php
echo $modules->get('WesanoxHelperClasses')->getSeparator($matrix, 'separator-class', 'mobile-class', 'tablet-class', 'desktop-class', '|');
```