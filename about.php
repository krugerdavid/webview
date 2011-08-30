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
  about.php
  openssi-webview about page
  ---------------------------------------------------------------------
  Started      on Mon, 11 Oct 2004 09:57:48 +0200
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
        <title>openSSI webView :: About</title>
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
                    <? display_intro("about"); ?>
                    <h5>Thanks</h5>
                    <ul>
                        <li>Special thanks to Tobi Oetiker, the creator of the powerful and limitless <a href="http://people.ee.ethz.ch/~oetiker/webtools/rrdtool/">RRDTool</a>,</li>
                        <li>Thanks to Dariusz Arciszewski, for its eyecandy <a href="http://www.deviantart.com/deviation/6814504/">KrystalKurve</a> icons,</li>
                        <li>And a big thanks to all <a href="http://openssi.org">openSSI</a> developers, who are giving the community a wonderful clustering solution.</li>
                    </ul>

                </div>  

            </div>
        </div>


        <? display_footer(); ?>

    </body>
</html>

<?
# vim:set tabstop=4 softtabstop=4 shiftwidth=4 expandtab:
?>
