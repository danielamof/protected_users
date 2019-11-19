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
 * Class containing helper methods for processing alias users.
 *
 * @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_protectedusers;

use coding_exception;
use context_helper;
use context_system;
use core\invalid_persistent_exception;
use core\message\message;
use core\task\manager;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist_collection;
use core_user;
use dml_exception;
use moodle_exception;
use moodle_url;
use required_capability_exception;
use stdClass;
use tool_protectedusers\local\helper;
use tool_protectedusers\alias_users;

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing helper methods for processing alias users.
 *
 * @package    tool_protectedusers
 * @copyright  2018 onwards Daniel Amo <danielamo@gmail.com>, Marc Alier <granludo@gmail.com>
 * @author     Amo Daniel, https://eduliticas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Lodges a data request and sends the request details to the site Data Protection Officer(s).
     *
     * @param int $foruser The user whom the alias user is created for.
     * @param int $alias The user to be linked to $foruser.
     * @param string $comments Request comments.
     * @return alias_users
     * @throws invalid_persistent_exception
     * @throws coding_exception
     */
    public static function create_alias_users($foruser, $alias, $comments = '') {
        global $USER;

        $aliasusers = new alias_users();
        // The user the alias user is linked for.
        $aliasusers->set('userid', $foruser);
        // The alias user to be linked.
        $aliasusers->set('alias', $alias);
        // Set dpo.
        $aliasusers->set('dpo', $USER->id);
        // Set dpo comments.
        $aliasusers->set('dpocomment', $comments);

        // Store subject access request.
        $aliasusers->create();

        return $aliasusers;
    }

    /**
     * Fetches the list of the alias users.
     *
     * If user ID is provided, it fetches the alias users for the user.
     * Otherwise, it fetches all of the alias users, provided that the user has the capability to manage alias users.
     * (e.g. Users with the Data Protection Officer roles)
     *
     * @param int $userid The User ID.
     * @param string $sort The order by clause.
     * @param int $offset Amount of records to skip.
     * @param int $limit Amount of records to fetch.
     * @return alias_users[]
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_alias_users($userid = 0, $sort = '', $offset = 0, $limit = 0) {
        global $DB, $USER;
        $results = [];
        $sqlparams = [];
        $sqlconditions = [];

        // Set default sort.
        if (empty($sort)) {
            $sort = 'userid ASC, alias ASC, timemodified ASC';
        }

        if ($userid) {
            // Get the data requests for the user or data requests made by the user.
            $sqlconditions[] = "(userid = :userid)";
            $params = [
                'userid' => $userid
            ];

            // Build a list of user IDs that the user is allowed to make data requests for.
            // Of course, the user should be included in this list.
            $alloweduserids = [$userid];
            
            list($insql, $inparams) = $DB->get_in_or_equal($alloweduserids, SQL_PARAMS_NAMED);
            $sqlconditions[] .= "userid $insql";
            $select = implode(' AND ', $sqlconditions);
            $params = array_merge($params, $inparams, $sqlparams);

            $results = alias_users::get_records_select($select, $params, $sort, '*', $offset, $limit);
        } else {
            // If the current user is one of the site's Data Protection Officers, then fetch all data requests.
            if (\tool_dataprivacy\api::is_site_dpo($USER->id)) {
                if (!empty($sqlconditions)) {
                    $select = implode(' AND ', $sqlconditions);
                    $results = alias_users::get_records_select($select, $sqlparams, $sort, '*', $offset, $limit);
                } else {
                    $results = alias_users::get_records(null, $sort, '', $offset, $limit);
                }
            }
        }

        return $results;
    }

    /**
     * Fetches the count of data request records based on the given parameters.
     *
     * @param int $userid The User ID.
     * @return int
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_alias_users_count($userid = 0) {
        global $DB, $USER;
        $count = 0;
        $sqlparams = [];
        $sqlconditions = [];
        if ($userid) {
            // Get the alias users for the user.
            $sqlconditions[] = "(userid = :userid)";
            $params = [
                'userid' => $userid
            ];

            // Build a list of user IDs that the user is allowed to make data requests for.
            // Of course, the user should be included in this list.
            $alloweduserids = [$userid];
            
            list($insql, $inparams) = $DB->get_in_or_equal($alloweduserids, SQL_PARAMS_NAMED);
            $sqlconditions[] .= "userid $insql";
            $select = implode(' AND ', $sqlconditions);
            $params = array_merge($params, $inparams, $sqlparams);

            $count = alias_users::count_records_select($select, $params);
        } else {
            // If the current user is one of the site's Data Protection Officers, then fetch all data requests.
            if (\tool_dataprivacy\api::is_site_dpo($USER->id)) {
                if (!empty($sqlconditions)) {
                    $select = implode(' AND ', $sqlconditions);
                    $count = alias_users::count_records_select($select, $sqlparams);
                } else {
                    $count = alias_users::count_records();
                }
            }
        }

        return $count;
    }

    /**
     * Fetches a request based on the request ID.
     *
     * @param int $requestid The request identifier
     * @return alias_users
     */
    public static function get_request($requestid) {
        return new alias_users($requestid);
    }

    /**
     * Delete alias users based on the request ID.
     *
     * @param int $requestid The request identifier
     * @return bool
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_persistent_exception
     * @throws required_capability_exception
     * @throws moodle_exception
     */
    public static function deny_data_request($requestid) {
        global $USER, $DB;

        /*if (!self::can_manage_data_requests($USER->id)) {
            $context = context_system::instance();
            throw new required_capability_exception($context, 'tool/dataprivacy:managedatarequests', 'nopermissions', '');
        }*/
        
        //array_keys($selectedaliasusers);
        //list($insql, $inparams) = $DB->get_in_or_equal($requestid);

        $delete = "DELETE FROM {" . self::TABLE . "}
        WHERE id = ".$requestid;

        return $DB->execute($delete);
    }

}