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
  common.inc.php
  openssi-webview common parts
  ---------------------------------------------------------------------
  Started      on Mon, 26 Apr 2004 16:20:08 +0200
  Last updated on Thu, 11 Nov 2004 15:09:00 +0100
  ---------------------------------------------------------------------
 */

## ======================================================================
##
##  Variables & constants
##
## ======================================================================
## Constants
define(OW_VERSION, "0.3");
define(CONFIGFILE, "config.php");
define(CLUSTERTAB, "/etc/clustertab");
define(DATADIR, "./graphs/data");
define(ENABLECACHE, "true");
define(CACHEFILE, "/tmp/ow_nodeinfo.tmp");
define(CACHETIME, 60); # cache expiration time (sec)

define(LOAD_LOW, 300);
define(LOAD_HIGH, 500);

## Global variables -----------------------------------------------------
$stats_what = array("cpu" => "CPU usage",
    "loadavg" => "Load average",
    "mem" => "Memory usage",
    "swap" => "Swap usage",
    "proc" => "Processes",
    "net" => "Network traffic");

$states = array("up", "transit", "down", "fault");

$trnetboot = array("P" => "PXE boot",
    "E" => "Etherboot");
$trstate = array("COMINGUP" => "transit",
    "UP" => "up",
    "SHUTDOWN" => "transit",
    "GOINGDOWN" => "transit",
    "KCLEANUP" => "transit",
    "UCLEANUP" => "transit",
    "DOWN" => "fault",
    "NEVERUP" => "down");

## Common display variables ---------------------------------------------
$display_vars = array("color_blue_light" => "#CCDDFF",
    "color_blue" => "#14b4ff",
    "color_blue_dark" => "#0077AA",
    "color_white" => "#FFFFFF",
    "color_black" => "#000000",
    "color_gray_lighter" => "#f7f7f7",
    "color_gray_light" => "#CCCCCC",
    "color_gray" => "#d3d2d0",
    "color_gray_dark" => "#555555",
    "color_gray_darker" => "#333333",
    "color_green_light" => "#D8F8D8",
    "color_green" => "#008000",
    "color_red_light" => "#FFCCCC",
    "color_red" => "#FF0000",
    "color_orange_light" => "#FFEEAA",
    "color_orange" => "#FFA500",
    "font_size_small" => "10px",
    "font_size_medium" => "14px",
    "font_size_menu" => "11px",
    "font_size_big" => "12px",
    "font_size_kingsize" => "69px",
    "font_family" => "arial, helvetica, sans-serif;",
    "font_family_fixed" => "Andale Mono, Monaco, Courier New, Courier, monospace, fixed, sans-serif monospace");


## ======================================================================
##
##  Objects
##
## ======================================================================
## Object declarations --------------------------------------------------

class ssi_node {

    var $id, $ip, $mac, $hostname, $netboot, $init, $devboot;
    var $status, $load;
    var $numcpus, $cpuname, $cpufreq, $totalmem;
    var $details;

    function get($what) {
        return $this->$what;
    }

    function set($what, $i) {
        $this->$what = $i;
    }

}

## ======================================================================
##
##  Functions
##
## ======================================================================
## Data related functions -----------------------------------------------

function get_clustername() {
    $clustername = @exec("/sbin/clustername", $output, $retval);
    if ($retval != 0)
        return "<h2 class=\"warning\">Warning! clustername is not set</h2>";
    else
        return "<h2>&raquo; $clustername</h2>\n";
}

function get_nodes() {
    /* Get nodes info from clustertab file */
    $all_nodes = populate_nodes();

    /* Sort nodes (init nodes, nodes) */
    foreach ($all_nodes as $node) {
        if ($node->init != 0)
            $initnodes[] = $node;
        else
            $nodes[] = $node;
    }
    return array($allnodes, $initnodes, $nodes);
}

