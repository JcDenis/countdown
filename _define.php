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
    '2.4.1',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'support'     => 'https://git.dotclear.watch/JcDenis/' . basename(__DIR__) . '/issues',
        'details'     => 'https://git.dotclear.watch/JcDenis/' . basename(__DIR__) . '/src/branch/master/README.md',
        'repository'  => 'https://git.dotclear.watch/JcDenis/' . basename(__DIR__) . '/raw/branch/master/dcstore.xml',
    ]
);
