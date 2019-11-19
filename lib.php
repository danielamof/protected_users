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
 * Protected users plugin library
 * 
 * @package   tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_user\output\myprofile\tree;

defined('MOODLE_INTERNAL') || die();

/**
 * Add nodes to myprofile page.
 *
 * @param tree $tree Tree object
 * @param stdClass $user User object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function tool_protectedusers_myprofile_navigation(tree $tree, $user, $iscurrentuser, $course) {
    global $PAGE, $USER;

    // Get the Privacy and policies category.
    if (!array_key_exists('privacyandpolicies', $tree->__get('categories'))) {
        // Create the category.
        $categoryname = get_string('privacyandpolicies', 'admin');
        $category = new core_user\output\myprofile\category('privacyandpolicies', $categoryname, 'contact');
        $tree->add_category($category);
    } else {
        // Get the existing category.
        $category = $tree->__get('categories')['privacyandpolicies'];
    }

    // Show list of alias users and login links.
    $renderer = $PAGE->get_renderer('tool_protectedusers');
    $url = new moodle_url('/admin/tool/protectedusers/myaliasusers.php');
    $node = new core_user\output\myprofile\node('privacyandpolicies', 'aliasusers',
        get_string('aliasusers', 'tool_protectedusers'), null, $url);
    $category->add_node($node);

    // Add the Privacy category to the tree if it's not empty and it doesn't exist.
    $nodes = $category->nodes;
    if (!empty($nodes)) {
        if (!array_key_exists('privacyandpolicies', $tree->__get('categories'))) {
            $tree->add_category($category);
        }
        return true;
    }

    return false;
}

/**
 * Custom environment check invoked from environment.xml.
 *
 * Make sure that the plugin is not present in Moodle that has it already merged into the core.
 *
 * @param environment_results $result
 * @return environment_results|null
 */
function tool_protectedusers_version_check(environment_results $result) {
    global $CFG;
    if (!empty($CFG->tool_protectedusers_disable_version_check)) {
        return null;
    }
    $version = null;
    $branch = null;
    require($CFG->dirroot.'/version.php');
    if ($branch >= 33 && $version >= 2019080315.012) {
        $result->setStatus(false);
        return $result;
    } else {
        return null;
    }
}
