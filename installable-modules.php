<?php

return array(
    'modules' => array(
        'WesanoxFrameworkPackage' => array(
            'name' => 'WesanoxFrameworkPackage',
            'title' => 'wesanox Framework Package',
            'summary' => 'Standard frontend framework package for ProcessWire projects.',
            'readmeHints' => array(
                'Einbindung laut Readme: echo wire()->modules->WesanoxFrameworkPackage->renderStyles(); im Head ausgeben.',
                'Einbindung laut Readme: echo wire()->modules->WesanoxFrameworkPackage->renderScripts(); im Footer ausgeben.',
                'In der Modulkonfiguration koennen einzelne Frameworks deaktiviert werden; nur vorhandene CSS/SCSS/JS Dateien werden ausgegeben.',
            ),
            'repository' => 'WesanoxFrameworkPackage',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxFrameworkPackage/archive/refs/heads/main.zip',
            'requires' => array(
                'ProcessWire>=3.0.210',
            ),
        ),
        'WesanoxHelperClasses' => array(
            'name' => 'WesanoxHelperClasses',
            'title' => 'wesanox Helper Classes',
            'summary' => 'Shared helper classes for ProcessWire projects.',
            'readmeHints' => array(
                'Autoload Helper; nach Installation direkt ueber wire()->modules->WesanoxHelperClasses verfuegbar.',
                'Beispiele aus dem Readme: getHeaderImage(...), getHeadline(...), renderMatrix(...), renderLink(...) und getSeparator(...).',
            ),
            'repository' => 'WesanoxHelperClasses',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxHelperClasses/archive/refs/heads/main.zip',
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
            ),
        ),
        'WesanoxHelperFields' => array(
            'name' => 'WesanoxHelperFields',
            'title' => 'wesanox Helper Fields',
            'summary' => 'Helper module to create, manage and delete fields.',
            'readmeHints' => array(
                'Autoload Helper; Felder koennen per $modules->get("WesanoxHelperFields")->createFields([...]) erstellt werden.',
                'deleteFields([...]) entfernt Felder, sofern sie nicht mehr in Fieldgroups verwendet werden.',
            ),
            'repository' => 'WesanoxHelperFields',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxHelperFields/archive/refs/heads/main.zip',
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
            ),
        ),
        'WesanoxHelperForms' => array(
            'name' => 'WesanoxHelperForms',
            'title' => 'wesanox Helper Forms',
            'summary' => 'Helper module to create simple forms in modules.',
            'readmeHints' => array(
                'Kein Readme-Hinweis im Modul vorhanden; im Setup als FormBuilder-basierter Helper eingeordnet.',
            ),
            'repository' => 'WesanoxHelperForm',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxHelperForm/archive/refs/heads/main.zip',
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'FormBuilder>=0.5.5',
            ),
        ),
        'WesanoxApi' => array(
            'name' => 'WesanoxApi',
            'title' => 'wesanox API Connector',
            'summary' => 'API connector for ProcessWire projects.',
            'readmeHints' => array(
                'Kein Readme-Hinweis im Modul vorhanden; installiert ProcessWesanoxApi und FieldtypeDynamicOptions als Begleitmodule.',
            ),
            'repository' => 'WesanoxApi',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxApi/archive/refs/heads/main.zip',
            'installs' => array(
                'WesanoxHelperFields',
                'ProcessWesanoxApi',
                'FieldtypeDynamicOptions',
            ),
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'WesanoxHelperClasses>=0.0.1',
                'WesanoxHelperFields>=0.0.1',
            ),
        ),
        'WesanoxMatrixContent' => array(
            'name' => 'WesanoxMatrixContent',
            'title' => 'wesanox Content Matrix',
            'summary' => 'Creates the content matrix for ProcessWire projects.',
            'readmeHints' => array(
                'Installiert/erstellt matrix_content und template_content_only inklusive /site/templates/template_content_only.php.',
                'Einbindung laut Readme: wire("config")->styles im Head und wire("config")->scripts im Footer vor main.js ausgeben.',
            ),
            'repository' => 'WesanoxMatrixContent',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxMatrixContent/archive/refs/heads/main.zip',
            'installs' => array(
                'PageFrontEdit',
                'CroppableImage3',
                'WesanoxFrameworkPackage',
                'WesanoxHelperFields',
            ),
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'FieldtypeRepeaterMatrix>=0.0.9',
                'WesanoxFrameworkPackage>=0.0.1',
                'WesanoxHelperFields>=0.0.1',
                'WesanoxHelperForms>=0.0.1',
                'WesanoxHelperClasses>=0.0.1',
            ),
        ),
        'WesanoxMatrixBasic' => array(
            'name' => 'WesanoxMatrixBasic',
            'title' => 'wesanox Basic Matrix',
            'summary' => 'Creates the basic matrix for ProcessWire projects.',
            'readmeHints' => array(
                'Installiert/erstellt matrix_basic und template_basic inklusive /site/templates/template_basic.php.',
                'Custom Matrix Items koennen unter /site/templates/fields/matrix_basic/<item>/ abgelegt werden; gleichnamige SCSS-Dateien ueberschreiben Modul-Defaults.',
            ),
            'repository' => 'WesanoxMatrixBasic',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxMatrixBasic/archive/refs/heads/main.zip',
            'installs' => array(
                'PageFrontEdit',
                'CroppableImage3',
                'WesanoxHelperFields',
                'WesanoxFrameworkPackage',
                'WesanoxMatrixContent',
            ),
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'FieldtypeRepeaterMatrix>=0.0.9',
                'WesanoxFrameworkPackage>=0.0.1',
                'WesanoxMatrixContent>=0.1.1',
                'WesanoxHelperClasses>=0.0.1',
                'WesanoxHelperFields>=0.0.1',
            ),
        ),
        'WesanoxMenuBuilder' => array(
            'name' => 'WesanoxMenuBuilder',
            'title' => 'wesanox Menu Builder',
            'summary' => 'Menu builder for ProcessWire.',
            'readmeHints' => array(
                'Erstellt die Settings-Seite /settings/ und Matrix/Repeater-Felder fuer Menues.',
                'Einbindung laut Readme: $modules->get("WesanoxMenuBuilder")->renderMenu($page->matrix_menu, 0) liefert JSON fuer das Frontend.',
            ),
            'repository' => 'WesanoxMenuBuilder',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxMenuBuilder/archive/refs/heads/main.zip',
            'installs' => array(
                'CroppableImage3',
            ),
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'FieldtypeRepeaterMatrix>=0.0.9',
                'WesanoxHelperFields>=0.0.1',
                'WesanoxHelperClasses>=0.0.1',
            ),
        ),
        'WesanoxBlog' => array(
            'name' => 'WesanoxBlog',
            'title' => 'wesanox Blog Tool',
            'summary' => 'News and blog module for ProcessWire.',
            'readmeHints' => array(
                'Readme enthaelt nur eine Kurzbeschreibung; konkrete Einbindung muss im Modul/Projekt geprueft werden.',
            ),
            'repository' => 'WesanoxBlog',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxBlog/archive/refs/heads/main.zip',
            'installs' => array(
                'ProcessWesanoxBlog',
                'FieldtypeDynamicOptions',
                'WesanoxMatrixContent',
                'WesanoxMatrixBasic',
            ),
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'WesanoxMatrixContent>=0.0.1',
                'WesanoxMatrixBasic>=0.0.1',
            ),
        ),
        'WesanoxAccessibilityTool' => array(
            'name' => 'WesanoxAccessibilityTool',
            'title' => 'wesanox Accessibility Tool',
            'summary' => 'Accessibility helper for ProcessWire projects.',
            'readmeHints' => array(
                'Einbindung laut Readme: renderStyles() und renderScripts() in _head.php oder _main.php ausgeben.',
                'Konfiguration in den Moduleinstellungen: Frontend-Tools ausblenden und URL zur Barrierefreiheitsseite setzen.',
            ),
            'repository' => 'WesanoxAccessibilityTool',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxAccessibilityTool/archive/refs/heads/main.zip',
            'installs' => array(
                'LanguageSupport',
                'LanguageSupportFields',
                'LanguageSupportPageNames',
                'LanguageTabs',
                'WesanoxFrameworkPackage',
                'WesanoxHelperFields',
                'WesanoxMatrixContent',
            ),
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'WesanoxFrameworkPackage>=0.0.1',
                'WesanoxHelperFields>=0.0.1',
                'WesanoxHelperClasses>=0.0.1',
                'WesanoxMatrixContent>=0.0.1',
            ),
        ),
        'WesanoxHazardDisplay' => array(
            'name' => 'WesanoxHazardDisplay',
            'title' => 'wesanox Hazard Display Matrix',
            'summary' => 'Hazard display modal module for ProcessWire.',
            'readmeHints' => array(
                'Konfiguration erfolgt ueber den Backend-Menuepunkt Stoerereinstellungen; Anzeige per checkbox_hazard aktivieren.',
                'Rendert per Page::render Hook ein Bootstrap-kompatibles Modal auf der Home-Seite, wenn aktiviert.',
            ),
            'repository' => 'WesanoxHazardDisplay',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxHazardDisplay/archive/refs/heads/main.zip',
            'installs' => array(
                'ProcessWesanoxHazardDisplay',
                'WesanoxFrameworkPackage',
            ),
            'requires' => array(
                'ProcessWire>=3.0.246',
                'PHP>=8.2.0',
                'WesanoxFrameworkPackage>=0.0.1',
                'WesanoxHelperClasses>=0.0.1',
            ),
        ),
        'WesanoxCareer' => array(
            'name' => 'WesanoxCareer',
            'title' => 'wesanox Career',
            'summary' => 'Career module for ProcessWire projects.',
            'readmeHints' => array(
                'Readme enthaelt nur eine Kurzbeschreibung; konkrete Einbindung muss im Modul/Projekt geprueft werden.',
            ),
            'repository' => 'WesanoxCareer',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxCareer/archive/refs/heads/main.zip',
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'WesanoxMatrixBasic>=0.1.1',
                'WesanoxMenuBuilder>=0.0.1',
            ),
        ),
        'WesanoxRecruiteeImporter' => array(
            'name' => 'WesanoxRecruiteeImporter',
            'title' => 'wesanox Recruitee Importer',
            'summary' => 'Imports job data from Recruitee.',
            'readmeHints' => array(
                'Readme enthaelt nur eine Kurzbeschreibung; Importkonfiguration muss im Modul/Backend geprueft werden.',
            ),
            'repository' => 'WesanoxRecruiteeImporter',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxRecruiteeImporter/archive/refs/heads/main.zip',
            'installs' => array(
                'ProcessWesanoxRecruiteeImporter',
                'LazyCron',
            ),
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'WesanoxCareer>=0.0.1',
            ),
        ),
        'WesanoxDealer' => array(
            'name' => 'WesanoxDealer',
            'title' => 'wesanox Dealer',
            'summary' => 'Dealer search package for ProcessWire.',
            'readmeHints' => array(
                'Kein Readme-Hinweis vorhanden; im Setup als Paket auf Basis von WesanoxApi und FieldtypeMapMarker eingeordnet.',
            ),
            'repository' => 'WesanoxDealer',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxDealer/archive/refs/heads/main.zip',
            'installs' => array(
                'ProcessWesanoxDealer',
                'FieldtypeMapMarker',
            ),
            'requires' => array(
                'WesanoxApi>=0.5.0',
                'ProcessWire>=3.0.210',
                'FieldtypeMapMarker>=2.0.1',
            ),
        ),
        'WesanoxLottery' => array(
            'name' => 'WesanoxLottery',
            'title' => 'wesanox Lottery',
            'summary' => 'Connects lotteries from a backend with a ProcessWire frontend.',
            'readmeHints' => array(
                'Kein Readme-Hinweis vorhanden; Modul ergaenzt Lottery Matrix Items fuer matrix_content.',
                'Abhaengig von FormBuilder, WesanoxHelperForms, WesanoxHelperFields und WesanoxApi.',
            ),
            'repository' => 'WesanoxLottery',
            'downloadUrl' => 'https://github.com/wesanox/WesanoxLottery/archive/refs/heads/main.zip',
            'requires' => array(
                'ProcessWire>=3.0.210',
                'PHP>=8.0.0',
                'FormBuilder>=0.5.5',
                'WesanoxHelperFields>=0.0.1',
                'WesanoxHelperForms>=0.0.1',
                'WesanoxApi>=0.0.1',
            ),
        ),
    ),
    'recipes' => array(
        'wesanox-base' => array(
            'title' => 'wesanox Basis',
            'summary' => 'Shared helper, field and frontend framework modules.',
            'modules' => array(
                'WesanoxHelperClasses',
                'WesanoxHelperFields',
                'WesanoxFrameworkPackage',
            ),
        ),
        'wesanox-matrix' => array(
            'title' => 'wesanox Matrix',
            'summary' => 'Basic and content matrix modules with required wesanox helpers.',
            'modules' => array(
                'WesanoxMatrixContent',
                'WesanoxMatrixBasic',
            ),
        ),
        'wesanox-content' => array(
            'title' => 'wesanox Content',
            'summary' => 'Blog, menu and accessibility modules.',
            'modules' => array(
                'WesanoxBlog',
                'WesanoxMenuBuilder',
                'WesanoxAccessibilityTool',
            ),
        ),
        'wesanox-career' => array(
            'title' => 'wesanox Career',
            'summary' => 'Career module and Recruitee importer.',
            'modules' => array(
                'WesanoxCareer',
                'WesanoxRecruiteeImporter',
            ),
        ),
        'wesanox-api' => array(
            'title' => 'wesanox API & Dealer',
            'summary' => 'API connector, dealer search and related dependencies.',
            'modules' => array(
                'WesanoxApi',
                'WesanoxDealer',
            ),
        ),
        'wesanox-lottery' => array(
            'title' => 'wesanox Lottery',
            'summary' => 'Lottery module and required helper/API modules.',
            'modules' => array(
                'WesanoxLottery',
            ),
        ),
        'wesanox-hazard' => array(
            'title' => 'wesanox Hazard Display',
            'summary' => 'Hazard display module with required framework package.',
            'modules' => array(
                'WesanoxHazardDisplay',
            ),
        ),
    ),
);
