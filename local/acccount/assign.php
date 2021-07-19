<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package    local_acccount
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php'); // load config.php
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/roles/lib.php');
admin_externalpage_setup('acccountassignroles');
global $DB;

$manager = new \local_acccount\manager();

$PAGE->set_url(\local_acccount\manager::get_assign_roles_url());
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Assign Roles');

//$context = $PAGE->context;
$contextid = required_param('contextid', PARAM_INT);
$roleid    = optional_param('roleid', 0, PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);

// These are needed early because of tabs.php.
list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);
$overridableroles = get_overridable_roles($context, ROLENAME_BOTH);

// Process any incoming role assignments before printing the header.
if ($roleid) {

    // Create the user selector objects.
    $options = array('context' => $context, 'roleid' => $roleid);

    $potentialuserselector = core_role_get_potential_user_selector($context, 'addselect', $options);
    $currentuserselector = new core_role_existing_role_holders('removeselect', $options);

    // Process incoming role assignments.
    $errors = array();
    if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
        $userstoassign = $potentialuserselector->get_selected_users();
        if (!empty($userstoassign)) {

            foreach ($userstoassign as $adduser) {
                $allow = true;

                if ($allow) {
                    role_assign($roleid, $adduser->id, $context->id);
                }
            }

            $potentialuserselector->invalidate_selected_users();
            $currentuserselector->invalidate_selected_users();

            // Counts have changed, so reload.
            list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);
        }
    }

    // Process incoming role unassignments.
    if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
        $userstounassign = $currentuserselector->get_selected_users();
        if (!empty($userstounassign)) {

            foreach ($userstounassign as $removeuser) {
                // Unassign only roles that are added manually, no messing with other components!!!
                role_unassign($roleid, $removeuser->id, $context->id, '');
            }

            $potentialuserselector->invalidate_selected_users();
            $currentuserselector->invalidate_selected_users();

            // Counts have changed, so reload.
            list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);
        }
    }
}

echo $OUTPUT->header();




if ($roleid) {
    // Show UI for assigning a particular role to users.
    // Print a warning if we are assigning system roles.
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        echo $OUTPUT->notification(get_string('globalroleswarning', 'core_role'));
    }

    // Print the form.
    $assignurl = new moodle_url($PAGE->url, [
            'contextid' => $contextid,
            'roleid' => $roleid,
    ]);

?>
    <form id="assignform" method="post" action="<?php echo $assignurl ?>"><div>
            <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />

            <table id="assigningrole" summary="" class="admintable roleassigntable generaltable" cellspacing="0">
                <tr>
                    <td id="existingcell">
                        <p><label for="removeselect"><?php print_string('extusers', 'core_role'); ?></label></p>
                        <?php $currentuserselector->display() ?>
                    </td>
                    <td id="buttonscell">
                        <div id="addcontrols">
                            <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>"
                                   title="<?php print_string('add'); ?>" class="btn btn-secondary"/><br />
                        </div>

                        <div id="removecontrols">
                            <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>"
                                   title="<?php print_string('remove'); ?>" class="btn btn-secondary"/>
                        </div>
                    </td>
                    <td id="potentialcell">
                        <p><label for="addselect"><?php print_string('potusers', 'core_role'); ?></label></p>
                        <?php $potentialuserselector->display() ?>
                    </td>
                </tr>
            </table>
        </div></form>

<?php
    $PAGE->requires->js_init_call('M.core_role.init_add_assign_page');

    if (!empty($errors)) {
        $msg = '<p>';
        foreach ($errors as $e) {
            $msg .= $e.'<br />';
        }
        $msg .= '</p>';
        echo $OUTPUT->box_start();
        echo $OUTPUT->notification($msg);
        echo $OUTPUT->box_end();
    }

    // Print a form to swap roles, and a link back to the all roles list.
    echo '<div class="backlink">';

    $select = new single_select($PAGE->url, 'roleid', $nameswithcounts, $roleid, null);
    $select->label = get_string('assignanotherrole', 'core_role');
    echo $OUTPUT->render($select);
    echo '<p><a href="' . \local_acccount\manager::get_roles_url(['contextid' => $contextid]) . '">' . get_string('backtoallroles', 'core_role') . '</a></p>';
    echo '</div>';

}




echo $OUTPUT->footer();