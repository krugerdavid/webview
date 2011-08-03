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
    graph.php
    openssi-webview graphing functions
    ---------------------------------------------------------------------
    Started      on Wed, 01 Sep 2004 11:11:48 +0200
    Last updated on Thu, 11 Nov 2004 15:05:00 +0100
    ---------------------------------------------------------------------
*/

include_once('./common.inc.php');

$node      = $_GET['n'];
$size      = $_GET['s'];
$type      = $_GET['t'];
$start     = $_GET['st'];

switch ($size) {
    case "big":
        $height = 120;
        $width  = 520;
        $legend = 1;
        break;
    case "small":
        $height = 90;
        $width  = 270;
        $legend = 0;
        $start  = "12h";
        break;
}

if (!$width)  $width  = $_GET['w'];
if (!$height) $height = $_GET['h'];


switch ($type) {
    case "loads":
        $title        = "Cumulated openSSI loads";
        $files        = list_files(DATADIR);
        $nodes_num    = sizeof($files);
        $linewidth    = floor($width/100);
        $dark_colors  = gen_color($nodes_num, 0,   1);
        $light_colors = gen_color($nodes_num, 0.7, 1);
        foreach ($files as $i => $file) {
            $j = ($i - 1); // color index
            (($i % $linewidth) == 0) ? $END = "\\n" : $END = ""; // newline each $linewidth items
            $DEFs[]  = "DEF:load$i=".DATADIR."/$file:load:AVERAGE";
            $AREAs[] = "STACK:load$i#$light_colors[$j]:Load #$i\:";
            $LINEs[] = "STACK:load$i#$dark_colors[$j]";
            $AREAs[] = "GPRINT:load$i:AVERAGE:%4.0lf $END";
        }
        $CDEFs    = array("CDEF:z=load1,0,*");
        $AREAs    = array_merge(array("AREA:z#000000"), $AREAs);
        $LINEs    = array_merge(array("LINE1:z#000000"), $LINEs);
        break;
    case "mem":
        $title     = "memory usage (node $node)";
        $vertlabel = "memory";
        $DEFs  = array ("DEF:used=".DATADIR."/${node}_mem.rrd:used:AVERAGE",
                        "DEF:free=".DATADIR."/${node}_mem.rrd:free:AVERAGE",
                        "DEF:tota=".DATADIR."/${node}_mem.rrd:total:AVERAGE",
                        "DEF:shar=".DATADIR."/${node}_mem.rrd:shared:AVERAGE",
                        "DEF:buff=".DATADIR."/${node}_mem.rrd:buffers:AVERAGE",
                        "DEF:cach=".DATADIR."/${node}_mem.rrd:cached:AVERAGE");
        $AREAs  = array("AREA:used#9FA4EE:Used",         "GPRINT:used:LAST:   %6.2lf %s ",
                        "STACK:free#D2D8F9:Free",        "GPRINT:free:LAST:   %6.2lf %s\\n",
                        "LINE1:cach#0A00CC:Cached",      "GPRINT:cach:LAST: %6.2lf %s ",
                        "AREA:buff#A766FF:Buffers",      "GPRINT:buff:LAST:%6.2lf %s ",
                        "LINE1:shar#D9B3FF:Shared",      "GPRINT:shar:LAST: %6.2lf %s\\n",
                        "LINE1:tota#FF0000:Total",       "GPRINT:tota:LAST:  %6.2lf %s\\n");
        break;
    case "cpu":
        $title     = "cpu usage (node $node)";
        $vertlabel = "cpu percentage";
        $DEFs  = array ("DEF:user=".DATADIR."/${node}_cpu.rrd:user:AVERAGE",
                        "DEF:nice=".DATADIR."/${node}_cpu.rrd:nice:AVERAGE",
                        "DEF:syst=".DATADIR."/${node}_cpu.rrd:system:AVERAGE",
                        "DEF:idle=".DATADIR."/${node}_cpu.rrd:idle:AVERAGE");
        $AREAs  = array("AREA:user#000F99:User",         "GPRINT:user:LAST:  %8.2lf %%\\n",
                        "STACK:nice#4668E4:Nice",        "GPRINT:nice:LAST:  %8.2lf %%\\n",
                        "STACK:syst#9FA4EE:System",      "GPRINT:syst:LAST:%8.2lf %%\\n",
                        "STACK:idle#D2D8F9:Idle",        "GPRINT:idle:LAST:  %8.2lf %%\\n");
        break;
    case "swap":
        $title     = "swap usage (node $node)";
        $vertlabel = "swap memory";
        $DEFs  = array ("DEF:used=".DATADIR."/${node}_swap.rrd:used:AVERAGE",
                        "DEF:free=".DATADIR."/${node}_swap.rrd:free:AVERAGE",
                        "DEF:tota=".DATADIR."/${node}_swap.rrd:total:AVERAGE");
        $AREAs  = array("AREA:used#4668E4:Used",         "GPRINT:used:LAST:  %6.2lf %s  ",
                        "STACK:free#D2D8F9:Free",        "GPRINT:free:LAST:  %6.2lf %s\\n",
                        "LINE1:tota#FF0000:Total",       "GPRINT:tota:LAST: %6.2lf %s\\n" );
        break;
    case "loadavg":
        $title     = "load average (node $node)";
        $vertlabel = "load average";
        $DEFs  = array ("DEF:15m=".DATADIR."/${node}_loadavg.rrd:15mn:AVERAGE",
                        "DEF:05m=".DATADIR."/${node}_loadavg.rrd:5mn:AVERAGE",
                        "DEF:01m=".DATADIR."/${node}_loadavg.rrd:1mn:AVERAGE");
        $CDEFs = array ("CDEF:high=01m,1,GT,INF,0,IF");
        $AREAs = array ("AREA:high#D2D8F9:High load [ > 1 ]\\n",
                        "AREA:15m#9FA4EE:15mn average",   "GPRINT:15m:LAST:%4.2lf",
                        "LINE1:05m#4668E4:5mn average",   "GPRINT:05m:LAST: %4.2lf",
                        "LINE1:01m#FF0000:1mn average",   "GPRINT:01m:LAST: %4.2lf");
        break;
    case "proc":
        $title     = "processes (node $node)";
        $vertlabel = "processes";
        $DEFs  = array ("DEF:load=".DATADIR."/${node}_proc.rrd:loadleveled:AVERAGE",
                        "DEF:stat=".DATADIR."/${node}_proc.rrd:static:AVERAGE",
                        "DEF:tota=".DATADIR."/${node}_proc.rrd:total:AVERAGE");
        $AREAs = array ("AREA:stat#D2D8F9:Static",       "GPRINT:stat:LAST: %5.0lf %s",
                        "STACK:load#4668E4:Loadleveled", "GPRINT:load:LAST: %5.0lf %s\\n",
                        "LINE1:tota#FF0000:Total",       "GPRINT:tota:LAST:  %5.0lf %s\\n" );
        break;
    case "net":
        $title     = "network traffic (node $node)";
        $vertlabel = "bytes/sec";
        $DEFs  = array ("DEF:in=".DATADIR."/${node}_eth.rrd:in:AVERAGE",
                        "DEF:out=".DATADIR."/${node}_eth.rrd:out:AVERAGE",
                        "DEF:inerr=".DATADIR."/${node}_eth.rrd:in_errors:AVERAGE",
                        "DEF:outerr=".DATADIR."/${node}_eth.rrd:out_errors:AVERAGE",
                        "DEF:indrop=".DATADIR."/${node}_eth.rrd:in_dropped:AVERAGE",
                        "DEF:outdrop=".DATADIR."/${node}_eth.rrd:out_dropped:AVERAGE",
                        );
        $CDEFs = array ("CDEF:out_neg=out,-1,*",
                        "CDEF:cdefinerr=inerr,0,GT,INF,0,IF",
                        "CDEF:cdefindrop=indrop,0,GT,INF,0,IF",
                        "CDEF:cdefouterr=outerr,0,GT,INF,0,IF",
                        "CDEF:cdefoutdrop=outdrop,0,GT,INF,0,IF",
                        );
        $AREAs = array ("COMMENT:Errors\\n",
                        "AREA:cdefindrop#FFD966:Discards In",
                        "AREA:cdefinerr#FFC4B3:Errors In ",
                        "AREA:cdefoutdrop#B3D7FF:Discards Out",
                        "AREA:cdefouterr#B6FFB3:Errors Out\\n",
                        "COMMENT:Traffic\\n",
                        "LINE1:in#32CD32:Incoming",
                        "GPRINT:in:MAX:  Max\\: %6.1lf %s",
                        "GPRINT:in:AVERAGE:Avg\\: %6.1lf %S",
                        "GPRINT:in:LAST: Current\\: %6.1lf %Sbytes/sec\\n",
                        "LINE1:out#0099CC:Outgoing",
                        "GPRINT:out:MAX:  Max\\: %6.1lf %S",
                        "GPRINT:out:AVERAGE:Avg\\: %6.1lf %S",
                        "GPRINT:out:LAST: Current\\: %6.1lf %Sbytes/sec",
                        "HRULE:0#000000");
        break;
    default: ?>
        <h1>Error</h1>
        Wrong parameters passed to the graph creation function<br /> <?
        exit;
}

($size == "small") ? $vertlabel = "" : $vertlabel;

$RRD_opts = array(  "--start", "-$start",
                    "--imgformat=PNG",
                    "--lazy",
                    "--height=$height",
                    "--width=$width",
                    "--alt-autoscale-max",
                    "--lower-limit=0",
                    "--color", "BACK#FFFFFF",
                    "--color", "SHADEA#FFFFFF",
                    "--color", "SHADEB#FFFFFF",
                    "--title=$title",
                    "--vertical-label=$vertlabel");

!$legend ? $RRD_opts[] = "--no-legend" : $RRD_opts;


foreach (array ("DEFs", "CDEFs", "AREAs", "LINEs") as $arr) {
    if (is_array(${$arr})) $RRD_opts = array_merge($RRD_opts, ${$arr});
}

graph($RRD_opts);

# vim:set tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
?>

