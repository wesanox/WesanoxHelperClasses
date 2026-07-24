<?php namespace ProcessWire;

use ProcessWire\classes\ModuleInstaller;
use ProcessWire\classes\SetupRecipeRepository;

class ProcessWesanoxSetupWizard extends Process
{
    protected ModuleInstaller $moduleInstaller;
    protected SetupRecipeRepository $recipeRepository;

    public static function getModuleInfo()
    {
        return array(
            'title' => 'Wesanox Setup Wizard',
            'summary' => 'Guided backend setup for predefined ProcessWire module recipes.',
            'version' => '0.2.6',
            'author' => 'Andre Wester',
            'icon' => 'magic',
            'requires' => 'WesanoxHelperClasses',
            'page' => array(
                'name' => 'wesanox-setup',
                'title' => 'Wesanox Setup',
            ),
        );
    }

    public function init()
    {
        if (!$this->wire()->user->isSuperuser()) {
            throw new WirePermissionException('Superuser is required.');
        }

        $this->wire()->classLoader->addNamespace('ProcessWire\\classes', __DIR__ . '/classes');
        $this->moduleInstaller = $this->wire(new ModuleInstaller());
        $this->recipeRepository = $this->wire(new SetupRecipeRepository());
        $this->syncAdminPageVisibility();

        parent::init();
    }

    /**
     * @param string $fromVersion
     * @param string $toVersion
     *
     * @return void
     */
    public function ___upgrade($fromVersion, $toVersion): void
    {
        $this->syncAdminPageVisibility();
    }

    /**
     * @return string
     */
    public function ___execute()
    {
        $this->headline($this->_('Wesanox Setup'));

        if ($this->wire()->input->requestMethod('post')) {
            $this->wire()->session->CSRF()->validate();
        }

        if ($this->wire()->input->post('submit_install') || $this->wire()->input->post('submit_install_module')) {
            return $this->processInstall();
        }

        if ($this->wire()->input->post('submit_review') || $this->wire()->input->post('submit_review_module')) {
            return $this->renderReview();
        }

        if ((string) $this->wire()->input->get('view') === 'module-hints') {
            return $this->renderInstalledModuleHints();
        }

        return $this->renderSelection();
    }

    /**
     * @return string
     */
    protected function renderSelection(): string
    {
        $recipes = $this->recipeRepository->getRecipes();
        $installableModules = $this->recipeRepository->getInstallableModules();

        /** @var InputfieldForm $form */
        $form = $this->wire()->modules->get('InputfieldForm');
        $form->attr('method', 'post');
        $form->description = $this->_('Waehle einzelne Module oder optionale Schnellwahl-Recipes aus.');

        /** @var InputfieldMarkup $markup */
        $markup = $this->wire()->modules->get('InputfieldMarkup');
        $markup->label = $this->_('Preflight');
        $markup->icon = 'check-square-o';
        $markup->value = $this->renderPreflightChecks();
        $form->add($markup);

        /** @var InputfieldMarkup $markup */
        $markup = $this->wire()->modules->get('InputfieldMarkup');
        $markup->label = $this->_('Auswahl');
        $markup->icon = 'cubes';
        $markup->value = $this->renderSelectionTabs($installableModules, $recipes);
        $form->add($markup);

        /** @var InputfieldSubmit $submit */
        $submit = $this->wire()->modules->get('InputfieldSubmit');
        $submit->attr('name', 'submit_review');
        $submit->icon = 'search';
        $submit->value = $this->_('Auswahl pruefen');
        $form->add($submit);

        return $this->renderStepHeader('select') . $form->render();
    }

