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
 * Class for loading/storing alias users from the DB.
 *
 * @package   tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_protectedusers;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Class for loading/storing alias users from the DB.
 *
 * @package   tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class alias_users extends persistent {

    /** The table name this persistent object maps to. */
    const TABLE = 'tool_pusers_aliasusers';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'comments' => [
                'type' => PARAM_TEXT,
                'default' => ''
            ],
            'commentsformat' => [
                'choices' => [
                    FORMAT_HTML,
                    FORMAT_MOODLE,
                    FORMAT_PLAIN,
                    FORMAT_MARKDOWN
                ],
                'type' => PARAM_INT,
                'default' => FORMAT_PLAIN
            ],
            'userid' => [
                'default' => 0,
                'type' => PARAM_INT
            ],
            'alias' => [
                'default' => 0,
                'type' => PARAM_INT
            ],
            'dpo' => [
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED
            ],
            'dpocomment' => [
                'default' => '',
                'type' => PARAM_TEXT,
                'null' => NULL_ALLOWED
            ],
            'dpocommentformat' => [
                'choices' => [
                    FORMAT_HTML,
                    FORMAT_MOODLE,
                    FORMAT_PLAIN,
                    FORMAT_MARKDOWN
                ],
                'type' => PARAM_INT,
                'default' => FORMAT_PLAIN
            ],
        ];
    }
}
