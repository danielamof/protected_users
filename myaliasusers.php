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
 * Prints the associated alias users
 *
 * @package   tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once("../../../config.php");
require_once('lib.php');

$courseid = optional_param('course', 0, PARAM_INT);

$url = new moodle_url('/admin/tool/protectedusers/myaliasusers.php');
if ($courseid) {
    $url->param('course', $courseid);
}

$PAGE->set_url($url);

require_login();
if (isguestuser()) {
    print_error('noguest');
}

$usercontext = context_user::instance($USER->id);
$PAGE->set_context($usercontext);

// Return URL.
$params = ['id' => $USER->id];
if ($courseid) {
    $params['course'] = $courseid;
}
$returnurl = new moodle_url('/user/profile.php', $params);

$title = get_string('aliasusers', 'tool_protectedusers');
$PAGE->set_heading($title);
$PAGE->set_title($title);
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$requests = tool_protectedusers\api::get_alias_users($USER->id, 'timecreated DESC');
$requestlist = new tool_protectedusers\output\my_alias_users_page($requests);
$requestlistoutput = $PAGE->get_renderer('tool_protectedusers');

echo $requestlistoutput->render($requestlist);

echo $OUTPUT->footer();
