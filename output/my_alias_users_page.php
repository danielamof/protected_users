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
 * Class containing data for a user's alias users.
 *
 * @package   tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_protectedusers\output;
defined('MOODLE_INTERNAL') || die();

use action_menu;
use action_menu_link_secondary;
use coding_exception;
use context_user;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use tool_protectedusers\api;
use tool_protectedusers\alias_users;
use tool_protectedusers\external\alias_users_exporter;

/**
 * Class containing data for a user's data requests.
 *
 * @package   tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class my_alias_users_page implements renderable, templatable {

    /** @var array $requests List of data requests. */
    protected $requests = [];

    /**
     * Construct this renderable.
     *
     * @param data_request[] $requests
     */
    public function __construct($requests) {
        $this->requests = $requests;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        global $USER;

        $data = new stdClass();
       
        if (!is_https()) {
            $httpwarningmessage = get_string('httpwarning', 'tool_protectedusers');
            $data->httpsite = array('message' => $httpwarningmessage, 'announce' => 1);
        }

        $requests = [];
        foreach ($this->requests as $request) {
            $requestid = $request->get('id');
            $userid = $request->get('userid');

            $usercontext = context_user::instance($userid, IGNORE_MISSING);
            if (!$usercontext) {
                // Use the context system.
                $outputcontext = \context_system::instance();
            } else {
                $outputcontext = $usercontext;
            }

            $requestexporter = new alias_users_exporter($request, ['context' => $outputcontext]);
            $item = $requestexporter->export($output);

            $item->alias = $item->aliasuser->fullname;

            $loginasurl = new moodle_url('loginas.php',
                array('id' => 2, 'user' => $item->aliasuser->id, 'sesskey' => sesskey()));
            $item->loginasurl = html_entity_decode($loginasurl);
            //$node = new  core_user\output\myprofile\node('administration', 'loginas', get_string('loginas'), null, $loginasurl);
            //$tree->add_node($node);

            $candownload = false;
            $cancancel = true;

            // Prepare actions.
            $actions = [];
            if ($cancancel) {
                $cancelurl = new moodle_url('#');
                $canceldata = ['data-action' => 'cancel', 'data-requestid' => $requestid];
                $canceltext = get_string('cancelrequest', 'tool_dataprivacy');
                $actions[] = new action_menu_link_secondary($cancelurl, null, $canceltext, $canceldata);
            }
            if ($candownload && $usercontext) {
                $actions[] = api::get_download_link($usercontext, $requestid);
            }
            if (!empty($actions)) {
                $actionsmenu = new action_menu($actions);
                $actionsmenu->set_menu_trigger(get_string('actions'));
                $actionsmenu->set_owner_selector('request-actions-' . $requestid);
                $actionsmenu->set_alignment(\action_menu::TL, \action_menu::BL);
                $item->actions = $actionsmenu->export_for_template($output);
            }

            $requests[] = $item;
        }
        $data->requests = $requests;
        
        return $data;
    }
}
