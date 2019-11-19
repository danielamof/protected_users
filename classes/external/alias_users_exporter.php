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
 * Class for exporting user evidence with all competencies.
 *
 *  @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_protectedusers\external;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use core\external\persistent_exporter;
use core_user;
use core_user\external\user_summary_exporter;
use dml_exception;
use moodle_exception;
use renderer_base;
use tool_protectedusers\api;
use tool_protectedusers\alias_users;
use tool_protectedusers\local\helper;

/**
 * Class for exporting user evidence with all competencies.
 *
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class alias_users_exporter extends persistent_exporter {

    /**
     * Class definition.
     *
     * @return string
     */
    protected static function define_class() {
        return alias_users::class;
    }

    /**
     * Related objects definition.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    /**
     * Other properties definition.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'foruser' => [
                'type' => user_summary_exporter::read_properties_definition(),
            ],
            'aliasuser' => [
                'type' => user_summary_exporter::read_properties_definition(),
                'optional' => false
            ],
            'dpouser' => [
                'type' => user_summary_exporter::read_properties_definition(),
                'optional' => true
            ],
            'messagehtml' => [
                'type' => PARAM_RAW,
                'optional' => true
            ],
        ];
    }

    /**
     * Assign values to the defined other properties.
     *
     * @param renderer_base $output The output renderer object.
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    protected function get_other_values(renderer_base $output) {
        $values = [];

        $foruserid = $this->persistent->get('userid');
        $user = core_user::get_user($foruserid, '*', MUST_EXIST);
        $userexporter = new user_summary_exporter($user);
        $values['foruser'] = $userexporter->export($output);

        $aliasid = $this->persistent->get('alias');
        $user = core_user::get_user($aliasid, '*', MUST_EXIST);
        $userexporter = new user_summary_exporter($user);
        $values['aliasuser'] = $userexporter->export($output);

        $dpoid = $this->persistent->get('dpo');
        $user = core_user::get_user($dpoid, '*', MUST_EXIST);
        $userexporter = new user_summary_exporter($user);
        $values['dpouser'] = $userexporter->export($output);

        $values['messagehtml'] = text_to_html($this->persistent->get('dpocomment'));

        return $values;
    }
}
