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
 * update an headline
 * @param $args['hid'] the ID of the link
 * @param $args['url'] the new url of the link
 */
function headlines_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($hid))
    {
        throw new BadParameterException('hid');
    }
    if (!isset($url))
    {
        throw new BadParameterException('url');
    }

    // The user API function is called
    $link = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($link == false) return;

    $settings = isset($settings) ? $settings : serialize(array());
    $date = isset($date) && is_numeric($date) && !empty($date) ? $date : time();
    $string = isset($string) && !empty($string) && is_string($string) ? $string : '';
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;
    // Get datbase setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $headlinestable = $xartable['headlines'];

    // Update the link
    $query = "UPDATE $headlinestable
            SET xar_url = ?,
                xar_title = ?,
                xar_desc = ?,
                xar_order = ?,
                xar_settings = ?,
                xar_date = ?,
                xar_string = ?
            WHERE xar_hid = ?";
    $bindvars = array($url, $title, $desc, $order, $settings, $date, $string, $hid);
    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let the calling process know that we have finished successfully
    xarModCallHooks('item', 'update', $hid, '');
    return true;
}
?>