function populate_nodes() {
    /* Serve from cache if it younger than CACHETIME,
      and if clustertab has not changed since last update */
    if (ENABLECACHE == "true" and file_exists(CACHEFILE)
            and time() - CACHETIME < filemtime(CACHEFILE)
            and filemtime(CACHEFILE) > filemtime(CLUSTERTAB)) {
        $s = implode("", @file(CACHEFILE));
        $nodes = unserialize($s);
        return $nodes;
    } else {
        $comment = "#";
        $handle = fopen(CLUSTERTAB, "r");
        while (!feof($handle)) {
            $line = trim(fgets($handle));
            if ($line && !ereg("^$comment", $line)) {
                list($id, $ip, $mac, $netboot, $init, $devboot) = explode("\t", $line);
                $hostname = explode('.', gethostbyaddr($ip));
                $hostname = $hostname[0];
                $nodes[$id] = new ssi_node();
                $nodes[$id]->set('id', $id);
                $nodes[$id]->set('ip', $ip);
                $nodes[$id]->set('mac', $mac);
                $nodes[$id]->set('hostname', $hostname);
                $nodes[$id]->set('netboot', $netboot);
                $nodes[$id]->set('init', $init);
                $nodes[$id]->set('devboot', $devboot);
                $nodes[$id]->set('status', exec("/sbin/clusternode_getstate $id"));
                $nodes[$id]->set('load', get_node_load($id));
                $nodes[$id]->set('details', get_node_details($id));
                list($numcpus, $cpuname, $cpufreq) = get_node_cpuinfo($id);
                $nodes[$id]->set('numcpus', $numcpus);
                $nodes[$id]->set('cpuname', $cpuname);
                $nodes[$id]->set('cpufreq', $cpufreq);
                $nodes[$id]->set('totalmem', exec("onnode -lp $id free -m | grep Mem: | awk '{print $2}'"));
            }
        }
        fclose($handle);
        $s = serialize($nodes);
        $fp = fopen(CACHEFILE, "w");
        fwrite($fp, $s);
        fclose($fp);
        return $nodes;
    }
}

function get_node_cpuinfo($id) {
    $fd = popen("onnode -lp $id cat /proc/cpuinfo", "r");
    while ($buf = fgets($fd, 4096)) {
        list($key, $value) = preg_split('/\s+:\s+/', trim($buf));
        switch ($key) {
            case 'processor':
                $numcpus += 1;
                break;
            case 'model name':
                $cpuname = $value;
                break;
            case 'cpu MHz':
                $cpufreq = $value;
                break;
        }
    }
    pclose($fd);
    return array($numcpus, $cpuname, $cpufreq);
}

function get_node_load($id) {
    exec("/usr/bin/loads -n $id", $raw_load, $retval);
    $load = explode(':', $raw_load[0]);
    $load = trim($load[1]);
    if ($retval != 0) {
        $load = "N/A";
    }
    return $load;
}

function get_node_details($id) {
    exec("/bin/cluster -V $id", $raw_details, $retval);
    foreach ($raw_details as $value) {
        $line = explode(':', $value);
        $details[trim($line[0])] = trim(implode(':', array_slice($line, 1)));
    }
    return $details;
}

## Display functions ----------------------------------------------------

function display_node_details(&$node) {
    $display_vars = $GLOBALS["display_vars"];
    $details = get_node_details($node->id);
    reset($details);

    $color1 = $display_vars["color_gray_lighter"];
    $color2 = $display_vars["color_white"];
    $row_count = 0;
    ?>

    <table cellspacing="0" class="details">
        <tr>
            <td class="item"><?= key($details);
    next($details) ?></td>
            <td class="item" align="right"><?= $node->get('hostname'); ?></td>
        </tr>
        <tr><td colspan="2" class="subdetail">
                <?
                $node->get('numcpus') ? print($node->get('numcpus') . "x " . $node->get('cpuname') . " @" .
                                        floor($node->get('cpufreq')) . "MHz, " .
                                        $node->get('totalmem') . "MB RAM")
                                 : print("no information available") ;
                ?></td>
        </tr> <?
            while (list($key, $val) = each($details)) {
                $row_color = ($row_count % 2) ? $color1 : $color2;
                    ?>
            <tr style="background-color:<?= $row_color ?>">
                <td style="font-weight:bold;"><?= $key ?></td> <?
        $status = strtr($val, $GLOBALS["trstate"]);
        switch ($status) {
            case "up" : $text_style = "font-weight:bold; color:" . $display_vars["color_green"];
                break;
            case "transit" : $text_style = "font-weight:bold; color:" . $display_vars["color_orange"];
                break;
            case "fault" : $text_style = "font-weight:bold; color:" . $display_vars["color_red"];
                break;
            case "down" : $text_style = "font-weight:bold; color:" . $display_vars["color_black"];
                break;
            default : $text_style = "black";
                break;
        }
                    ?>
                <td style="<?= $text_style ?>"><?= $val ?></td>
            </tr> <?
        $row_count++;
    }
                ?>
    </table> <?
}

