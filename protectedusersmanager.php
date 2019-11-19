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
 * Prints the contact form to the site's Data Protection Officer
 *
 * @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once("../../../config.php");
require_once('lib.php');

require_login(null, false);

$perpage = optional_param('perpage', 0, PARAM_INT);

$url = new moodle_url('/admin/tool/protectedusers/protectedusersmanager.php');

$title = get_string('protectedusersmanager', 'tool_protectedusers');

\tool_protectedusers\page_helper::setup($url, $title, 'protectedusers', 'tool/protectedusers:manageprotectedusers');

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

if (\tool_dataprivacy\api::is_site_dpo($USER->id)) {

    $table = new \tool_protectedusers\output\alias_users_table(0);
    if (!empty($perpage)) {
        set_user_preference(\tool_protectedusers\local\helper::PREF_REQUEST_PERPAGE, $perpage);
    } else {
        $prefperpage = get_user_preferences(\tool_protectedusers\local\helper::PREF_REQUEST_PERPAGE);
        $perpage = ($prefperpage) ? $prefperpage : $table->get_alias_users_per_page_options()[0];
    }
    $table->set_alias_users_per_page($perpage);
    $table->baseurl = $url;

    $aliasuserslist = new tool_protectedusers\output\alias_users_page($table);
    $aliasuserslistoutput = $PAGE->get_renderer('tool_protectedusers');
    echo $aliasuserslistoutput->render($aliasuserslist);
} else {
    $dponamestring = implode (', ', tool_dataprivacy\api::get_dpo_role_names());
    $message = get_string('privacyofficeronly', 'tool_protectedusers', $dponamestring);
    echo $OUTPUT->notification($message, 'error');
}

echo $OUTPUT->footer();
