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
    style.php
    openssi-webview CSS definitions
    ---------------------------------------------------------------------
    Started      on Mon, 26 Apr 2004 16:20:08 +0200
    Last updated on Wed, 10 Nov 2004 15:24:00 +0100
    ---------------------------------------------------------------------
*/

## First, let the browser know (indeed, believe) this is a CSS file.
header('Content-type: text/css');

## Next, define some variables:
require_once('../common.inc.php');
require_once('../config.php');
$display_vars = $GLOBALS['display_vars'];

## now, lets the real style sheet begin ?>

/* ======================================================================
   start of the real css */


/* ----------------------------------------------------------------------
   basic elements */

body {
    margin:              0px;
    padding:             0px;
    text-align:          justify;
    font-family:         <?=$display_vars['font_family']?>;
    font-size:           <?=$display_vars['font_size_small']?>;
    color:               <?=$display_vars['color_gray_darker']?>;
    background-image:    url(h2_bg.jpg);
    background-position: left top;
    background-repeat:   no-repeat; }

h1 {
    margin:              0px;
    margin-bottom:       15px;
    padding:             0px;
    font-size:           28px;
    line-height:         28px;
    font-weight:         bold;
    color:               <?=$display_vars['color_gray']?>; }
h2 {
    padding:             5px;
    line-height:         8px;
    font-weight:         bold;
    font-size:           <?=$display_vars['font_size_big']?>;
    color:               <?=$display_vars['color_gray_dark']?>;
    border:              1px solid <?=$display_vars['color_gray_light']?>;
    background-image:    url(h2_bg.jpg);
    background-position: left top;
    background-repeat:   no-repeat; }
h2.warning {
    color:               <?=$display_vars['color_red']?>; }


a {
    text-decoration:     none;
    font-weight:         bold;
    color:               <?=$display_vars['color_blue']?>;
    font-family:         <?=$display_vars['font_family']?>; }
a:visited {
    color:               <?=$display_vars['color_blue_dark']?>; }
a:hover {
    background-color:    <?=$display_vars['color_gray_lighter']?>; }

a.toggle {
    padding:             1px;
    margin:              1px;
    font-family:         <?=$display_vars['font_family_fixed']?>; }
a.header {
    font-size:           <?=$display_vars['font_size_kingsize']?>;
    color:               <?=$display_vars['color_gray_light']?>; }
a.procheader {
    font-family:         <?=$display_vars['font_family_fixed']?>; }
a.silent {
    color:               none; }
a.pid {
    font-weight :        normal;
    font-family:         <?=$display_vars['font_family_fixed']?>; }

a.header:hover,
a.silent:hover,
a.header:visited,
a.silent:visited {
    color:               none;
    background-color:    transparent; }



/* ----------------------------------------------------------------------
   containers */

#Header {
    overflow:            hidden !important;
    overflow:            visible; /* IE */
    margin:              50px 0px 0px 0px;
    padding:             17px 0px 0px 20px;
    height:              24px;
    border-style:        solid;
    border-color:        <?=$display_vars['color_gray_light']?>;
    border-width:        1px 0px;
    line-height:         11px;
    color:               <?=$display_vars['color_gray']?>;
    font-size:           69px;
    font-weight:         bold;
    background-color:    white;
    <? if ($USE_TRANSPARENCY == true) { ?> opacity:0.5; filter: alpha(opacity=50); <? } ?> }

#Footer {
    padding:             6px;
    margin:              0px;
    margin-top:          20px;
    background-image:    url(h2_bg.jpg);
    background-position: top left;
    background-repeat:   no-repeat;
    border-width:        1px 0px;
    border-style:        solid;
    border-color:        <?=$display_vars['color_gray_light']?>; }

#Content {
    z-index:             0;
    background-color:    white;
    margin:              17px 0px 0px 160px !important;
    margin:              -5px 0px 0px 160px; /* IE */
    padding:             10px;
    top:                 110px;
    width:               720px;
    border:              1px solid <?=$display_vars['color_gray']?>;
    <? if ($USE_TRANSPARENCY == true) { ?> opacity:0.85; filter: alpha(opacity=85); <? } ?> }

#Contentshadow {
    margin:              0px 0px 0px 160px;
    width:               742px;
    background:          url(dropsh.png) repeat-x left top !important;
    background:          url() repeat-x left top; /* IE */ }

#Menu {
    position:            absolute;
    top:                 110px;
    left:                15px;
    width:               120px;
    padding:             10px;
    background-color:    <?=$display_vars['color_gray_lighter']?>;
    border:              1px solid <?=$display_vars['color_gray']?>;
    font-size:           <?=$display_vars['font_size_menu']?>;
    line-height:         17px;
    width:               110px;
    <? if ($USE_TRANSPARENCY == true) { ?> opacity:0.9; filter: alpha(opacity=90); <? } ?> }

#Menu h3 {
    color:               <?=$display_vars['color_gray_dark']?>;
    background-color:    <?=$display_vars['color_gray_light']?>;
    font-size:           <?=$display_vars['font_size_menu']?>;
    line-height:         16px;
    margin:              -10px;
    margin-bottom:       4px;
    padding:             4px;
    padding-left:        10px; }

#Intro {
    color:               <?=$display_vars['color_gray_dark']?>;
    background-color:    <?=$display_vars['color_white']?>;
    padding:             5px 10px;
    border:              1px solid <?=$display_vars['color_gray_light']?>; }

#Intro h3 {
    color:               <?=$display_vars['font_size_menu']?>;
    background-color:    <?=$display_vars['color_gray_lighter']?>;
    font-size:           <?=$display_vars['font_size_medium']?>;
    line-height:         14px;
    margin:              -5px -10px 4px -10px;
    padding:             4px;
    padding-left:        10px; }