function display_node_load(&$node) {
    $display_vars = $GLOBALS["display_vars"];
    $load = $node->get('load');
    $width = min(sqrt(10 * $load), 100);
    $bgcolor = $display_vars["color_gray_lighter"];
    if (!$load or $load == "N/A") {
        $color = $display_vars["color_gray_light"];
        $width = 100;
    } else {
        $color = (($load < LOAD_LOW) ? $display_vars["color_green"] :
                        (($load < LOAD_HIGH) ? $display_vars["color_orange"] : $display_vars["color_red"]));
    }
                ?>
    <div style="position:relative; width:100%; background-color:<?= $bgcolor ?>">
        <div class="coloredbar" style="background-color:white; width:100%;">&nbsp;</div>
        <div class="coloredbar" style="background-color:<?= $color ?>; width:<?= $width ?>%;">&nbsp;</div>
        <div class="loadcontainer">
            <div class="shadow">load <?= $load ?><div class="shadowed">load <?= $load ?></div></div>
        </div>
    </div> <?
}

function display_node_info(&$node) {
                ?>
    <table class="properties">
        <tr><td class="item">node <?= $node->get('id'); ?></td>
            <td class="load"><? display_node_load($node); ?></td></tr>
        <tr><td class="subitem">IP</td>
            <td><?= $node->get('ip') ?></td></tr>
        <tr><td class="subitem">MAC</td>
            <td><?= $node->get('mac') ?></td></tr> <? if ($node->get('init') == 0) { ?>
            <tr><td class="subitem">boot type</td>
                <td><?= strtr($node->get('netboot'), $GLOBALS["trnetboot"]); ?></td></tr> <? } elseif ($node->get('init') == 1) { ?>
            <tr><td colspan="2" class="item">init node</td>
            </tr>
            <tr><td class="subitem">boot device</td>
                <td><?= $node->get('devboot'); ?></td></tr> <? } ?>
    </table> <?
}

## Common interface display functions -----------------------------------

function check_ssi_system() {
    if (!@exec("/bin/clusternode_num")) {
                    ?>
        <div class="error">The current system is not openSSI enabled, aborting...</div>
        </div>
        </div>
        </div>

        <? display_footer(); ?>

        </body>
        </html>
        <?
        exit;
    }
}

function display_header() {
    ?>
    <div id="header">
        <div class="container">
            <h1 class="span-5"><a href="index.php" title="Cluster Monitoring System" class="span-5"><span>openSSI webView</span></a></h1>
            <ul class="nav-bar-top right">
                <li><a href="about.php" title="about">About</a></li>
                <li><a href="settings.php" title="settings">Settings</a></li>
            </ul>
        </div>
    </div> 
    <?
}

function javascript_warning() {
    ?>
    <noscript>
    <div class="warning">
        <h3>This page uses JavaScript</h3>
        <div class="indented">This page make intensive use of JavaScript to display information about cluster nodes.<br />
            You should either activate Javascript in your browser, or use a Javascript-enabled browser, in order to see all information displayed.
        </div>
    </div>
    </noscript> <?
}