    /**
     * @return string
     */
    protected function renderReview(): string
    {
        $recipeIds = $this->getSelectedRecipeIds();
        $moduleNames = $this->getSelectedModuleNames();

        if (!$recipeIds && !$moduleNames) {
            $this->warning($this->_('Bitte waehle mindestens ein Modul oder Recipe aus.'));
            return $this->renderSelection();
        }

        $moduleDefinitions = $this->recipeRepository->getModulesForSelection($recipeIds, $moduleNames);
        $moduleStatuses = $this->getModuleStatuses($moduleDefinitions);
        $externalRequirements = $this->recipeRepository->getExternalRequirementsForModules($moduleDefinitions);
        $externalRequirementStatuses = $this->getExternalRequirementStatuses($externalRequirements);
        $canInstall = $this->canInstallSelection($moduleStatuses, $externalRequirementStatuses);

        /** @var InputfieldForm $form */
        $form = $this->wire()->modules->get('InputfieldForm');
        $form->attr('method', 'post');
        $form->description = $this->_('Pruefe die geplanten Installationen vor der Ausfuehrung.');

        /** @var InputfieldMarkup $markup */
        $markup = $this->wire()->modules->get('InputfieldMarkup');
        $markup->label = $this->_('Geplante Module');
        $markup->icon = 'cubes';
        $markup->value = $this->renderSelectedItems($recipeIds, $moduleNames) .
            $this->renderModuleStatusTable($moduleDefinitions, $moduleStatuses) .
            $this->renderExternalRequirementStatusTable($externalRequirementStatuses) .
            $this->renderSelectionHiddenInputs($recipeIds, $moduleNames);
        $form->add($markup);

        /** @var InputfieldButton $back */
        $back = $this->wire()->modules->get('InputfieldButton');
        $back->href = './';
        $back->icon = 'arrow-left';
        $back->value = $this->_('Zurueck');
        $back->setSecondary();
        $form->add($back);

        if ($canInstall) {
            /** @var InputfieldSubmit $submit */
            $submit = $this->wire()->modules->get('InputfieldSubmit');
            $submit->attr('name', 'submit_install');
            $submit->icon = 'download';
            $submit->value = $this->_('Setup ausfuehren');
            $form->add($submit);
        } else {
            $this->error($this->_('Die Auswahl enthaelt Module, die aktuell nicht installiert werden koennen.'));
        }

        return $this->renderStepHeader('review') . $form->render();
    }

    /**
     * @return string
     */
    protected function processInstall(): string
    {
        $recipeIds = $this->getSelectedRecipeIds();
        $moduleNames = $this->getSelectedModuleNames();

        if (!$recipeIds && !$moduleNames) {
            $this->warning($this->_('Bitte waehle mindestens ein Modul oder Recipe aus.'));
            return $this->renderSelection();
        }

        $moduleDefinitions = $this->recipeRepository->getModulesForSelection($recipeIds, $moduleNames);
        $moduleStatuses = $this->getModuleStatuses($moduleDefinitions);
        $externalRequirements = $this->recipeRepository->getExternalRequirementsForModules($moduleDefinitions);
        $externalRequirementStatuses = $this->getExternalRequirementStatuses($externalRequirements);

        if (!$this->canInstallSelection($moduleStatuses, $externalRequirementStatuses)) {
            $this->error($this->_('Die Auswahl kann nicht installiert werden. Bitte pruefe die Statusmeldungen.'));
            return $this->renderReview();
        }

        $results = $this->moduleInstaller->installMany($moduleDefinitions);
        $this->markCompletedIfSuccessful($results);

        /** @var InputfieldForm $form */
        $form = $this->wire()->modules->get('InputfieldForm');

        /** @var InputfieldMarkup $markup */
        $markup = $this->wire()->modules->get('InputfieldMarkup');
        $markup->label = $this->_('Ergebnis');
        $markup->icon = 'check';
        $markup->value = $this->renderResultTable($results);
        $form->add($markup);

        /** @var InputfieldButton $back */
        $back = $this->wire()->modules->get('InputfieldButton');
        $back->href = './';
        $back->icon = 'arrow-left';
        $back->value = $this->_('Zur Auswahl');
        $back->setSecondary();
        $form->add($back);

        return $this->renderStepHeader('install') . $form->render();
    }

    /**
     * @return array
     */
    protected function getSelectedRecipeIds(): array
    {
        $recipeIds = $this->wire()->input->post('recipes');

        if (!is_array($recipeIds)) {
            $recipeIds = $recipeIds ? array($recipeIds) : array();
        }

        return $this->recipeRepository->filterRecipeIds($recipeIds);
    }

