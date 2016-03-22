<?php
/**
 * Manual Export for Wentworth Institute of Higher Education project
 *
 * Form definitions
 *
 * @package    local_powerproexport
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2016 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class local_powerproexport_manual_export_form extends moodleform {

    /**
     * Define the form
     */
    public function definition() {
        global $DB;

        $mform =& $this->_form;
        $id = $this->_customdata;

        $strrequired = get_string('required');

        $strsubmit = get_string('exportnow', 'local_powerproexport');
        $this->add_action_buttons(true, $strsubmit);
    }

    /**
     * Validate the form submission
     *
     * @param array $data  submitted form data
     * @param array $files submitted form files
     * @return array
     */
    public function validation($data, $files) {
        global $DB;

        $error = array();

        if ($data['course'] == 'selectacourse') {
            $error['course'] = get_string('errorcourse', 'local_powerproexport');
        }
        if ($data['group'] == 'selectagroup') {
            $error['group'] = get_string('errorgroup', 'local_powerproexport');
        }

        return (count($error) == 0) ? true : $error;
    }
}