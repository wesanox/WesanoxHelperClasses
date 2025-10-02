<?php

namespace ProcessWire\classes;

use ProcessWire\RepeaterMatrixPageArray;
use ProcessWire\WireData;

class MatrixHelper extends WireData
{
    private array $custom_styles = [];
    private array $custom_scripts = [];

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

        $custom_dir  = $this->config->paths->templates . "fields/matrix_{$name}/";
        $custom_path = $this->config->urls->templates . "fields/matrix_{$name}/";

        foreach ($matrix as $item) {
            $type = (string) $item->type;

            $this->enqueueAssetsForType($type, $custom_dir, $custom_path);
            $this->enqueueAssetsForType($type, $default_dir, $default_path);

            $matrix_file = $custom_dir . $type . '/' . $type . '.php';

            if (!file_exists($matrix_file)) {
                $fallback = $default_dir . $type . '/' . $type . '.php';

                if (file_exists($fallback)) {
                    $matrix_file = $fallback;
                } else {
                    echo sprintf(
                        '<%1$s data-aos="fade-up" data-aos-duration="1000">%2$s</%1$s>',
                        $tags,
                        'file not found ' . $custom_dir . $type . '/' . $type . '.php'
                    );
                    continue;
                }
            }

            echo sprintf(
                '<%1$s class="%2$s" data-aos="fade-up" data-aos-duration="1000">%3$s</%1$s>',
                $tags,
                $type,
                $item->render('', $matrix_file)
            );
        }
    }

    /**
     * Enqueues styles and scripts for a specific type by checking the file existence and avoiding duplicate inclusion.
     *
     * @param string $type_name The name of the type for which assets are to be enqueued.
     * @param string $base_dir The base directory path where the assets are located.
     * @param string $base_path The public URL path to the assets.
     *
     * @return void
     */
    private function enqueueAssetsForType(string $type_name, string $base_dir, string $base_path): void
    {
        $dir  = rtrim($base_dir,  '/'). '/'. $type_name . '/';
        $path = rtrim($base_path, '/'). '/'. $type_name . '/';

        $scss_file  = $dir  . $type_name . '.scss';
        $scss_url = $path . $type_name . '.scss';

        $js_file  = $dir  . $type_name . '.js';
        $js_url = $path . $type_name . '.js';

        if (file_exists($scss_file) && empty($this->custom_styles[$type_name])) {
            $this->config->styles->add($scss_url);
            $this->custom_styles[$type_name] = true;
        }

        if (file_exists($js_file) && empty($this->custom_scripts[$type_name])) {
            $this->config->scripts->add($js_url);
            $this->custom_scripts[$type_name] = true;
        }
    }
}