-- ========================================================================
-- Copyright (C) 2001-2004	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
-- Copyright (C) 2004		Laurent Destailleur		<eldy@users.sourceforge.net>
-- Copyright (C) 2014		Alexandre Spangaro		<aspangaro@open-dsi.fr>
-- Copyright (C) 2022 	    Juanjo Menent           <jmenent@2byte.es>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <https://www.gnu.org/licenses/>.
--
-- ========================================================================

create table llx_c_country
(
  rowid    		integer            PRIMARY KEY,
  code     		varchar(2)         NOT NULL,	-- the iso 2 alpha code
  code_iso 		varchar(3),						-- the iso 3 alpha code
  numeric_code 	varchar(3),						-- the iso numeric number
  label    		varchar(128)       NOT NULL,
  eec      		tinyint DEFAULT 0  NOT NULL,
  sepa     		tinyint DEFAULT 0  NOT NULL,
  active   		tinyint DEFAULT 1  NOT NULL,
  favorite 		tinyint DEFAULT 0  NOT NULL
)ENGINE=innodb;
