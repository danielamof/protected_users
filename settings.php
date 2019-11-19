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
 * Adds Protected users privacy-related settings.
 *
 * @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Restrict config links to the DPO.
if (tool_dataprivacy\api::is_site_dpo($USER->id)) {
    // Link that leads to the data requests management page.
    $ADMIN->add('privacy', new admin_externalpage('protectedusersmanager', get_string('protectedusersmanager', 'tool_protectedusers'),
        new moodle_url('/admin/tool/protectedusers/protectedusersmanager.php'), 'tool/protectedusers:manageprotectedusers')
    );
}