function display_intro($location) {
    ?>
    <div id="intro"> <?
    switch ($location) {
        case "options":
            ?>
                <h2 class="title settings">Settings</h2>
                <p>Here you can configure some openSSI webView parameters.</p>
                <?
                break;
            case "about":
                ?>
                <h2 class="title about">About openSSI-webView</h2>
                <p class="version">Version <?= OW_VERSION ?></p>
                <p>openSSI-webView is designed to be a simple and easy-to-use monitoring system for your <a href="http://openssi.org">openSSI</a> cluster. Its goal is to provide a quick overview of the cluster state, by graphing vital functions and graphically representing key figures. It also aims to provide a simple illustration of process migration, by allowing users to migrate their processes across the cluster.</p>

                <?
                break;
            case "overview":
                ?>
                <h2 class="title overview">Overview</h2>
                <p>Welcome to openSSI-webView, a simple openSSI monitoring web interface. Here you can have a quick overview of your openSSI cluster, keep an eye on each node status (offline, comingup, up...) and load. Clicking on nodes icons brings you to the node statistics page.</p>
                <?
                break;
            case "show_nodes":
                ?>
                <h2 class="title clustermap">Cluster Map</h2>
                <p>Here you can view your cluster nodes, and some stats about them: load, current state, boot time, hardware details, and so on.
                    You can hover computer icons to display more information, and clicking them will bring the statistics page.</p>
                <?
                break;
            case "stats":
                ?>
                <h2 class="title graphandstats">Stats</h2>
                <p>Here you can view some graphical statistics about your openSSI cluster:</p>
                <ul>
                    <li><a href="#overview">Cluster overview</a> gathers links bringing to thematic stats pages, showing data accross the cluster. </li>
                    <li><a href="#loads">Load overview</a> shows openSSI cumulated load for each node on the cluster.</li>
                    <li><a href="#nodes">Node overview</a> gives statistics by node.</li>
                </ul>
                <?
                break;
            case "loads":
                ?>
                <h2 class="title graphandstats">Stats &raquo; Loads</h2>
                <p>Here is a graphical overview of your openSSI cluster load.</p>
                <?
                break;
            case "processes":
                ?>
                <h2 class="title processes">Processes</h2>
                <p>Here you can view all the processes running on your openSSI cluster. You can focus your attention on a specific node, or user, and even
                    migrate user processes from one node to another.</p>
                <?
                break;
            case "cpu":
                ?>
                <h2 class="title graphandstats">Stats &raquo; cpu</h2>
                Here is a graphical overview of the cpu usage on selected nodes. <br />
                NB: SMP computers have a maximal cpu occupation percentage of [number of cpus x 100].
                <?
                break;
            default: # sort of 404
                $stats_what = $GLOBALS["stats_what"];
                if (array_key_exists($location, $stats_what)) {
                    ?>
                    <h2 class="title graphandstats">Stats &raquo; <?= $stats_what[$location] ?></h2>
                    <p>Here is a graphical overview of the <?= strtolower($stats_what[$location]) ?> on selected nodes.</p> <? } else { ?>
                    <h2>404</h2>
                    <p>I don't know where I am, so I can't say anything about the page you're currently viewing.
                        Well, hmm, how are you? :)</p>
                </div>
                </div>
                </div>

                <? display_footer(); ?>

                </body>
                </html>
                <?
                exit;
            }
            break;
    }
    ?>
    </div>
    <?
}

function display_menu() {
    $display_vars = $GLOBALS["display_vars"];
    ?>
    <div id="menu" class="span-5">
        <h3>Menu</h3>
        <ul>
            <li><a href="index.php" title="overview">Overview</a></li>
            <li><a href="show_nodes.php" title="nodes">Cluster Map</a></li>
            <li><a href="graph_disp.php?t=stats" title="stats">Stats &amp; Graphs</a></li>
            <li><a href="processes.php?ns=1" title="processes">Show Processes</a></li>
        </ul>
    </div> <?
}

function display_footer() {
    ?>
    <div id="footer">
        <div class="container">
            <table width="100%">
                <tr><td width="90px">
                        <a href="http://validator.w3.org/check?uri=referer"><img
                                src="http://www.w3.org/Icons/valid-xhtml10"
                                alt="Valid XHTML 1.0!" height="31" width="88" /></a></td>
                    <td class="footer">Copyright &copy; 2004 - <?php echo date('Y'); ?> Kilian CAVALOTTI. All rights reserved.<br/>
                        Software released under the <a href="http://www.cecill.info/licences/Licence_CeCILL_V1-US.txt">CeCILL</a> licence</td>
                    <td class="footer" style="text-align:right;">Last modified: <?= date("F d Y H:i:s", getlastmod()); ?></td>
                </tr>
            </table>
        </div>
    </div> <?
}

## Graphing functions ---------------------------------------------------