    /**
     * @return array
     */
    protected function getSelectedModuleNames(): array
    {
        $moduleNames = $this->wire()->input->post('modules');
        $directModuleName = $this->wire()->input->post('submit_review_module');
        $directInstallModuleName = $this->wire()->input->post('submit_install_module');

        if (!is_array($moduleNames)) {
            $moduleNames = $moduleNames ? array($moduleNames) : array();
        }

        if ($directModuleName) {
            $moduleNames[] = (string) $directModuleName;
        }

        if ($directInstallModuleName) {
            $moduleNames[] = (string) $directInstallModuleName;
        }

        return $this->recipeRepository->filterModuleNames($moduleNames);
    }

    /**
     * @param array $moduleDefinitions
     *
     * @return array
     */
    protected function getModuleStatuses(array $moduleDefinitions): array
    {
        $statuses = array();

        foreach ($moduleDefinitions as $moduleDefinition) {
            $moduleName = (string) ($moduleDefinition['name'] ?? '');
            $moduleUrl = (string) ($moduleDefinition['downloadUrl'] ?? '');
            $statuses[$moduleName] = $this->moduleInstaller->getModuleStatus($moduleName, $moduleUrl);
        }

        return $statuses;
    }

    /**
     * @param array $externalRequirements
     *
     * @return array
     */
    protected function getExternalRequirementStatuses(array $externalRequirements): array
    {
        $statuses = array();

        foreach ($externalRequirements as $externalRequirement) {
            $requirement = $externalRequirement['requirement'];
            $moduleName = $externalRequirement['name'];

            if ($this->wire()->modules->isInstalled($requirement)) {
                $status = 'installed';
                $success = true;
                $message = $this->_('Installed.');
            } else if ($this->wire()->modules->isInstallable($moduleName)) {
                $status = 'installable';
                $success = true;
                $message = $this->_('Module files are available.');
            } else if ($externalRequirement['type'] === 'installs') {
                $status = 'optional-missing';
                $success = true;
                $message = $this->_('Optional companion module is not available in the catalog.');
            } else {
                $status = 'missing';
                $success = false;
                $message = $this->_('Required module is missing.');
            }

            $statuses[$requirement] = array(
                'name' => $moduleName,
                'requirement' => $requirement,
                'source' => $externalRequirement['source'],
                'type' => $externalRequirement['type'],
                'status' => $status,
                'success' => $success,
                'message' => $message,
            );
        }

        return $statuses;
    }

