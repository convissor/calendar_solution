#! /bin/bash

echo "This will flush the Calendar Solution's cache."
echo "Proceed? [N/y]"
read -e

if [ "$REPLY" == "y" ] ; then
	php ../../../calendar/Admin/flush.php proceed > /dev/null
	if [ "$?" -ne "0" ] ; then
		echo "The flush did not work."
		exit 1
	else
		echo "The cache has been flushed."
	fi
else
	echo "No problem.  Bye."
	exit 1
fi
