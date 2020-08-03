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
 * create a new headline
 * @param $args['url'] url of the item
 * @return int headline ID on success, false on failure
 */
function headlines_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($url)) {
        throw new BadParameterException('url');
    }
    // Security Check
    if(!xarSecurityCheck('AddHeadlines')) return;

    $order = xarModAPIFunc('headlines', 'user', 'countitems');
    $title = !isset($title) || empty($title) || !is_string($title) || strlen($title) > 100 ? '' : $title;
    $desc = !isset($desc) || empty($desc) || !is_string($desc) || strlen($desc) > 254 ? '' : $desc;
    $settings = isset($settings) ? $settings : serialize(array());
    $date = !isset($date) || empty($date) || !is_numeric($date) ? time() : $date;
    $string = !isset($string) || !is_string($string) ? '' : $string;
    // Get datbase setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $headlinestable = $xartable['headlines'];
    // Get next ID in table
    $nextId = $dbconn->GenId($headlinestable);
    // Add item
    $query = "INSERT INTO $headlinestable (
              xar_hid,
              xar_title,
              xar_desc,
              xar_url,
              xar_order,
              xar_string,
              xar_date,
              xar_settings)
            VALUES (
              ?,
              ?,
              ?,
              ?,
              ?,
              ?,
              ?,
              ?)";

    $bindvars = array($nextId, $title, $desc, $url, $order, $string, $date, $settings);
    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $hid = $dbconn->PO_Insert_ID($headlinestable, 'xar_hid');
    // Let any hooks know that we have created a new link

    $item['module']='headlines';
    $item['itemid']=$hid;
    xarModCallHooks('item', 'create', $hid, $item);

    // Return the id of the newly created link to the calling process
    return $hid;
}
?>