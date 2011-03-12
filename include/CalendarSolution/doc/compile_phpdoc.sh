#! /bin/sh

# Generates documentation by running phpDocumentor against the PHP source code.

phpdoc \
    -q on \
    -d ../../.. \
    -t ./phpdoc-output \
    -o HTML:frames:phpdoc.de \
    -po CalendarSolution \
    -ti "Calendar Solution: PHP Code Documentation"
