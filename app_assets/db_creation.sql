create database a2cc;

use a2cc;
create table opennebula_allocated_vms (
	username varchar(20),
	vmid int,
	primary key (username, vmid)
);

create user 'a2cc'@'localhost' identified by '1!2@3#';
grant select, insert, delete on a2cc.* to 'a2cc'@'localhost';
