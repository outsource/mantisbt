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
 * Custom Field Configuration
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
 * @uses custom_field_api.php
 * @uses form_api.php
 * @uses gpc_api.php
 * @uses helper_api.php
 * @uses html_api.php
 * @uses lang_api.php
 * @uses print_api.php
 * @uses string_api.php
 */

require_once( '../core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'config_api.php' );
require_api( 'custom_field_api.php' );
require_api( 'form_api.php' );
require_api( 'gpc_api.php' );
require_api( 'helper_api.php' );
require_api( 'html_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'string_api.php' );

auth_reauthenticate();

access_ensure_global_level( config_get( 'manage_custom_fields_threshold' ) );

$f_field_id	= gpc_get_int( 'field_id' );
$f_return	= strip_tags( gpc_get_string( 'return', 'custom_field_page.php' ) );

custom_field_ensure_exists( $f_field_id );

html_page_top();

print_manage_menu( 'custom_field_edit_page.php' );

$t_definition = custom_field_get_definition( $f_field_id );
?>

<div id="manage-custom-field-update-div" class="form-container">
	<form id="manage-custom-field-update-form" method="post" action="custom_field_update.php">
		<fieldset>
			<legend><span><?php echo _( 'Edit custom field' ) ?></span></legend>
			<?php echo form_security_field( 'manage_custom_field_update' ); ?>
			<input type="hidden" name="field_id" value="<?php echo $f_field_id ?>" />
			<input type="hidden" name="return" value="<?php echo $f_return ?>" />
			<div class="field-container">
				<label for="custom-field-name"><span><?php echo _( 'Name' ) ?></span></label>
				<span class="input"><input type="text" id="custom-field-name" name="name" size="32" maxlength="64" value="<?php echo string_attribute( $t_definition['name'] ) ?>" /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-type"><span><?php echo _( 'Type' ) ?></span></label>
				<span class="select">
					<select id="custom-field-type" name="type">
						<?php print_enum_string_option_list( 'custom_field_type', $t_definition['type'] ) ?>
					</select>
				</span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-possible-values"><span><?php echo _( 'Possible Values' ) ?></span></label>
				<span class="input"><input type="text" id="custom-field-possible-values" name="possible_values" size="32" value="<?php echo string_attribute( $t_definition['possible_values'] ) ?>" /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-default-value"><span><?php echo _( 'Default Value' ) ?></span></label>
				<span class="input"><input type="text" id="custom-field-default-value" name="default_value" size="32" maxlength="255" value="<?php echo string_attribute( $t_definition['default_value'] ) ?>" /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-valid-regexp"><span><?php echo _( 'Regular Expression' ) ?></span></label>
				<span class="input"><input type="text" id="custom-field-valid-regexp" name="valid_regexp" size="32" maxlength="255" value="<?php echo string_attribute( $t_definition['valid_regexp'] ) ?>" /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-access-level-r"><span><?php echo _( 'Read Access' ) ?></span></label>
				<span class="select">
					<select id="custom-field-access-level-r" name="access_level_r">
						<?php print_enum_string_option_list( 'access_levels', $t_definition['access_level_r'] ) ?>
					</select>
				</span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-access-level-rw"><span><?php echo _( 'Write Access' ) ?></span></label>
				<span class="select">
					<select id="custom-field-access-level-rw" name="access_level_rw">
						<?php print_enum_string_option_list( 'access_levels', $t_definition['access_level_rw'] ) ?>
					</select>
				</span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-length-min"><span><?php echo _( 'Min. Length' ) ?></span></label>
				<span class="input"><input type="text" id="custom-field-length-min" name="length_min" size="32" maxlength="64" value="<?php echo $t_definition['length_min'] ?>" /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-length-max"><span><?php echo _( 'Max. Length' ) ?></span></label>
				<span class="input"><input type="text" id="custom-field-length-max" name="length_max" size="32" maxlength="64" value="<?php echo $t_definition['length_max'] ?>" /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-filter-by"><span><?php echo _( 'Add to Filter' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="custom-field-filter-by" name="filter_by" <?php if ( $t_definition['filter_by'] ) { ?>checked="checked"<?php } ?>  /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-display-report"><span><?php echo _( 'Display When Reporting Issues' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="custom-field-display-report" name="display_report" value="1" <?php check_checked( $t_definition['display_report'] ) ?> /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-display-update"><span><?php echo _( 'Display When Updating Issues' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="custom-field-display-update" name="display_update" value="1" <?php check_checked( $t_definition['display_update'] ) ?> /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-display-resolved"><span><?php echo _( 'Display When Resolving Issues' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="custom-field-display-resolved" name="display_resolved" value="1" <?php check_checked( $t_definition['display_resolved'] ) ?> /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-display-closed"><span><?php echo _( 'Display When Closing Issues' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="custom-field-display-closed" name="display_closed" value="1" <?php check_checked( $t_definition['display_closed'] ) ?> /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-require-report"><span><?php echo _( 'Required On Report' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="custom-field-require-report" name="require_report" value="1" <?php check_checked( $t_definition['require_report'] ) ?> /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-require-update"><span><?php echo _( 'Required On Update' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="custom-field-require-update" name="require_update" value="1" <?php check_checked( $t_definition['require_update'] ) ?> /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-require-resolved"><span><?php echo _( 'Required On Resolve' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="custom-field-require-resolved" name="require_resolved" value="1" <?php check_checked( $t_definition['require_resolved'] ) ?> /></span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-require-closed"><span><?php echo _( 'Required On Close' ) ?></span></label>
				<span class="checkbox"><input type="checkbox" id="custom-field-require-closed" name="require_closed" value="1" <?php check_checked( $t_definition['require_closed'] ) ?> /></span>
				<span class="label-style"></span>
			</div>
			<span class="submit-button"><input type="submit" class="button" value="<?php echo _( 'Update Custom Field' ) ?>" /></span>
		</fieldset>
	</form>
</div>

<br />

<div class="form-container">
	<form method="post" action="custom_field_delete.php" class="action-button">
		<fieldset>
			<?php echo form_security_field( 'manage_custom_field_delete' ); ?>
			<input type="hidden" name="field_id" value="<?php echo $f_field_id ?>" />
			<input type="hidden" name="return" value="<?php echo string_attribute( $f_return ) ?>" />
			<input type="submit" class="button" value="<?php echo _( 'Delete Custom Field' ) ?>" />
		</fieldset>
	</form>
</div>

<?php /** @todo There is access checking in the ADD action page and at the top of this file.
           * We may need to add extra checks to exclude projects from the list that the user
		   * can't link/unlink fields from/to. */
?>
<div class="form-container">
	<div class="field-container">
		<span class="display-label"><span><?php echo _( 'Link custom field to project' ) ?></span></span>
		<div class="display-value">
			<?php print_custom_field_projects_list( $f_field_id ) ?>
		</div>
		<span class="label-style"></span>
	</div>
	<form method="post" action="custom_field_proj_add.php">
		<fieldset>
			<input type="hidden" name="field_id" value="<?php echo $f_field_id ?>" />
			<?php echo form_security_field( 'manage_custom_field_proj_add' ); ?>
			<div class="field-container">
				<label for="custom-field-project-id"><span><?php echo _( 'Projects:' ) ?></span></label>
				<span class="select">
					<select id="custom-field-project-id" name="project_id[]" multiple="multiple" size="5">
						<?php print_project_option_list( null, false ); ?>
					</select>
				</span>
				<span class="label-style"></span>
			</div>
			<div class="field-container">
				<label for="custom-field-sequence"><span><?php echo _( 'Sequence:' ) ?></span></label>
				<span class="input"><input type="text" id="custom-field-sequence" name="sequence" value="0" /></span>
				<span class="label-style"></span>
			</div>
			<span class="submit-button"><input type="submit" class="button" value="<?php echo _( 'Link Custom Field' ) ?>" /></span>
		</fieldset>
	</form>
</div><?php

html_page_bottom();