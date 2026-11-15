<?php

declare(strict_types=1);

namespace Dotclear\Plugin\countdown;

use DateTimeImmutable;
use DateTimeZone;
use Dotclear\App;
use Exception;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsStack;
use Dotclear\Plugin\widgets\WidgetsElement;

/**
 * @brief       countdown widgets class.
 * @ingroup     countdown
 *
 * @author      Moe (author)
 * @author      Jean-Christian Denis (latest)
 * @copyright   GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
class Widgets
{
    public static function initWidgets(WidgetsStack $w): void
    {
        if (!App::blog()->isDefined()) {
            return;
        }

        $tz = App::blog()->settings()->get('system')->getStr('blog_timezone', false);

        $array_year  = $array_number_of_times = [];
        $array_month = $array_day = $array_hour = $array_minute = [];
        // Combo values are strings, as saved settings, to be selected
        for ($i = 1902;$i <= 2037;$i++) {
            $array_year[(string) $i] = (string) $i;
        }
        for ($i = 1;$i <= 12;$i++) {
            $m                                                                                = sprintf('%02d', $i);
            $array_month[ucfirst(Date::str('%B', (int) mktime(0, 0, 0, $i, 1, 2000))) . ' (' . $m . ')'] = $m;
        }
        for ($i = 1;$i <= 31;$i++) {
            $i             = str_repeat('0', (2 - strlen((string) $i))) . $i;
            $array_day[$i] = $i;
        }
        for ($i = 0;$i <= 23;$i++) {
            $i              = str_repeat('0', (2 - strlen((string) $i))) . $i;
            $array_hour[$i] = $i;
        }
        for ($i = 0;$i <= 59;$i++) {
            $i                = str_repeat('0', (2 - strlen((string) $i))) . $i;
            $array_minute[$i] = $i;
        }
        for ($i = 1;$i <= 5;$i++) {
            $array_number_of_times[(string) $i] = (string) $i;
        }
        $array_number_of_times['6 (' . __('all') . ')'] = '6';

        $w->create(
            'CountDown',
            __('Countdown'),
            self::parseWidget(...),
            null,
            __('A countdown to a future date or stopwatch to a past date')
        )
        ->addTitle(__('Countdown'))
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

        ->setting('year', ucfirst(__('year')) . ':', Date::str('%Y', null, $tz), 'combo', self::combo($array_year))
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
            self::combo($array_number_of_times)
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
        if (!App::blog()->isDefined()
            || $w->get('offline')
            || !$w->checkHomeOnly(App::url()->getType())
        ) {
            return '';
        }

        // Target date in blog time zone, an invalid day is set to the last day of the month
        try {
            $tz = new DateTimeZone(App::blog()->settings()->get('system')->getStr('blog_timezone', false) ?: 'UTC');
        } catch (Exception) {
            $tz = new DateTimeZone('UTC');
        }

        $_hour   = is_numeric($w->get('hour')) ? min(23, max(0, (int) $w->get('hour'))) : 0;
        $_minute = is_numeric($w->get('minute')) ? min(59, max(0, (int) $w->get('minute'))) : 0;
        $_second = is_numeric($w->get('second')) ? min(59, max(0, (int) $w->get('second'))) : 0;
        $_month  = is_numeric($w->get('month')) ? min(12, max(1, (int) $w->get('month'))) : 1;
        $_year   = is_numeric($w->get('year')) ? (int) $w->get('year') : (int) date('Y');
        $_day    = is_numeric($w->get('day')) ? max(1, (int) $w->get('day')) : 1;
        $_day    = min($_day, (int) (new DateTimeImmutable('now', $tz))->setDate($_year, $_month, 1)->format('t'));

        $now    = new DateTimeImmutable('now', $tz);
        $target = (new DateTimeImmutable('now', $tz))->setDate($_year, $_month, $_day)->setTime($_hour, $_minute, $_second);
        $after  = $now > $target;

        // Calendar difference, months and years have their real length
        $interval = $now->diff($target);
        $values   = [
            [$interval->y, __('year'), __('years'), false],
            [$interval->m, __('month'), __('months'), false],
            [$interval->d, __('day'), __('days'), false],
            [$interval->h, __('hour'), __('hours'), true],
            [$interval->i, __('minute'), __('minutes'), true],
            [$interval->s, __('second'), __('seconds'), true],
        ];

        $times   = [];
        $started = false;
        foreach ($values as [$time, $one, $more, $zeros]) {
            if ($time > 0 || $started) {
                $started = true;
                $times[] = (($w->get('zeros') && $zeros) ? sprintf('%02d', $time) : $time) . ' ' . (($time <= 1) ? $one : $more);
            }
        }

        # output
        $text = ($after) ? $w->get('text_after') : $w->get('text_before');
        $text = is_string($text) ? $text : '';
        if (strlen($text) > 0) {
            $text .= ' ';
        }

        # get times and make a string
        $nbt = is_numeric($w->get('number_of_times')) ? (int) $w->get('number_of_times') : 0;
        $times = array_slice($times, 0, $nbt);
        if (count($times) > 1) {
            $last = array_pop($times);
            $str  = implode(', ', $times) . ' ' . __('and') . ' ' . $last;
        } else {
            $str = implode('', $times);
        }

        if (!$w->get('dynamic')) {
            $res = (is_string($w->get('title')) && !empty($w->get('title')) ? $w->renderTitle(Html::escapeHTML($w->get('title'))) : '') .
            '<p>' . $text . '<span>' . $str . '</span></p>';

            return $w->renderDiv((bool) $w->get('content_only'), 'countdown ' . (is_string($w->get('class')) ? $w->get('class') : ''), '', $res);
        }

        # dynamic display with Countdown for jQuery
        if (!is_numeric(App::frontend()->context()->__get('countdown'))) {
            App::frontend()->context()->__set('countdown', 0);
        }
        $id = App::frontend()->context()->__get('countdown');
        $id = is_numeric($id) ? $id : 0;
        App::frontend()->context()->__set('countdown', $id + 1);

        $script = '';

        if (!defined('COUNTDOWN_SCRIPT')) {
            $script .= My::cssLoad('jquery.countdown.css') .
                My::jsLoad('jquery.plugin.min.js') .
                My::jsLoad('jquery.countdown.min.js');

            $l10n_file = 'jquery.countdown-' . App::blog()->settings()->get('system')->getStr('lang', false) . '.js';
            if (file_exists(__DIR__ . '/../js/' . $l10n_file)) {
                $script .= My::jsLoad($l10n_file);
            }

            define('COUNTDOWN_SCRIPT', true);
        }

        if ($after) {
            $to     = 'since';
            $layout = $w->get('dynamic_layout_after');
        } else {
            $to     = 'until';
            $layout = $w->get('dynamic_layout_before');
        }

        $res = (is_string($w->get('title')) && !empty($w->get('title')) ? $w->renderTitle(Html::escapeHTML($w->get('title'))) : '') .
            '<p id="countdown-' . $id . '">' . $text . $str . '</p>' .
            $script .
            '<script type="text/javascript">' . "\n" .
            '//<![CDATA[' . "\n" .
                '$().ready(function() {' .
                "$('#countdown-" . $id . "').countdown({" .
                    # Same instant for all visitors, whatever their time zone
                    $to . ': new Date(' . ($target->getTimestamp() * 1000) . "),
						description: '" . Html::escapeJS($text) . "',
						format: '" . (is_string($w->get('dynamic_format')) ? $w->get('dynamic_format') : '') . "',
						layout: '" . (is_string($layout) ? $layout : '') . "',
						expiryText: '" . Html::escapeJS(is_string($w->get('text_after')) ? $w->get('text_after') : '') . "'
					});" .
                '});' . "\n" .
            '//]]>' .
            '</script>' . "\n";

        return $w->renderDiv((bool) $w->get('content_only'), 'countdown' . (is_string($w->get('class')) ? ' ' . $w->get('class') : ''), '', $res);
    }

    /**
     * Get combo options.
     *
     * Numeric labels become integer keys in PHP, Dotclear only uses them as labels.
     *
     * @param   array<int|string, string>   $options    The options
     *
     * @return  array<string, mixed>
     */
    private static function combo(array $options): array
    {
        $res = [];
        foreach ($options as $label => $value) {
            $res[(string) $label] = $value;
        }

        return $res;
    }
}
