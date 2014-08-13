#!/bin/bash

N=$1  #number of devisions of the full run

#### Preparing folders of each devision ####
mkdir n1 
cp namd.pdb namd.psf namd.inp namd-output0.coor namd-output0.vel n1
grep -v first-only base-namd.conf | sed 's/#first-only//' | sed 's/<current>/1/' | sed 's/<previous>/0/' > n1/namd.conf

for i in `seq 2 1 $N`; do
	mkdir "n$i"
	cp namd.pdb namd.psf namd.inp "n$i"
	
	grep -v first-only base-namd.conf | sed "s/renato\s\+[0-9]\+/minimize 0/" | sed "s/<current>/$i/" | sed "s/<previous>/$(($i-1))/" > n$i/namd.conf
done
outdir=`pwd`

#cleaning
rm namd.pdb namd.inp namd.psf base-namd.conf namd-output0.coor namd-output0.vel

#### Executing every run ####
cd
echo progress with divisions: 1/$N
./namd/namd2 "$outdir"/n1/namd.conf &> "$outdir"/n1/job.log #run the first job

for i in `seq 2 1 $N`; do #run subsequent jobs
	prev=$((i-1))
	echo progress with divisions: $i/$N
	cp "$outdir"/n$prev/namd-output$prev.coor "$outdir"/n$prev/namd-output$prev.vel "$outdir"/n$i
	./namd/namd2 "$outdir"/n$i/namd.conf &> "$outdir"/n$i/job.log
done

rm $0 #delete this script from output folder
