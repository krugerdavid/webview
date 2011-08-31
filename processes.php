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
  processes.php
  openssi-webview processes page
  ---------------------------------------------------------------------
  Started      on Mon, 27 Sep 2004 11:08:13 +0200
  Last updated on Wed, 10 Nov 2004 15:24:00 +0100
  ---------------------------------------------------------------------
 */

require_once('./common.inc.php');
require_once(CONFIGFILE);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <meta http-equiv="refresh" content="300" />
        <title>openSSI webView :: Processes</title>
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

                <div class="span-17 prepend-top last"><?
                    display_intro("processes");
                    check_ssi_system();

                    javascript_warning();
                    ?>

                    <?
                    $user = $_GET['u'];
                    $node = $_GET['n'];
                    $sort_by = $_GET['s'];
                    $no_syst = $_GET['ns'];

                    $migr_node = $_GET['mt'];
                    $migr_pid = $_GET['pid'];

                    ## ==================================================================
                    ##
                    ## process migration
                    ##
                    ## ==================================================================
                    # verify that /usr/bin/migrate can be executed via sudo
                    exec("echo foo | sudo -S -l | grep \"NOPASSWD:.*/usr/bin/migrate$\"", $sudo_output, $sudo_return);
                    if ($sudo_return != 0) {
                        $migr_msg = "This system does not seems to be configured to allow process migration from here.
                         Please refer to the documentation for instructions on how to enable process migration from openSSI-webView";
                        $migr_color = "orange";
                        $migr_disable = 1;
                    } else {
                        if ($migr_node != "" and $migr_pid != "") {
                            if ($PREVENT_SYSPROC_MIGR and @fileowner("/proc/$migr_pid") < $SYSPROC_MAXUID) { # prevent migration of system processes
                                $migr_msg = "<strong>You can't migrate system processes.</strong>";
                                $migr_color = "red";
                            } elseif (exec("where_pid $migr_pid") == $migr_node) { # prevent second migration of same process
                                $migr_msg = "The process you want to migrate (PID $migr_pid) is already running on node $migr_node.";
                                $migr_color = "green";
                            } else { # do the migration
                                exec("sudo migrate $migr_node $migr_pid 2>&1", $migr_output, $migr_return);
                                # check results
                                if ($migr_return == 0 and exec("where_pid $migr_pid") == $migr_node) {
                                    $migr_msg = "<strong>The process $migr_pid has been successfully migrated to node $migr_node!</strong><br />
                                   <em>NB: during the page reloading, and according to the load of the destination node,
                                   the selected process has possibly already been migrated to another less-loaded node.</em>";
                                    $migr_color = "green";
                                } else {
                                    $migr_msg = "<strong>An error occured during the process migration.</strong><br />";
                                    if (sizeof($migr_output) != 0)
                                        $migr_msg = $migr_msg . "Error message is: <br />" . implode("<br />", $migr_output);
                                    $migr_color = "red";
                                }
                            }
                        }
                    }

                    ## ==================================================================
                    ##
                    ## ps parameters
                    ##
                    ## ==================================================================

                    switch ($user) {
                        case "" :
                        case "all" :
                            switch ($node) {
                                case "" :
                                case "all" : $ps_args = "-A";
                                    break;
                                default : $ps_args = "--node $node";
                            }
                            break;
                        default :
                            switch ($node) {
                                case "" :
                                case "all" : $ps_args = "-U $user";
                                    break;
                                default : $ps_args = "--node $node";
                                    if (!is_int($user))
                                        $name = posix_getpwnam($user); $user = $name["uid"];
                                    $ps_grep = "| grep $user";
                            }
                    }

                    ## ==================================================================
                    ##
                    ## ps results
                    ##
                    ## ==================================================================

                    exec("ps $ps_args Snh -o user,pid,enode,pcpu,pmem,nice,tty,stat,time,comm,start $ps_grep 2>&1", $ps_output, $ps_return);

                    # sort results if any
                    if ($ps_return == 0 and sizeof($ps_output) != 0) {

                        foreach ($ps_output as $line) {
                            list($uid, $pid, $enode, $pcpu, $pmem, $nice,
                                    $tty, $stat, $time, $comm, $start, $startext) = sscanf($line, "%s %s %s %s %s %s %s %s %s %s %s %s");

                            $userinfo = posix_getpwuid($uid);
                            # NIS user
                            if (!is_array($userinfo))
                                $userinfo = split(":", yp_match(yp_get_default_domain(), "passwd.byname", $uid));
                            # skip system processes if requested
                            if ($PREVENT_SYSPROC_MIGR and $no_syst and $uid < $SYSPROC_MAXUID)
                                continue;

                            $procs[$pid] = array("user" => trim($userinfo["name"]),
                                "uid" => trim($uid),
                                "node" => trim($enode),
                                "pid" => trim($pid),
                                "pcpu" => trim($pcpu),
                                "pmem" => trim($pmem),
                                "nice" => trim($nice),
                                "tty" => trim($tty),
                                "stat" => trim($stat),
                                "start" => trim($start . " " . $startext),
                                "time" => trim($time),
                                "comm" => trim($comm));
                        }

                        if (is_array($procs)) {

                            # transpose procs array, to retrieve users and nodes information
                            $trans_procs = array_transpose($procs);
                            $users = array_unique($trans_procs['user']);
                            sort(&$users);
                            $nodes = array_unique($trans_procs['node']);
                            sort(&$nodes);

                            # sort results if needed
                            if ($sort_by)
                                aasort($procs, explode(",", $sort_by));

                            $color1 = $display_vars["color_gray_lighter"];
                            $color2 = $display_vars["color_white"];
                            $row_count = 0;
                        } else
                            $ps_error = 2;
                    } else
                        $ps_error = 1;

                    ## ==================================================================
                    ##
                    ## options
                    ##
                    ## ================================================================== 
                    ?>


                    <form class="append-bottom" name="options" action="<?= $_SERVER['PHP_SELF'] ?>">
                        <input type="hidden" name="s" value="<?= $sort_by ?>"/>
                        <div class="options">
                            <div class="nodehead">Options (<a href="processes.php">Reset filters</a>)</div>
                            <table> 
                                <? foreach (array("user") as $type) { ?>
                                    <tr>
                                        <td width="150px">Select <?= $type ?> to display</td>
                                        <td>
                                            <select name="<?= $type[0] ?>" onchange="javascript:this.form.submit()">
                                                <option value="">all</option> <?
                                if (sizeof(${$type . 's'}) != 0) {
                                    foreach (${$type . 's'} as $item) {
                                            ?>
                                                        <option <? if (${$type} == $item)
                                                echo "selected=\"selected\""; ?> value="<?= $item ?>"><?= $item ?></option> <?
                                            }
                                        }
                                    ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?
                                }
                                if ($PREVENT_SYSPROC_MIGR) {
                                    ?>
                                    <tr><td colspan="2">
                                            <input type="checkbox" name="ns" <? if ($no_syst)
                                        echo "checked=\"checked\""; ?> onclick="javascript:this.form.submit()"/>Hide system processes (uid &#060; <?= $SYSPROC_MAXUID ?>)</td></tr>
<? } ?>
                            </table>
                        </div>

                        <div class="spacer"></div>
                    </form>

                    <? if ($migr_msg != "") { ?>
                        <div class="warning_<?= $migr_color ?>"><?= $migr_msg ?></div> <?
                }

                ## ==================================================================
                ##
                ## ps errors
                ##
                ## ==================================================================

                if ($ps_error) {
                    ?>
                        <h1>Error</h1>
                        <div class="warning_red">
                            Your request did not output any result. <br /> <?
                    switch ($ps_error) {
                        case 1:
                            if (sizeof($ps_output) != 0) {
                                ?>
                        </div>
                        <b>Error output: </b> <br />
                        <pre> <? foreach ($ps_output as $line) { ?>
                                        <?= $line ?><br /> <? } ?>
                        </pre>
                        <?
                                }
                                break;
                            case 2:
                                ?>
                                Try to query again, with less strict parameters.
                                <?
                                break;
                        }
                        ?>
                    </div>
                </div>
                </div>

                <? display_footer(); ?>

                </body>
                </html>
                <?
                exit;
            }

## ==================================================================
##
## processes table display
##
## ================================================================== 
            ?>


<table cellspacing="2" class="procs append-bottom">
    <tr class="procheader"> <?
            foreach (array("user" => "user",
        "uid" => "uid",
        "node" => "node",
        "pid" => "pid",
        "pcpu" => "%cpu",
        "pmem" => "%mem",
        "nice" => "nice",
        "tty" => "tty",
        "stat" => "state",
        "start" => "start",
        "time" => "time",
        "comm" => "command") as $key => $text) {
                ?>
            <td><?= $text ?> <a class="procheader"
                                href="processes.php?u=<?= $user ?>&#038;ns=<?= $no_syst ?>&#038;n=<?= $node ?>&#038;s=d<?= $key ?>">^</a>/<a
                                class="procheader"
                                href="processes.php?u=<?= $user ?>&#038;ns=<?= $no_syst ?>&#038;n=<?= $node ?>&#038;s=a<?= $key ?>">v</a>
            </td> <? } ?>
    </tr>

    <?
    foreach ($procs as $proc) {
        $row_color = ($row_count % 2) ? $color1 : $color2;
        # highlight migrated process
        if ($proc["pid"] == $migr_pid and $migr_node != 0) {
            $row_color = $display_vars["color_" . $migr_color . "_light"];
        }
        ?>

        <tr style="background-color:<?= $row_color ?>"> <?
        foreach ($proc as $key => $field) {
            switch ($key) {
                case "pid" :
                    ?>
                        <td><a class="pid" title="migrate process <?= $field ?>"
                               href="processes.php?u=<?= $user ?>&#038;ns=<?= $no_syst ?>&#038;n=<?= $node ?>&#038;pid=<?= $field ?>&#038;mt=<?= $migr_node ?>&#038;s=<?= $sort_by ?>"><?= $field ?></a></td> <?
                break;
            case "pcpu" :
            case "pmem" :
                $color = (($field < 30) ? $display_vars["color_green_light"] :
                                (($field < 75) ? $display_vars["color_orange_light"] : $display_vars["color_red_light"]));
                ?>
                        <td bgcolor="<?= $color ?>"><?= $field ?></td> <?
                break;
            case "user":
            case "comm" :
                ?>
                        <td align="left"><?= $field ?></td> <?
                break;
            default :
                ?>
                        <td><?= $field ?></td> <?
        }
    }
    ?>
        </tr> <?
    $row_count++;
}
    ?>
</table>
</div>  

</div>
</div>


<? display_footer(); ?>

</body>
</html>


<?
# vim:set tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
?>
