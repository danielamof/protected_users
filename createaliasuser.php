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
 * Prints the aliser user form to the site's Data Protection Officer
 *
 * @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once('../../../config.php');
require_once('lib.php');
require_once('classes/api.php');
require_once('createaliasuser_form.php');

$manage = optional_param('manage', 0, PARAM_INT);

$url = new moodle_url('/admin/tool/protectedusers/createaliasuser.php');

$PAGE->set_url($url);

require_login();
if (isguestuser()) {
    print_error('noguest');
}

// Return URL and context.
$returnurl = new moodle_url($CFG->wwwroot . '/admin/tool/protectedusers/protectedusersmanager.php');
$context = context_system::instance();
// Make sure the user has the proper capability.
require_capability('tool/protectedusers:manageprotectedusers', $context);

$PAGE->set_context($context);

// If contactdataprotectionofficer is disabled, send the user back to the profile page, or the privacy policy page.
// That is, unless you have sufficient capabilities to perform this on behalf of a user.
if (!\tool_dataprivacy\api::is_site_dpo($USER->id)) {
    redirect($returnurl, get_string('contactdpoviaprivacypolicy', 'tool_dataprivacy'), 0, \core\output\notification::NOTIFY_ERROR);
}

$mform = new tool_protectedusers_alias_user_form($url->out(false));

// Data request cancelled.
if ($mform->is_cancelled()) {
    redirect($returnurl);
}

// Data request submitted.
if ($data = $mform->get_data()) {
    \tool_protectedusers\api::create_alias_users($data->userid, $data->alias, $data->comments);

    $foruser = core_user::get_user($data->userid);
    $redirectmessage = get_string('newaliasuser:aliascreatedforuser', 'tool_protectedusers', fullname($foruser));

    redirect($returnurl, $redirectmessage);
}

$heading_title = get_string('protectedusersmanager', 'tool_protectedusers');
$title = get_string('newaliasuser:title', 'tool_protectedusers');
$PAGE->set_heading($heading_title);
$PAGE->set_title($title);
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo $OUTPUT->box_start();
$mform->display();
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
