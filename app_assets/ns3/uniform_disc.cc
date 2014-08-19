/* -*- Mode:C++; c-file-style:"gnu"; indent-tabs-mode:nil; -*- */
/*
 * Copyright (c) 2008,2009 IITP RAS
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation;
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * Author: Kirill Andreev <andreev@iitp.ru>
 *
 *
 * By default this script creates m_xSize * m_ySize square grid topology with
 * IEEE802.11s stack installed at each node with peering management
 * and HWMP protocol.
 * The side of the square cell is defined by m_step parameter.
 * When topology is created, UDP ping is installed to opposite corners
 * by diagonals. packet size of the UDP ping and interval between two
 * successive packets is configurable.
 * 
 *  m_xSize * step
 *  |<--------->|
 *   step
 *  |<--->|
 *  * --- * --- * <---Ping sink  _
 *  | \   |   / |                ^
 *  |   \ | /   |                |
 *  * --- * --- * m_ySize * step |
 *  |   / | \   |                |
 *  | /   |   \ |                |
 *  * --- * --- *                _
 *  ^ Ping source
 *
 *  See also MeshTest::Configure to read more about configurable
 *  parameters.
 */


#include "ns3/core-module.h"
#include "ns3/internet-module.h"
#include "ns3/network-module.h"
#include "ns3/applications-module.h"
#include "ns3/wifi-module.h"
#include "ns3/mesh-module.h"
#include "ns3/mobility-module.h"
#include "ns3/mesh-helper.h"
#include "ns3/random-variable.h"
#include "ns3/flow-monitor-module.h"
#include "ns3/hwmp-protocol.h"

#include <iostream>
#include <sstream>
#include <fstream>
#include <ctime>
#include <cstdlib>
#include <cmath>
#include <unistd.h>
#include <cstdio>
#include <string>
#include <set>

using namespace ns3;

