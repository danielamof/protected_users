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
 * Strings for component 'tool_dataprivacy'
 *
 * @package    tool_dataprivacy
 * @copyright  2018 onwards Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Protected users';
$string['pluginname_help'] = 'Protected users plugin';
$string['protectedusersmanager'] = 'Protected users';
$string['protectedusers:manageprotectedusers'] = 'Manage protected users';
$string['protectedusers:managealiasusers'] = 'Manage alias users of protected users';


$string['aliasusers'] = 'Alias users';

$string['unlinkaliasuser'] = 'Delete alias user.';

$string['selectaliasuser'] = 'Please select alias users.';
$string['selectuseraliasuser'] = 'Select {$a->username}\'s alias users.';

$string['aliasusers:create:userid:field'] = 'Protected user';
$string['aliasusers:create:userid:field_help'] = 'The ID of the user to link the alias user';
$string['aliasusers:create:alias:field'] = 'Alias user';
$string['aliasusers:create:alias:field_help'] = 'The ID of the user to whom the request belongs';
$string['aliasusers:create:dpocomment:field'] = 'Comments';
$string['aliasusers:create:dpocomment:field_help'] = 'Any comments made by the site\'s privacy officer regarding the request.';
$string['aliasusers:create:timecreated:desc'] = 'The timestamp indicating when the DPO create the alias user.';

$string['httpwarning'] = 'Any data downloaded from this site may not be encrypted. Please contact your system administrator and request that they install SSL on this site.';

$string['selectaliasuser'] = '';
$string['selectuseraliasuser'] = 'Select an Alias user';

$string['noaliasuserslinked'] = 'You don\'t have any Alias user';
$string['noaliasusers'] = 'There are no alias users';
$string['user'] = 'User';
$string['aliasuser'] = 'Alias user';
$string['aliasuser:help'] = 'Click on the name of the Alias user to log in as such user.';
$string['timecreated'] = 'Created';
$string['comments'] = 'Comments';

$string['newaliasuser'] = 'New Alias user';
$string['newaliasuser:title'] = 'New alias user';
$string['newaliasuser:aliascreatedforuser'] = 'Alias user created for {$a}';

$string['contactdpoviaprivacypolicy'] = 'Please contact the privacy officer as described in the privacy policy.';
$string['privacyofficeronly'] = 'Only users who are assigned a privacy officer role ({$a}) have access to this content';
