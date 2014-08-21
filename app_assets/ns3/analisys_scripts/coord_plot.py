#!/usr/bin/python

from lxml import etree
from glob import glob
import os, sys
from utils import *
from numpy import zeros, mean
from csv import writer
import pylab as pl

if os.path.isdir(sys.argv[1]):
	os.chdir(sys.argv[1])
else:
	print 'please pass the directory with the results as the first parameter'
	sys.exit(1)

pfile_contents = open('nodes_description.txt', 'r').read()
ids, x_positions, y_positions = list(), list(), list()
clientsx, clientsy, serverx = list(), list(), list()
servery, othersx, othersy = list(), list(), list()

for line in pfile_contents.strip().split("\n"):
	line = line.split('\t')
	if len(line) == 4:
		i, x, y, t = line
		if t == 'SERVER':
			serverx.append(float(x))
			servery.append(float(y))
		elif t == 'CLIENT':
			clientsx.append(float(x))
			clientsy.append(float(y))
	else:
		i, x, y = line
		othersx.append(float(x))
		othersy.append(float(y))
	ids.append(i)
	x_positions.append(float(x))
	y_positions.append(float(y))
	
pl.clf()
pl.scatter(serverx, servery, label='server', color='r', s=400)
pl.scatter(clientsx, clientsy, label='clients', color='b', s=400)
pl.scatter(othersx, othersy, color='k', s=100)

for i, x, y in zip(ids, x_positions, y_positions):
	pl.annotate(str(i), xy = (x, y), xytext = (7, 7), textcoords = 'offset points')
pl.legend(loc='best', ncol=2, bbox_to_anchor=(0., 1.02, 1., .102))
pl.margins(0.1)
pl.savefig('nodes.png')
