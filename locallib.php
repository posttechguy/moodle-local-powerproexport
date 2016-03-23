<?php
/**
  * Export user and completion data to CSV files
  *
  * Local library definitions
  *
  * @package    local_powerproexport
  * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
  * @author     Bevan Holman <bevan@pukunui.com>, Pukunui
  * @copyright  2016 onwards, Pukunui
  * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once($CFG->dirroot.'/user/profile/lib.php');

function local_powerproexport_cron($runhow = 'auto', $data = null) {

    $config = get_config('local_powerproexport');

    if (($runhow == 'auto' and $config->ismanual) or ($runhow == 'manual' and empty($config->ismanual))) {
      return false;
    }
    local_powerproexport_write_user_data($config);
    local_powerproexport_write_course_completions_data($config);

    set_config('local_powerproexport', 'lastrun', time());
    return true;
}

/**
  * Write the user CSV output to file
  *
  * @param string $csv  the csv data
  * @return boolean  success?
*/
function local_powerproexport_write_user_data($config, $runhow = 'auto', $data = null) {
  global $CFG, $DB;

  if (empty($config->csvlocation)) {
      $config->usercsvlocation = $CFG->dataroot.'/powerproexport';
  }
  if (!isset($config->csvprefix)) {
      $config->usercsvprefix = '';
  }
  if (!isset($config->lastrun)) {
      $config->lastrun = 0;
  }

  // Open the file for writing.
  $filename = '';

  if ($data) {
    $filename = $config->usercsvlocation.'/'.$config->usercsvprefix.date("Ymd").'-'.date("His").'.csv';
  } else {
    $filename = $config->usercsvlocation.'/'.$config->usercsvprefix.date("Ymd").'.csv';
  }

  if ($fh = fopen($filename, 'w')) {

      // Write the headers first.
      fwrite($fh, implode(',', local_powerproexport_get_user_csv_headers())."\r\n");

      $users = local_powerproexport_get_user_data($config->lastrun);

      if ($users->valid()) {

          // Cycle through data and add to file.
          foreach ($users as $user) {

            //  profile_load_custom_fields($user);

              $user->profile  = (array)profile_user_record($user->id);
              $employer       = (!empty($user->profile['employerother'])
                                  and strtolower($user->profile['employerother']) != 'n/a'
                                  and $user->profile['employer'] == 'Other')
                              ? $user->profile['employerother']
                              : $user->profile['employer'];

              // Write the line to CSV file.
              fwrite($fh,
                  implode(',', array(
                      $user->username,
                      $user->email,
                      $user->firstname,
                      $user->lastname,
                      $user->country,
                      $user->profile['dob'],
                      $user->profile['streetnumber'],
                      $user->profile['streetname'],
                      $user->profile['town'],
                      $user->profile['postcode'],
                      $user->profile['state'],
                      $user->profile['gender'],
                      $user->profile['postaladdress'],
                      $employer,
                      $user->profile['usi'],
                      $user->profile['phone'])
               )."\r\n");
          }

          // Close the recordset to free up RDBMS memory.
          $users->close();
      }
      // Close the file.
      fclose($fh);

      return true;
  } else {
      return false;
  }
}

/**
 * Return a record set with the grade, group, enrolment data.
 * We use a record set to minimise memory usage as this report may get quite large.
 *
 * @param integer   $from   time stamp
 * @return object   $DB     record set
 */
function local_powerproexport_get_user_data($lastrun = 0) {
    global $DB;

    $params = array('lastrun' => $lastrun);
    $sql = "
        SELECT  *
        FROM    {user} AS u
        WHERE   u.timemodified > :lastrun
    ";

/*

    if ($_SERVER['REMOTE_ADDR'] == '203.59.120.7')
    {
        print_object($params);
         echo "<pre>$sql</pre>";
    }
*/
    return $DB->get_recordset_sql($sql, $params);
}


/**
 * Return the CSV headers
 *
 * @return array
 */
