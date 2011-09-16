#!/bin/sh
#
# Find orphaned png and bif files from mythtv

MYTHDIR=/pub/myth
PURGEDIR=$MYTHDIR/purge
echo "Looking for orphans in $MYTHDIR ..."
count=0

# Verify that an actual video file exists for
# each bif file.
for f in $MYTHDIR/*SD.bif
do
	base=${f%%-*}
#	echo -n "."
	if [ ! -e $base.mp4 ] && [ ! -e $base.mpg ]
	then
		# If there wasn't a video file, move
		# all the ancillary files to PURGEDIR
		mv $base* $PURGEDIR
		count=$((count+1))
	fi
done

#echo ""
echo "$count files moved. Checking $PURGEDIR ..."

count=0
# Double check that we didn't move anything unexpected.
for f in $PURGEDIR/*
do
	ext=${f##*.}
	if [ $ext != bif ] && [ $ext != png ] 
	then
		echo "Suspect file: $f"
		count=$((count+1))
	fi
done
echo "$count suspect files found!!!!"
