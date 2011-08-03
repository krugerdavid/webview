<?php
/*
-------------------------------------------------------------------------

        openSSI webView configuration file

-------------------------------------------------------------------------
*/

##### rrdtool binary path
# --- indicate the path where the rrdtool binary is located

$RRDTOOL_PATH = "/usr/bin/rrdtool";


##### System processes max UID
# --- processes whose uid < $SYSPROC_MAXUID are considered as
#     system processes

$SYSPROC_MAXUID = 3000;


##### Prevent system processes migration
# --- if true, prevent system processes (uid < $SYSPROC_MAXUID) to be
#     migrated from openSSI webView interface.
# (true/false)

$PREVENT_SYSPROC_MIGR = true;


##### web interface transparency
# --- use transparency to display web pages (works in Mozilla and IE)
#     disable transparency use could save cpu on web client side.
# (true/false)

$USE_TRANSPARENCY = false;
?>
