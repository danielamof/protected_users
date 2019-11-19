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
 * Contains the class used for the displaying the alias users table.
 *
 * @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_protectedusers\output;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use action_menu;
use action_menu_link_secondary;
use coding_exception;
use dml_exception;
use html_writer;
use moodle_url;
use stdClass;
use table_sql;
use tool_protectedusers\api;
use tool_protectedusers\external\alias_users_exporter;

defined('MOODLE_INTERNAL') || die;

/**
 * The class for displaying the data requests table.
 *
 * @copyright  2018 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class alias_users_table extends table_sql {

    /** @var int The user ID. */
    protected $userid = 0;

    /** @var \tool_protectedusers\alias_users[] Array of data request persistents. */
    protected $aliasusers = [];

    /** @var int The number of data request to be displayed per page. */
    protected $perpage;

    /** @var int[] The available options for the number of data request to be displayed per page. */
    protected $perpageoptions = [25, 50, 100, 250];

    /**
     * alias_users_table constructor.
     *
     * @param int $userid The user ID
     * @throws coding_exception
     */
    public function __construct($userid = 0) {
        parent::__construct('alias-users-table');

        $this->userid = $userid;

        $checkboxattrs = [
            'title' => get_string('selectall'),
            'data-action' => 'selectall'
        ];

        $columnheaders = [
            //'select' => html_writer::checkbox('selectall', 1, false, null, $checkboxattrs),
            'userid' => get_string('user', 'tool_protectedusers'),
            'alias' => get_string('aliasuser', 'tool_protectedusers'),
            'timecreated' => get_string('timecreated', 'tool_protectedusers'),
            'comments' => get_string('comments', 'tool_protectedusers'),
            'actions' => '',
        ];

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));
        $this->no_sorting('select', 'actions');
    }

    /**
     * The select column.
     *
     * @param stdClass $data The row data.
     * @return string
     * @throws \moodle_exception
     * @throws coding_exception
     */
    /*public function col_select($data) {

        $stringdata = [
            'username' => $data->foruser->fullname
        ];

        return \html_writer::checkbox('aliasusersids[]', $data->id, false, '',
                ['class' => 'selectaliasuser', 'title' => get_string('selectuseraliasuser',
                'tool_protectedusers', $stringdata)]);
    }*/

    /**
     * The user column.
     *
     * @param stdClass $data The row data.
     * @return mixed
     */
    public function col_userid($data) {
        $user = $data->foruser;
        return html_writer::link($user->profileurl, $user->fullname, ['title' => get_string('viewprofile')]);
    }

    /**
     * The context information column.
     *
     * @param stdClass $data The row data.
     * @return string
     */
    public function col_timecreated($data) {
        return userdate($data->timecreated);
    }

    /**
     * The alias user user's column.
     *
     * @param stdClass $data The row data.
     * @return mixed
     */
    public function col_alias($data) {
        $user = $data->aliasuser;
        return html_writer::link($user->profileurl, $user->fullname, ['title' => get_string('viewprofile')]);
    }

    /**
     * The comments column.
     *
     * @param stdClass $data The row data.
     * @return string
     */
    public function col_comments($data) {
        return shorten_text($data->dpocomment, 60);
    }

    /**
     * The actions column.
     *
     * @param stdClass $data The row data.
     * @return string
     */
    public function col_actions($data) {
        global $OUTPUT;

        $requestid = $data->id;
        $persistent = $this->aliasusers[$requestid];

        // Prepare actions.
        $actions = [];

        // Unlink action.
        
        $actionurl = new moodle_url('#');
        $actiondata['data-action'] = 'deny';
        $actiondata['data-requestid'] = $data->id;
        $actiontext = get_string('unlinkaliasuser', 'tool_protectedusers');
        $actions[] = new action_menu_link_secondary($actionurl, null, $actiontext, $actiondata);

        $actionsmenu = new action_menu($actions);
        $actionsmenu->set_menu_trigger(get_string('actions'));
        $actionsmenu->set_owner_selector('alias-users-actions-' . $requestid);
        $actionsmenu->set_alignment(\action_menu::TL, \action_menu::BL);
        $actionsmenu->set_constraint('[data-region=alias-users-table] > .no-overflow');

        return $OUTPUT->render($actionsmenu);
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     * @throws dml_exception
     * @throws coding_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $PAGE;

        // Set dummy page total until we fetch full result set.
        $this->pagesize($pagesize, $pagesize + 1);

        $sort = $this->get_sql_sort();

        // Get data requests from the given conditions.
        $aliasusers = api::get_alias_users($this->userid, $sort, $this->get_page_start(), $this->get_page_size());

        // Count data requests from the given conditions.
        $total = api::get_alias_users_count($this->userid);
        $this->pagesize($pagesize, $total);

        $this->rawdata = [];
        $context = \context_system::instance();
        $renderer = $PAGE->get_renderer('tool_protectedusers');

        foreach ($aliasusers as $persistent) {
            $this->aliasusers[$persistent->get('id')] = $persistent;
            $exporter = new alias_users_exporter($persistent, ['context' => $context]);
            $this->rawdata[] = $exporter->export($renderer);
        }

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * Override default implementation to display a more meaningful information to the user.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;
        echo $this->render_reset_button();
        $this->print_initials_bar();
        $message = get_string('noaliasusers', 'tool_protectedusers');
        echo $OUTPUT->notification($message, 'warning');
    }

    /**
     * Override the table's show_hide_link method to prevent the show/hide links from rendering.
     *
     * @param string $column the column name, index into various names.
     * @param int $index numerical index of the column.
     * @return string HTML fragment.
     */
    protected function show_hide_link($column, $index) {
        return '';
    }

    /**
     * Set the number of data request records to be displayed per page.
     *
     * @param int $perpage The number of data request records.
     */
    public function set_alias_users_per_page(int $perpage) {
        $this->perpage = $perpage;
    }

    /**
     * Get the number of data request records to be displayed per page.
     *
     * @return int The number of data request records.
     */
    public function get_alias_users_per_page() : int {
        return $this->perpage;
    }

    /**
     * Set the available options for the number of data request to be displayed per page.
     *
     * @param array $perpageoptions The available options for the number of data request to be displayed per page.
     */
    public function set_alias_users_per_page_options(array $perpageoptions) {
        $this->$perpageoptions = $perpageoptions;
    }

    /**
     * Get the available options for the number of data request to be displayed per page.
     *
     * @return array The available options for the number of data request to be displayed per page.
     */
    public function get_alias_users_per_page_options() : array {
        return $this->perpageoptions;
    }
}
