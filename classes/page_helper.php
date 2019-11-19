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
 * Page helper.
 *
 * @package   tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_protectedusers;
use context_system;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

class page_helper {

    /**
     * Sets up $PAGE for protected users admin pages.
     *
     * @param moodle_url $url The page URL.
     * @param string $title The page's title.
     * @param string $attachtoparentnode The parent navigation node where this page can be accessed from.
     * @param string $requiredcapability The required capability to view this page.
     */
    public static function setup(moodle_url $url, $title, $attachtoparentnode = '',
                                 $requiredcapability = 'tool/protectedusers:manageprotectedusers') {
        global $PAGE, $SITE;

        $context = context_system::instance();

        require_login();
        if (isguestuser()) {
            print_error('noguest');
        }

        // TODO Check that data privacy is enabled.
        require_capability($requiredcapability, $context);

        $PAGE->navigation->override_active_url($url);

        $PAGE->set_url($url);
        $PAGE->set_context($context);
        $PAGE->set_pagelayout('admin');
        $PAGE->set_title($title);
        $PAGE->set_heading($SITE->fullname);

        
    }
}