    /**
     * @param array $moduleStatuses
     * @param array $externalRequirementStatuses
     *
     * @return bool
     */
    protected function canInstallSelection(array $moduleStatuses, array $externalRequirementStatuses = array()): bool
    {
        foreach ($moduleStatuses as $moduleStatus) {
            if (!$moduleStatus['success'] && $moduleStatus['status'] !== 'installed') {
                return false;
            }
        }

        foreach ($externalRequirementStatuses as $externalRequirementStatus) {
            if (!$externalRequirementStatus['success']) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $results
     *
     * @return void
     */
    protected function markCompletedIfSuccessful(array $results): void
    {
        foreach ($results as $result) {
            if (!$result['success']) {
                return;
            }
        }

        $this->wire()->modules->saveConfig($this, 'setupWizardCompleted', 1);
        $this->syncAdminPageVisibility();
    }

    /**
     * @return bool
     */
    protected function isSetupWizardCompleted(): bool
    {
        return (bool) $this->wire()->modules->getConfig($this, 'setupWizardCompleted');
    }

    /**
     * @return void
     */
    protected function syncAdminPageVisibility(): void
    {
        $page = $this->getSetupWizardAdminPage();

        if (!$page->id) {
            return;
        }

        $adminRoot = $this->wire()->pages->get($this->wire()->config->adminRootPageID);
        $changed = false;

        if ($page->parent_id !== $adminRoot->id) {
            $page->parent = $adminRoot;
            $changed = true;
        }

        if ($page->title !== 'Wesanox Setup') {
            $page->title = 'Wesanox Setup';
            $changed = true;
        }

        if ($this->isSetupWizardCompleted()) {
            if (!$page->hasStatus(Page::statusHidden)) {
                $page->addStatus(Page::statusHidden);
                $changed = true;
            }
        } else if ($page->hasStatus(Page::statusHidden)) {
            $page->removeStatus(Page::statusHidden);
            $changed = true;
        }

        if ($changed) {
            $this->wire()->pages->save($page);
        }
    }

    /**
     * @return Page
     */
    protected function getSetupWizardAdminPage(): Page
    {
        return $this->wire()->pages->get('template=admin, include=all, process=ProcessWesanoxSetupWizard');
    }

    /**
     * @return string
     */
    protected function renderPreflightChecks(): string
    {
        /** @var MarkupAdminDataTable $table */
        $table = $this->wire()->modules->get('MarkupAdminDataTable');
        $table->setEncodeEntities(false);
        $table->headerRow(array(
            $this->_('Check'),
            $this->_('Status'),
            $this->_('Hinweis'),
        ));

        foreach ($this->moduleInstaller->getPreflightChecks() as $check) {
            $table->row(array(
                $this->entities($check['label']),
                $this->statusLabel($check['passed'] ? 'ok' : 'error'),
                $this->entities($check['message']),
            ));
        }

        return $table->render();
    }

    /**
     * @param array $installableModules
     *
     * @return string
     */
    protected function renderModuleSelectionTable(array $installableModules): string
    {
        /** @var MarkupAdminDataTable $table */
        $table = $this->wire()->modules->get('MarkupAdminDataTable');
        $table->setEncodeEntities(false);
        $table->setColNotSortable(0);
        $table->headerRow(array(
            '',
            $this->_('Modul'),
            $this->_('Status'),
            $this->_('Beschreibung'),
            $this->_('Repository'),
        ));

        foreach ($installableModules as $moduleDefinition) {
            $moduleName = $moduleDefinition['name'];
            $status = $this->moduleInstaller->getModuleStatus($moduleName, $moduleDefinition['downloadUrl'] ?? '');
            $checkbox = '';

            if (!$status['success'] || $status['status'] !== 'installed') {
                $checkbox = "<input type='checkbox' class='uk-checkbox' name='modules[]' value='" . $this->entities($moduleName) . "'>";
            }

            $table->row(array(
                $checkbox,
                '<strong>' . $this->entities($moduleDefinition['title'] ?? $moduleName) . '</strong><br><span class="detail">' . $this->entities($moduleName) . '</span>',
                $this->renderModuleSelectionStatus($moduleName, $status),
                $this->entities($moduleDefinition['summary'] ?? ''),
                $this->entities($moduleDefinition['repository'] ?? ''),
            ));
        }

        return $table->render();
    }

    /**
     * @param string $moduleName
     * @param array $status
     *
     * @return string
     */
    protected function renderModuleSelectionStatus(string $moduleName, array $status): string
    {
        if ($status['success'] && !in_array($status['status'], array('installed', 'already-installed'), true)) {
            return '<button type="submit" class="uk-label" style="border:0;cursor:pointer;" name="submit_install_module" value="' .
                $this->entities($moduleName) .
                '"><i class="fa fa-fw fa-download"></i> ' .
                $this->_('JETZT INSTALLIEREN') .
                '</button>';
        }

        return $this->statusLabel($status['status']);
    }

    /**
     * @param string $activeStep
     *
     * @return string
     */
    protected function renderStepHeader(string $activeStep): string
    {
        $steps = array(
            'select' => $this->_('1. Auswahl'),
            'review' => $this->_('2. Pruefung'),
            'install' => $this->_('3. Installation'),
            'hints' => $this->_('4. Modul-Hinweise'),
        );
        $out = '<ul class="uk-subnav uk-subnav-pill">';

        foreach ($steps as $step => $label) {
            $class = $step === $activeStep ? ' class="uk-active"' : '';
            $href = $step === 'hints' ? './?view=module-hints' : '#';

            if ($step === 'select') {
                $href = './';
            }

            $out .= '<li' . $class . '><a href="' . $this->entities($href) . '">' . $this->entities($label) . '</a></li>';
        }

        return $out . '</ul>';
    }

    /**
     * @return string
     */
    protected function renderInstalledModuleHints(): string
    {
        /** @var InputfieldForm $form */
        $form = $this->wire()->modules->get('InputfieldForm');

        /** @var InputfieldMarkup $markup */
        $markup = $this->wire()->modules->get('InputfieldMarkup');
        $markup->label = $this->_('Hinweise zu installierten Modulen');
        $markup->icon = 'info-circle';
        $markup->value = $this->renderInstalledModuleHintTabs();
        $form->add($markup);

        /** @var InputfieldButton $back */
        $back = $this->wire()->modules->get('InputfieldButton');
        $back->href = './';
        $back->icon = 'arrow-left';
        $back->value = $this->_('Zur Auswahl');
        $back->setSecondary();
        $form->add($back);

        return $this->renderStepHeader('hints') . $form->render();
    }

    /**
     * @return string
     */
    protected function renderInstalledModuleHintTabs(): string
    {
        $installableModules = $this->recipeRepository->getInstallableModules();
        $tabItems = array();

        foreach ($installableModules as $moduleDefinition) {
            $moduleName = (string) ($moduleDefinition['name'] ?? '');

            if ($moduleName === '' || !$this->wire()->modules->isInstalled($moduleName)) {
                continue;
            }

            $tabItems[$moduleName] = $this->renderInstalledModuleHintTab($moduleDefinition);
        }

        if (!$tabItems) {
            return '<p>' . $this->_('Aktuell ist kein Wesanox Setup-Modul aus dem Katalog installiert.') . '</p>';
        }

        /** @var JqueryWireTabs $tabs */
        $tabs = $this->wire()->modules->get('JqueryWireTabs');

        return $tabs->render($tabItems, array(
            'id' => 'WesanoxSetupInstalledModuleHintsTabs',
            'linksID' => 'WesanoxSetupInstalledModuleHintsTabLinks',
            'itemClass' => 'wesanox-setup-installed-module-hints-tab',
        ));
    }

    /**
     * @param array $moduleDefinition
     *
     * @return string
     */
    protected function renderInstalledModuleHintTab(array $moduleDefinition): string
    {
        $moduleName = (string) ($moduleDefinition['name'] ?? '');
        $moduleTitle = (string) ($moduleDefinition['title'] ?? $moduleName);
        $summary = trim((string) ($moduleDefinition['summary'] ?? ''));
        $repository = trim((string) ($moduleDefinition['repository'] ?? ''));
        $out = '<h3>' . $this->entities($moduleTitle) . '</h3>';
        $out .= '<p><span class="detail">' . $this->entities($moduleName) . '</span></p>';

        if ($summary !== '') {
            $out .= '<p>' . $this->entities($summary) . '</p>';
        }

        $out .= '<h4>' . $this->_('Readme-Hinweise') . '</h4>';
        $out .= $this->renderModuleReadmeHints($moduleDefinition);

        if ($repository !== '') {
            $out .= '<p><strong>' . $this->_('Repository') . ':</strong> ' . $this->entities($repository) . '</p>';
        }

        return $out;
    }

    /**
     * @param array $installableModules
     * @param array $recipes
     *
     * @return string
     */
    protected function renderSelectionTabs(array $installableModules, array $recipes): string
    {
        /** @var JqueryWireTabs $tabs */
        $tabs = $this->wire()->modules->get('JqueryWireTabs');

        return $tabs->render(array(
            $this->_('Module') => $this->renderModuleSelectionTable($installableModules),
            $this->_('Schnellwahl') => $this->renderRecipeTable($recipes),
        ), array(
            'id' => 'WesanoxSetupSelectionTabs',
            'linksID' => 'WesanoxSetupSelectionTabLinks',
            'itemClass' => 'wesanox-setup-selection-tab',
        ));
    }

    /**
     * @param array $recipes
     *
     * @return string
     */
    protected function renderRecipeTable(array $recipes): string
    {
        /** @var MarkupAdminDataTable $table */
        $table = $this->wire()->modules->get('MarkupAdminDataTable');
        $table->setEncodeEntities(false);
        $table->setColNotSortable(0);
        $table->headerRow(array(
            '',
            $this->_('Recipe'),
            $this->_('Beschreibung'),
            $this->_('Module'),
        ));

        foreach ($recipes as $recipeId => $recipe) {
            $modules = array();
            $moduleDefinitions = $this->recipeRepository->getModulesForRecipes(array($recipeId));

            foreach ($moduleDefinitions as $moduleDefinition) {
                $modules[] = $this->entities($moduleDefinition['name']);
            }

            $checkbox = "<input type='checkbox' class='uk-checkbox' name='recipes[]' value='" . $this->entities($recipeId) . "'>";
            $table->row(array(
                $checkbox,
                '<strong>' . $this->entities($recipe['title']) . '</strong>',
                $this->entities($recipe['summary']),
                implode('<br>', $modules),
            ));
        }

        return $table->render();
    }

    /**
     * @param array $recipeIds
     *
     * @return string
     */
    protected function renderSelectedItems(array $recipeIds, array $moduleNames): string
    {
        $recipes = $this->recipeRepository->getRecipes();
        $installableModules = $this->recipeRepository->getInstallableModules();
        $selectedItems = array();

        foreach ($recipeIds as $recipeId) {
            if (isset($recipes[$recipeId])) {
                $selectedItems[] = $this->entities($recipes[$recipeId]['title']);
            }
        }

        foreach ($moduleNames as $moduleName) {
            if (isset($installableModules[$moduleName])) {
                $selectedItems[] = $this->entities($installableModules[$moduleName]['title'] ?? $moduleName);
            }
        }

        return '<p><strong>' . $this->_('Auswahl') . ':</strong> ' . implode(', ', $selectedItems) . '</p>';
    }

    /**
     * @param array $recipeIds
     * @param array $moduleNames
     *
     * @return string
     */
    protected function renderSelectionHiddenInputs(array $recipeIds, array $moduleNames): string
    {
        $out = '';

        foreach ($recipeIds as $recipeId) {
            $out .= "<input type='hidden' name='recipes[]' value='" . $this->entities($recipeId) . "'>";
        }

        foreach ($moduleNames as $moduleName) {
            $out .= "<input type='hidden' name='modules[]' value='" . $this->entities($moduleName) . "'>";
        }

        return $out;
    }

    /**
     * @param array $moduleDefinitions
     * @param array $moduleStatuses
     *
     * @return string
     */
    protected function renderModuleStatusTable(array $moduleDefinitions, array $moduleStatuses): string
    {
        /** @var MarkupAdminDataTable $table */
        $table = $this->wire()->modules->get('MarkupAdminDataTable');
        $table->setEncodeEntities(false);
        $table->headerRow(array(
            $this->_('Modul'),
            $this->_('Status'),
            $this->_('Beschreibung'),
            $this->_('Abhaengigkeiten'),
            $this->_('Hinweis'),
        ));

        foreach ($moduleDefinitions as $moduleDefinition) {
            $moduleName = $moduleDefinition['name'];
            $status = $moduleStatuses[$moduleName];

            $table->row(array(
                '<strong>' . $this->entities($moduleDefinition['title'] ?? $moduleName) . '</strong><br><span class="detail">' . $this->entities($moduleName) . '</span>',
                $this->statusLabel($status['status']),
                $this->entities($moduleDefinition['summary'] ?? ''),
                $this->renderRequirementList($moduleDefinition),
                $this->entities($status['message']),
            ));
        }

        return $table->render();
    }

    /**
     * @param array $externalRequirementStatuses
     *
     * @return string
     */
    protected function renderExternalRequirementStatusTable(array $externalRequirementStatuses): string
    {
        if (!$externalRequirementStatuses) {
            return '';
        }

        /** @var MarkupAdminDataTable $table */
        $table = $this->wire()->modules->get('MarkupAdminDataTable');
        $table->setEncodeEntities(false);
        $table->headerRow(array(
            $this->_('Externe Abhaengigkeit'),
            $this->_('Status'),
            $this->_('Benotigt von'),
            $this->_('Hinweis'),
        ));

        foreach ($externalRequirementStatuses as $requirementStatus) {
            $table->row(array(
                '<strong>' . $this->entities($requirementStatus['requirement']) . '</strong>',
                $this->statusLabel($requirementStatus['status']),
                $this->entities($requirementStatus['source']),
                $this->entities($requirementStatus['message']),
            ));
        }

        return '<h3>' . $this->_('Externe Abhaengigkeiten') . '</h3>' . $table->render();
    }

    /**
     * @param array $moduleDefinition
     *
     * @return string
     */
    protected function renderRequirementList(array $moduleDefinition): string
    {
        $requirements = array_merge(
            $moduleDefinition['requires'] ?? array(),
            $moduleDefinition['installs'] ?? array()
        );

        if (!$requirements) {
            return '<span class="detail">-</span>';
        }

        $out = array();

        foreach ($requirements as $requirement) {
            $out[] = $this->entities($requirement);
        }

        return implode('<br>', $out);
    }

    /**
     * @param array $moduleDefinition
     *
     * @return string
     */
    protected function renderModuleReadmeHints(array $moduleDefinition): string
    {
        $hints = $moduleDefinition['readmeHints'] ?? array();

        if (!is_array($hints)) {
            $hints = $hints ? array((string) $hints) : array();
        }

        if (!$hints) {
            return '<span class="detail">-</span>';
        }

        $items = array();

        foreach ($hints as $hint) {
            $hint = trim((string) $hint);

            if ($hint === '') {
                continue;
            }

            $items[] = '<li>' . $this->entities($hint) . '</li>';
        }

        if (!$items) {
            return '<span class="detail">-</span>';
        }

        return '<ul class="uk-list uk-list-bullet" style="margin:0;">' . implode('', $items) . '</ul>';
    }

    /**
     * @param array $results
     *
     * @return string
     */
    protected function renderResultTable(array $results): string
    {
        /** @var MarkupAdminDataTable $table */
        $table = $this->wire()->modules->get('MarkupAdminDataTable');
        $table->setEncodeEntities(false);
        $table->headerRow(array(
            $this->_('Modul'),
            $this->_('Status'),
            $this->_('Meldung'),
        ));

        foreach ($results as $result) {
            $table->row(array(
                $this->entities($result['module']),
                $this->statusLabel($result['status']),
                $this->entities($result['message']),
            ));
        }

        return $table->render();
    }

    /**
     * @param string $status
     *
     * @return string
     */
    protected function statusLabel(string $status): string
    {
        $labels = array(
            'ok' => array($this->_('OK'), 'success'),
            'installed' => array($this->_('Installiert'), 'success'),
            'already-installed' => array($this->_('Bereits installiert'), 'success'),
            'installable' => array($this->_('Installierbar'), ''),
            'downloadable' => array($this->_('Download bereit'), ''),
            'downloaded' => array($this->_('Heruntergeladen'), ''),
            'missing' => array($this->_('Fehlt'), 'danger'),
            'optional-missing' => array($this->_('Optional fehlt'), 'warning'),
            'download-disabled' => array($this->_('Download deaktiviert'), 'danger'),
            'invalid' => array($this->_('Ungueltig'), 'danger'),
            'error' => array($this->_('Fehler'), 'danger'),
        );

        $label = $labels[$status] ?? array($status, '');
        $class = $label[1] ? ' uk-label-' . $label[1] : '';

        return '<span class="uk-label' . $class . '">' . $this->entities($label[0]) . '</span>';
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function entities(string $value): string
    {
        return $this->wire()->sanitizer->entities($value);
    }
}
