<?php
/**
 * @file
 * @brief       The plugin countdown definition
 * @ingroup     countdown
 *
 * @defgroup    countdown Plugin countdown.
 *
 * Countdown and stopwatch.
 *
 * @author      Moe (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

$this->registerModule(
    'CountDown',
    'Countdown and stopwatch',
    'Moe (http://gniark.net/) and contributors',
    '2.5',
    [
        'requires'    => [['core', '2.36']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://github.com/JcDenis/' . $this->id . '/issues',
        'details'     => 'https://github.com/JcDenis/' . $this->id . '/',
        'repository'  => 'https://raw.githubusercontent.com/JcDenis/' . $this->id . '/master/dcstore.xml',
        'date'        => '2025-09-09T15:25:18+00:00',
    ]
);
