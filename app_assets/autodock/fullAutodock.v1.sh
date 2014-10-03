#!/bin/bash

cd $(dirname "$1")
autogrid4 -p $(basename "$1") -l $(basename "$1"|cut -d"." -f1).glg
shift

for f in $@; do
	nohup srun -p long autodock4 -p $(basename "$f") -l $(basename "$f"|cut -d"." -f1).dlg &> slurm.out&
done
