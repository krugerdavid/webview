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
  graph_disp.php
  openssi-webview graph displaying functions
  ---------------------------------------------------------------------
  Started      on Wed, 01 Sep 2004 11:11:48 +0200
  Last updated on Wed, 10 Nov 2004 15:24:00 +0100
  ---------------------------------------------------------------------
 */

include_once('./common.inc.php');

$type = $_GET['t'];
$node = $_GET['n'];
$files = list_files(DATADIR);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <meta http-equiv="refresh" content="300" />
        <title>openSSI webView :: Graphs &amp; Stats</title>
        <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="css/print.css" type="text/css" media="print" />
        <style type="text/css" media="all">
            @import "css/style.php";
        </style>
    </head>

    <body>

        <script type="text/javascript">
            <!--
            function toggledisplay(category) {
                var item = document.getElementById(category + "_block")
                if (item.style && item.style.display && item.style.display == "none") {
                    item.style.display = ""
                    document.getElementById(category + "_tog").innerHTML = "[-]" }
                else {
                    item.style.display = "none"
                    document.getElementById(category + "_tog").innerHTML = "[+]" }
            }
            function switchpage(select) {
                var index;
                for(index=0; index<select.options.length; index++)
                if(select.options[index].selected) {
                    if(select.options[index].value!="") window.location.href=select.options[index].value;
                    break;
                }
            }
            // -->
        </script>

        <!--[if gte IE 5.5000]>
        <script type="text/javascript" src="css/iefixs.js"></script>
        <![endif]-->

        <? display_header(); ?>

        <div id="content">
            <div class="container">

                <div class="span-6 prepend-top">
                    <? display_menu(); ?>
                </div>

                <div class="span-17 prepend-top last">

                    display_intro($type);
                    check_ssi_system();

                    javascript_warning(); ?>

                    <!--[if gte IE 5.5000]>
                    <br /><br />
                    <![endif]-->

                    <?
                    ## ======================================================================
                    ##
                    ##  Overall stats
                    ##
                    ## ======================================================================

                    if ($type == "stats") {
                        ?>

                        <!-- cluster overview -->
                        <h2 id="overview"><a id="cluster_tog" class="toggle" href="javascript:toggledisplay('cluster')">[-]</a> Cluster overview</h2>
                        <div id="cluster_block" class="block"><div class="centertable"><table> <?
                    $c = 0;
                    foreach ($stats_what as $l => $text) {
                        if (($c % 3) == 0)
                            echo "<tr>\n\t";
                            ?>
                                        <td style="width:40px"><a href="graph_disp.php?t=<?= $l ?>"><img alt="<?= $l ?>" src="./images/<?= $l ?>.png" /></a></td>
                                        <td><a href="graph_disp.php?t=<?= $l ?>"><?= $text ?></a></td> <?
                                if (($c % 3) == 2)
                                    echo "</tr>";
                                $c++;
                            }
                        ?>
                                </table></div></div>

                        <!-- load overview -->
                        <h2 id="loads"><a id="load_tog" class="toggle" href="javascript:toggledisplay('load')">[-]</a> Load overview</h2>
                        <div id="load_block" class="block"><table>
                                <tr><td align="center">
                                        <a class="silent" href="graph_disp.php?t=loads"> <img alt="graph" class="graph" src="graph.php?t=loads&#038;s=big&#038;st=12h" /> </a>
                                    </td></tr>
                            </table></div>

                        <!-- node overview -->
                        <h2 id="nodes"><a id="node_tog" class="toggle" href="javascript:toggledisplay('node')">[-]</a> Node overview</h2>
                        <div id="node_block" class="block"><?
                                !$node ? $node = 1 : $node;
                                # if cache file exists, propose drop down list
                                if (is_readable(CACHEFILE)) {
                                    $s = implode("", @file(CACHEFILE));
                                    $allnodes = unserialize($s);
                                    # Sort nodes (initnodes, nodes)
                                    foreach ($allnodes as $nd) {
                                        if ($nd->init != 0)
                                            $initnodes[] = $nd;
                                        else
                                            $nodes[] = $nd;
                                    }
                            ?>
                                <form action="<?= $_SERVER['PHP_SELF'] ?>">
                                    Select the node you want to display stats for:<br/>
                                    <select name="node" onchange="switchpage(this)"> <? foreach (array("init", "") as $type) { ?>
                                            <optgroup label="<?= $type ?> nodes"> <?
                            foreach (${$type . 'nodes'} as $i) {
                                $id = $i->id;
                                    ?>
                                                    <option <? if ($node == $id)
                                        echo "selected=\"selected\""; ?> value="graph_disp.php?t=stats&#038;n=<?= $id ?>#nodes">
                                                        node <?= $id; ?>
                                                    </option> <? } ?>
                                            </optgroup> <? } ?>
                                    </select>
                                </form> <? } ?>

                            <table> <?
                            $c = 0;
                            foreach ($stats_what as $l => $text) {
                                if (($c % 2) == 0)
                                    echo "<tr>\n\t";
                            ?>
                                    <td align="center">
                                        <a class="silent" href="graph_disp.php?t=<?= $l ?>&#038;n=<?= $node ?>">
                                            <img alt="graph_<?= $l ?>" class="graph" src="graph.php?t=<?= $l ?>&#038;n=<?= $node ?>&#038;s=small" />
                                        </a>
                                        <div style="text-align:center; font-size:9px; font-weight: bold"><?= $text ?></div>
                                    </td>
                                    <?
                                    if (($c % 2) == 1)
                                        echo "</tr>";
                                    $c++;
                                }
                                ?>
                            </table></div> <?
                        } else {

                            ## ======================================================================
                            ##
                            ##  Detailled view of a specific quantity, on a specific node
                            ##
                            ## ======================================================================

                            if ($node != "" or $type == "loads") {
                                    ?>
                            <br />
                            <table> <?
                    foreach (array("d" => "daily (5mn average)",
                "w" => "weekly (30mn average)",
                "m" => "monthly (2h average)",
                "y" => "yearly (1day average)") as $l => $text) {
                                        ?>
                                    <tr><td align="center">
                                            <img alt="graph<?= $l ?>" class="graph" src="graph.php?n=<?= $node ?>&#038;t=<?= $type ?>&#038;st=1<?= $l ?>&#038;s=big" />
                                            <br />
                                            <div class="legend"><?= $text ?></div>
                                            <br /></td></tr> <? } ?>
                            </table> <?
                } else {

                    ## ======================================================================
                    ##
                    ##  Overview of a specific quantity, all over the cluster
                    ##
                    ## ====================================================================== 
                                    ?>
                            <br />
                            <table width="100%"> <?
                    foreach ($files as $i => $file) {
                                        ?>
                                    <tr><td align="center">
                                            <a class="silent" href="graph_disp.php?n=<?= $i ?>&#038;t=<?= $type ?>">
                                                <img alt="graph<?= $i ?>" class="graph" src="graph.php?n=<?= $i ?>&#038;t=<?= $type ?>&#038;st=1d&#038;s=big" />
                                            </a>
                                            <br />
                                            <div class="legend">node <?= $i ?></div>
                                            <br />
                                        </td></tr>
                                <? } ?>
                            </table> <?
                    }
                }
                        ?>
                </div>

            </div>
        </div>


        <? display_footer(); ?>

    </body>
</html>

<?
# vim:set tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
?>
