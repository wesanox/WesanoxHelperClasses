<?php

namespace ProcessWire\classes;

use ProcessWire\WireData;

class SetupRecipeRepository extends WireData
{
    protected array $catalog = array();

    /**
     * @return array
     */
    public function getRecipes(): array
    {
        return $this->getCatalog()['recipes'];
    }

    /**
     * @return array
     */
    public function getInstallableModules(): array
    {
        return $this->getCatalog()['modules'];
    }

    /**
     * @param array $recipeIds
     *
     * @return array
     */
    public function getModulesForRecipes(array $recipeIds): array
    {
        return $this->getModulesForSelection($recipeIds, array());
    }

    /**
     * @param array $moduleNames
     *
     * @return array
     */
    public function getModulesByNames(array $moduleNames): array
    {
        return $this->getModulesForSelection(array(), $moduleNames);
    }

    /**
     * @param array $recipeIds
     * @param array $moduleNames
     *
     * @return array
     */
    public function getModulesForSelection(array $recipeIds, array $moduleNames): array
    {
        $recipes = $this->getRecipes();
        $moduleDefinitions = array();
        $seen = array();

        foreach ($recipeIds as $recipeId) {
            if (!isset($recipes[$recipeId])) {
                continue;
            }

            foreach ($recipes[$recipeId]['modules'] as $moduleName) {
                $this->addModuleWithDependencies($moduleName, $moduleDefinitions, $seen);
            }
        }

        foreach ($this->filterModuleNames($moduleNames) as $moduleName) {
            $this->addModuleWithDependencies($moduleName, $moduleDefinitions, $seen);
        }

        return array_values($moduleDefinitions);
    }

    /**
     * @param array $moduleDefinitions
     *
     * @return array
     */
    public function getExternalRequirementsForModules(array $moduleDefinitions): array
    {
        $catalog = $this->getInstallableModules();
        $requirements = array();

        foreach ($moduleDefinitions as $moduleDefinition) {
            foreach ($moduleDefinition['requires'] ?? array() as $requirement) {
                $requirementName = $this->getRequirementModuleName($requirement);

                if ($this->isPlatformRequirement($requirementName) || isset($catalog[$requirementName])) {
                    continue;
                }

                $requirements[$requirement] = array(
                    'name' => $requirementName,
                    'requirement' => $requirement,
                    'source' => $moduleDefinition['name'],
                    'type' => 'requires',
                );
            }

            foreach ($moduleDefinition['installs'] ?? array() as $installName) {
                if (isset($catalog[$installName]) || $this->isCompanionModule($installName)) {
                    continue;
                }

                $requirements[$installName] = array(
                    'name' => $installName,
                    'requirement' => $installName,
                    'source' => $moduleDefinition['name'],
                    'type' => 'installs',
                );
            }
        }

        return array_values($requirements);
    }

    /**
     * @param array $recipeIds
     *
     * @return array
     */
    public function filterRecipeIds(array $recipeIds): array
    {
        $recipes = $this->getRecipes();
        $filteredRecipeIds = array();

        foreach ($recipeIds as $recipeId) {
            $recipeId = (string) $recipeId;

            if (isset($recipes[$recipeId])) {
                $filteredRecipeIds[] = $recipeId;
            }
        }

        return array_values(array_unique($filteredRecipeIds));
    }

    /**
     * @param array $moduleNames
     *
     * @return array
     */
    public function filterModuleNames(array $moduleNames): array
    {
        $catalog = $this->getInstallableModules();
        $filteredModuleNames = array();

        foreach ($moduleNames as $moduleName) {
            $moduleName = (string) $moduleName;

            if (isset($catalog[$moduleName])) {
                $filteredModuleNames[] = $moduleName;
            }
        }

        return array_values(array_unique($filteredModuleNames));
    }

    /**
     * @return array
     */
    protected function getCatalog(): array
    {
        if ($this->catalog) {
            return $this->catalog;
        }

        $catalogFile = dirname(__DIR__) . '/installable-modules.php';
        $catalog = is_file($catalogFile) ? include $catalogFile : array();

        $this->catalog = array(
            'modules' => is_array($catalog['modules'] ?? null) ? $catalog['modules'] : array(),
            'recipes' => is_array($catalog['recipes'] ?? null) ? $catalog['recipes'] : array(),
        );

        return $this->catalog;
    }

    /**
     * @param string $moduleName
     * @param array $moduleDefinitions
     * @param array $seen
     *
     * @return void
     */
    protected function addModuleWithDependencies(string $moduleName, array &$moduleDefinitions, array &$seen): void
    {
        $catalog = $this->getInstallableModules();

        if (isset($seen[$moduleName]) || !isset($catalog[$moduleName])) {
            return;
        }

        $seen[$moduleName] = true;
        $moduleDefinition = $catalog[$moduleName];

        foreach ($this->getCatalogDependencyNames($moduleDefinition) as $dependencyName) {
            $this->addModuleWithDependencies($dependencyName, $moduleDefinitions, $seen);
        }

        $moduleDefinitions[$moduleName] = $moduleDefinition;
    }

    /**
     * @param array $moduleDefinition
     *
     * @return array
     */
    protected function getCatalogDependencyNames(array $moduleDefinition): array
    {
        $catalog = $this->getInstallableModules();
        $dependencyNames = array();

        foreach ($moduleDefinition['requires'] ?? array() as $requirement) {
            $dependencyName = $this->getRequirementModuleName($requirement);

            if (isset($catalog[$dependencyName])) {
                $dependencyNames[] = $dependencyName;
            }
        }

        foreach ($moduleDefinition['installs'] ?? array() as $installName) {
            if (isset($catalog[$installName])) {
                $dependencyNames[] = $installName;
            }
        }

        return array_values(array_unique($dependencyNames));
    }

    /**
     * @param string $requirement
     *
     * @return string
     */
    protected function getRequirementModuleName(string $requirement): string
    {
        if (preg_match('/^([A-Za-z_][A-Za-z0-9_\\\\]*)/', $requirement, $matches)) {
            return trim($matches[1], '\\');
        }

        return $requirement;
    }

    /**
     * @param string $requirementName
     *
     * @return bool
     */
    protected function isPlatformRequirement(string $requirementName): bool
    {
        return in_array($requirementName, array('PHP', 'ProcessWire'), true);
    }

    /**
     * @param string $moduleName
     *
     * @return bool
     */
    protected function isCompanionModule(string $moduleName): bool
    {
        return strpos($moduleName, 'ProcessWesanox') === 0;
    }
}
