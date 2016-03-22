<?php
/**
 * Export user and completion data to CSV files
 *
 * Administration settings
 *
 * @package    local_powerproexport
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2016 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (has_capability('local/powerproexport:config', context_system::instance())) {

    $settings = new admin_settingpage('local_powerproexport_settings',
                                      new lang_string('pluginname', 'local_powerproexport'),
                                      'local/powerproexport:config');

    $settings->add(new admin_setting_configdirectory(
                'local_powerproexport/usercsvlocation',
                new lang_string('usercsvlocation', 'local_powerproexport'),
                new lang_string('usercsvlocationdesc', 'local_powerproexport'),
                $CFG->dataroot.'/powerproexport',
                PARAM_RAW,
                80
                ));

    $settings->add(new admin_setting_configtext(
                'local_powerproexport/usercsvprefix',
                new lang_string('usercsvprefix', 'local_powerproexport'),
                new lang_string('usercsvprefixdesc', 'local_powerproexport'),
                'powerproexport_',
                PARAM_RAW,
                80
                ));

    $settings->add(new admin_setting_configdirectory(
                'local_powerproexport/coursecompletioncsvlocation',
                new lang_string('coursecompletioncsvlocation', 'local_powerproexport'),
                new lang_string('coursecompletionsvlocationdesc', 'local_powerproexport'),
                $CFG->dataroot.'/powerproexport',
                PARAM_RAW,
                80
                ));

    $settings->add(new admin_setting_configtext(
                'local_powerproexport/coursecompletioncsvprefix',
                new lang_string('coursecompletioncsvprefix', 'local_powerproexport'),
                new lang_string('coursecompletioncsvprefixdesc', 'local_powerproexport'),
                'powerproexport_',
                PARAM_RAW,
                80
                ));

    $settings->add(new admin_setting_configcheckbox(
                'local_powerproexport/ismanual',
                new lang_string('ismanual', 'local_powerproexport'),
                new lang_string('ismanualdesc', 'local_powerproexport'),
                'Automatic grade export (not checked)'
                ));

    $ADMIN->add('root', new admin_category('local_powerproexport', get_string('pluginname', 'local_powerproexport')));

    $ADMIN->add('local_powerproexport', new admin_externalpage('manualexport', get_string('manualexport', 'local_powerproexport'),
                new moodle_url('/local/powerproexport/manual.php'),
                'local/powerproexport:config'));

    $ADMIN->add('localplugins', $settings);
}
