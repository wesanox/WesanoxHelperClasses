<?php

namespace ProcessWire\classes;

class LinkHelper
{
    /**
     * @param $page
     * @return string
     */
    public function renderLink($page) : string
    {
        if($this->getLink($page) != '') {
            return '<a' . $this->getLinkStyle($page) . ' href="' . $this->getLink($page) . '" aria-label="' .$this->getLinkTitle($page) . '" title="' . $this->getLinkTitle($page) . '"' . $this->getLinkTarget($page) . '>' . $this->getLinkText($page) . '</a>';
        } else {
            return '';
        }
    }

    /**
     * @param $page
     * @return string
     */
    private function getLink($page) : string
    {
        return ($page->link_intern->id !== 0) ? $page->link_intern->url : (($page->link_extern) ? $page->link_extern : '');
    }

    /**
     * @param $page
     * @return string
     */
    private function getLinkText($page) : string
    {
        switch ( true )
        {
            case $page->link_text:
                $link_text = $page->link_text;
                break;
            case $page->link_intern->id !== 0:
                $link_text = $page->link_intern->title;
                break;
            default:
                $link_text = 'mehr erfahren';
                break;
        }

        return $link_text;
    }

    /**
     * @param $page
     * @return string
     */
    private function getLinkTitle($page) : string
    {
        if ( $page->link_text || $page->link_intern ) {
            $link_title = $page->link_aria ?: $page->link_text;
        } else {
            $link_title = $page->link_aria ?: $this->title;
        }

        return $link_title;
    }

    /**
     * @param $page
     * @return string
     */
    private function getLinkTarget($page) : string
    {
        return ($page->link_new_tab) ? ' target="_blank"' : '';
    }

    /**
     * @param $page
     * @return string
     */
    private function getLinkStyle($page) : string
    {
        switch ($page->select_button_style) {
            case "1" :
                $btn_style = ' class="btn btn-primary"';
                break;
            case "2" :
                $btn_style = ' class="btn btn-secondary"';
                break;
            case "3" :
                $btn_style = ' class="btn btn-link"';
                break;
            default:
                $btn_style = '';
        }

        return $btn_style;
    }
}