function local_powerproexport_get_user_csv_headers() {
    return array(
        get_string('username',   'local_powerproexport'),
        get_string('email',   'local_powerproexport'),
        get_string('firstname',      'local_powerproexport'),
        get_string('lastname',    'local_powerproexport'),
        get_string('country',      'local_powerproexport'),
        get_string('dob', 'local_powerproexport'),
        get_string('streetnumber', 'local_powerproexport'),
        get_string('streetname', 'local_powerproexport'),
        get_string('town', 'local_powerproexport'),
        get_string('postcode', 'local_powerproexport'),
        get_string('state', 'local_powerproexport'),
        get_string('gender', 'local_powerproexport'),
        get_string('postaladdress', 'local_powerproexport'),
        get_string('employer', 'local_powerproexport'),
        get_string('idnumber', 'local_powerproexport'),
        get_string('phone', 'local_powerproexport'),
    );
}


/**





*/




/**
  * Write the coursecompletions CSV output to file
  *
  * @param string $csv  the csv data
  * @return boolean  success?
*/
function local_powerproexport_write_course_completions_data($config, $runhow = 'auto', $data = null) {
  global $CFG, $DB;

  if (empty($config->coursecompletioncsvlocation)) {
      $config->coursecompletioncsvlocation = $CFG->dataroot.'/powerproexport/coursecompletions';
  }
  if (!isset($config->coursecompletionscsvprefix)) {
      $config->coursecompletionscsvprefix = '';
  }
  if (!isset($config->lastrun)) {
      $config->lastrun = 0;
  }

  // Open the file for writing.
  $filename = '';

  if ($data) {
    $filename = $config->coursecompletioncsvlocation.'/'.$config->coursecompletioncsvprefix.date("Ymd").'-'.date("His").'.csv';
  } else {
    $filename = $config->coursecompletioncsvlocation.'/'.$config->coursecompletioncsvprefix.date("Ymd").'.csv';
  }

  if ($fh = fopen($filename, 'w')) {

      // Write the headers first.
      fwrite($fh, implode(',', local_powerproexport_get_course_completions_csv_headers())."\r\n");

      $completions = local_powerproexport_get_course_completions_data($config->lastrun);

      if ($completions->valid()) {

          // Cycle through data and add to file.
          foreach ($completions as $usercompletions) {

              // Write the line to CSV file.
              fwrite($fh,
                  implode(',', array(
                      $usercompletions->username,
                      $usercompletions->coursename,
                      $usercompletions->courseshortname,
                      $usercompletions->courseidnumber,
                      $usercompletions->certificatecode,
                      $usercompletions->timecompleted)
               )."\r\n");
          }

          // Close the recordset to free up RDBMS memory.
          $completions->close();
      }
      // Close the file.
      fclose($fh);

      return true;
  } else {
      return false;
  }
}

/**
 * Return a record set with the grade, group, enrolment data.
 * We use a record set to minimise memory usage as this report may get quite large.
 *
 * @param integer   $from   time stamp
 * @return object   $DB     record set
 */
function local_powerproexport_get_course_completions_data($lastrun = 0) {
    global $DB;

    $params = array('lastrun' => $lastrun);
    $sql = "
        SELECT
          u.id, u.username,
          c.fullname AS coursename,
          c.shortname AS courseshortname,
          c.idnumber AS courseidnumber,
          ci.code AS certificatecode,
          cc.timecompleted
        FROM mdl_user AS u
        JOIN mdl_user_enrolments AS ue ON ue.userid = u.id
        JOIN mdl_enrol AS e ON e.id = ue.enrolid
        JOIN mdl_course AS c ON e.courseid = c.id
        JOIN mdl_course_completions AS cc ON cc.course = c.id AND cc.userid = u.id
        JOIN mdl_certificate_issues AS ci ON ci.userid = u.id
        WHERE cc.timecompleted > :lastrun
    ";

/*

    if ($_SERVER['REMOTE_ADDR'] == '203.59.120.7')
    {
        print_object($params);
         echo "<pre>$sql</pre>";
    }
*/
    return $DB->get_recordset_sql($sql, $params);
}


/**
 * Return the CSV headers
 *
 * @return array
 */
function local_powerproexport_get_course_completions_csv_headers() {
    return array(
        get_string('username',        'local_powerproexport'),
        get_string('coursename',      'local_powerproexport'),
        get_string('courseshortname', 'local_powerproexport'),
        get_string('courseidnumber',  'local_powerproexport'),
        get_string('certificatecode', 'local_powerproexport'),
        get_string('timecompleted',   'local_powerproexport'),
    );
}


