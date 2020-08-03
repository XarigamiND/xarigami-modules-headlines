<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami headlines module
 * @copyright (C) 2008-2010 2skies.com
 * @link http://xarigami.com/project/headlines
 */
/**
 * return the path for a short URL to xarModURL for this module
 *
 * @author the Headlines module development team
 * @param  $args the function and arguments passed to xarModURL
 * @return string path to be added to index.php for a short URL, or empty if failed
 */
function headlines_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);
    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }
    // Note : make sure you don't pass the following variables as arguments in
    // your module too - adapt here if necessary
    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here -> you must specify the modname !
    $module = 'headlines';

    // Alias for the module, set in the config screen.
    $aliasisset = xarModGetVar($module, 'useModuleAlias');
    $aliasname = xarModGetVar($module, 'aliasname');

    if (!empty($aliasisset) && isset($aliasname)) {
        $module_for_alias = xarModGetAlias($aliasname);

        // If the alias is for this module, then
        // use it instead of the module name.
        if ($module_for_alias == $module) {
            $module = $aliasname;
        }
    }

    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $path = '/' . $module . '/';

    } elseif ($func == 'view') {
        // check for required parameters
        if (isset($hid) && is_numeric($hid)) {
            $path = '/' . $module . '/' . $hid;
        }

    } elseif ($func == 'my') {
        $path = '/' . $module . '/my/';
        if (!empty($config)) {
            $path .= 'config';
        }

    }
    if (!empty($path)) {
        if (isset($startnum)) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&';
        }
        if (!empty($catid)) {
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        } elseif (!empty($cids) && count($cids) > 0) {
            if (!empty($andcids)) {
                $catid = join('+', $cids);
            } else {
                $catid = join('-', $cids);
            }
            $path .= $join . 'catid=' . $catid;
            $join = '&';
        }
    }

    return $path;
}

?>
