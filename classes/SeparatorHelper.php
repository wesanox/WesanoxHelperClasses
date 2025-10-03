<?php

namespace ProcessWire\classes;

class SeparatorHelper
{
    /**
     * @param $page
     *
     * @return string
     */
    public function getSeparator($page) : string
    {
        $css_class = $page->text_class;
        $image = $page->image;
        $options = $page->select_separator_options;

        switch ($options) {
            case "1":
                return $this->getSeparatorOffset($page, $css_class);
            case "2":
                return '<div class="position-relative separator-image">' . $this->getSeparatorImage($image, " left start-0") . '</div>';
            case "3":
                return '<div class="position-relative separator-image">' . $this->getSeparatorImage($image, " center") . '</div>';
            case "4":
                return '<div class="position-relative separator-image">' . $this->getSeparatorImage($image, " right end-0") . '</div>';
            case "5":
                return $this->getNextSectionArrow();
            default:
                return '';
        }
    }

    private function getSeparatorOffset($page, $css_class): string
    {
        $mobile = $page->text_difference_mobile;
        $tablet = $page->text_difference_tablet;
        $desktop = $page->text_difference_desktop;
        $separator = $page->checkbox_separator;

        $c = ($css_class) ? ' ' . $css_class : '';
        $m = ($mobile != '') ? ' value-mobile="' . $mobile . '"' : ' value-mobile="30"';
        $t = ($tablet != '') ? ' value-tablet="' . $tablet . '"' : ' value-tablet="50"';

        switch (true) {
            case $separator && $desktop != '' :
                return '
                    <div class="separator-settings' . $c . '" style="margin: ' . $desktop . 'px 0;"' . $m . $t . '>
                        <hr>
                    </div>';
            case $separator :
                return '
                    <div class="separator-settings' . $c . '" style="margin: 50px 0"' . $m . $t . '>
                        <hr>
                    </div>';
            case $desktop != '' :
                return '
                    <div class="separator-settings' . $c . '" style="margin: ' . $desktop . 'px 0;"' . $m . $t . '></div>';
            default :
                return '<div class="separator-settings' . $c . '" style="margin:  50px 0; "' . $m . $t . '></div>';
        }
    }

    private function getSeparatorImage($image, $css_class = '')
    {
        return '<img class="position-absolute top-50' . $css_class . '" src="' . $image->url . '" alt="' . $image->description . '">';
    }

    private function getNextSectionArrow(): string
    {
        return '
            <div class="next-section-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        ';
    }
}