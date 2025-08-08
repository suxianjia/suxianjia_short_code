readme.md

优化要求 一 
src/Driver/PdoDriver.php
src/Driver/MysqliDriver.php
src/Driver/MysqlDriver.php
/Users/yx-dev/work_directory/wwwroot/composer_xianjia_root/xianjia_short_code/src/Services/MysqlServices.php    跟据  MysqlServices.php 逻辑 在 Factory 实现一个工厂类 [mysqli PDO mysql]  /Users/yx-dev/work_directory/wwwroot/composer_xianjia_root/xianjia_short_code/src/Factory/MysqlFactory.php 

src/Services/MysqlServices.php 继承 src/Factory/MysqlFactory.php 并通过  src/Factory/MysqlFactory.php  实例化 


优化要求 二 
src/Driver/RedisDriver.php
src/Driver/PredisDriver.php
/Users/yx-dev/work_directory/wwwroot/composer_xianjia_root/xianjia_short_code/src/Services/MysqlServices.php    跟据  MysqlServices.php 逻辑 在 Factory 实现一个工厂类 [redis predis ] /Users/yx-dev/work_directory/wwwroot/composer_xianjia_root/xianjia_short_code/src/Factory/RedisFactory.php 

src/Services/RedisServices.php  继承 src/Factory/RedisFactory.php  并通过  src/Factory/RedisFactory.php 