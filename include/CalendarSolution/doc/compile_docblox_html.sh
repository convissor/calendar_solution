#! /bin/sh

# Generates docs (as HTML) by running DocBlox against the PHP source code.

rm -rf docblox-output
mkdir docblox-output

echo `date` > docblox.log

docblox \
	-f ../../CalendarSolution.php \
	-d ..,../../../calendar \
	-t docblox-output \
	--title "Calendar Solution: PHP Code Documentation" \
	2>&1 >> docblox.log
