<?php

namespace ProcessWire\classes;

use ProcessWire\WireData;

class ModuleInstaller extends WireData
{
    /**
     * @var \ProcessWire\ProcessModuleInstall|null
     */
    protected $processModuleInstall = null;

    /**
     * @param array $moduleDefinitions
     *
     * @return array
     */
    public function installMany(array $moduleDefinitions): array
    {
        $results = array();

        foreach ($moduleDefinitions as $moduleDefinition) {
            $moduleName = (string) ($moduleDefinition['name'] ?? '');
            $moduleUrl = (string) ($moduleDefinition['downloadUrl'] ?? '');
            $results[] = $this->install($moduleName, $moduleUrl);
        }

        return $results;
    }

    /**
     * @param string $moduleName
     * @param string $moduleUrl
     *
     * @return array
     */
    public function install(string $moduleName, string $moduleUrl = ''): array
    {
        $moduleName = trim($moduleName);
        $moduleUrl = trim($moduleUrl);

        if (!$this->isValidModuleName($moduleName)) {
            return $this->result($moduleName, 'error', false, 'Invalid module name.');
        }

        if ($this->wire()->modules->isInstalled($moduleName)) {
            return $this->result($moduleName, 'already-installed', true, "Module $moduleName is already installed.");
        }

        $this->wire()->modules->refresh();

        if (!$this->wire()->modules->isInstallable($moduleName) && $moduleUrl !== '') {
            $downloadResult = $this->downloadModule($moduleName, $moduleUrl);
            if (!$downloadResult['success']) {
                return $downloadResult;
            }

            $this->wire()->modules->refresh();
        }

        if (!$this->wire()->modules->isInstallable($moduleName)) {
            return $this->result($moduleName, 'error', false, "Module $moduleName is not available for installation.");
        }

        try {
            $module = $this->wire()->modules->install($moduleName, array(
                'dependencies' => true,
                'resetCache' => true,
                'force' => false,
            ));
        } catch (\Exception $exception) {
            return $this->result($moduleName, 'error', false, $exception->getMessage());
        }

        if (!$module || !$this->wire()->modules->isInstalled($moduleName)) {
            return $this->result($moduleName, 'error', false, "Module $moduleName could not be installed.");
        }

        return $this->result($moduleName, 'installed', true, "Module $moduleName was installed.");
    }

    /**
     * @param string $moduleName
     * @param string $moduleUrl
     *
     * @return array
     */
    public function getModuleStatus(string $moduleName, string $moduleUrl = ''): array
    {
        $moduleName = trim($moduleName);
        $moduleUrl = trim($moduleUrl);

        if (!$this->isValidModuleName($moduleName)) {
            return $this->result($moduleName, 'invalid', false, 'Invalid module name.');
        }

        if ($this->wire()->modules->isInstalled($moduleName)) {
            return $this->result($moduleName, 'installed', true, 'Installed.');
        }

        $this->wire()->modules->refresh();

        if ($this->wire()->modules->isInstallable($moduleName)) {
            return $this->result($moduleName, 'installable', true, 'Module files are available.');
        }

        if ($moduleUrl !== '') {
            $canDownload = $this->getProcessModuleInstall()->canInstallFromDownloadUrl(false);
            return $this->result(
                $moduleName,
                $canDownload ? 'downloadable' : 'download-disabled',
                $canDownload,
                $canDownload ? 'Module can be downloaded.' : 'Module download is disabled by configuration.'
            );
        }

        return $this->result($moduleName, 'missing', false, 'Module files are missing and no download URL is configured.');
    }

    /**
     * @return array
     */
    public function getPreflightChecks(): array
    {
        $config = $this->wire()->config;
        $processModuleInstall = $this->getProcessModuleInstall();

        return array(
            array(
                'label' => 'Superuser',
                'passed' => $this->wire()->user->isSuperuser(),
                'message' => 'The setup wizard may only be used by superusers.',
            ),
            array(
                'label' => 'Site modules directory writable',
                'passed' => is_writable($config->paths->siteModules),
                'message' => $config->paths->siteModules,
            ),
            array(
                'label' => 'Cache directory writable',
                'passed' => is_writable($config->paths->cache),
                'message' => $config->paths->cache,
            ),
            array(
                'label' => 'ZipArchive available',
                'passed' => class_exists('ZipArchive'),
                'message' => 'ZipArchive is required for ZIP downloads.',
            ),
            array(
                'label' => 'Module downloads enabled',
                'passed' => $processModuleInstall->canInstallFromDownloadUrl(false),
                'message' => 'Controlled by $config->moduleInstall("download", ...).',
            ),
        );
    }

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
        $result = $this->install($module_name, $module_url);

        return $result['message'];
    }

    /**
     * @param string $moduleName
     * @param string $moduleUrl
     *
     * @return array
     */
    protected function downloadModule(string $moduleName, string $moduleUrl): array
    {
        if (!$this->isValidDownloadUrl($moduleUrl)) {
            return $this->result($moduleName, 'error', false, 'Invalid module download URL.');
        }

        $targetDir = $this->wire()->config->paths->siteModules . $moduleName . '/';

        if (is_dir($targetDir)) {
            return $this->result($moduleName, 'error', false, "Module directory already exists: $targetDir");
        }

        $processModuleInstall = $this->getProcessModuleInstall();

        if (!$processModuleInstall->canInstallFromDownloadUrl(false)) {
            return $this->result($moduleName, 'error', false, 'Module download is disabled by configuration.');
        }

        $destinationDir = $processModuleInstall->downloadModule($moduleUrl, $targetDir);

        if (!$destinationDir) {
            return $this->result($moduleName, 'error', false, "Module $moduleName could not be downloaded.");
        }

        return $this->result($moduleName, 'downloaded', true, "Module $moduleName was downloaded.");
    }

    /**
     * @return \ProcessWire\ProcessModuleInstall
     */
    protected function getProcessModuleInstall()
    {
        if ($this->processModuleInstall) {
            return $this->processModuleInstall;
        }

        if (!class_exists('ProcessWire\\ProcessModuleInstall', false)) {
            require_once $this->wire()->config->paths->root . 'wire/modules/Process/ProcessModule/ProcessModuleInstall.php';
        }

        $this->processModuleInstall = $this->wire(new \ProcessWire\ProcessModuleInstall());

        return $this->processModuleInstall;
    }

    /**
     * @param string $moduleName
     *
     * @return bool
     */
    protected function isValidModuleName(string $moduleName): bool
    {
        return (bool) preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $moduleName);
    }

    /**
     * @param string $moduleUrl
     *
     * @return bool
     */
    protected function isValidDownloadUrl(string $moduleUrl): bool
    {
        return (bool) preg_match('{^https://[^\\s]+\\.zip(?:\\?.*)?$}i', $moduleUrl);
    }

    /**
     * @param string $moduleName
     * @param string $status
     * @param bool $success
     * @param string $message
     *
     * @return array
     */
    protected function result(string $moduleName, string $status, bool $success, string $message): array
    {
        return array(
            'module' => $moduleName,
            'status' => $status,
            'success' => $success,
            'message' => $message,
        );
    }
}
