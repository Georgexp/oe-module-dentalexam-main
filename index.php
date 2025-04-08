<?php
/**
 * Bootstrap custom module skeleton.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Your Name <your.email@example.com>
 * @copyright Copyright (c) 2025
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

use OpenEMR\Core\Kernel;
use OpenEMR\Modules\DentalChart\Bootstrap;

// Ensure accessing this file from OpenEMR context
$ignoreAuth = false;
require_once dirname(__FILE__, 4) . '/globals.php';

// Verify access control
if (!acl_check('admin', 'manage_modules')) {
    echo xlt('Access Denied');
    exit;
}

// Instantiate the module bootstrap
$kernel = new Kernel();
$bootstrap = new Bootstrap($kernel->getEventDispatcher(), $GLOBALS['current_user']);
$bootstrap->subscribeToEvents();

// Redirect to the module's public entry point
header('Location: public/index.php');
