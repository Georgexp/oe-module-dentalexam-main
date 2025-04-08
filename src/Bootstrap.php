<?php
/**
 * Dental Chart module bootstrap.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Your Name <your.email@example.com>
 * @copyright Copyright (c) 2025
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenEMR\Modules\DentalChart;

use OpenEMR\Common\Logging\SystemLogger;
use OpenEMR\Core\Kernel;
use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use OpenEMR\Events\Main\Tabs\RenderEvent;
use OpenEMR\Events\RestApiExtend\RestApiResourceServiceEvent;
use OpenEMR\Menu\MenuEvent;
use OpenEMR\Services\Globals\GlobalSetting;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    private $eventDispatcher;
    private $logger;
    private $moduleDirectoryName;
    private $modulePath;
    private $user;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param array                    $user
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, $user)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->user = $user;
        $this->moduleDirectoryName = basename(dirname(__DIR__));
        $this->modulePath = dirname(__DIR__);
        $this->logger = new SystemLogger();
    }

    public function subscribeToEvents()
    {
        $this->addGlobalSettings();
        $this->registerMenuItems();
        $this->registerTabMenu();
    }

    /**
     * @return void
     */
    public function addGlobalSettings()
    {
        $this->eventDispatcher->addListener(GlobalsInitializedEvent::EVENT_HANDLE, function(GlobalsInitializedEvent $event) {
            $globalsService = $event->getGlobalsService();
            $serviceSettings = [
                'dentalchart_enabled' => new GlobalSetting(
                    xl('Enable Dental Chart'),
                    'bool',
                    '1',
                    xl('Enable the Dental Chart module')
                ),
            ];
            $globalsService->addUserSpecificGlobalSettings('dentalchart', $serviceSettings);
        });
    }

    /**
     * Add menu item for the Dental Chart module.
     *
     * @return void
     */
    public function registerMenuItems()
    {
        $this->eventDispatcher->addListener(MenuEvent::MENU_UPDATE, function(MenuEvent $event) {
            // Main menu
            $menu = $event->getMenu();
            $moduleConfig = $event->getGlobals()->getModuleConfig();
            
            // Check if module is enabled
            if ($moduleConfig->isRegistered('dentalchart') && empty($GLOBALS['dentalchart_enabled'])) {
                return;
            }

            $menuItem = new \stdClass();
            $menuItem->requirement = 0;
            $menuItem->target = 'mod';
            $menuItem->menu_id = 'mod0';
            $menuItem->label = xlt("Dental Chart");
            $menuItem->url = "/interface/modules/custom_modules/dentalchart/public/index.php";
            $menuItem->children = [];
            $menuItem->acl_req = ["patients", "dental"];
            $menuItem->global_req = ["dentalchart_enabled"];

            foreach ($menu as $item) {
                if ($item->menu_id == 'patimg') {
                    $item->children[] = $menuItem;
                    break;
                }
            }
            $event->setMenu($menu);
        });
    }

    /**
     * Register a tab for Dental Chart in the patient dashboard.
     *
     * @return void
     */
    public function registerTabMenu()
    {
        $this->eventDispatcher->addListener(RenderEvent::EVENT_RENDER_TABS_ABOVE, function(RenderEvent $event) {
            $pid = $event->getPid();
            if (empty($pid)) {
                return;
            }
            
            if (acl_check('patients', 'dental')) {
                // Define tab as array for core to render
                $tab = [
                    'title' => xl('Dental Chart'),
                    'link' => "/interface/modules/custom_modules/dentalchart/public/index.php?pid=" . urlencode($pid),
                    'linkClass' => 'iframe dental-chart',
                    'icon' => 'fa-teeth',
                    'dataToggle' => '',
                    'dataToggleTarget' => '',
                ];
                $event->addTab($tab);
            }
        });
    }
}