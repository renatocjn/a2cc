#!/usr/bin/python

from os.path import isdir, join
from lxml import etree
from glob import glob
import os, sys, numpy, networkx as nx
import pylab as pl
from utils import *
from numpy import array, zeros, inf, arange, floor, sqrt, argmin
from glob import glob
from csv import writer
from scipy.ndimage.filters import *
from time import sleep	
if len(sys.argv) == 2 and isdir(sys.argv[1]): #minimum parameter checking
	os.chdir(sys.argv[1])
else:
	print 'please pass the directory with the results as the first parameter'
	sys.exit(1)


node_number = len(glob('mp-report-*'))

per_run_values = [ dict() for i in range(node_number) ]

per_run_statistics = {'rxOpen', 'txOpen',
					  'rxBytes', 'txBytes',
					  'rxConfirm', 'txConfirm',
					  'rxClose', 'txClose',
					  'rxPerr', 'txPerr',
					  'rxPrep', 'txPrep',
					  'rxPreq', 'txPreq',
					  'initiatedPreq', 'initiatedPrep', 'initiatedPerr',
					  'dropped', 'droppedTtl',
					  'totalQueued',
					  'totalDropped'
					  }




   ### Data acquisition ###

for nodeXml in glob('mp-report-*.xml'):
	xmlRootElement = etree.XML( open(nodeXml,'r').read() )
	Id = int(filter(str.isdigit, nodeXml))

	n = xmlRootElement.find('Hwmp').find('HwmpProtocolMac').find('Statistics')
	for key in ['rxPerr', 'rxPrep', 'rxPreq', 'txPerr', 'txPrep', 'txPreq']:
		per_run_values[Id][key] = clean_result( n.get(key) )

	n = xmlRootElement.find('PeerManagementProtocol').find('PeerManagementProtocolMac').find('Statistics')
	for key in ['txOpen', 'txConfirm', 'txClose', 'rxOpen', 'rxConfirm', 'rxClose', 'dropped']:
		per_run_values[Id][key] = clean_result( n.get(key) )

	n = xmlRootElement.find('Hwmp').find('Statistics')
	for key in ['droppedTtl', 'totalQueued', 'totalDropped', 'initiatedPreq', 'initiatedPrep', 'initiatedPerr']:
		per_run_values[Id][key] = clean_result( n.get(key) )

	n = xmlRootElement.find('Interface').find('Statistics')
	for key in ['txBytes', 'rxBytes']:
		per_run_values[Id][key] = clean_result( n.get(key) )



   ### aquiring positions ###

pfile_contents = open('nodes_description.txt', 'r').read()
positions = dict()
x_min, x_max = inf, -inf
y_min, y_max = inf, -inf
for line in pfile_contents.strip().split("\n"):
	line = line.split('\t')
	i = int(line[0])

	x = float(line[1])
	if x > x_max: x_max = int(floor(x))
	elif x< x_min: x_min = int(floor(x))

	y = float(line[2])
	if y > y_max: y_max = int(floor(y))
	elif y < y_min: y_min = int(floor(y))
	
	positions[i] = x,y

   ### Plotting ###

if not isdir('graphics'):
	os.mkdir('graphics')
os.chdir('graphics')

x = range(x_min-100, x_max+100)
y = range(y_min-100, y_max+100)
X, Y = numpy.meshgrid(x, y)

for k in per_run_statistics:
	print k
	pl.clf()
	pl.title(k)
	Z = zeros(X.shape)
	for i in positions.keys():
		xi, yi = floor(positions[i])
		pl.text(xi, yi, i, horizontalalignment='center', verticalalignment='center')
		Z[x.index(xi),y.index(yi)] = per_run_values[i][k]
	Z = maximum_filter(Z, size=80, mode='constant')
	print X
	print Y
	print (Z==0).all()
	raw_input ("press <enter>\n")
	#pl.contourf(X,Y,Z)
	#pl.colorbar()
	#pl.savefig('%s.png' % k)

