#! /bin/sh

# Generates docs (as PDF) by running phpDocumentor against the PHP source code.

phpdoc \
	-q on \
	-d ../../.. \
	-t . \
	-o PDF:default:default \
	-po CalendarSolution \
	-ti "Calendar Solution: PHP Code Documentation"

mv documentation.pdf calendar_solution_documentation.pdf
