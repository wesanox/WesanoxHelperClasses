<?php

namespace ProcessWire\classes;

use ProcessWire\RepeaterMatrixPageArray;
use ProcessWire\WireData;

class MatrixHelper extends WireData
{
    /**
     * Renders a matrix field with its styles, scripts, and corresponding HTML tags.
     *
     * @param string $name
     * @param RepeaterMatrixPageArray $matrix An array representing the matrix items to be rendered.
     * @param string $tags The HTML tag wrapper for the matrix items.
     * @param string $files_dir The directory path where the style and script files are located.
     * @param string $files_path The public path to the style and script files.
     *
     * @return void
     */
    public function renderMatrix( string $name, RepeaterMatrixPageArray $matrix, string $tags, string $files_dir = '', string $files_path = '' ): void
    {
        $module_name = ucfirst($name);

        $default_dir  = $this->config->paths->root . "site/modules/WesanoxMatrix{$module_name}/src/fields/";
        $default_path = $this->config->urls->root  . "site/modules/WesanoxMatrix{$module_name}/src/fields/";

        $files_dir  = $files_dir  !== '' ? $files_dir  : $default_dir;
        $files_path = $files_path !== '' ? $files_path : $default_path;

        foreach ($matrix as $item) {
            $file = $files_dir . $item->type . '/' . $item->type . '.php';

            if (!$this->matrixFileExists($file)) {
                $custom_dir  = $this->config->paths->templates . "fields/matrix_{$name}/";
                $custom_path = $this->config->urls->templates . "fields/matrix_{$name}/";

                if( $this->matrixFileExists($custom_dir . $item->type . '/' . $item->type . '.php')) {
                    $this->renderMatrixItem($item, $tags, $custom_dir, $custom_path);
                } else {
                    echo sprintf(
                        '<%1$s data-aos="fade-up" data-aos-duration="1000">%2$s</%1$s>',
                        $tags,
                        'file not found ' . $custom_dir . $item->type . '/' . $item->type . '.php',
                    );
                }
            } else {
                $this->renderMatrixItem($item, $tags, $files_dir, $files_path);
            }
        }
    }

    /**
     * @param string $file
     * @return bool
     */
    private function matrixFileExists(string $file): bool
    {
        return file_exists($file);
    }

    /**
     * @param $item
     * @param string $tags
     * @param string $files_dir
     * @param string $files_path
     * @return void
     */
    private function renderMatrixItem($item, string $tags, string $files_dir, string $files_path): void
    {
        $this->getMatrixStyles($files_dir, $files_path, $item->type);
        $this->getMatrixScripts($files_dir, $files_path, $item->type);

        $file = $files_dir . $item->type . '/' . $item->type . '.php';

        if (file_exists($file)) {
            echo sprintf(
                '<%1$s class="%2$s" data-aos="fade-up" data-aos-duration="1000">%3$s</%1$s>',
                $tags,
                $item->type,
                $item->render('', $file)
            );
        }
    }

    /**
     * Add matrix styles to the configuration.
     *
     * @param string $files_dir The directory path where the style files are located.
     * @param string $files_path The public path to the style files.
     * @param string $type_name The specific type name used to locate the style file.
     *
     * @return void
     */
    private function getMatrixStyles(string $files_dir, string $files_path, string $type_name): void
    {
        if (file_exists($files_dir . $type_name . '/' . $type_name . '.scss')) {
            $this->config->styles->add($files_path . $type_name . '/' . $type_name . '.scss');
        }
    }

    /**
     * Add matrix scripts to the configuration if the specified script file exists.
     *
     * @param string $files_dir Directory path where the script files are located.
     * @param string $files_path Base web-accessible path to the script files.
     * @param string $type_name Name of the type used to locate the script file.
     *
     * @return void
     */
    private function getMatrixScripts(string $files_dir, string $files_path, string $type_name) : void
    {
        if (file_exists($files_dir . $type_name . '/' . $type_name . '.js')) {
            $this->config->scripts->add($files_path . $type_name . '/' . $type_name . '.js');
        }
    }
}