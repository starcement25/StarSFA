What is RAC?
RAC stands for Real Application cluster. It is a clustering solution from Oracle Corporation that ensures high availability of databases by providing instance failover, media failover features.

What is RAC and how is it different from non RAC databases?
RAC stands for Real Application Cluster, you have n number of instances running in their own separate nodes and based on the shared storage. Cluster is the key component and is a collection of servers operations as one unit. RAC is the best solution for high performance and high availably. Non RAC databases has single point of failure in case of hardware failure or server crash.
Give the usage of srvctl :
srvctl start instance -d db_name -i “inst_name_list” [-o start_options]
srvctl stop instance -d name -i “inst_name_list” [-o stop_options]
srvctl stop instance -d orcl -i “orcl3,orcl4” -o immediate
srvctl start database -d name [-o start_options]
srvctl stop database -d name [-o stop_options]
srvctl start database -d orcl -o mount

Mention the Oracle RAC software components :
Oracle RAC is composed of two or more database instances. They are composed of Memory structures and background
processes same as the single instance database.Oracle RAC instances use two processes GES(Global Enqueue Service),
GCS(Global Cache Service) that enable cache fusion.Oracle RAC instances are composed of following background
processes:
ACMS—Atomic Controlfile to Memory Service (ACMS)
GTX0-j—Global Transaction Process
LMON—Global Enqueue Service Monitor
LMD—Global Enqueue Service Daemon
LMS—Global Cache Service Process
LCK0—Instance Enqueue Process
RMSn—Oracle RAC Management Processes (RMSn)
RSMN—Remote Slave Monitor

What is GRD?
GRD stands for Global Resource Directory. The GES and GCS maintains records of the statuses of each datafile and each cahed block using global resource directory.This process is referred to as cache fusion and helps in data integrity.

What are the different network components are in 10g RAC?
public, private, and vip components
Private interfaces is for intra node communication. VIP is all about availability of application. When a node fails then the VIP component fail over to some other node, this is the reason that all applications should based on vip components means tns entries should have vip entry in the host list

Give Details on ACMS:
ACMS stands for Atomic Controlfile Memory Service.In an Oracle RAC environment ACMS is an agent that ensures a distributed SGA memory update(ie)SGA updates are globally committed on success or globally aborted in event of a failure.

What is Cache Fusion?
Cache fusion is the mechanism to transfer the data block from memory to memory of one node to the other.If two
nodes require the same block for query or update, the block must be transfered from the cache of one node to the
other. RAC system must equipped with low-latency and high speed inter-connect to make it happen.

Give Details on Cache Fusion:

Oracle RAC is composed of two or more instances. When a block of data is read from datafile by an instance within the cluster and another instance is in need of the same block,it is easy to get the block image from the insatnce which has the block in its SGA rather than reading from the disk. To enable inter instance communication Oracle RAC makes use of interconnects. The Global Enqueue Service(GES) monitors and Instance enqueue process manages the cahce fusion.

Cache Fusion is essentially a memory-to-memory transfer of data between the nodes in the RAC environment. Before Cache Fusion, a node was required to write some of the data to disk before it could be transferred to the next node in the cluster. Cache Fusion does a straight memory-to-memory transfer. In addition, each node’s SGA has a map of what data is contained in the other node’s data caches.

The performance improvement is phenomenal. Oracle leverages the vendor’s high speed interconnects between the
nodes to achieve the cache-to-cache data transfers. Before Cache Fusion, when you added a node to the cluster to
increase performance of the application, it didn’t always provide you with the performance improvement that you hoped for. With Cache Fusion, you can easily cost justify the addition of another node into a RAC cluster to increase the performance of the application running on it. Oracle sales pitches describe it as ‘near linear horizontal scalability’.

What are the major RAC wait events?
In a RAC environment the buffer cache is global across all instances in the cluster and hence the processing differs.The most common wait events related to this are gc cr request and gc buffer busy
GC CR request :the time it takes to retrieve the data from the remote cache
Reason: RAC Traffic Using Slow Connection or Inefficient queries (poorly tuned queries will increase the amount of data blocks requested by an Oracle session. The more blocks requested typically means the more often a block will need to be read from a remote instance via the interconnect.)
GC BUFFER BUSY: It is the time the remote instance locally spends accessing the requested data block.

Give details on GTX0-j :
The process provides transparent support for XA global transactions in a RAC environment.The database autotunes the number of these processes based on the workload of XA global transactions.

Give details on LMON:
This process monitors global enques and resources across the cluster and performs global enqueue recovery
operations.This is called as Global Enqueue Service Monitor.

Give details on LMD:
This process is called as global enqueue service daemon. This process manages incoming remote resource requests
within each instance.

