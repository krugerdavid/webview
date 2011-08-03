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
#   update_data.sh
#   updates RRD data for an openSSI node
#   ---------------------------------------------------------------
#   Started      on Wed, 06 Sep 2004 09:47:48 +0200
#   Last updated on Wed, 10 Nov 2004 15:24:00 +0100
#   ---------------------------------------------------------------


## Check conditions
if [ $# = 0 ] ; then
    echo "Missing argument"
    echo "Usage : update_node.sh <node_number>"
    exit 1
fi

egrep "^$1" /etc/clustertab > /dev/null 2>&1
if [ $? != 0 ]; then
	echo "Specified node number does not exist" && exit 1
fi

NODE=$1

## Config
RRDTOOL=`awk -F \" '/RRDTOOL_PATH/ {print $2}' < ../config.php`
IFCONFIG=/sbin/ifconfig
DATADIR=./data

STATIC=0
LOADLEVELED=0
TOTAL=0


## Create rrd if needed -------------------------------------------

[ ! -d $DATADIR ] && echo "Data directory does not exist" && exit 1
# Load
if [ ! -r $DATADIR/${NODE}_load.rrd ]; then
    $RRDTOOL create $DATADIR/${NODE}_load.rrd \
    --step 300 \
    DS:load:GAUGE:600:0:1000000000 \
    RRA:AVERAGE:0.5:1:600 \
    RRA:AVERAGE:0.5:6:700 \
    RRA:AVERAGE:0.5:24:775 \
    RRA:AVERAGE:0.5:288:797 \
    RRA:LAST:0.5:1:600 \
    RRA:LAST:0.5:6:700 \
    RRA:LAST:0.5:24:775 \
    RRA:LAST:0.5:288:797 \
    RRA:MAX:0.5:1:600 \
    RRA:MAX:0.5:6:700 \
    RRA:MAX:0.5:24:775 \
    RRA:MAX:0.5:288:797
fi
# Load average
if [ ! -r $DATADIR/${NODE}_loadavg.rrd ]; then
    $RRDTOOL create $DATADIR/${NODE}_loadavg.rrd \
    --step 300 \
    DS:1mn:GAUGE:600:0:1000000000 \
    DS:5mn:GAUGE:600:0:1000000000 \
    DS:15mn:GAUGE:600:0:1000000000 \
    RRA:AVERAGE:0.5:1:600 \
    RRA:AVERAGE:0.5:6:700 \
    RRA:AVERAGE:0.5:24:775 \
    RRA:AVERAGE:0.5:288:797 \
    RRA:LAST:0.5:1:600 \
    RRA:LAST:0.5:6:700 \
    RRA:LAST:0.5:24:775 \
    RRA:LAST:0.5:288:797 \
    RRA:MAX:0.5:1:600 \
    RRA:MAX:0.5:6:700 \
    RRA:MAX:0.5:24:775 \
    RRA:MAX:0.5:288:797
fi
# CPU
if [ ! -r $DATADIR/${NODE}_cpu.rrd ]; then
    $RRDTOOL create $DATADIR/${NODE}_cpu.rrd \
    --step 300 \
    DS:user:DERIVE:600:0:1000000000 \
    DS:nice:DERIVE:600:0:1000000000 \
    DS:system:DERIVE:600:0:1000000000 \
    DS:idle:DERIVE:600:0:1000000000 \
    RRA:AVERAGE:0.5:1:600 \
    RRA:AVERAGE:0.5:6:700 \
    RRA:AVERAGE:0.5:24:775 \
    RRA:AVERAGE:0.5:288:797 \
    RRA:LAST:0.5:1:600 \
    RRA:LAST:0.5:6:700 \
    RRA:LAST:0.5:24:775 \
    RRA:LAST:0.5:288:797 \
    RRA:MAX:0.5:1:600 \
    RRA:MAX:0.5:6:700 \
    RRA:MAX:0.5:24:775 \
    RRA:MAX:0.5:288:797
fi
# Memory
if [ ! -r $DATADIR/${NODE}_mem.rrd ]; then
    $RRDTOOL create $DATADIR/${NODE}_mem.rrd \
    --step 300 \
    DS:used:GAUGE:600:0:1000000000000 \
    DS:free:GAUGE:600:0:1000000000000 \
    DS:total:GAUGE:600:0:1000000000000 \
    DS:shared:GAUGE:600:0:1000000000000 \
    DS:buffers:GAUGE:600:0:1000000000000 \
    DS:cached:GAUGE:600:0:1000000000000 \
    RRA:AVERAGE:0.5:1:600 \
    RRA:AVERAGE:0.5:6:700 \
    RRA:AVERAGE:0.5:24:775 \
    RRA:AVERAGE:0.5:288:797 \
    RRA:LAST:0.5:1:600 \
    RRA:LAST:0.5:6:700 \
    RRA:LAST:0.5:24:775 \
    RRA:LAST:0.5:288:797 \
    RRA:MAX:0.5:1:600 \
    RRA:MAX:0.5:6:700 \
    RRA:MAX:0.5:24:775 \
    RRA:MAX:0.5:288:797
fi
# Swap
if [ ! -r $DATADIR/${NODE}_swap.rrd ]; then
    $RRDTOOL create $DATADIR/${NODE}_swap.rrd \
    --step 300 \
    DS:used:GAUGE:600:0:1000000000000 \
    DS:free:GAUGE:600:0:1000000000000 \
    DS:total:GAUGE:600:0:1000000000000 \
    RRA:AVERAGE:0.5:1:600 \
    RRA:AVERAGE:0.5:6:700 \
    RRA:AVERAGE:0.5:24:775 \
    RRA:AVERAGE:0.5:288:797 \
    RRA:LAST:0.5:1:600 \
    RRA:LAST:0.5:6:700 \
    RRA:LAST:0.5:24:775 \
    RRA:LAST:0.5:288:797 \
    RRA:MAX:0.5:1:600 \
    RRA:MAX:0.5:6:700 \
    RRA:MAX:0.5:24:775 \
    RRA:MAX:0.5:288:797
fi
# Processes
if [ ! -r $DATADIR/${NODE}_proc.rrd ]; then
    $RRDTOOL create $DATADIR/${NODE}_proc.rrd \
    --step 300 \
    DS:loadleveled:GAUGE:600:0:1000000000 \
    DS:static:GAUGE:600:0:1000000000 \
    DS:total:GAUGE:600:0:1000000000 \
    RRA:AVERAGE:0.5:1:600 \
    RRA:AVERAGE:0.5:6:700 \
    RRA:AVERAGE:0.5:24:775 \
    RRA:AVERAGE:0.5:288:797 \
    RRA:LAST:0.5:1:600 \
    RRA:LAST:0.5:6:700 \
    RRA:LAST:0.5:24:775 \
    RRA:LAST:0.5:288:797 \
    RRA:MAX:0.5:1:600 \
    RRA:MAX:0.5:6:700 \
    RRA:MAX:0.5:24:775 \
    RRA:MAX:0.5:288:797
fi
# Network
if [ ! -r $DATADIR/${NODE}_eth.rrd ]; then
    $RRDTOOL create $DATADIR/${NODE}_eth.rrd \
    --step 300 \
    DS:in:DERIVE:600:0:12500000 \
    DS:out:DERIVE:600:0:12500000 \
    DS:in_errors:DERIVE:600:0:12500000 \
    DS:out_errors:DERIVE:600:0:12500000 \
    DS:in_dropped:DERIVE:600:0:12500000 \
    DS:out_dropped:DERIVE:600:0:12500000 \
    RRA:AVERAGE:0.5:1:600 \
    RRA:AVERAGE:0.5:6:700 \
    RRA:AVERAGE:0.5:24:775 \
    RRA:AVERAGE:0.5:288:797 \
    RRA:LAST:0.5:1:600 \
    RRA:LAST:0.5:6:700 \
    RRA:LAST:0.5:24:775 \
    RRA:LAST:0.5:288:797 \
    RRA:MAX:0.5:1:600 \
    RRA:MAX:0.5:6:700 \
    RRA:MAX:0.5:24:775 \
    RRA:MAX:0.5:288:797
fi

## Update data ----------------------------------------------------

# Load
OUT=`loads -n ${NODE}`
if [ $? != 0 ]; then 
	LOAD='U'
	LOADS='U:U:U'
	CPU='U:U:U:U'
	MEM='U:U:U:U:U:U'
	SWAP='U:U:U'
	PROC='U:U:U'
	NET='U:U:U:U:U:U'
else
	# SSI Load
	LOAD=`loads -n ${NODE} | awk '{print $2}'`
	
	# Load average
	LOADS=`onnode ${NODE} uptime | sed 's/^.*: \(.*\), \(.*\), \(.*\)$/\1:\2:\3/'`
	
	# CPU
	CPU=(`onnode ${NODE} awk '/cpu / {print $2":"$3":"$4":"$5}' < /proc/stat`)
	
	# Memory
	MEM=`onnode ${NODE} free -b | grep Mem: | sed 's/Mem:[ ]*//' | sed 's/  */:/g'`
	
	# Swap
	SWAP=`onnode ${NODE} free -b | grep Swap: | sed 's/Swap:[ ]*//' | sed 's/  */:/g'`
	
	# Processes
	for i in `ps h -o pid --node ${NODE}`; do 
	    LL=`cat /proc/$i/loadlevel 2>/dev/null`
	    if [ $? == 0 ]; then
	        if [ $LL == 0 ]; then 
	 	    ((STATIC=$STATIC+1))
	        else
		    ((LOADLEVELED=$LOADLEVELED+1))
	        fi
	    	((TOTAL=$TOTAL+1))
	    fi	
	done
	PROC=$STATIC:$LOADLEVELED:$TOTAL
	
	# Network
	ETH=`onnode ${NODE} $IFCONFIG -a | grep \`egrep "^${NODE}" /etc/clustertab | awk '{print $3}'\` | head -n1 | awk '{print $1}'`
	TRAF=(`onnode ${NODE} $IFCONFIG $ETH | awk -F '(:| *)' '/RX bytes/ {print $4":"$9}'`) 
	IN_ERR=(`onnode ${NODE} $IFCONFIG $ETH | awk -F ' [a-z]*:' '/RX packets/ {print $3":"$4}'`)
	OUT_ERR=(`onnode ${NODE} $IFCONFIG $ETH | awk -F ' [a-z]*:' '/TX packets/ {print $3":"$4}'`)
	NET=${TRAF[*]}:${IN_ERR[*]}:${OUT_ERR[*]}
fi

$RRDTOOL update $DATADIR/${NODE}_load.rrd --template load N:$LOAD
$RRDTOOL update $DATADIR/${NODE}_loadavg.rrd --template 1mn:5mn:15mn N:$LOADS
$RRDTOOL update $DATADIR/${NODE}_cpu.rrd --template user:nice:system:idle N:$CPU
$RRDTOOL update $DATADIR/${NODE}_mem.rrd --template total:used:free:shared:buffers:cached N:$MEM
$RRDTOOL update $DATADIR/${NODE}_swap.rrd --template total:used:free N:$SWAP
$RRDTOOL update $DATADIR/${NODE}_proc.rrd --template static:loadleveled:total N:$PROC
$RRDTOOL update $DATADIR/${NODE}_eth.rrd --template in:out:in_errors:in_dropped:out_errors:out_dropped N:$NET
