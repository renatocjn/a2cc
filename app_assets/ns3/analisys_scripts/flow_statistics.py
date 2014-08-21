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

metrics = { 'txBytes',
		   'rxBytes',
		   'txPackets',
		   'rxPackets',
		   'timeFirstTxPacket',
		   'timeFirstRxPacket',
		   'timeLastTxPacket',
		   'timeLastRxPacket',
		   'deliveryRate',
		   'delaySum',
		   'jitterSum',
		   'lastDelay',
		   'lostPackets',
		   'timesForwarded',
		   'throughput'}

stats = { metric: list() for metric in metrics }
nflows = 0

xmlString = open('FlowMonitorResults.xml', 'r').read()
xmlRoot = etree.XML(xmlString)

FlowStats = xmlRoot.find('FlowStats')
all_flows = FlowStats.findall('Flow')
for flow in all_flows: # get flow simulation values
	nflows += 1
	stats['rxBytes'].append(clean_result(flow.get('rxBytes')))
	stats['txBytes'].append(clean_result(flow.get('txBytes')))
	stats['rxPackets'].append(clean_result(flow.get('rxPackets')))
	stats['txPackets'].append(clean_result(flow.get('txPackets')))
	stats['timeLastRxPacket'].append(clean_result(flow.get('timeLastRxPacket')))
	stats['timeLastTxPacket'].append(clean_result(flow.get('timeLastTxPacket')))
	stats['timeFirstRxPacket'].append(clean_result(flow.get('timeFirstRxPacket')))
	stats['timeFirstTxPacket'].append(clean_result(flow.get('timeFirstTxPacket')))
	stats['delaySum'].append(clean_result(flow.get('delaySum')))
	stats['jitterSum'].append(clean_result(flow.get('jitterSum')))
	stats['lastDelay'].append(clean_result(flow.get('lastDelay')))
	stats['lostPackets'].append(clean_result(flow.get('lostPackets')))
	stats['timesForwarded'].append(clean_result(flow.get('timesForwarded')))
stats['deliveryRate'] = map( lambda x: x[0]*100/x[1], zip(stats['rxPackets'], stats['txPackets']))
try:
	stats['throughput'] = map( lambda x: 8*(10**9)*x[0]/(x[1]-x[2]), zip(stats['rxBytes'], stats['timeLastRxPacket'], stats['timeFirstTxPacket']) )
except:
	stats['throughput'] = [ 0 for i in range(nflows) ]

if len(sys.argv) == 2:
	output = open('flow-statistics.txt','w')
	lines = list()
	for metric in stats.keys():
		try:
			lines.append( '%s|%f\n' % (metric, mean(stats[metric])) )
		except: pass
	output.writelines(lines)
	output.close()
else:
	for i in sys.argv[2:]:
		try:
			print i+':', '\n\tmedia:', stats[i][0], '\n\tdesvio padrao:', stats[i][1], '\n\t valores: ', stats[i]
		except KeyError:
			print i, 'nao encontrado'

