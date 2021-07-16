<?php

defined('MOODLE_INTERNAL') || die();

/**
 * @param $oldversion
 * @return bool
 */
function xmldb_local_program_upgrade($oldversion)
{
    global $DB;
    $dbman = $DB->get_manager();

    /// Add a new column newcol to the mdl_myqtype_options
    if ($oldversion < 2015031200) {
        // Code to add the column, generated by the 'View PHP Code' option of the XMLDB editor.
    }

    return true;
}