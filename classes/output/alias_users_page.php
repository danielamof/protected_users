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
 * Class containing data for a user's alias users link.
 *
 * @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_protectedusers\output;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use dml_exception;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use single_select;
use stdClass;
use templatable;
use tool_protectedusers\local\helper;

/**
 * Class containing data for a user's alias users link.
 *
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class alias_users_page implements renderable, templatable {

    /** @var alias_users_table $table The alias users table. */
    protected $table;
    /**
     * Construct this renderable.
     *
     * @param alias_users_table $table The alias users table.
     */
    public function __construct($table) {
        $this->table = $table;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->newaliasusersurl = new moodle_url('/admin/tool/protectedusers/createaliasuser.php');

        if (!is_https()) {
            $httpwarningmessage = get_string('httpwarning', 'tool_protectedusers');
            $data->httpsite = array('message' => $httpwarningmessage, 'announce' => 1);
        }

        ob_start();
        $this->table->out($this->table->get_alias_users_per_page(), true);
        $aliasusers = ob_get_contents();
        ob_end_clean();

        $data->aliasusers = $aliasusers;
        return $data;
    }
}