form {
    padding :            5px;
    border :             1px solid <?=$display_vars['color_gray_lighter']?>;
    font-size:           <?=$display_vars['font_size_small']?>; }

img {
    border:              0px;
    background-color:    transparent; }
img.graph {
    border:              1px solid <?=$display_vars['color_gray_light']?>; }


table {
    width:               100%;
    font-size:           <?=$display_vars['font_size_small']?>; }
table.details {
    width:               100%;
    text-align:          left;
    background-color:    <?=$display_vars['font_color_white']?>;
    font-size:           <?=$display_vars['font_size_small']?>; }
table.properties {
    width:               100%;
    text-align:          justify;
    font-size:           <?=$display_vars['font_size_small']?>;
    margin-left:         auto;
    margin-right:        auto; }
table.procs {
    text-align:          right;
    font-family:         <?=$display_vars['font_family_fixed']?>;
    font-size:           <?=$display_vars['font_size_medium']?>;
    border:              2px solid <?=$display_vars['color_gray_light']?>; }
tr.procheader {
    text-align:          left;
    background-color:    <?=$display_vars['color_gray_light']?>; }
tr.procheader td {
    padding:             0px 3px; }

td.item {
    background-color:    <?=$display_vars['color_blue']?>;
    color:               <?=$display_vars['color_white']?>;
    font-weight:         bold;
    padding-left:        10px;
    padding-right:       10px; }
td.subitem {
    text-align:          right;
    font-weight:         bold; }
td.subdetail {
    padding-left:        10px;
    padding-right:       10px;
    text-align:          right;
    font-weight:         bold;
    color:               <?=$display_vars['color_blue']?>;
    background-color:    <?=$display_vars['color_gray_lighter']?>;
    border-width:        0px 1px 1px 1px;
    border-style:        solid ;
    border-color:        <?=$display_vars['color_gray_light']?>; }
td.load {
    font-weight:         bold;
    border:              1px solid <?=$display_vars['color_blue']?>; }
td.footer {
    color:               <?=$display_vars['color_gray']?>; }

div.warning {
    padding-left:        10px;
    padding-right:       10px;
    margin:              20px;
    text-align:          justify;
    border:              1px solid <?=$display_vars['color_red']?>; }

div.centertable {
    width:               60%;
    margin-left:         auto;
    margin-right:        auto; }
div.block {
    display:             block; }

<?
foreach (array('green', 'orange', 'red') as $color) { ?>
div.warning_<?=$color?> {
    margin-top:          7px;
    padding:             5px;
    color:               <?=$display_vars['color_gray_dark']?>;
    background-color:    <?=$display_vars['color_'.$color.'_light']?>;
    border:              1px solid <?=$display_vars['color_'.$color]?>; } <?
} ?>

div.indented {
    padding:             0 10px 10px 30px;
    text-align:          justify; }
div.legend {
    text-align:          center;
    font-size:           <?=$display_vars['font_size_small']?>
    font-weight:         bold; }
div.nodehead {
    color:               <?=$display_vars['color_gray_dark']?>;
    padding:             0px 0px 1px 3px;
    background-image:    url(nd_bg.png);
    background-position: bottom left;
    background-repeat:   no-repeat; }


## Network map display

div.cluster {            /* master container */
    width:               100%; }
div.nodes {              /* nodes (slaves, masters) container */
    z-index:             0;
    border:              1px solid <?=$display_vars['color_gray_light']?>;
    padding:             10px; }
div.nodeslight {
    padding:             5px;
    border:              1px solid <?=$display_vars['color_gray_lighter']?>;
    font-size:           <?=$display_vars['font_size_small']?>;}
div.bartight {
    padding:             1px;
    border:              1px solid <?=$display_vars['color_gray_light']?>; }

div.node {               /* node container */
    z-index:             0;
    float:               left;
    margin-top:          5px;
    margin-bottom:       5px;
    margin-left:         auto;
    margin-right:        auto;
    text-align:          center; }
div.nodepicture {        /* node picture */
    z-index:             0;
    position:            relative;
    visibility:          visible;
    padding:             10px;
    margin-left:         auto;
    margin-right:        auto;
    text-align:          center; }
span.nodename {          /* node name */
    visibility:          visible; }
div.nodeinfo {           /* node details */
    z-index:             0;
    visibility:          hidden;
    padding:             2px;
    width:               190px;
    background-color:    <?=$display_vars['color_gray_lighter']?>;
    border:              1px solid <?=$display_vars['color_gray_light']?>;
    margin-left:         auto;
    margin-right:        auto;
    text-align:          center; }

div.spacer {
    clear:               both; }

div.options {
    float:               left;
    width:               49%; }

div.detail {
    <? if ($USE_TRANSPARENCY == true) { ?> opacity:0.9; filter: alpha(opacity=90); <? } ?>
    z-index:             3;
    position:            absolute;
    visibility:          hidden;
    padding:             5px;
    top:                 -290px;
    left:                130px  !important;
    left:                50px;
    width:               350px;
    border:              2px solid <?=$display_vars['color_blue']?>;
    background-color:    <?=$display_vars['color_white']?>; }

div.loadcontainer {
    position:            relative;
    z-index:             2;
    padding-left:        5px;
    background-color:    transparent;
    color:               <?=$display_vars['color_white']?>; }
div.coloredbar {
    position:            absolute;
    z-index:             1;
    top:                 0;
    left:                0;
    float:               left; }
div.shadow {
    position:            relative;
    left:                1px;
    top:                 1px;
    color:               <?=$display_vars['color_gray_dark']?>; }
div.shadowed {
    position:            absolute;
    left:                -1px;
    top:                 -1px;
    color:               <?=$display_vars['color_white']?>;
}
