C:\wamp\bin\mysql\mysql5.7.9\bin\mysqld --install-manual 740_mysql_master --defaults-file=C:\wamp\bin\mysql\replication_project\master.ini
C:\wamp\bin\mysql\mysql5.7.9\bin\mysqld --install-manual 740_mysql_slave1 --defaults-file=C:\wamp\bin\mysql\replication_project\slave1.ini
C:\wamp\bin\mysql\mysql5.7.9\bin\mysqld --install-manual 740_mysql_slave2 --defaults-file=C:\wamp\bin\mysql\replication_project\slave2.ini
C:\wamp\bin\mysql\mysql5.7.9\bin\mysqld --initialize-insecure --datadir=C:\wamp\bin\mysql\replication_project\data_master
C:\wamp\bin\mysql\mysql5.7.9\bin\mysqld --initialize-insecure --datadir=C:\wamp\bin\mysql\replication_project\data_slave1
C:\wamp\bin\mysql\mysql5.7.9\bin\mysqld --initialize-insecure --datadir=C:\wamp\bin\mysql\replication_project\data_slave2

CREATE USER 'repl'@'localhost' IDENTIFIED BY 'pass';
GRANT REPLICATION SLAVE ON *.* TO 'repl'@'localhost';
SELECT @@server_id;
SHOW MASTER STATUS;
SHOW SLAVE HOSTS;
SHOW PROCESSLIST;
C:\wamp\bin\mysql\mysql5.7.9\bin\mysqldump -uroot --port=3306 --all-databases --master-data > C:\wamp\bin\mysql\replication_project\master_dump.sql

C:\wamp\bin\mysql\mysql5.7.9\bin\mysqld --defaults-file=C:\wamp\bin\mysql\replication_project\slave1.ini --skip-slave-start
cat master.info
SELECT @@server_id;
CHANGE MASTER TO MASTER_HOST='localhost', MASTER_PORT=3306, MASTER_USER='repl', MASTER_PASSWORD='pass';
C:\wamp\bin\mysql\mysql5.7.9\bin\mysql -uroot --port=3307 < C:\wamp\bin\mysql\replication_project\master_dump.sql
START SLAVE;
SHOW SLAVE STATUS;
STOP SLAVE;
C:\wamp\bin\mysql\mysql5.7.9\bin\mysqladmin -uroot --port=3307 shutdown

C:\wamp\bin\apache\apache2.4.17\bin>cls && ab -n 500 -c 100 740project.lc/add.php

DELETE FROM `reservation`;
DELETE FROM `accommodation`;
DELETE FROM `user`;
ALTER TABLE `reservation` AUTO_INCREMENT = 1;
ALTER TABLE `accommodation` AUTO_INCREMENT = 1;
ALTER TABLE `user` AUTO_INCREMENT = 1;
