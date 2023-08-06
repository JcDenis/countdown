<?php
/**
 * @brief countdown, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugin
 *
 * @author Moe (http://gniark.net/) and contributors
 *
 * @copyright Jean-Christian Denis
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\countdown;

use dcCore;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsStack;
use Dotclear\Plugin\widgets\WidgetsElement;

class Widgets
{
    public static function initWidgets(WidgetsStack $w): void
    {
        if (is_null(dcCore::app()->blog)) {
            return;
        }

        $tz = dcCore::app()->blog->settings->get('system')->get('blog_timezone');

        $array_year   = $array_month = $array_day = $array_hour = [];
        $array_minute = $array_number_of_times = [];
        for ($i = 1902;$i <= 2037;$i++) {
            $array_year[$i] = $i;
        }
        for ($i = 1;$i <= 12;$i++) {
            $i                                                                                                          = str_repeat('0', (2 - strlen((string) $i))) . $i;
            $array_month[ucfirst(__(Date::strftime('%B', (int) mktime(0, 0, 0, (int) $i, 1, 1970)))) . ' (' . $i . ')'] = $i;
        }
        for ($i = 1;$i <= 31;$i++) {
            $i             = str_repeat('0', (2 - strlen((string) $i))) . $i;
            $array_day[$i] = $i;
        }
        for ($i = 0;$i <= 23;$i++) {
            $i              = str_repeat('0', (2 - strlen((string) $i))) . $i;
            $array_hour[$i] = $i;
        }
        for ($i = 0;$i <= 60;$i++) {
            $i                = str_repeat('0', (2 - strlen((string) $i))) . $i;
            $array_minute[$i] = $i;
        }
        for ($i = 1;$i <= 5;$i++) {
            $array_number_of_times[$i] = $i;
        }
        $array_number_of_times['6 (' . __('all') . ')'] = 6;

        $w->create(
            'CountDown',
            __('Countdown'),
            [self::class, 'parseWidget'],
            null,
            __('A countdown to a future date or stopwatch to a past date')
        )
        ->addTitle(__('CountDown'))
        ->setting(
            'text_before',
            __('Text displayed if the date is in the future:'),
            __('In'),
            'text'
        )
        ->setting(
            'text_after',
            __('Text displayed if the date is in the past:'),
            __('For'),
            'text'
        )

        ->setting('year', ucfirst(__('year')) . ':', Date::str('%Y', null, $tz), 'combo', $array_year)
        ->setting('month', ucfirst(__('month')) . ':', Date::str('%m', null, $tz), 'combo', $array_month)
        ->setting('day', ucfirst(__('day')) . ':', Date::str('%d', null, $tz), 'combo', $array_day)
        ->setting('hour', ucfirst(__('hour')) . ':', Date::str('%H', null, $tz), 'combo', $array_hour)
        ->setting('minute', ucfirst(__('minute')) . ':', Date::str('%M', null, $tz), 'combo', $array_minute)
        ->setting('second', ucfirst(__('second')) . ':', Date::str('%S', null, $tz), 'combo', $array_minute)

        ->setting(
            'number_of_times',
            __('Number of values to be displayed:'),
            '6',
            'combo',
            $array_number_of_times
        )
        ->setting(
            'zeros',
            __('Show zeros before hours, minutes and seconds'),
            false,
            'check'
        )
        ->setting(
            'dynamic',
            __('Enable dynamic display'),
            false,
            'check'
        )
        ->setting(
            'dynamic_format',
            sprintf(
                __('Dynamic display format (see <a href="%1$s" %2$s>jQuery Countdown Reference</a>):'),
                'http://keith-wood.name/countdownRef.html#format',
                'onclick="return window.confirm(\'' .
                __('Are you sure you want to leave this page?') . '\')"'
            ),
            __('yowdHMS'),
            'text'
        )
        ->setting(
            'dynamic_layout_before',
            sprintf(
                __('Dynamic display layout if the date is in the future (see <a href="%1$s" %2$s>jQuery Countdown Reference</a>):'),
                'http://keith-wood.name/countdownRef.html#layout',
                'onclick="return window.confirm(\'' .
                __('Are you sure you want to leave this page?') . '\')"'
            ),
            __('In {y<}{yn} {yl}, {y>} {o<}{on} {ol}, {o>} {w<}{wn} {wl}, {w>} {d<}{dn} {dl}, {d>} {hn} {hl}, {mn} {ml} and {sn} {sl}'),
            'textarea'
        )
        ->setting(
            'dynamic_layout_after',
            sprintf(
                __('Dynamic display layout if the date is in the past (see <a href="%1$s" %2$s>jQuery Countdown Reference</a>):'),
                'http://keith-wood.name/countdownRef.html#layout',
                'onclick="return window.confirm(\'' .
                __('Are you sure you want to leave this page?') . '\')"'
            ),
            __('For {y<}{yn} {yl}, {y>} {o<}{on} {ol}, {o>} {w<}{wn} {wl}, {w>} {d<}{dn} {dl}, {d>} {hn} {hl}, {mn} {ml} and {sn} {sl}'),
            'textarea'
        )

        ->addHomeOnly()
        ->addContentOnly()
        ->addClass()
        ->addOffline();
    }

    public static function parseWidget(WidgetsElement $w): string
    {
        if (is_null(dcCore::app()->blog)
            || $w->__get('offline')
            || !$w->checkHomeOnly(dcCore::app()->url->type)
        ) {
            return '';
        }

        # get local time
        $local_time = Date::addTimeZone(dcCore::app()->blog->settings->get('system')->get('blog_timezone'));

        $ts = mktime((int) $w->hour, (int) $w->minute, (int) $w->second, (int) $w->month, (int) $w->day, (int) $w->year);
        # get difference
        (int) $diff = ($local_time - $ts);
        $after      = ($diff > 0) ? true : false;
        $diff       = abs($diff);

        $times = [];

        $intervals = [
            (3600 * 24 * 365.24) => ['one' => __('year'), 'more' => __('years'), 'zeros' => false],
            (3600 * 24 * 30.4)   => ['one' => __('month'), 'more' => __('months'), 'zeros' => false],
            (3600 * 24)          => ['one' => __('day'), 'more' => __('days'), 'zeros' => false],
            (3600)               => ['one' => __('hour'), 'more' => __('hours'), 'zeros' => true],
            (60)                 => ['one' => __('minute'), 'more' => __('minutes'), 'zeros' => true],
            (1)                  => ['one' => __('second'), 'more' => __('seconds'), 'zeros' => true],
        ];

        foreach ($intervals as $k => $v) {
            if ($diff >= $k) {
                $time    = floor($diff / $k);
                $times[] = (($w->zeros and $v['zeros'])
                    ? sprintf('%02d', $time) : $time) . ' ' . (($time <= 1) ? $v['one']
                    : $v['more']);
                $diff = $diff % $k;
            }
        }

        # output
        $text = ($after) ? $w->text_after : $w->text_before;
        if (strlen($text) > 0) {
            $text .= ' ';
        }

        # get times and make a string
        $times = array_slice($times, 0, (int) $w->number_of_times);
        if (count($times) > 1) {
            $last = array_pop($times);
            $str  = implode(', ', $times) . ' ' . __('and') . ' ' . $last;
        } else {
            $str = implode('', $times);
        }

        if (!$w->dynamic || is_null(dcCore::app()->ctx)) {
            $res = ($w->title ? $w->renderTitle(Html::escapeHTML($w->title)) : '') .
            '<p>' . $text . '<span>' . $str . '</span></p>';

            return $w->renderDiv((bool) $w->content_only, 'countdown ' . $w->class, '', $res);
        }

        # dynamic display with Countdown for jQuery
        if (!is_numeric(dcCore::app()->ctx->__get('countdown'))) {
            dcCore::app()->ctx->__set('countdown', 0);
        }
        $id = (int) dcCore::app()->ctx->__get('countdown');
        dcCore::app()->ctx->__set('countdown', $id + 1);

        $script = '';

        if (!defined('COUNTDOWN_SCRIPT')) {
            $script .= My::cssLoad('jquery.countdown.css') .
                My::jsLoad('jquery.plugin.min.js') .
                My::jsLoad('jquery.countdown.min.js');

            $l10n_file = 'jquery.countdown-' . dcCore::app()->blog->settings->get('system')->get('lang') . '.js';
            if (file_exists(__DIR__ . '/../js/' . $l10n_file)) {
                $script .= My::jsLoad($l10n_file);
            }

            define('COUNTDOWN_SCRIPT', (bool) true);
        }

        if ($after) {
            $to     = 'since';
            $layout = $w->dynamic_layout_after;
        } else {
            $to     = 'until';
            $layout = $w->dynamic_layout_before;
        }

        $res = ($w->title ? $w->renderTitle(Html::escapeHTML($w->title)) : '') .
            '<p id="countdown-' . $id . '">' . $text . $str . '</p>' .
            $script .
            '<script type="text/javascript">' . "\n" .
            '//<![CDATA[' . "\n" .
                '$().ready(function() {' .
                "$('#countdown-" . $id . "').countdown({" .
                    # In Javascript, 0 = January, 11 = December
                    $to . ': new Date(' . (int) $w->year . ',' . (int) $w->month . '-1,' .
                    (int) $w->day . ',' . (int) $w->hour . ',' . (int) $w->minute . ',' .
                    (int) $w->second . "),
						description: '" . Html::escapeJS($text) . "',
						format: '" . $w->dynamic_format . "',
						layout: '" . $layout . "',
						expiryText: '" . Html::escapeJS($w->text_after) . "'
					});" .
                '});' . "\n" .
            '//]]>' .
            '</script>' . "\n";

        return $w->renderDiv((bool) $w->content_only, 'countdown ' . $w->class, '', $res);
    }
}
