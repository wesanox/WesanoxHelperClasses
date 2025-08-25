<?php

namespace ProcessWire\classes;

use ProcessWire\CroppableImage3;

class HeaderImageHelper
{
    protected string $html;

    /**
     * @param $image
     * @param $image_tablet
     * @param $image_mobile
     *
     * @return string
     */
    public function getHeaderImage($image, $image_tablet = null, $image_mobile = null): string
    {
        if( $image ) {
            $url_tablet = ($image_tablet) ? $image_tablet->url : $image->getCrop("tablet")->url;
            $url_mobile = ($image_mobile) ? $image_mobile->url : $image->getCrop("mobile")->url;

            $this->html = '
                <picture>
                    <source media="(min-width: 1024px)" srcset="' . $image->getCrop("desktop")->url . '">
                    <source media="(min-width: 768px)" srcset="' . $url_tablet . '">
                    <source media="(min-width: 768px)" srcset="' . $url_mobile . '">
                  
                    <img class="img-fluid" src="' . $image->getCrop("desktop")->url . '" alt="' . $image->description . '">
                </picture>';
        } else {
            $this->html = '
                <div class="container py-5">
                    <div class="alert alert-warning" role="alert">
                        Bitte ein Foto im Backend hochladen! <a href="' . $page->editUrl() . '">Direkt zum Backend</a>
                    </div>
                </div>
                ';
        }

        return $this->html;
    }
}