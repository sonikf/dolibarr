<?php
/* Copyright (C) 2006-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2024		MDW						<mdeweerd@users.noreply.github.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 * or see https://www.gnu.org/
 */

/**
 *	    \file       htdocs/core/lib/member.lib.php
 *		\brief      Ensemble de functions de base pour les adherents
 */

/**
 *  Return array head with list of tabs to view object information
 *
 * @return	array<array{0:string,1:string,2:string}>	Array of tabs to show
 */
function mailmanspip_admin_prepare_head()
{
	global $langs;

	return array(
		array(
			DOL_URL_ROOT.'/admin/mailman.php',
			'Mailman',
			'mailman'
		),
		array(
			DOL_URL_ROOT.'/admin/spip.php',
			'SPIP',
			'spip'
		)
	);
}
