#!/bin/bash

d=$(dirname $0)

while [[ ! -z $1 ]]; do
	$d/flow_statistics.py "$1"
	$d/coord_plot.py "$1"
	$d/node_statistics.py "$1"
	$d/saveTopology.py "$1"
	shift
done

