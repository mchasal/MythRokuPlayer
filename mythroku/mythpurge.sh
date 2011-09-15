#!/bin/sh
#
# Find orphaned png and bif files from mythtv
#

MYTHDIR=/pub/myth
PURGEDIR=$MYTHDIR/purge/
echo "Looking for orphans in $MYTHDIR ..."
count=0
for f in $MYTHDIR/*SD.bif
do
	base=${f%%-*}
	echo -n "."
	if [ ! -e $base.mp4 ] && [ ! -e $base.mpg ]
	then
		mv $base* $PURGEDIR
		count=$((count+1))
	fi
done
echo ""
echo "$count files moved. Check $PURGEDIR and cleanup."
