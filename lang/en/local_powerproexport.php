<?php
/**
 * Export user and completion data to CSV files
 *
 * String definitions
 *
 * @package    local_powerproexport
 * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
 * @copyright  2016 onwards, Pukunui
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['coursename'] = 'Course';
$string['courseshortname'] = 'Course Shortname';
$string['courseidnumber'] = 'Unit name';
$string['certificatecode'] = 'Certificate issue code';
$string['timecompleted'] = 'Date completed';
$string['usercsvlocation'] = 'User CSV File Location';
$string['usercsvlocationdesc'] = 'Full server path where User CSV files should be created. This directory must be writable by the web user';
$string['usercsvprefix'] = 'User CSV File Name Prefix';
$string['usercsvprefixdesc'] = 'The User CSV files will be created with a name of the current date in the format YYYYMMDD and an extension of ".csv". The prefix will be prepended to the beginning of the name';
$string['coursecompletioncsvlocation'] = 'Course Completion CSV File Location';
$string['coursecompletioncsvlocationdesc'] = 'Full server path where Course Completion CSV files should be created. This directory must be writable by the web user';
$string['coursecompletioncsvprefix'] = 'Course Completion CSV File Name Prefix';
$string['coursecompletioncsvprefixdesc'] = 'The Course Completion CSV files will be created with a name of the current date in the format YYYYMMDD and an extension of ".csv". The prefix will be prepended to the beginning of the name';
$string['errorcourse'] = 'Please choose a course';
$string['errorgroup'] = 'Please choose a group';
$string['exporterror'] = 'Error: An error has occurred while exporting the csv file, please return to the export page and try again';
$string['exportnow'] = 'Export Power Pro files now';
$string['exportsuccess'] = 'Success: your file has successfully exported as a csv file';
$string['grouptype'] = 'Group';
$string['ismanual'] = 'Is Power Pro Export manual?';
$string['ismanualdesc'] = 'The grade export can be configured as a manual process or a automatic process, Tick the box to make the process manual<br>
Turn off to make the process run automatically again';
$string['isnotmanual'] = 'The manual Power Pro Export is unavailable, you will be redirected to change the settings';
$string['local/powerproexport:config'] = 'Configure Power Pro Export';
$string['manualexport'] = 'Manual Export';
$string['manualexportheader'] = 'Manually Export to Power Pro';
$string['pluginname'] = 'Power Pro Export';
$string['selectacourse'] = 'Select a course';
$string['selectagroup'] = 'Select a group';
$string['selectallgroups'] = 'All groups';
$string['unitcode'] = 'USI';
$string['username'] = 'Moodle User';
$string['email'] = 'Email';
$string['firstname'] = 'Given names';
$string['lastname'] = 'Surname';
$string['country'] = 'Nationality';
$string['dob'] = 'DOB';
$string['streetnumber'] = 'Street No';
$string['streetname'] = 'Street Name';
$string['town'] = 'Town/Suburb';
$string['postcode'] = 'Postcode';
$string['state'] = 'State';
$string['gender'] = 'Gender';
$string['postaladdress'] = 'Postal addr.';
$string['employer'] = 'Employer';
$string['idnumber'] = 'USI';
$string['phone'] = 'Mobile';


$string['manualexport']         = 'Power Pro Export Description';
$string['manualexportdesc']     = 'The Power Pro Export creates two files:
<ul>
    <li>
        User profile data:
        <ol style="list-style-type:decimal;">
            <li>Username</li>
            <li>Email address</li>
            <li>First name</li>
            <li>Surname</li>
            <li>Country</li>
            <li>Date of birth</li>
            <li>Address street number</li>
            <li>Address street name</li>
            <li>Address location – suburb, locality or town</li>
            <li>Postcode</li>
            <li>State or territory</li>
            <li>Gender</li>
            <li>Postal details (if different from address details)</li>
            <li>Employer</li>
            <li>Unique Student Identifier</li>
            <li>Contact phone number</li>
        </ol>
    </li>
    <li>
        Course completion data
        <ol style="list-style-type:decimal;">
            <li>Username</li>
            <li>Course name</li>
            <li>Course shortname</li>
            <li>Unit name</li>
            <li>Certificate issue code</li>
            <li>Date completed</li>
        </ol>
    </li>
</ul>
';