Give details on LMS:
This process is called as Global Cache service process.This process maintains statuses of datafiles and each cahed block by recording information in a Global Resource Dectory(GRD).This process also controls the flow of messages to remote instances and manages global data block access and transmits block images between the buffer caches of different instances.This processing is a part of cache fusion feature.

Give details on LCK0:
This process is called as Instance enqueue process.This process manages non-cache fusion resource requests such as
libry and row cache requests.

Give details on RMSn:
This process is called as Oracle RAC management process.These pocesses perform managability tasks for Oracle
RAC.Tasks include creation of resources related Oracle RAC when new instances are added to the cluster.

Give details on RSMN:
This process is called as Remote Slave Monitor.This process manages background slave process creation andd
communication on remote instances. This is a background slave process.This process performs tasks on behalf of a
co-ordinating process running in another instance.

What components in RAC must reside in shared storage?
All datafiles, controlfiles, SPFIles, redo log files must reside on cluster-aware shred storage.
What is the significance of using cluster-aware shared storage in an Oracle RAC environment?
All instances of an Oracle RAC can access all the datafiles,control files, SPFILE’s, redolog files when these files are
hosted out of cluster-aware shared storage which are group of shared disks.

Give few examples for solutions that support cluster storage:
ASM(automatic storage management),raw disk devices,network file system(NFS), OCFS2 and OCFS(Oracle Cluster Fiesystems).

What is an interconnect network?
An interconnect network is a private network that connects all of the servers in a cluster. The interconnect network uses a switch/multiple switches that only the nodes in the cluster can access.

How can we configure the cluster interconnect?
Configure User Datagram Protocol(UDP) on Gigabit ethernet for cluster interconnect.On unix and linux systems we use UDP and RDS(Reliable data socket) protocols to be used by Oracle Clusterware.Windows clusters use the TCP protocol.

Can we use crossover cables with Oracle Clusterware interconnects?
No, crossover cables are not supported with Oracle Clusterware intercnects.

What is the use of cluster interconnect?
Cluster interconnect is used by the Cache fusion for inter instance communication

How do users connect to database in an Oracle RAC environment?
Users can access a RAC database using a client/server configuration or through one or more middle tiers ,with or
without connection pooling.Users can use oracle services feature to connect to database.

What is the use of a service in Oracle RAC environment?
Applications should use the services feature to connect to the Oracle database.Services enable us to define rules and
characteristics to control how users and applications connect to database instances.

What are the characteristics controlled by Oracle services feature?
The charateristics include a unique name, workload balancing and failover options,and high availability characteristics.

What enables the load balancing of applications in RAC?
Oracle Net Services enable the load balancing of application connections across all of the instances in an Oracle RAC
database.

What is a virtual IP address or VIP?
A virtual IP address or VIP is an alternate IP address that the client connections use instead of the standard public IP
address. To configure VIP address, we need to reserve a spare IP address for each node, and the IP addresses must use the same subnet as the public network

What is the use of VIP?
If a node fails, then the node’s VIP address fails over to another node on which the VIP address can accept TCP
connections but it cannot accept Oracle connections.

Give situations under which VIP address failover happens:
VIP addresses failover happens when the node on which the VIP address runs fails, all interfaces for the VIP address
fails, all interfaces for the VIP address are disconnected from the network.

What is the significance of VIP address failover?
When a VIP address failover happens, Clients that attempt to connect to the VIP address receive a rapid connection
refused error .They don’t have to wait for TCP connection timeout messages.

What are the administrative tools used for Oracle RAC environments?
Oracle RAC cluster can be administered as a single image using OEM(Enterprise  Manager),SQL*PLUS,Servercontrol(SRVCTL),clusterverificationutility(cvu),DBCA,NETCA

Why should we have seperate homes for ASm instance?
It is a good practice to have ASM home seperate from the database hom(ORACLE_HOME).This helps in upgrading and patching ASM and the Oracle database software independent of each other.Also,we can deinstall the Oracle database software independent of the ASM instance.

What is the advantage of using ASM?
Having ASM is the Oracle recommended storage option for RAC databases as the ASM maximizes performance by
managing the storage configuration across the disks.ASM does this by distributing the database file across all of the
available storage within our cluster database environment.

What is rolling upgrade?
It is a new ASM feature from Database 11g.ASM instances in Oracle database 11g release(from 11.1) can be upgraded
or patched using rolling upgrade feature. This enables us to patch or upgrade ASM nodes in a clustered environment
without affecting database availability.During a rolling upgrade we can maintain

 

 

Share via:

Facebook
Twitter
LinkedIn
Email
Copy Link
More
Oracle RacRac
You may also like
 Previous Post
Oracle-19c Recover a loss…


Next Post 
Oracle19c-Export Data From Physical Standby…


Achievement

Search
