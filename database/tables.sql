
drop table if exists users;

create table users(
	id int not null auto_increment,
	firstname varchar(50),
	lastname varchar(50),
	email varchar(50),
	dob date,
	salary double(10,2),
	created_on datetime,
	modified_on datetime,
	department_id int,
	primary key(id)
);


drop table if exists departments;

create table departments(
	id int not null auto_increment,
	name varchar(50),
	primary key(id)
);
