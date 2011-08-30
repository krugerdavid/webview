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
  index.php
  openssi-webview main page
  ---------------------------------------------------------------------
  Started      on Mon, 26 Apr 2004 16:20:08 +0200
  Last updated on Wed, 10 Nov 2004 15:24:00 +0100
  ---------------------------------------------------------------------
 */

require_once('./common.inc.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <meta http-equiv="refresh" content="300" />
        <title>openSSI webView :: Overview</title>
        <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="css/print.css" type="text/css" media="print" />

        <style type="text/css" media="all">
            @import "css/style.php";
        </style>
    </head>

    <body>

        <? display_header(); ?>

        <div id="content">
            <div class="container">

                <div class="span-6 prepend-top">
                    <? display_menu(); ?>
                </div>

                <div class="span-18 prepend-top last">
                    <?
                    display_intro("overview");
                    check_ssi_system();


                    $clustername = get_clustername();
                    list($allnodes, $initnodes, $nodes) = get_nodes();

                    ## Display openSSI overview ----------------------------------------- 
                    ?>

                    <?= $clustername ?>

                    <div class="cluster append-bottom"> <?
                    foreach (array("init", "") as $type) {
                        ${$type . '_nb'} = sizeof(${$type . 'nodes'});
                        ${$type . '_size'} = @max(33, 100 / ${$type . '_nb'});

                        if (${$type . '_nb'} != 0) {
                            ?>
                                <div class="nodehead"><?= $type ?> nodes</div>
                                <div class="nodes append-bottom">
                                    <div class="spacer"></div> <?
                        foreach (${$type . 'nodes'} as ${$type}) {
                            $status = strtr(${$type}->status, $GLOBALS["trstate"]);
                            (!in_array($status, $GLOBALS["states"])) ? $status = "fault" : $status;
                                ?>

                                        <div style="float:left; width:<?= ${$type . '_size'} ?>%;">
                                            <table>
                                                <tr><td width="42px">
                                                        <a class="silent" href="graph_disp.php?t=stats&#038;n=<?= ${$type}->id ?>#nodes">
                                                            <img alt="node<?= ${$type}->id ?>" src="images/<?= 'small_' . $type . 'node_' . $status ?>.png"/></a></td>
                                                    <td><div class="nodeslight">
                                                            <div><b> &raquo; node <?= ${$type}->id; ?> : <?= ${$type}->hostname; ?></b></div>
                                                            <div class="bartight">
                                                                <? display_node_load(${$type}); ?>
                                                            </div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div> <?
                                                }
                                            }
                                                        ?>
                                <div class="spacer"></div>
                            </div> <? } ?>
                    </div>
                </div>

            </div>
        </div>


        <? display_footer(); ?>

    </body>
</html>

<?
# vim:set tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
?>
