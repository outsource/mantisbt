<?php
# MantisBT - A PHP based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Manage configuration for workflow Config
 *
 * @package MantisBT
 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright (C) 2002 - 2012  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 *
 * @uses core.php
 * @uses access_api.php
 * @uses authentication_api.php
 * @uses config_api.php
 * @uses constant_inc.php
 * @uses form_api.php
 * @uses helper_api.php
 * @uses html_api.php
 * @uses lang_api.php
 * @uses print_api.php
 * @uses project_api.php
 * @uses string_api.php
 * @uses user_api.php
 */

require_once( '../core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'form_api.php' );
require_api( 'helper_api.php' );
require_api( 'html_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'project_api.php' );
require_api( 'string_api.php' );
require_api( 'user_api.php' );

auth_reauthenticate();

html_page_top( _( 'Workflow Thresholds' ) );

print_manage_menu( 'adm_permissions_report.php' );
print_manage_config_menu( 'config_work_threshold_page.php' );

$t_user = auth_get_current_user_id();
$t_project_id = helper_get_current_project();
$t_access = user_get_access_level( $t_user, $t_project_id );
$t_show_submit = false;

$t_access_levels = MantisEnum::getAssocArrayIndexedByValues( config_get( 'access_levels_enum_string' ) );

$t_overrides = array();

/**
 * Set overrides
 * @param string $p_config config value
 */
function set_overrides( $p_config ) {
   global $t_overrides;
   if ( !in_array( $p_config, $t_overrides ) ) {
	   $t_overrides[] = $p_config;
   }
}

/**
 * Section header
 * @param string $p_section_name section name
 */
function get_section_begin_mcwt( $p_section_name ) {
	global $t_access_levels;

	echo '<table class="width100">';
	echo '<tr><td class="form-title" colspan="' . ( count( $t_access_levels ) + 2 ) . '">' . $p_section_name . '</td></tr>' . "\n";
	echo '<tr><td class="form-title" width="40%" rowspan="2">' . _( 'Capability' ) . '</td>';
	echo '<td class="form-title" style="text-align:center"  width="40%" colspan="' . count( $t_access_levels ) . '">' . _( 'Access Levels' ) . '</td>';
	echo '<td class="form-title" style="text-align:center" rowspan="2">&#160;' . _( 'Who can alter this value' ) . '&#160;</td></tr><tr>';
	foreach( $t_access_levels as $t_access_level => $t_access_label ) {
		echo '<td class="form-title" style="text-align:center">&#160;' . MantisEnum::getLabel( lang_get( 'access_levels_enum_string' ), $t_access_level ) . '&#160;</td>';
	}
	echo '</tr>' . "\n";
}

/**
 * Get row
 * @param string $p_caption caption
 * @param string $p_threshold threshold
 * @param bool $p_all_projects_only all projects only
 */
function get_capability_row( $p_caption, $p_threshold, $p_all_projects_only=false ) {
	global $t_user, $t_project_id, $t_show_submit, $t_access_levels;

	$t_file = config_get_global( $p_threshold );
	if ( !is_array( $t_file ) ) {
		$t_file_exp = array();
		foreach( $t_access_levels as $t_access_level => $t_label ) {
			if ( $t_access_level >= $t_file ) {
				$t_file_exp[] = $t_access_level;
			}
		}
	} else {
		$t_file_exp = $t_file;
	}

	$t_global = config_get( $p_threshold, null, null, ALL_PROJECTS );
	if ( !is_array( $t_global ) ) {
		$t_global_exp = array();
		foreach( $t_access_levels as $t_access_level => $t_label ) {
			if ( $t_access_level >= $t_global ) {
				$t_global_exp[] = $t_access_level;
			}
		}
	} else {
		$t_global_exp = $t_global;
	}

	$t_project = config_get( $p_threshold );
	if ( !is_array( $t_project ) ) {
		$t_project_exp = array();
		foreach( $t_access_levels as $t_access_level => $t_label ) {
			if ( $t_access_level >= $t_project ) {
				$t_project_exp[] = $t_access_level;
			}
		}
	} else {
		$t_project_exp = $t_project;
	}

	$t_can_change = access_has_project_level( config_get_access( $p_threshold ), $t_project_id, $t_user )
			  && ( ( ALL_PROJECTS == $t_project_id ) || !$p_all_projects_only );

	echo '<tr><td>' . string_display( $p_caption ) . '</td>';
	foreach( $t_access_levels as $t_access_level => $t_access_label ) {
		$t_file = in_array( $t_access_level, $t_file_exp );
		$t_global = in_array( $t_access_level, $t_global_exp );
		$t_project = in_array( $t_access_level, $t_project_exp ) ;

		$t_colour = '';
		if ( $t_global != $t_file ) {
			$t_colour = ' class="colour-global" '; # all projects override
			if ( $t_can_change ) {
				set_overrides( $p_threshold );
			}
		}
		if ( $t_project != $t_global ) {
			$t_colour = ' class="colour-project" '; # project overrides
			if ( $t_can_change ) {
				set_overrides( $p_threshold );
			}
		}

		if ( $t_can_change ) {
			$t_checked = $t_project ? "checked=\"checked\"" : "";
			$t_value = "<input type=\"checkbox\" name=\"flag_thres_" . $p_threshold . "[]\" value=\"$t_access_level\" $t_checked />";
			$t_show_submit = true;
		} else {
			if ( $t_project ) {
				$t_value = '<img src="'.helper_mantis_url( 'themes/' . config_get( 'theme' ) . '/images/ok.png' ).'" width="20" height="15" alt="X" title="X" />';
			} else {
				$t_value = '&#160;';
			}
		}
		echo '<td class="center"' . $t_colour . '>' . $t_value . '</td>';
	}
	if ( $t_can_change ) {
		echo '<td> <select name="access_' . $p_threshold . '">';
		print_enum_string_option_list( 'access_levels', config_get_access( $p_threshold ) );
		echo '</select> </td>';
	} else {
		echo '<td>' . MantisEnum::getLabel( lang_get( 'access_levels_enum_string' ), config_get_access( $p_threshold ) ) . '&#160;</td>';
	}

	echo '</tr>' . "\n";
}

/**
 * Get boolean row
 * @param string $p_caption caption
 * @param string $p_threshold threshold
 * @param bool $p_all_projects_only all projects only
 */
function get_capability_boolean( $p_caption, $p_threshold, $p_all_projects_only=false ) {
	global $t_user, $t_project_id, $t_show_submit, $t_access_levels;

	$t_file = config_get_global( $p_threshold );
	$t_global = config_get( $p_threshold, null, null, ALL_PROJECTS );
	$t_project = config_get( $p_threshold );

	$t_can_change = access_has_project_level( config_get_access( $p_threshold ), $t_project_id, $t_user )
			  && ( ( ALL_PROJECTS == $t_project_id ) || !$p_all_projects_only );

	$t_colour = '';
	if ( $t_global != $t_file ) {
		$t_colour = ' class="colour-global" '; # all projects override
		if ( $t_can_change ) {
			set_overrides( $p_threshold );
		}
	}
	if ( $t_project != $t_global ) {
		$t_colour = ' class="colour-project" '; # project overrides
		if ( $t_can_change ) {
			set_overrides( $p_threshold );
		}
	}

	echo '<tr><td>' . string_display( $p_caption ) . '</td>';
	if ( $t_can_change ) {
		$t_checked = ( ON == config_get( $p_threshold ) ) ? "checked=\"checked\"" : "";
		$t_value = "<input type=\"checkbox\" name=\"flag_" . $p_threshold . "\" value=\"1\" $t_checked />";
		$t_show_submit = true;
	} else {
		if ( ON == config_get( $p_threshold ) ) {
			$t_value = '<img src="'.helper_mantis_url( 'themes/' . config_get( 'theme' ) . '/images/ok.png' ).'" width="20" height="15" title="X" alt="X" />';
		} else {
			$t_value = '&#160;';
		}
	}
	echo '<td' . $t_colour . '>' . $t_value . '</td><td class="left" colspan="' . ( count( $t_access_levels ) - 1 ). '"></td>';

	if ( $t_can_change ) {
		echo '<td><select name="access_' . $p_threshold . '">';
		print_enum_string_option_list( 'access_levels', config_get_access( $p_threshold ) );
		echo '</select> </td>';
	} else {
		echo '<td>' . MantisEnum::getLabel( lang_get( 'access_levels_enum_string' ), config_get_access( $p_threshold ) ) . '&#160;</td>';
	}

	echo '</tr>' . "\n";
}

/**
 * Get enum row
 * @param string $p_caption caption
 * @param string $p_threshold threshold
 * @param string $p_enum enum
 * @param bool $p_all_projects_only all projects only
 */
function get_capability_enum( $p_caption, $p_threshold, $p_enum, $p_all_projects_only=false ) {
	global $t_user, $t_project_id, $t_show_submit, $t_access_levels;

	$t_file = config_get_global( $p_threshold );
	$t_global = config_get( $p_threshold, null, null, ALL_PROJECTS );
	$t_project = config_get( $p_threshold );

	$t_can_change = access_has_project_level( config_get_access( $p_threshold ), $t_project_id, $t_user )
			  && ( ( ALL_PROJECTS == $t_project_id ) || !$p_all_projects_only );

	$t_colour = '';
	if ( $t_global != $t_file ) {
		$t_colour = ' class="colour-global" '; # all projects override
		if ( $t_can_change ) {
			set_overrides( $p_threshold );
		}
	}
	if ( $t_project != $t_global ) {
		$t_colour = ' class="colour-project" '; # project overrides
		if ( $t_can_change ) {
			set_overrides( $p_threshold );
		}
	}

	echo '<tr><td>' . string_display( $p_caption ) . '</td>';
	if ( $t_can_change ) {
		echo '<td class="left" colspan="3"' . $t_colour . '><select name="flag_' . $p_threshold . '">';
		print_enum_string_option_list( $p_enum, config_get( $p_threshold ) );
		echo '</select></td><td colspan="' . ( count( $t_access_levels ) - 3 ) . '"></td>';
		$t_show_submit = true;
	} else {
		$t_value = MantisEnum::getLabel( lang_get( $p_enum . '_enum_string' ), config_get( $p_threshold ) ) . '&#160;';
		echo '<td class="left" colspan="3"' . $t_colour . '>' . $t_value . '</td><td colspan="' . ( count( $t_access_levels ) - 3 ) . '"></td>';
	}

	if ( $t_can_change ) {
		echo '<td><select name="access_' . $p_threshold . '">';
		print_enum_string_option_list( 'access_levels', config_get_access( $p_threshold ) );
		echo '</select> </td>';
	} else {
		echo '<td>' . MantisEnum::getLabel( lang_get( 'access_levels_enum_string' ), config_get_access( $p_threshold ) ) . '&#160;</td>';
	}

	echo '</tr>' . "\n";
}

/**
 * Get section end
 */
function get_section_end() {
	echo '</table><br />' . "\n";
}

echo "<br /><br />\n";

if ( ALL_PROJECTS == $t_project_id ) {
	$t_project_title = _( 'Note: These configurations affect all projects, unless overridden at the project level.' );
} else {
	$t_project_title = sprintf( _( 'Note: These configurations affect only the %1 project.' ) , string_display( project_get_name( $t_project_id ) ) );
}
echo '<p class="bold">' . $t_project_title . '</p>' . "\n";
echo '<p>' . _( 'In the table below, the following color code applies:' ) . '<br />';
if ( ALL_PROJECTS <> $t_project_id ) {
	echo '<span class="colour-project">' . _( 'Project setting overrides others.' ) .'</span><br />';
}
echo '<span class="colour-global">' . _( 'All Project settings override default configuration.' ) . '</span></p>';

echo "<form id=\"mail_config_action\" method=\"post\" action=\"config_work_threshold_set.php\">\n";
echo form_security_field( 'manage_config_work_threshold_set' );

# Issues
get_section_begin_mcwt( _( 'Issues' ) );
get_capability_row( _( 'Report an issue' ), 'report_bug_threshold' );
get_capability_enum( _( 'Status to which a new issue is set' ), 'bug_submit_status', 'status' );
get_capability_row( _( 'Update an issue' ), 'update_bug_threshold' );
get_capability_boolean( _( 'Allow Reporter to close Issue' ), 'allow_reporter_close' );
get_capability_row( _( 'Monitor an issue' ), 'monitor_bug_threshold' );
get_capability_row( _( 'Handle an issue' ), 'handle_bug_threshold' );
get_capability_row( _( 'Assign an issue' ), 'update_bug_assign_threshold' );
get_capability_row( _( 'Move an issue' ), 'move_bug_threshold', true );
get_capability_row( _( 'Delete an issue' ), 'delete_bug_threshold' );
get_capability_row( _( 'Reopen an issue' ), 'reopen_bug_threshold' );
get_capability_boolean( _( 'Allow Reporter to re-open Issue' ), 'allow_reporter_reopen' );
get_capability_enum( _( 'Status to which a reopened issue is set' ), 'bug_reopen_status', 'status' );
get_capability_enum( _( 'Resolution to which a reopened issue is set' ), 'bug_reopen_resolution', 'resolution' );
get_capability_enum( _( 'Status where an issue is considered resolved' ), 'bug_resolved_status_threshold', 'status' );
get_capability_enum( _( 'Status where an issue becomes read only' ), 'bug_readonly_status_threshold', 'status' );
get_capability_row( _( 'Update readonly issues' ), 'update_readonly_bug_threshold' );
get_capability_row( _( 'Update issue status' ), 'update_bug_status_threshold' );
get_capability_row( _( 'View private issues' ), 'private_bug_threshold' );
get_capability_row( _( 'Set view state when reporting a new issue or note' ), 'set_view_status_threshold' );
get_capability_row( _( 'Change view state of existing issue or note' ), 'change_view_status_threshold' );
get_capability_row( _( 'Show list of users monitoring issue' ), 'show_monitor_list_threshold' );
get_capability_boolean( _( 'Set status on assignment of Handler' ), 'auto_set_status_to_assigned' );
get_capability_enum( _( 'Status to set auto-assigned issues to' ), 'bug_assigned_status', 'status' );
get_capability_boolean( lang_get( 'limit_access' ), 'limit_reporters', true );
get_section_end();

# Notes
get_section_begin_mcwt( _( 'Notes' ) );
get_capability_row( _( 'Add notes' ), 'add_bugnote_threshold' );
get_capability_row( lang_get( 'edit_others_bugnotes' ), 'update_bugnote_threshold' );
get_capability_row( _( 'Edit own notes' ), 'bugnote_user_edit_threshold' );
get_capability_row( lang_get( 'delete_others_bugnotes' ), 'delete_bugnote_threshold' );
get_capability_row( _( 'Delete own notes' ), 'bugnote_user_delete_threshold' );
get_capability_row( lang_get( 'view_private_notes' ), 'private_bugnote_threshold' );
get_capability_row( _( 'Change view state of own notes' ), 'bugnote_user_change_view_state_threshold' );
get_section_end();

# Others
get_section_begin_mcwt( _( 'Others' ) );
get_capability_row( _( 'View' ) . ' ' . _( 'Change Log' ), 'view_changelog_threshold' );
get_capability_row( _( 'View' ) . ' ' . _( 'Assigned To' ), 'view_handler_threshold' );
get_capability_row( _( 'View' ) . ' ' . _( 'Issue History' ), 'view_history_threshold' );
get_capability_row( _( 'Send reminders' ), 'bug_reminder_threshold' );
get_section_end();


if ( $t_show_submit ) {
	echo "<input type=\"submit\" class=\"button\" value=\"" . _( 'Update Configuration' ) . "\" />\n";
}

echo "</form>\n";

if ( $t_show_submit && ( 0 < count( $t_overrides ) ) ) {
	echo "<div class=\"right\"><form name=\"threshold_config_action\" method=\"post\" action=\"config_revert.php\">\n";
	echo form_security_field( 'manage_config_revert' );
	echo "<input name=\"revert\" type=\"hidden\" value=\"" . implode( ',', $t_overrides ) . "\" />";
	echo "<input name=\"project\" type=\"hidden\" value=\"$t_project_id\" />";
	echo "<input name=\"return\" type=\"hidden\" value=\"\" />";
	echo "<input type=\"submit\" class=\"button\" value=\"";
	if ( ALL_PROJECTS == $t_project_id ) {
		echo _( 'Delete All Projects Settings' );
	} else {
	echo _( 'Delete Project Specific Settings' );
	}
	echo "\" />\n";
	echo "</form></div>\n";
}

html_page_bottom();