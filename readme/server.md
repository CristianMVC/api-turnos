hxv-snt.hexacta.com + sntapi.hexacta.com
========================================

```
# install php
sudo yum install php
sudo yum install php-pgsql
# httpd and php are dependent, so stop apache
sudo service httpd stop
# install EPEL, so YUM know where to find Nginx
sudo yum install epel-release
# install nginx
sudo yum install nginx
# start nginx
sudo server nginx start
# check if something is running on the port 8080
netstat -punta | grep ':8080'
# check the iptables for port 80 and 443 open
sudo iptables --line -vnL | grep reject
# add rule to iptables
iptables -I INPUT 6 -i eth0 -p tcp --dport 80 -m state --state NEW,ESTABLISHED -j ACCEPT
iptables -I INPUT 7 -i eth0 -p tcp --dport 8000 -m state --state NEW,ESTABLISHED -j ACCEPT
iptables -I INPUT 8 -i eth0 -p tcp --dport 8008 -m state --state NEW,ESTABLISHED -j ACCEPT
iptables -I INPUT 9 -i eth0 -p tcp --dport 8080 -m state --state NEW,ESTABLISHED -j ACCEPT
# delete the rule number 4 of the iptables
iptables -D INPUT 4
# save iptables config
iptables-save > /etc/sysconfig/iptables
# add user to group
usermod -a -G nginx hexacta
```

```
# In case you have a 403 forbiden error
# check SELinux permissions enforcement
getenforce
# set the enforcemente to permisive
setenforce Permissive
service nginx restart
# check if you have access to the page, if you have access
setenforce Enforcing
chcon -Rt httpd_sys_content_t
service nginx restart
# If you still have 403 forbiden problem, use google
```

# PostgreSQL
[How to install postgresql](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-postgresql-on-centos-7)

```
# Install postgresql
sudo yum install postgresql-server postgresql-contrib
# create new cluster
sudo postgresql-setup initdb
# edit postgresql config file, change authentication to md5
sudo vim /var/lib/pgsql/data/pg_hba.conf
# start postgresql
sudo service postgresql start
# enable postgresql
sudo systemctl enable postgresql
# switch to postgres user and enter to the posgresql console
sudo -i -u postgres
psql
# create new user
CREATE DATABASE mydb;
CREATE USER someuser PASSWORD 'somepassword';
GRANT ALL PRIVILEGES ON DATABASE mydb TO someuser;
ALTER DATABASE mydb OWNER TO someuser;
ALTER SCHEMA public OWNER TO someuser;
# check the iptables for port 5432
sudo iptables --line -vnL | grep reject
# add rule to iptables
iptables -I INPUT 9 -i eth0 -p tcp --dport 5432 -m state --state NEW,ESTABLISHED -j ACCEPT
# open the connections
https://stackoverflow.com/questions/3278379/how-to-configure-postgresql-to-accept-all-incoming-connections
# restar postgresql
sudo service postgresql restart
# connect from outside
psql -h hxv-snt.hexacta.com -U someuser -W mydb
```

