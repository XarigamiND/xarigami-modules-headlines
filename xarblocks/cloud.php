<?php
/**
 * Displays an RSS Display.
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * @author RevJim (revjim.net), John Cox
 * @todo Make the admin selectable number of headlines work.
 * @todo show search and image of rss site
 */
/**
 * Block init - holds security.
 */
function headlines_cloudblock_init()
{
    return array(
        'rssurl' => '',
        'maxitems' => 5,
        'showdescriptions' => false
    );
}
/**
 * Block info array
 */
function headlines_cloudblock_info()
{
    return array(
        'text_type' => 'RSS Cloud',
        'text_type_long' => 'RSS Cloud',
        'module' => 'headlines',
        'func_update' => 'headlines_cloudblock_insert',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}
/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function headlines_cloudblock_display($blockinfo)
{
     // Keep all the default values in one place.
    $defaults = headlines_cloudblock_init();
    // Break out options from our content field.
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    $blockinfo['content'] = '';
    // TODO: provide config options, link to last item, link to headline, show image/cats/date
    $maxitems = isset($vars['maxitems']) ? $vars['maxitems'] : $defaults['maxitems'];
    $showdescriptions = isset($vars['showdescriptions']) ? $vars['showdescriptions'] : $defaults['showdescriptions'];
    $links = xarModAPIFunc('headlines', 'user', 'getall',
        array(
            'numitems' => $maxitems,
            'sort' => 'date'
        )
    );
    $feedcontent = array();
    //if (empty($links)) return
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        // Check and see if a feed has been supplied to us.
        if (empty($link['url'])) {
            continue;
        }
        $feedfile = $link['url'];
        // TODO: make refresh configurable
        $links[$i] = xarModAPIFunc(
            'headlines', 'user', 'getparsed',
            array('feedfile' => $feedfile)
        );
        // Check and see if a valid feed has been supplied to us.
        if (!isset($links[$i]) || isset($links[$i]['warning'])) continue;
        if (!empty($link['title'])){
            $links[$i]['chantitle'] = $link['title'];
        }
        if (!empty($link['desc'])){
            $links[$i]['chandesc'] = $link['desc'];
        }

        $feedcontent[] = array('title' => $links[$i]['chantitle'], 'url' => $links[$i]['chanlink'], 'channel' => $links[$i]['chandesc']);
    }
    $blockinfo['content'] = array(
        'feedcontent'  => $feedcontent,
        'showdescriptions' => $showdescriptions
    );
    return $blockinfo;
}
/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function headlines_cloudblock_modify($blockinfo)
{
    // Keep all the default values in one place.
    $defaults = headlines_cloudblock_init();
    // Break out options from our content field.
    // Prepare for when content is passed in as an array.
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    // TODO:
    $vars['maxitems'] = isset($vars['maxitems']) ? $vars['maxitems'] : $defaults['maxitems'];
    $vars['showdescriptions'] = isset($vars['showdescriptions']) ? $vars['showdescriptions'] : $defaults['showdescriptions'];    
    $vars['blockid'] = $blockinfo['bid'];
     // Just return the template variables.
    return $vars;
}
/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function headlines_cloudblock_insert($blockinfo)
{
    // Keep all the default values in one place.
    $defaults = headlines_cloudblock_init();
    $vars = array();
    if (!xarVarFetch('maxitems', 'int:0', $vars['maxitems'], $defaults['maxitems'], XARVAR_NOT_REQUIRED)) {return;} 
    if (!xarVarFetch('showdescriptions', 'checkbox', $vars['showdescriptions'], $defaults['showdescriptions'], XARVAR_NOT_REQUIRED)) {return;}    
    $blockinfo['content'] = $vars;
    return $blockinfo;
}

?>