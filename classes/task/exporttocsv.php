<?php
/**
 * Export user and completion data to CSV files
 *
 * Class definition for scheduled task execution
 *
 * @package    local_powerproexport
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2016 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_powerproexport\task;

require_once($CFG->dirroot.'/local/powerproexport/locallib.php');

/**
 * Extend core scheduled task class
 */
class exporttocsv extends \core\task\scheduled_task {

    /**
     * Return name of the task
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'local_powerproexport');
    }

    /**
     * Perform the task
     */
    public function execute() {
        local_powerproexport_cron();
    }
}
