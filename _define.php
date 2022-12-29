<?php 
# ***** BEGIN LICENSE BLOCK *****
#
# This file is part of CountDown, a plugin for Dotclear 2
# Copyright 2007,2010 Moe (http://gniark.net/)
#
# CountDown is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License v2.0
# as published by the Free Software Foundation.
#
# CountDown is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public
# License along with this program. If not, see
# <http://www.gnu.org/licenses/>.
#
# ***** END LICENSE BLOCK *****

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    'CountDown',
    'Countdown and stopwatch',
    'Moe (http://gniark.net/), Pierre Van Glabeke',
    '2.0.0',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_ADMIN,
        ]),
        'type'      => 'plugin',
        'support'   => 'https://forum.dotclear.org/viewforum.php?id=16',
		'details'	=> 'http://plugins.dotaddict.org/dc2/details/countdown'
    ]
);