function graph($RRD_opts) {

    /* with php-rrdtool bindings */
    if (function_exists('rrd_graph')) {

        /* rrd_graph can only output to a file, so we use a temporary buffer */
        $f = tempnam("/tmp", "rrd");

        $graph = rrd_graph($f, $RRD_opts, count($RRD_opts));

        if (!is_array($graph)) {
            $err = rrd_error();
            ?>
            <h1>Error</h1>
            An error occured while trying to create the requested graph.<br />
            rrd_graph() ERROR: <?= $err ?> <?
        } else {
            if (!headers_sent())
                header("Content-type: image/png");
            readfile($f);
        }

        unlink($f);

        /* without php-rrdtool, use shell call */
    } else {
        /* get rrdtool path from CONFIGFILE */
        include_once(CONFIGFILE);
        /* surround strings with spaces with quotes */
        foreach ($RRD_opts as $i => $line) {
            $RRD_opts[$i] = preg_replace("/(=|:)([^:]*(\\\\|\s)+.+)/", "\$1\"\$2\"", $line);
        }

        $cmd = $RRDTOOL_PATH . " graph - " . implode(" ", $RRD_opts);
        $output = passthru($cmd . " 2>&1", $ret);

        if ($ret != 0) {
            ?>
            <h1>Error</h1>
            An error occured while trying to create the requested graph.<br />
            ERROR: <?= $output ?> <?
        } else {
            if (!headers_sent())
                header("Content-type: image/png");
            print $output;
        }
    }
}

function list_files($dir) {
    $files = array();
    $d = opendir($dir);
    while ($f = readdir($d)) {
        if (is_file("$dir/$f") and ereg("^([0-9]+)_load.rrd$", $f, $num))
            $files[$num[1]] = basename($f);
    }
    closedir($d);
    asort($files);
    return $files;
}

function find_path($binary) {
    $search_paths = array("/bin", "/sbin", "/usr/bin", "/usr/sbin", "/usr/local/bin", "/usr/local/sbin");
    for ($i = 0; $i < count($search_paths); $i++) {
        $testpath = $search_paths[$i] . "/" . $binary;
        if ((file_exists($testpath)) && (is_executable($testpath))) {
            return $testpath;
        }
    }
}

# generate rainbow RGB colors

function gen_color($num_colors, $l, $h) {
    $red_states = array($h, $h, $l, $l, $l, $h, $h, $h);
    $green_states = array($l, $h, $h, $h, $l, $l, $l, $h);
    $blue_states = array($l, $l, $l, $h, $h, $h, $l, $l);
    $functions = array("RRs" => "red_states", "GGs" => "green_states", "BBs" => "blue_states");

    $length = sizeof($red_states) - 1; // first index is 0
    $step = ($length - 1) / $num_colors;
    $x = 1;

    for ($i = 0; $i <= $num_colors; $i++) {

        $xdec = $x - floor($x);
        $xinf = floor($x);
        $xsup = $xinf + 1;
        $xarrayinf = $xinf - 1;
        $xarraysup = $xsup - 1;

        foreach ($functions as $code => $function) {
            $diff = ${$function}[$xarrayinf] - ${$function}[$xarraysup];
            $a = -$diff;

            if ($diff > 0)
                $out = $h + $a * $xdec;
            elseif ($diff < 0)
                $out = $l + $a * $xdec;
            else
                $out = ${$function}[$xarrayinf];

            $hex = sprintf("%02X", round(255 * $out));
            ${$code}[] = $hex;
        }

        $x += $step;
        $RGB[] = end($RRs) . end($GGs) . end($BBs);
    }
    return $RGB;
}

## Arrays manipulation functions ----------------------------------------

function array_transpose($array) {
    $aux = Array();
    foreach ($array as $keymaster => $value)
        foreach ($value as $key => $element)
            $aux[$key][$keymaster] = $element;
    return $aux;
}

# multicriteria sort function

function aasort(&$array, $args) {
    foreach ($args as $arg) {
        $order_field = substr($arg, 1, strlen($arg));
        foreach ($array as $array_row) {
            $sort_array[$order_field][] = $array_row[$order_field];
        }
        $sort_rule .= '$sort_array[' . $order_field . '], ' . ($arg[0] == "d" ? SORT_DESC : SORT_ASC) . ',';
    }
    eval("array_multisort($sort_rule" . ' &$array);');
}

# vim:set tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
?>
