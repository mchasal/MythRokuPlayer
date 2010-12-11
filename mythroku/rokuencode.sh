#!/bin/bash

#convert mpeg file to mp4 using handbrakecli
MYTHDIR=$1
MPGFILE=$2

# Should try and get these from settings.php, but for now...
DATABASEUSER=mythtv
DATABASEPASSWORD=password
 
newbname=`echo $MPGFILE | sed 's/\(.*\)\..*/\1/'`
newname="$MYTHDIR/$newbname.mp4"

#/usr/bin/HandBrakeCLI -i $1/$2 -o $newname -e x264 -b 1500 -E faac -B 256 -R 48 -w 720
/usr/bin/HandBrakeCLI -i $MYTHDIR/$MPGFILE -o $newname -e x264 -b 1500 -E faac -B 256 -R 48

#Mythtv seems to have problems with keyframes in mp4s, so make previews with ffmpeg
ffmpeg -ss 34 -vframes 1 -i $newname -y -f image2  $MYTHDIR/$newbname.mp4.png
ffmpeg -ss 34 -vframes 1 -i $newname -y -f image2 -s 100x75 $MYTHDIR/$newbname.mp4.64.100x75.png
ffmpeg -ss 34 -vframes 1 -i $newname -y -f image2 -s 320x240 $MYTHDIR/$newbname.mp4.64.320x240.png

# remove the orignal mpg and update the db to point to the mp4
NEWFILESIZE=`du -b "$newname" | cut -f1`
echo "UPDATE recorded SET basename='$newbname.mp4',filesize='$NEWFILESIZE',transcoded='1' WHERE basename='$2';" > /tmp/update-database.sql
mysql --user=$DATABASEUSER --password=$DATABASEPASSWORD mythconverg < /tmp/update-database.sql
rm $MYTHDIR/$MPGFILE

# Make the bif files for trick play
cd $MYTHDIR
# If it's HD we assume it's 16:9
/usr/local/bin/makebif.py -m 3 $newname
# If it's SD we assume it's 4:3
/usr/local/bin/makebif.py -m 0 $newname
