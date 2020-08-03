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
function headlines_user_main()
{
    xarVarFetch('startnum', 'id', $startnum, '1', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('catid', 'str:0:', $data['catid'], '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('sort', 'enum:default:date:title', $sort, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY);

    // Security Check
    if(!xarSecurityCheck('OverviewHeadlines')) return;

    $numitems = xarModGetVar('headlines', 'itemsperpage');
    // TODO: admin configurable sort
    // $showsort = xarModGetVar('headlines', 'showsort'); // show sort options to users
    $sort = !empty($sort) ? $sort : xarModGetVar('headlines', 'sortorder'); // default sort order
    // The user API function is called
    $links = xarModAPIFunc('headlines', 'user', 'getall',
        array(
            'catid' => $data['catid'],
            'startnum' => $startnum,
            'numitems' => $numitems,
            'sort' => $sort
        )
    );
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
        $links[$i] = xarModAPIFunc( 'headlines', 'user', 'getparsed',
                array('feedfile' => $feedfile, 'refresh' => 7200)
            );

        if (!isset($links[$i])) {

            $links[$i] = array('chantitle' => xarML('Bad chantitle'),
                              'chandesc'  => xarML('Unable to retrieve feed data.'));
            continue;
        } elseif (!empty($links[$i]['warning'])){
            // TODO: option to hide broken feeds for all/admin/user
            $links[$i]['chantitle'] = xarML('Feed unavailable');
            $links[$i]['chandesc'] = $links[$i]['warning'];
        }
        if (!empty($link['title'])){
            $links[$i]['chantitle'] = $link['title'];
        }
        if (!empty($link['desc'])){
            $links[$i]['chandesc'] = $link['desc'];
        }
        if (empty($link['date'])) {
            $link['date'] = time();
        }
        if (empty($links[$i]['lastitem'])) {
            $links[$i]['lastitem'] = $link['date'];
        }

        // TODO: Check individual permissions for View / Import / Edit / Delete
        $links[$i]['viewlink'] = xarModURL('headlines',
                                           'user',
                                           'view',
                                           array('hid' => $link['hid']));
        $links[$i]['importlink'] = xarModURL('headlines',
                                             'admin',
                                             'import',
                                             array('hid' => $link['hid']));
        /* TODO: use the correct api funcs (getall etc) to grab lists of comments, hits, ratings, keywords */

        $showcomments = xarModGetVar('headlines', 'showcomments');

        if ($showcomments) {
            if (!xarModIsAvailable('comments') || !xarModIsHooked('comments', 'headlines')) {
                $showcomments = 0;
            }
        }

        if ($showcomments) {
            $links[$i]['comments'] = xarModAPIFunc('comments',
                                                   'user',
                                                   'get_count',
                                                   array('modid' => xarModGetIDFromName('headlines'),
                                                         'objectid' => $link['hid']));

            if (!$links[$i]['comments']) {
                $links[$i]['comments'] = xarML('No comments');
            } elseif ($links[$i]['comments'] == 1) {
                $links[$i]['comments'] .= ' ' . xarML('comment');
            } else {
                $links[$i]['comments'] .= ' ' . xarML('comments');
            }
        } else {
            $links[$i]['comments'] = '';
        }

        $showratings = xarModGetVar('headlines', 'showratings');

        if ($showratings) {
            if (!xarModIsAvailable('ratings') || !xarModIsHooked('ratings', 'headlines')) {
                $showratings = 0;
            }
        }

        if ($showratings) {
            $links[$i]['ratings'] = xarModAPIFunc('ratings',
                                                   'user',
                                                   'get',
                                                   array('modid' => xarModGetIDFromName('headlines'),
                                                         'objectid' => $link['hid']));

            if (!$links[$i]['ratings']) {
                $links[$i]['ratings'] = xarML('Unrated');
            } else {
                $links[$i]['ratings'] = xarML('Rated ') . $links[$i]['ratings'];
            }
        } else {
            $links[$i]['ratings'] = '';
        }

        $showhitcount = xarModGetVar('headlines', 'showhitcount');

        if ($showhitcount) {
            if (!xarModIsAvailable('hitcount') || !xarModIsHooked('hitcount', 'headlines')) {
                $showhitcount = 0;
            }
        }

        if ($showhitcount) {
            $links[$i]['hitcount'] = xarModAPIFunc('hitcount',
                                                   'user',
                                                   'get',
                                                   array('modid' => xarModGetIDFromName('headlines'),
                                                         'objectid' => $link['hid']));

            if (!$links[$i]['hitcount']) {
                $links[$i]['hitcount'] = xarML('No reads');
            } elseif ($links[$i]['hitcount'] == 1) {
                $links[$i]['hitcount'] .= ' ' . xarML('read');
            } else {
                $links[$i]['hitcount'] .= ' ' . xarML('reads');
            }
        } else {
            $links[$i]['hitcount'] = '';
        }

        $showkeywords = xarModGetVar('headlines', 'showkeywords');

        if ($showkeywords) {
            if (!xarModIsAvailable('keywords') || !xarModIsHooked('keywords', 'headlines')) {
                $showkeywords = 0;
            }
        }
        if ($showkeywords) {
            $links[$i]['keywords'] = xarModAPIFunc('keywords', 'user', 'getwords',
                                                    array('modid' => xarModGetIDFromName('headlines'),
                                                            'itemid' => $link['hid']));
        }
    }

    $data['indlinks'] = $links;
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('headlines', 'user', 'countitems'),
                                    xarModURL('headlines', 'user', 'main', array('startnum' => '%%')),
                                    $numitems);

    xarTPLSetPageTitle(xarML('Syndicated Headlines'));

    return $data;
}
?>
