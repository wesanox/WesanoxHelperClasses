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

1. Copy the module into your `/site/modules/` directory or download it from GitHub with the github URL.
2. Install it from the ProcessWire backend under **Modules > Refresh > Install**.
3. The helper classes are automatically loaded and ready to use.

## Usage Examples

### Header Image

The HeaderImageHelper class generates a responsive <picture> element for header images.
It uses desktop, tablet, and mobile image versions—either provided directly or automatically taken from predefined image crops—and outputs a fully structured ```<picture>``` block with appropriate ```<source>``` tags and a lazy-loaded ```<img>``` fallback.

If no image is available, it returns a warning message prompting the editor to upload a header image in the ProcessWire backend.

```php
echo wire()->modules->WesanoxHelperClasses->getHeaderImage($image, $imageTablet, $imageMobile);
```
### Headline

The HeadlineHelper class generates fully formatted HTML headline elements. It takes a headline text, a tag definition, optional style classes, and an alignment setting, and returns a complete HTML element (e.g., ```<h2 class="title text-center">My Headline</h2>```).

The class ensures that the correct HTML tag is used, combines custom and alignment-related CSS classes, and outputs a clean, ready-to-render headline string.

```php
echo wire()->modules->WesanoxHelperClasses->getHeadline('My Headline', 'h2', 'headline-class', 'center');
```
### RepeaterMatrix Rendering

The renderMatrix() method renders a RepeaterMatrix field along with its associated assets:

For each matrix item, it detects the item’s type and automatically loads the corresponding PHP template—first from the custom template directory, then from the module directory as a fallback.

Each item is wrapped in a configurable HTML tag ($tags) and output with the item’s type as a CSS class and predefined AOS animation attributes.

SCSS and JS files belonging to each matrix type are automatically detected and enqueued once (to prevent duplicates), using either custom overrides or default module files.

This method ensures that every matrix block is rendered with its correct markup, styles, and scripts.

```php
echo wire()->modules->WesanoxHelperClasses->renderMatrix('myMatrix', $page->my_matrix_field, 'tags', '/files/dir/', '/files/path/');
```
### Link Rendering

The LinkHelper class generates fully formatted HTML anchor elements based on the link data stored in a ProcessWire page.

It automatically determines whether to use an internal or external link, chooses the appropriate link text and ARIA/title attributes, applies optional button-style CSS classes, and adds a target="_blank" attribute when required.

The final output is a complete ```<a>``` tag—e.g. ```<a class="btn btn-primary" href="…" title="…" aria-label="…" target="_blank">Link Text</a>``` — or an empty string when no valid link exists.

```php
echo wire()->modules->WesanoxHelperClasses->renderLink($page);
```
### Separator

The SeparatorHelper class generates different types of visual separators between content sections.
Based on configuration values from a ProcessWire page, it outputs one of several separator types:

* a spacing separator with adjustable margins for mobile, tablet, and desktop, optionally containing an ```<hr>``` line
* an image-based separator aligned left, centered, or right (with AOS animation attributes)
* an arrow icon prompting users to scroll to the next section
* The final output is fully assembled HTML—such as a styled ```<div>``` with spacing, an absolutely positioned ```<img>``` separator, or an SVG arrow—depending on the selected separator option.

```php
echo wire()->modules->WesanoxHelperClasses->getSeparator($matrix, 'separator-class', 'mobile-class', 'tablet-class', 'desktop-class', '|');
```