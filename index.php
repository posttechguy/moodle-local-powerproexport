<?php
/**
 * Export user and completion data to CSV files
 *
 * Main landing page. Nothing to do here so simply redirect to front page.
 *
 * @package    local_powerproexport
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2016 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');

redirect($CFG->wwwroot);
