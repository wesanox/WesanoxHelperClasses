<?php

namespace ProcessWire\classes;

use ProcessWire\WireData;

class ModuleInstaller extends WireData
{
    /**
     * @param string $module_name
     * @param string $module_url
     *
     * @return string
     *
     * @throws \ProcessWire\WireException
     * @throws \ProcessWire\WirePermissionException
     */
    public function downloadInstall(string $module_name, string $module_url): string
    {
        $success = false;
        $targetDir = $this->wire()->config->paths->siteModules . $module_name . '/';
        $moduleInstaller = $this->wire()->modules->get('ProcessModuleInstall');
        $destinationDir = $moduleInstaller->downloadModule($module_url, $targetDir);

        if($destinationDir) {
            $this->modules->refresh();
        }

        if ($this->modules->isInstallable($module_name)) {
            $this->modules->get($module_name);
        }

        if ($this->modules->isInstalled($module_name)) {
            $success = true;
        }

        return $success ?: "Module $module_name could not be installed.";
    }
}