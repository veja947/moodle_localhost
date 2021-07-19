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

$roleid    = optional_param('roleid', 0, PARAM_INT);


// Process any incoming role assignments before printing the header.
if ($roleid) {
    $context = $PAGE->context;

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

echo $OUTPUT->footer();