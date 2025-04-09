<?php

/**
 * Bootstrap Class for the Dental Chart Module
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Your Name <your.email@example.com>
 * @copyright Copyright (c) 2025
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenEMR\Modules\DentalChart;

/**
 * Bootstrap class for the Dental Chart Module
 */
class Bootstrap
{
    const MODULE_MENU_NAME = 'Dental Chart';
    const MODULE_PERMISSION = 'patients';
    const MODULE_DIRECTORY = 'oe-module-dentalchart';
    
    /**
     * @var \Module
     */
    private $module;

    /**
     * Constructor
     *
     * @param \Module $module The OpenEMR module instance
     */
    public function __construct(\Module $module)
    {
        $this->module = $module;
    }

    /**
     * Initialize the module
     *
     * @return void
     */
    public function init()
    {
        // Register menu item for the module
        $this->registerMenuItems();
        
        // Register ACL permissions
        $this->registerAclExtensions();
        
        // Register scripts and styles
        $this->registerScriptsAndStyles();
    }

    /**
     * Register the module's menu items
     *
     * @return void
     */
    private function registerMenuItems()
    {
        $hookObj = $GLOBALS['kernel']->getEventDispatcher();
        $hookObj->addListener('menu_update_entries', [$this, 'addMenuItems']);
    }

    /**
     * Adds the module's menu items
     *
     * @param MenuEvent $event The menu event
     * @return MenuEvent
     */
    public function addMenuItems($event)
    {
        $menu = $event->getMenu();
        
        $menuItem = new \stdClass();
        $menuItem->requirement = 0;
        $menuItem->target = 'den';
        $menuItem->menu_id = 'den0';
        $menuItem->label = self::MODULE_MENU_NAME;
        $menuItem->url = '/interface/modules/custom_modules/' . self::MODULE_DIRECTORY . '/public/index.php';
        $menuItem->children = [];
        $menuItem->acl_req = [self::MODULE_PERMISSION, 'write'];
        $menuItem->global_req = [];

        foreach ($menu as $item) {
            if ($item->menu_id == 'patimg') {
                $item->children[] = $menuItem;
                break;
            }
        }

        $event->setMenu($menu);
        
        return $event;
    }

    /**
     * Register ACL extensions
     *
     * @return void
     */
    private function registerAclExtensions()
    {
        // We are using existing permissions for now
        // If needed, custom permissions can be added here
    }

    /**
     * Register scripts and styles
     *
     * @return void
     */
    private function registerScriptsAndStyles()
    {
        // We'll use the scripts directly in the templates
        // If we need global scripts, we can register them here
    }
}