NS_LOG_COMPONENT_DEFINE ("TestMeshScript");
class MeshTest
{
public:
  /// Init test
  MeshTest ();
  /// Configure test from command line arguments
  void Configure (int argc, char ** argv);
  /// Run test
  int Run ();
private:
  int       m_radius;
  int       m_number_of_nodes;
  double    m_randomStart;
  double    m_totalTime;
  double    m_packetInterval;
  uint16_t  m_packetSize;
  uint32_t  m_nIfaces;
  bool      m_chan;
  bool      m_pcap;
  bool      m_xml;
  bool      m_flowmonitor;
  int       m_nFlows;
  int       m_seed;
  Ptr<FlowMonitor> m_monitor;
  std::string m_stack;
  std::string m_root;
  /// List of network nodes
  NodeContainer nodes;
  /// List of all mesh point devices
  NetDeviceContainer meshDevices;
  //Addresses of interfaces:
  Ipv4InterfaceContainer interfaces;
  // MeshHelper. Report is not static methods
  MeshHelper mesh;
private:
  /// Create nodes and setup their mobility
  void CreateNodes ();
  /// Install internet m_stack on nodes
  void InstallInternetStack ();
  /// Install applications
  void InstallApplication ();
  /// Print mesh devices diagnostics
  void Report ();
};
MeshTest::MeshTest () :
  m_radius (100),
  m_number_of_nodes (10),
  m_randomStart (0.1),
  m_totalTime (100.0),
  m_packetInterval (0.1),
  m_packetSize (1024),
  m_nIfaces (2),
  m_chan (true),
  m_pcap (false),
  m_xml (true),
  m_flowmonitor (true),
  m_nFlows (1),
  m_stack ("ns3::Dot11sStack"),
  m_root ("ff:ff:ff:ff:ff:ff")
{
}
void
MeshTest::Configure (int argc, char *argv[])
{
  srand(time(NULL));
  m_seed = rand();
  
  CommandLine cmd;
  cmd.AddValue ("radius", "Size in meters of the disc. ", m_radius);
  cmd.AddValue ("number-of-nodes", "Number of rows in the simulation.", m_number_of_nodes);
  /*
   * As soon as starting node means that it sends a beacon,
   * simultaneous start is not good.
   */
  cmd.AddValue ("start",  "Maximum random start delay, seconds.", m_randomStart);
  cmd.AddValue ("time",  "Simulation time, seconds", m_totalTime);
  cmd.AddValue ("packet-interval",  "Interval between packets in UDP ping, seconds", m_packetInterval);
  cmd.AddValue ("packet-size", "Size of packets in UDP ping", m_packetSize);
  cmd.AddValue ("interfaces", "Number of radio interfaces used by each mesh point.", m_nIfaces);
  cmd.AddValue ("channels", "Use different frequency channels for different interfaces.", m_chan);
  cmd.AddValue ("pcap", "Enable PCAP traces on interfaces.", m_pcap);
  cmd.AddValue ("xml", "Enable XML traces on nodes.", m_xml);
  cmd.AddValue ("flows", "Amount of flows in the simulation.", m_nFlows);
  cmd.AddValue ("flowmonitor", "Enable Flow monitor traces on all flows.", m_flowmonitor);
  cmd.AddValue ("stack", "Type of protocol stack. ns3::Dot11sStack by default", m_stack);
  cmd.AddValue ("root", "Mac address of root mesh point in HWMP", m_root);

  SeedManager::SetSeed(m_seed);

  cmd.Parse (argc, argv);
  NS_LOG_DEBUG ("Simulation time: " << m_totalTime << " s");
}
void
MeshTest::CreateNodes ()
{ 
  /*
   * Create m_ySize*m_xSize stations to form a grid topology
   */
  nodes.Create (m_number_of_nodes);
  // Configure YansWifiChannel
  YansWifiPhyHelper wifiPhy = YansWifiPhyHelper::Default ();
  YansWifiChannelHelper wifiChannel = YansWifiChannelHelper::Default ();
  wifiPhy.SetChannel (wifiChannel.Create ());
  /*
   * Create mesh helper and set stack installer to it
   * Stack installer creates all needed protocols and install them to
   * mesh point device
   */
  mesh = MeshHelper::Default ();
  if (!Mac48Address (m_root.c_str ()).IsBroadcast ())
    {
      mesh.SetStackInstaller (m_stack, "Root", Mac48AddressValue (Mac48Address (m_root.c_str ())));
    }
  else
    {
      //If root is not set, we do not use "Root" attribute, because it
      //is specified only for 11s
      mesh.SetStackInstaller (m_stack);
    }
  if (m_chan)
    {
      mesh.SetSpreadInterfaceChannels (MeshHelper::SPREAD_CHANNELS);
    }
  else
    {
      mesh.SetSpreadInterfaceChannels (MeshHelper::ZERO_CHANNEL);
    }
  mesh.SetMacType ("RandomStart", TimeValue (Seconds (m_randomStart)));
  // Set number of interfaces - default is single-interface mesh point
  mesh.SetNumberOfInterfaces (m_nIfaces);
  // Install protocols and return container if MeshPointDevices
  meshDevices = mesh.Install (wifiPhy, nodes);
  // Setup mobility - static grid topology
  MobilityHelper mobility;
  Ptr<UniformDiscPositionAllocator> positionAllocator = CreateObject<UniformDiscPositionAllocator>();
  positionAllocator->SetX(0.0);
  positionAllocator->SetY(0.0);
  positionAllocator->SetRho(m_radius);
  mobility.SetPositionAllocator(positionAllocator);

  mobility.SetMobilityModel ("ns3::ConstantPositionMobilityModel");
  mobility.Install (nodes);
  if (m_pcap)
    wifiPhy.EnablePcapAll (std::string ("mp-"));
}
void
MeshTest::InstallInternetStack ()
{
  InternetStackHelper internetStack;
  internetStack.Install (nodes);
  Ipv4AddressHelper address;
  address.SetBase ("10.1.1.0", "255.255.255.0");
  interfaces = address.Assign (meshDevices);
}
void
MeshTest::InstallApplication ()
{
  FILE* fp = std::fopen("nodes_description.txt", "w");
   
  UdpServerHelper echoServer (9);
  Ptr<Node> serverNode = nodes.Get (0);
  Vector serverp = serverNode->GetObject<MobilityModel>()->GetPosition();
  std::fprintf(fp, "%d\t%f\t%f\tSERVER\n", 0, serverp.x, serverp.y);
  
  ApplicationContainer serverApps = echoServer.Install (nodes.Get (0));
  serverApps.Start (Seconds (0.0));
  serverApps.Stop (Seconds (m_totalTime));
  
  int start = m_seed % m_number_of_nodes;
  std::set<int> clientIds;
  
  do {
    int tmp = (start + rand()) % m_number_of_nodes;
    tmp = (tmp == 0) ? 1 : tmp;
    clientIds.insert(tmp);
  } while (clientIds.size() < (unsigned) m_nFlows);
  NodeContainer clients;

  for (int i=1; i<m_number_of_nodes; i++) {
    Ptr<Node> node = nodes.Get (i);
    Vector p = node->GetObject<MobilityModel>()->GetPosition();
    if (clientIds.count(i)) {
        clients.Add(node);
        std::fprintf(fp, "%d\t%f\t%f\tCLIENT\n", i, p.x, p.y);
    } else {
        std::fprintf(fp, "%d\t%f\t%f\n", i, p.x, p.y);
    }
  }
  std::fclose(fp);
  
  UdpClientHelper echoClient (interfaces.GetAddress (0), 9);
  echoClient.SetAttribute ("MaxPackets", UintegerValue ((uint32_t)(m_totalTime*(1/m_packetInterval))));
  echoClient.SetAttribute ("Interval", TimeValue (Seconds (m_packetInterval)));
  echoClient.SetAttribute ("PacketSize", UintegerValue (m_packetSize));
  ApplicationContainer clientApps = echoClient.Install (clients);
  clientApps.Start (Seconds (0.0));
  clientApps.Stop (Seconds (m_totalTime));
}

int
MeshTest::Run ()
{

  CreateNodes ();
  InstallInternetStack ();
  InstallApplication ();

  FlowMonitorHelper fmh;
  fmh.InstallAll();
  m_monitor = fmh.GetMonitor();

  if (m_xml) Simulator::Schedule (Seconds (m_totalTime), &MeshTest::Report, this);
  Simulator::Stop (Seconds (m_totalTime));
  Simulator::Run ();
  m_monitor->CheckForLostPackets();
  m_monitor->SerializeToXmlFile("FlowMonitorResults.xml", true, true);

  Simulator::Destroy ();
  return 0;
}
void
MeshTest::Report ()
{
  unsigned n (0);
  for (NetDeviceContainer::Iterator i = meshDevices.Begin (); i != meshDevices.End (); ++i, ++n)
    {
      std::ostringstream os;
      os << "mp-report-" << n << ".xml";
      std::cerr << "Printing mesh point device #" << n << " diagnostics to " << os.str () << "\n";
      std::ofstream of;
      of.open (os.str ().c_str ());
      if (!of.is_open ())
        {
          std::cerr << "Error: Can't open file " << os.str () << "\n";
          return;
        }
      mesh.Report (*i, of);
      of.close ();
    }
}
int
main (int argc, char *argv[])
{
  MeshTest t; 
  t.Configure (argc, argv);
  return t.Run ();
}
