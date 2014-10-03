#!/bin/bash

dir=$1
cd "$dir"
autodockRuns=$2

macroCellParams=$(ls *.gpf)
ligantsParams=$(ls *.dpf)
ligantNum=$(ls -1  *.dpf|wc -l)

echo autogrid
autogrid4 -p $macroCellParams -l $(echo $macroCellParams|cut -d"." -f1).glg

if [[ $ligantNum -gt 1 ]]; then
	files=$(ls -1)
	for lig in $ligantsParams; do
		ligDir=$(echo $lig|cut -d"." -f1)
		mkdir $ligDir
		cp $files $ligDir
		cd $ligDir
		r=1
		while [[ $r -le $autodockRuns ]]; do
			nohup srun -p long autodock4 -p $(basename $lig) -l $(basename $lig|cut -d"." -f1).$r.dlg &> output$r.txt&
			r=$(($r + 1))
		done
		cd ..
	done
	rm $files
else
	r=1
	while [[ $r -le $autodockRuns ]]; do
		nohup srun -p long autodock4 -p $(basename $ligantsParams) -l $(basename $ligantsParams|cut -d"." -f1).$r.dlg &> output$r.txt&
		r=$(($r + 1))
	done
fi

