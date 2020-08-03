<?php
/**
 * Displays an RSS Display.
 *
 * @package modules
 * @copyright (C) 2005-2009 The Digital Development Foundation
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
function headlines_rssblock_init()
{
    return array(
        'rssurl' => '',
        'maxitems' => 5,
        'showdescriptions' => false,
        'show_chantitle' => 1,
        'show_chandesc' => 1,
        'truncate' => 0, // added for bug 4545
        'alt_chantitle' => '',  // FR - alt channel title
        'alt_chandesc' => '',   // FR - alt channel desc
        'alt_chanlink' => '',   // FR - alt channel link
        'linkhid' => false,     // FR - link to headline feed
        'show_warning' => 1,      // TODO: FR - option to hide block/display warning on failed feed
        'show_chanimage' => 0,    // added for SimplePie
        'show_itemimage' => 0,    // added for SimpePie
        'show_itemcats' => 0,     // added for SimpePie
        'refresh' => 3600,
        'nocache' => 0, // cache by default
        'pageshared' => 1, // don't share across pages here
        'usershared' => 1, // share across group members
        'cacheexpire' => 3601 // right after the refresh rate :-)
    );
}

/**
 * Block info array
 */
function headlines_rssblock_info()
{
    return array(
        'text_type' => 'RSS',
        'text_type_long' => 'RSS Newsfeed',
        'module' => 'headlines',
        'func_update' => 'headlines_rssblock_insert',
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
function headlines_rssblock_display($blockinfo)
{
    // Keep all the default values in one place.
    $defaults = headlines_rssblock_init();

    // Break out options from our content field.
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    $blockinfo['content'] = '';

    // Check and see if a feed has been supplied to us.
    if (empty($vars['rssurl'])) {
        $blockinfo['title'] = xarML('Headlines');
        $blockinfo['content'] = xarML('No Feed URL Specified');
        return $blockinfo;
    }
    // bug[ 5322 ]
    if (is_numeric($vars['rssurl'])) {
        $headline = xarModAPIFunc('headlines', 'user', 'get', array('hid' => $vars['rssurl']));
        if (!empty($headline)) {
            $feedfile = $headline['url'];
            $thishid = $headline['hid'];
        } else {
            $blockinfo['title'] = xarML('Headlines');
            $blockinfo['content'] = xarML('No Feed URL Specified');
            return $blockinfo;
        }
    } else {
        $feedfile = $vars['rssurl'];
    }

    if (!isset($vars['maxitems'])) $vars['maxitems'] = $defaults['maxitems'];
    if (!isset($vars['show_chantitle'])) $vars['show_chantitle'] = $defaults['show_chantitle'];
    if (!isset($vars['show_chandesc'])) $vars['show_chandesc'] = $defaults['show_chandesc'];
    if (empty($vars['refresh'])) $vars['refresh'] = $defaults['refresh'];
    if (!isset($vars['linkhid'])) $vars['linkhid'] = $defaults['linkhid'];
    if (!isset($vars['show_warning'])) $vars['show_warning'] = $defaults['show_warning'];

    // CHECKME: this forces early refresh regardless of caching options, why do we do that? old code below
    // define('MAGPIE_CACHE_AGE', round($refresh/2)); // set lower than block cache so we always get something
    // simplepie'cache_max_minutes' => round($refresh/2*60) - bug, simplepie caching is in seconds
    // this only happens in the rss block, need to work out if it's necessary
    $refresh = $vars['refresh']/2;
    if (!$vars['showdescriptions']) {
        $vars['truncate'] = 0; // no point doing extra work for nothing :)
    }
    // call api function to get the parsed feed (or warning)
    $data = xarModAPIFunc('headlines', 'user', 'getparsed',
                array('feedfile' => $feedfile, 'refresh' => $refresh,
                      'numitems' => $vars['maxitems'], 'truncate' => $vars['truncate']));
    // TODO: option to hide block here instead
    if (!empty($data['warning'])) {
        if (empty($vars['show_warning'])) return;
        $blockinfo['title'] = xarML('Headlines');
        $blockinfo['content'] = xarVarPrepForDisplay($data['warning']);
        return $blockinfo;
    } else {
        // here we see if this feed has been updated by comparing the stored hash against the
        // hash provided by the getparsed function, if they're different, we update the feed
        // with the new hash, and the time of the last item in the feed, or the current time
        // this means the feeds can now be sorted reliably by date ala. the cloud block
        if (isset($headline['string']) && ($headline['string'] != $data['compare'])) {
            // call api function to update our feed item
            if (!xarModAPIFunc('headlines', 'user', 'update', array('hid' => $headline['hid'], 'date' => $data['lastitem'], 'string' => $data['compare']))) return;
        }
    }

    // FR: add alt channel title/desc/link
    if (!isset($vars['alt_chantitle'])) $vars['alt_chantitle'] = $defaults['alt_chantitle'];
    if (!isset($vars['alt_chandesc'])) $vars['alt_chandesc'] = $defaults['alt_chandesc'];
    if (!isset($vars['alt_chanlink'])) $vars['alt_chanlink'] = $defaults['alt_chanlink'];
    if (!empty($vars['alt_chantitle'])) $data['chantitle'] = $vars['alt_chantitle'];
    if (!empty($vars['alt_chandesc'])) $data['chandesc'] = $vars['alt_chandesc'];
    if (!empty($vars['alt_chanlink'])) $data['chanlink'] = $vars['alt_chanlink'];
    // optionally show images and cats if available (SimplePie required)
    if (!isset($vars['show_chanimage'])) $vars['show_chanimage'] = $defaults['show_chanimage'];
    if (!isset($vars['show_itemimage'])) $vars['show_itemimage'] = $defaults['show_itemimage'];
    if (!isset($vars['show_itemcats'])) $vars['show_itemcats'] = $defaults['show_itemcats'];
    // make sure SimplePie's available
    if (!xarModIsAvailable('simplepie')) {
        $vars['show_chanimage'] = $defaults['show_chanimage'];
        $vars['show_itemimage'] = $defaults['show_itemimage'];
        $vars['show_itemcats'] = $defaults['show_itemcats'];
    }
    if ($vars['linkhid'] && (isset($thishid)&& !empty($thishid))) {
        $vars['linkhid'] = $thishid;
    }
    if (!isset($vars['show_warning'])) $vars['show_warning'] = $defaults['show_warning'];

    $blockinfo['content'] = array(
        'feedcontent'  => $data['feedcontent'],
        'blockid'      => $blockinfo['bid'],
        'chantitle'    => $data['chantitle'],
        'chanlink'     => $data['chanlink'],
        'chandesc'     => $data['chandesc'],
        'chanimage'    => $data['image'],
        'show_desc'     => $vars['showdescriptions'],
        'show_chantitle' => $vars['show_chantitle'],
        'show_chandesc'  => $vars['show_chandesc'],
        'show_chanimage' => $vars['show_chanimage'],
        'show_itemimage' => $vars['show_itemimage'],
        'show_itemcats' => $vars['show_itemcats'],
        'show_warning' => $vars['show_warning'],
        'linkhid' => $vars['linkhid']
    );

    return $blockinfo;
}

/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function headlines_rssblock_modify($blockinfo)
{
    // Keep all the default values in one place.
    $defaults = headlines_rssblock_init();

    // Break out options from our content field.
    // Prepare for when content is passed in as an array.
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Migrate $row['rssurl'] to content if present
    if (!empty($vars['url'])) {
        $vars['rssurl'] = $vars['url'];
        unset($vars['url']);
    }

    // Get parameters from whatever input we need
    $vars['items'] = array();

    // The user API function is called
    $links = xarModAPIFunc('headlines', 'user', 'getall');
    $vars['items'] = $links;

    // Defaults
    if (!isset($vars['rssurl'])) $vars['rssurl'] = $defaults['rssurl'];

    // bug[ 5322 ] - check for hid
    if (is_numeric($vars['rssurl'])) {
        $headline = xarModAPIFunc('headlines', 'user', 'get', array('hid' => $vars['rssurl']));
        if (!empty($headline)) { // found headline, use that url
            $vars['rssurl'] = $headline['url'];
        } else {
            $vars['rssurl'] = $defaults['rssurl'];
        }
    }

    // If the current URL is not in the headlines list, then pass it in as 'custom'
    $vars['otherrssurl'] = $vars['rssurl'];
    if (is_array($links) && $vars['rssurl'] != $defaults['rssurl']) {
        foreach($links as $link) {
            if ($link['url'] == $vars['rssurl']) {
                // The URL was found in the list, so it is not custom
                $vars['otherrssurl'] = '';
            }
        }
    }

    // Defaults
    if (!isset($vars['show_chantitle'])) $vars['show_chantitle'] = $defaults['show_chantitle'];
    if (!isset($vars['show_chandesc'])) $vars['show_chandesc'] = $defaults['show_chandesc'];
    if (!isset($vars['showdescriptions'])) $vars['showdescriptions'] = $defaults['showdescriptions'];
    if (!isset($vars['maxitems'])) $vars['maxitems'] = $defaults['maxitems'];
    if (!isset($vars['refresh'])) $vars['refresh'] = $defaults['refresh'];
    // bug [4545]
    if (!isset($vars['truncate'])) $vars['truncate'] = $defaults['truncate'];
    // FR: add alt title/description/link
    if (!isset($vars['alt_chantitle'])) $vars['alt_chantitle'] = $defaults['alt_chantitle'];
    if (!isset($vars['alt_chandesc'])) $vars['alt_chandesc'] = $defaults['alt_chandesc'];
    if (!isset($vars['alt_chanlink'])) $vars['alt_chanlink'] = $defaults['alt_chanlink'];
    if (!isset($vars['linkhid'])) $vars['linkhid'] = $defaults['linkhid'];
    // get the current parser
    $vars['parser'] = xarModGetVar('headlines', 'parser');
    // check for legacy magpie code, checkme: is this still necessary?
    if (xarModGetVar('headlines', 'magpie')) $vars['parser'] = 'magpie';
    // check module available if not default parser
    if ($vars['parser'] != 'default' && !xarModIsAvailable($vars['parser'])) $vars['parser'] = 'default';
    if ($vars['parser'] == 'simplepie') {
        // optionally show images and cats if available (SimplePie only)
        if (!isset($vars['show_chanimage'])) $vars['show_chanimage'] = $defaults['show_chanimage'];
        if (!isset($vars['show_itemimage'])) $vars['show_itemimage'] = $defaults['show_itemimage'];
        if (!isset($vars['show_itemcats'])) $vars['show_itemcats'] = $defaults['show_itemcats'];
    } else {
        // otherwise set false (defaults)
        $vars['show_chanimage'] = $defaults['show_chanimage'];
        $vars['show_itemimage'] = $defaults['show_itemimage'];
        $vars['show_itemcats'] = $defaults['show_itemcats'];
    }
    if (!isset($vars['show_warning'])) $vars['show_warning'] = $defaults['show_warning'];


    $vars['blockid'] = $blockinfo['bid'];

    // Just return the template variables.
    return $vars;
}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function headlines_rssblock_insert($blockinfo)
{
    // Keep all the default values in one place.
    $defaults = headlines_rssblock_init();

    $vars = array();

    if (!xarVarFetch('rssurl', 'str:1:', $vars['rssurl'], $defaults['rssurl'], XARVAR_NOT_REQUIRED)) {return;}
    // The 'otherrssurl' can override the 'rssurl'
    if (!xarVarFetch('otherrssurl', 'str:1:', $otherrssurl, $defaults['rssurl'], XARVAR_NOT_REQUIRED)) {return;}
    // FR: added check for correct url format, including local urls
    if (!empty($otherrssurl) && $otherrssurl != $defaults['rssurl']) {
        if (strstr($otherrssurl,'://')) {
            if (preg_match("@^http://|https://|ftp://@", $otherrssurl)) {
                $vars['rssurl'] = $otherrssurl;
            }
        } elseif (substr($otherrssurl,0,1) == '/') {
            $server = xarServerGetHost();
            $protocol = xarServerGetProtocol();
            $vars['rssurl'] = $protocol . '://' . $server . $otherrssurl;
        } else {
            $baseurl = xarServerGetBaseURL();
            $vars['rssurl'] = $baseurl . $otherrssurl;
        }
    }
    // bug[ 5322 ] replace url value with numeric hid
    // allowing changes to module feeds to be reflected in blocks
    if (is_numeric($vars['rssurl'])) {
        $headline = xarModAPIFunc('headlines', 'user', 'get', array('hid' => $vars['rssurl']));
        if (empty($headline)) {
            $vars['rssurl'] = $defaults['rssurl'];
        }
    }
    // TODO: check for duplicates
    // TODO: check otherrssurl against stored headlines

    if (!xarVarFetch('maxitems', 'int:0', $vars['maxitems'], $defaults['maxitems'], XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showdescriptions', 'checkbox', $vars['showdescriptions'], $defaults['showdescriptions'], XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('show_chantitle', 'checkbox', $vars['show_chantitle'], $defaults['show_chantitle'], XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('show_chandesc', 'checkbox', $vars['show_chandesc'], $defaults['show_chandesc'], XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('refresh', 'int:0', $vars['refresh'], $defaults['refresh'], XARVAR_NOT_REQUIRED)) {return;}
    // bug [4545]
    if (!xarVarFetch('truncate', 'int:0', $vars['truncate'], $defaults['truncate'], XARVAR_NOT_REQUIRED)) return;
    // FR: add alt title/description/link
    if (!xarVarFetch('alt_chantitle', 'str:1:', $vars['alt_chantitle'], $defaults['alt_chantitle'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('alt_chandesc', 'str:1:', $vars['alt_chandesc'], $defaults['alt_chandesc'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('alt_chanlink', 'str:1:', $vars['alt_chanlink'], $defaults['alt_chanlink'], XARVAR_NOT_REQUIRED)) return;
    if (!preg_match("@^http://|https://|ftp://@", $vars['alt_chanlink'])) $vars['alt_chanlink'] = $defaults['alt_chanlink'];
    if (!xarVarFetch('linkhid', 'checkbox', $vars['linkhid'], $defaults['linkhid'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('show_chanimage', 'checkbox', $vars['show_chanimage'], $defaults['show_chanimage'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('show_itemimage', 'checkbox', $vars['show_itemimage'], $defaults['show_itemimage'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('show_itemcats', 'checkbox', $vars['show_itemcats'], $defaults['show_itemcats'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('show_warning', 'checkbox', $vars['show_warning'], false, XARVAR_NOT_REQUIRED)) return;

    $blockinfo['content'] = $vars;
    return $blockinfo;
}

?>
