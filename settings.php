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
  settings.php
  openssi-webview configuration page
  ---------------------------------------------------------------------
  Started      on Thu, 04 Nov 2004 14:56:04 +0100
  Last updated on Wed, 10 Nov 2004 15:24:00 +0100
  ---------------------------------------------------------------------
 */

require_once('./common.inc.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
        <title>openSSI webView :: Settings</title>
        <link rel="stylesheet" href="css/screen.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="css/print.css" type="text/css" media="print" />
        <style type="text/css" media="all">
            @import "css/style.php";
        </style>
    </head>

    <body>

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

                    <?
                    display_intro("options");
                    javascript_warning();

                    if (isset($_POST['save'])) {

                        import_request_variables("P");

                        /* basically check entries */
                        if (!ereg("^[0-9]{0,5}$", $sysproc_maxuid)) {
                            ?>
                            <div class="warning">System processes max UID must be an integer between 0 and 65535</div> <? } elseif ($rrdtool_path[0] != "/") { ?>
                            <div class="warning">RRDTool binary path does not look like a valid path</div> <?
                } else {
                    $variables = array("RRDTOOL_PATH", "USE_TRANSPARENCY",
                        "SYSPROC_MAXUID", "PREVENT_SYSPROC_MIGR");
                    /* format values */
                    $sysproc_maxuid = min($sysproc_maxuid, 65535);
                    $rrdtool_path = "\"" . $rrdtool_path . "\"";
                    $prevent_sysproc_migr ? $prevent_sysproc_migr = "true" : $prevent_sysproc_migr = "false";
                    $use_transparency ? $use_transparency = "true" : $use_transparency = "false";

                    /* read to CONFIGFILE */
                    $fp = @fopen(CONFIGFILE, "r");
                    if ($fp) {
                        $current = fread($fp, filesize(CONFIGFILE));
                        fclose($fp);

                        /* replace old values by new ones */
                        foreach ($variables as $option) {
                            $localoption = ${strtolower($option)};
                            $current = preg_replace("/$option = .*;/", "$option = $localoption;", $current);
                        }

                        /* save config */
                        $fp = @fopen(CONFIGFILE, "w");
                        if ($fp) {
                            fputs($fp, $current);
                            fclose($fp);
                                    ?>
                                    <div class="success">Sucessfully saved</div> <? } else { ?>
                                    <div class="warning">The configuration file (<?= CONFIGFILE ?>) could not be saved, please verify that it can be written by the webserver user.</div> <?
                }
            } else {
                                ?>
                                <div class="warning">The configuration file (<?= CONFIGFILE ?>) could not be opened.</div> <?
                }
            }
        }
        /* read the config file once it has been updated */
        require_once(CONFIGFILE);
                    ?>

                    <hr />
                    <form name="options" method="post" class="append-bottom" action="<?= $_SERVER['PHP_SELF'] ?>">

                        <div class="options span-8">
                            <div class="nodehead">Paths</div>
                            <table>
                                <tr>
                                    <td>RRDTool Binary Path:</td>
                                    <td><input type="text" name="rrdtool_path"
                                               value="<? $RRDTOOL_PATH ? print($RRDTOOL_PATH)
                                     : print(find_path("rrdtool")); ?>" /></td>
                                </tr>
                            </table>
                        </div>

                        <div class="options span-8 last">
                            <div class="nodehead">Display Options</div>
                            <table>
                                <tr>
                                    <td width="10px"><input type="checkbox" name="use_transparency"
                                        <? if ($USE_TRANSPARENCY)
                                            echo "checked=\"checked\""; ?> /></td>
                                    <td>Use transparency to display web pages</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Choose whether you want to use transparency on pages or not.
                                        Works on IE and Mozilla(-likes), but can consume quite resources.</td>
                                </tr>
                            </table>
                        </div>


                        <div class="options span-17">
                            <input type="submit" class="submit" name="save" value="Save Changes" />
                        </div>


                    </form>
                </div>  

            </div>
        </div>


        <? display_footer(); ?>

    </body>
</html>


<?
# vim:set tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
?>
