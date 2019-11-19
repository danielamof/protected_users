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
 * The alias user form to the site's Data Protection Officer
 *
 * @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

use tool_protectedusers\api;
use tool_protectedusers\local\helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
\MoodleQuickForm::registerElementType('request_user_autocomplete',
    $CFG->dirroot . '/admin/tool/protectedusers/classes/form/request_user_autocomplete.php',
    '\\tool_protectedusers\\form\\request_user_autocomplete');

/**
 * The alias user form to the site's Data Protection Officer
 *
 * @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class tool_protectedusers_alias_user_form extends moodleform {

    /**
     * Form definition.
     *
     * @throws coding_exception
     */
    public function definition() {
        global $USER;
        $mform =& $this->_form;

        $options = [
            'ajax' => 'tool_dataprivacy/form-user-selector',
        ];
        $mform->addElement('request_user_autocomplete', 'userid', get_string('aliasusers:create:userid:field', 'tool_protectedusers'), [], $options);
        $mform->addRule('userid', null, 'required', null, 'client');

        $mform->setType('userid', PARAM_INT);

        $mform->addElement('request_user_autocomplete', 'alias', get_string('aliasusers:create:alias:field', 'tool_protectedusers'), [], $options);
        $mform->addRule('alias', null, 'required', null, 'client');

        $mform->setType('alias', PARAM_INT);

        // Request comments text area.
        $textareaoptions = ['cols' => 60, 'rows' => 10];
        $mform->addElement('textarea', 'comments', get_string('aliasusers:create:dpocomment:field', 'tool_protectedusers'), $textareaoptions);
        $mform->setType('type', PARAM_ALPHANUM);
        $mform->addHelpButton('comments', 'aliasusers:create:dpocomment:field', 'tool_protectedusers');

        // Action buttons.
        $this->add_action_buttons();

    }

    /**
     * Form validation.
     *
     * @param array $data
     * @param array $files
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public function validation($data, $files) {
        $errors = [];

        return $errors;
    }
}
