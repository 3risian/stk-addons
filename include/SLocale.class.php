<?php
/**
 * copyright 2013 Stephen Just <stephenjust@users.sf.net>
 *           2014 Daniel Butum <danibutum at gmail dot com>
 * This file is part of stkaddons
 *
 * stkaddons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * stkaddons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with stkaddons.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class SLocale
 * Note that PHP has a built-in Locale class in the newest versions of PHP
 */
class SLocale
{

    /**
     * Array of supported languages, format is:
     * language code, flag image x-offset, flag image y-offset, flag label
     * @var array
     */
    private static $languages = [
        ['en_US', 0, 0, 'EN'],
        ['ca_ES', 0, -40, 'CA'],
        ['de_DE', 0, -80, 'DE'],
        ['es_ES', 0, -120, 'ES'],
        ['eu_ES', 0, -160, 'EU'],
        ['fr_FR', 0, -200, 'FR'],
        ['ga_IE', 0, -240, 'GA'],
        ['gd_GB', 0, -280, 'GD'],
        ['gl_ES', 0, -320, 'GL'],
        ['id_ID', 0, -360, 'ID'],
        ['it_IT', 0, -400, 'IT'],
        ['nl_NL', 0, -440, 'NL'],
        ['pt_BR', 0, -480, 'PT'],
        ['ru_RU', 0, -520, 'RU'],
        ['zh_TW', 0, -560, 'ZH (T)']
    ];

    /**
     * @var int
     */
    const COOKIE_LIFETIME = 31536000; // One year

    /**
     * Create the locale object
     *
     * @param string $locale optional
     */
    public function __construct($locale = null)
    {
        if (!$locale && !empty($_GET['lang']))
        {
            $locale = $_GET['lang'];
        }
        elseif (isset($_COOKIE['lang']))
        {
            $locale = $_COOKIE['lang'];
        }
        else
        {
            $locale = "en_US";
        }

        if (!SLocale::isValid($locale))
        {
            exit("Invalid locale");
        }

        SLocale::setLocale($locale);
    }

    /**
     * Get all the supported translate languages
     *
     * @return array
     */
    public static function getLanguages()
    {
        return static::$languages;
    }

    /**
     * Check if locale is a valid value
     *
     * @param string $locale
     *
     * @return bool
     */
    public static function isValid($locale)
    {
        foreach (SLocale::$languages as $lang)
        {
            if ($locale === $lang[0])
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Set page locale
     *
     * @param string $locale Locale string - input should already be checked
     */
    private static function setLocale($locale)
    {
        $domain = 'translations';

        // Set cookie
        header('Content-Type: text/html; charset=utf-8');
        setcookie('lang', $locale, time() + static::COOKIE_LIFETIME);
        putenv("LC_ALL=$locale.UTF-8");
        if (setlocale(LC_ALL, $locale . ".UTF-8") === false)
        {
            trigger_error("Set locale has failed. No localization is possible");
        }
        $_COOKIE['lang'] = $locale;

        // Set translation file info
        bindtextdomain($domain, ROOT_PATH . 'locale');
        textdomain($domain);
        bind_textdomain_codeset($domain, 'UTF-8');

        define('LANG', $locale);
    }
}
