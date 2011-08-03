#!/bin/bash
#
#   ---------------------------------------------------------------
#   Copyright (C) 2004 Kilian CAVALOTTI
#                     <kilian.cavalotti@stix.polytechnique.fr>
#
#   This program is free software; you can redistribute it and/or
#   modify it under the terms of the GNU General Public License
#   as published by the Free Software Foundation; either version 2
#   of the License, or (at your option) any later version.
#
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#   ---------------------------------------------------------------
#   update_all.sh
#   updates RRD data for all nodes
#   ---------------------------------------------------------------
#   Started      on Wed Sep  6 09:47:48 CEST 2004
#   Last updated on Wed, 10 Nov 2004 15:24:00 +0100
#   ---------------------------------------------------------------


### Ugly but working way to get the path we're actually located in

IFS=$'\n'; 

# store where we're launched from
START=$(pwd)

cd $(dirname $0)
IMHERE=$(pwd)

# go back to starting path
cd $START

cd $IMHERE
for i in `egrep "^[0-9]" /etc/clustertab |awk '{print $1}'`; do 
    ./update_node.sh $i
done
