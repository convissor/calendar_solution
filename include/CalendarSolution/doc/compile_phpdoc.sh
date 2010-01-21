# Generates documentation by running phpDocumentor against the PHP source code.

#c:/progra~1/pear/phpdoc \
phpdoc \
    -q on \
    -d ../../.. \
    -t ./phpdoc-output \
    -o HTML:frames:phpdoc.de \
    -po CalendarSolution \
    -ti "Calendar Solution: PHP Code Documentation"
