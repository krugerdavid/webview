<?php
/*
    ---------------------------------------------------------------------
    Copyright (C) 2004 Kilian CAVALOTTI
                        <kilian.cavalotti@stix.polytechnique.fr>

    This software is governed by the CeCILL  license under French law and
    abiding by the rules of distribution of free software.  You can  use,
    modify and/or redistribute the software under the terms of the CeCILL
    license  as  circulated by  CEA, CNRS and INRIA  at the following URL
    "http://www.cecill.info".

    As a counterpart to the access to the source code and rights to copy,
    modify and redistribute  granted by the license,  users are  provided
    only with a limited warranty and  the software's  author,  the holder
    of  the  economic  rights,  and the  successive  licensors  have only
    limited liability.

    In this respect,the user's attention is drawn to the risks associated
    with loading,  using,  modifying and/or developing or reproducing the
    software by the user in light of its specific status of free software,
    that may mean  that it is complicated to manipulate,  and  that  also
    therefore means  that it is reserved for developers  and  experienced
    professionals having in-depth computer knowledge. Users are therefore
    encouraged to  load and test  the software's  suitability  as regards
    their  requirements  in  conditions  enabling  the  security of their
    systems  and/or  data to be ensured and,  more generally,  to use and
    operate it in the same conditions as regards security.

    The fact that you are presently reading this means  that you have had
    knowledge of the CeCILL license and that you accept its terms.
    ---------------------------------------------------------------------
    show_nodes.php
    openssi-webview nodes map page
    ---------------------------------------------------------------------
    Started      on Mon, 26 Apr 2004 16:20:08 +0200
    Last updated on Wed, 10 Nov 2004 15:24:00 +0100
    ---------------------------------------------------------------------
*/

require_once('./common.inc.php'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="refresh" content="300" />
    <title>openSSI webView</title>
    <style type="text/css" media="all">
        @import "css/style.php";
    </style>
</head>

<body>

<script type="text/javascript">
    <!--
    function show(item) {
        var item = document.getElementById(item);
        item.style.visibility = "visible";
    }

    function hide(item) {
        var item = document.getElementById(item);
        item.style.visibility = "hidden";
    }

    function togglevisibility(item) {
        (item.style && item.style.visibility && item.style.visibility == "visible")
            ? item.style.visibility = "hidden"
            : item.style.visibility = "visible"
    }

    function togglevisibilitysingle(item) {
        var item = document.getElementById(item);
        togglevisibility(item)
    }

    function togglevisibilityall(category) {
        var items = document.getElementsByTagName('div')
        var reg   = new RegExp(category)
        if (items) {
            for (i=0; i<items.length; i++) {
                (items[i].id.match(reg)) ? togglevisibility(items[i]) : items[i];
            }
        }
    }
    // -->
</script>

<!--[if gte IE 5.5000]>
<script type="text/javascript" src="css/iefixs.js"></script>
<![endif]-->

<? display_header(); ?>

<div id="Content">

    <? display_intro("show_nodes");

    javascript_warning(); ?>

    <!--[if gte IE 5.5000]>
    <br /><br />
    <![endif]-->

    <? check_ssi_system();

    $clustername = get_clustername();
    list($allnodes, $initnodes, $nodes) = get_nodes();

    ## Display openSSI cluster map -------------------------------------- ?>

    <?=$clustername?>

    <div class="cluster"> <?
        foreach (array("init", "") as $type) {
            ${$type.'_nb'} = sizeof(${$type.'nodes'});
            ${$type.'_size'} = @max(33, 100/${$type.'_nb'});

            if (${$type.'_nb'} != 0) { ?>
            <div class="nodehead"><?=$type?> nodes (<a href="javascript:togglevisibilityall('^<?=$type?>_node[1-9]+$')">toggle</a> details)</div>
            <div class="nodes">
                <div class="spacer"></div> <?

                    foreach (${$type.'nodes'} as ${$type}) {
                        $status = strtr(${$type}->status, $GLOBALS["trstate"]);
                        (!in_array($status, $GLOBALS["states"])) ? $status = "fault" : $status; ?>

                        <div class="node" style="width:<?=${$type.'_size'}?>%;">
                            <div class="nodeinfo" id="<?=$type?>_node<?=${$type}->id?>">
                                <div class="nodepicture" onmouseover="javascript:show('node<?=${$type}->id?>_details');
                                                                      javascript:show('<?=$type?>_node<?=${$type}->id?>')"
                                                         onmouseout ="javascript:hide('node<?=${$type}->id?>_details');
                                                                      javascript:hide('<?=$type?>_node<?=${$type}->id?>')">
                                    <a class="silent" href="graph_disp.php?t=stats&#038;n=<?=${$type}->id?>">
                                        <img alt="node<?=${$type}->id?>" src="images/<?=$type.'node_'.$status?>.png"/><br/>
                                        <span class="nodename"><?=${$type}->hostname?></span>
                                    </a>
                                </div>
                                <?display_node_info(${$type});?>

                                <div style='position: relative; z-index: 3;'>
                                    <div class="detail" id="node<?=${$type}->id?>_details">
                                        <?display_node_details(${$type});?>
                                    </div>
                                </div>
                            </div>


                        </div> <?
                    } ?>
                    <div class="spacer"></div>
                </div> <?
            }
        } ?>
        <div class="spacer"></div>
    </div>
</div>

<div id="Contentshadow">&nbsp;</div>


<? display_menu();
   display_footer(); ?>

</body>
</html>

<?
# vim:set tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
?>
