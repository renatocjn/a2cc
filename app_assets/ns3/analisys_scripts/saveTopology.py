#!/usr/bin/python

from glob import glob
import networkx as nx
from lxml import etree
import os
import sys
import matplotlib.pyplot as plt

if len(sys.argv) == 2:
	os.chdir(sys.argv[1]);
elif len(sys.argv) > 2:
	print 'ERROR: Run this without parameters if the XMLs are in the current folder or with the only parameter as the directory with the XMLs'
	sys.exit(1)


reports = glob('mp-report-*.xml')
if len(reports) == 0:
	print "ERROR: There wasnt any mesh point report found in the '%s' folder" % working_dir
	sys.exit(1)


ids = dict()
for report in reports:
	aux = open(report).read()
	meshPointDevice = etree.XML(aux)
	curr_address = meshPointDevice.get('address')
	curr_id = int(filter(str.isdigit, report))
	ids[curr_address] = curr_id

G = nx.Graph()
for report in reports:
	aux = open(report).read()
	meshPointDevice = etree.XML(aux)
	currAddress = meshPointDevice.get('address')
	currId = ids[currAddress]
	G.add_node(currId)

	peerManagementProtocol = meshPointDevice.find('PeerManagementProtocol')
	links = peerManagementProtocol.findall('PeerLink')
	for link in links:
		peerAddress = link.get('peerMeshPointAddress')
		peerId = ids[peerAddress]
		G.add_edge(currId, peerId)


nx.draw_graphviz(G)
plt.savefig('connections.png')

