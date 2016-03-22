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

function local_powerproexport_cron($runhow, $data = null) {

  $config = get_config('local_powerproexport');

  local_powerproexport_write_user_data($config);
  local_powerproexport_write_course_completions_data($config);


  set_config('local_powerproexport', 'lastrun', time());

}

/**
  * Write the CSV output to file
  *
  * @param string $csv  the csv data
  * @return boolean  success?
*/
function local_powerproexport_write_user_data($config, $runhow, $data = null) {
  global $CFG, $DB;

  $config = get_config('local_powerproexport');

  if (($runhow == 'auto' and $config->ismanual) or ($runhow == 'manual' and empty($config->ismanual))) {
    return false;
  }

  if (empty($config->csvlocation)) {
      $config->csvlocation = $CFG->dataroot.'/powerproexport';
  }
  if (!isset($config->csvprefix)) {
      $config->csvprefix = '';
  }
  if (!isset($config->lastrun)) {
      // First time run we get all data.
      $config->lastrun = 0;
  }
  // Open the file for writing.
  $filename = '';

  if ($data) {
    $filename = $config->csvlocation.'/'.$config->csvprefix.date("Ymd").'-'.date("His").'.csv';
  } else {
    $filename = $config->csvlocation.'/'.$config->csvprefix.date("Ymd").'.csv';
  }

  if ($fh = fopen($filename, 'w')) {

      // Write the headers first.
      fwrite($fh, implode(',', local_powerproexport_get_csv_headers())."\r\n");

      $rs = local_powerproexport_get_user_data($config->lastrun, $data);

      if ($rs->valid()) {

          // Cycle through data and add to file.
          foreach ($rs as $r) {
              // Write the line to CSV file.
              fwrite($fh,
                  implode(',', array(
                      $r->username,
                      $r->email,
                      $r->firstname,
                      $r->lastname,
                      $r->country,
                      $r->dob,
                      $r->streetnumber,
                      $r->streetname,
                      $r->town,
                      $r->postcode,
                      $r->state,
                      $r->gender,
                      $r->postaladdress,
                      $r->employer,
                      $r->idnumber,
                      $r->phone)
               )."\r\n");
          }

          // Close the recordset to free up RDBMS memory.
          $rs->close();
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
function local_powerproexport_get_user_data($from, $data = null) {
    global $DB;
// CONCAT_WS('-', u.id, c.id, g.id),

    $usersql = "
        (
            (
                {user} u
                JOIN
                (
                    (
                        SELECT ue.userid as userid, e.courseid as courseid
                        FROM {user_enrolments} ue
                        JOIN {enrol} e ON ue.enrolid = e.id
                        WHERE ue.timeend IS NOT NULL AND ue.timemodified >= :from1
                    )
                    UNION
                    (
                        SELECT gg.userid as userid, gi.courseid as courseid
                        FROM {grade_grades} gg
                        JOIN {grade_items} gi
                        ON gi.id = gg.itemid
                        WHERE gg.timemodified IS NOT NULL
                        AND gg.timemodified >= :from2
                        AND gi.itemtype = 'course'
                    )
                ) AS x ON x.userid = u.id
            )
            JOIN {course} c ON x.courseid = c.id %%COURSECLAUSE%%
        )
        LEFT JOIN {groups} g ON g.courseid = c.id
    ";
    if ($data->group != "All") {
      $usersql = "
        (
        $usersql
        )
        JOIN {groups_members} gm ON gm.groupid = g.id %%GROUPCLAUSE%% AND gm.userid = u.id
      ";
    }

    $sql = "
        SELECT
            u.id as userid, u.username, u.idnumber,
            u.firstname, u.lastname, x.courseid,
            c.shortname as unitcode, g.name as batch,
            y.finalgrade, y.scaleid, y.scale, y.timemodified, y.finalpercent
        FROM
        (
            $usersql
        )
        LEFT JOIN
        (
            SELECT
                gi.itemtype, gi.scaleid, round(gg.finalgrade) as finalgrade, gg.rawgrade,
                s.scale, gg.userid, gi.courseid, gg.timemodified, round(gg.finalgrade/gg.rawgrademax*100) as finalpercent
            FROM
            (
                {grade_items} gi
                JOIN {grade_grades} gg ON gg.itemid = gi.id
            )
            LEFT JOIN {scale} s ON gi.scaleid = s.id
            WHERE gi.itemtype = 'course'
        ) as y ON x.userid = y.userid AND x.courseid = y.courseid
        GROUP BY 1,2,3,4,5,6,7,8
    ";

    $params = array();

    if ($data)
    {
        // This for the manually run exports of grades

        $params['from1']  = 0; // Gets all records from 185 days ago
        $params['from2']  = 0; // Gets all records from 185 days ago
        $params['course'] = $data->course;
        $params['group']  = $data->group;
        $sql              = str_replace("%%COURSECLAUSE%%", ($data->course) ? " AND x.courseid = :course " : "", $sql);
        $sql              = str_replace("%%GROUPCLAUSE%%", ($data->group != "All") ? " AND g.name = :group " : "", $sql);
    }
    else
    {
        // Gets the last run time, removes the seconds from today (which is usually run early in the morning),
        // yesterday, and the day before, (so around 48 hours).
        // It will then allow the export to get the records for the last two days

        //                          seconds of today   seconds of yesterday     seconds of day before that
        $runfrom          = $from - ($from % 86400)      - 86400                  - 86400;
        $params['from1']  = $runfrom;
        $params['from2']  = $runfrom;
        $sql              = str_replace("%%COURSECLAUSE%%", "", $sql);
        $sql              = str_replace("%%GROUPCLAUSE%%", "", $sql);
    }
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
function local_powerproexport_get_csv_headers() {
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

