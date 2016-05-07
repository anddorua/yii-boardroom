<?php

use yii\db\Migration;

class m160502_140001_init_db extends Migration implements \Iterator
{

    private $key;
    public static $statements = [
<<<EOT
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
EOT
    ,
<<<EOT
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) DEFAULT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime NOT NULL,
  `notes` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `chain` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOT
    ,
<<<EOT
CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `login` varchar(64) COLLATE utf8_bin NOT NULL,
  `email` varchar(129) COLLATE utf8_bin NOT NULL,
  `pwd_hash` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `is_admin` tinyint(4) NOT NULL DEFAULT '0',
  `hour_mode` tinyint(4) NOT NULL DEFAULT '24',
  `first_day` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(128) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
EOT
        ,
<<<EOT
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(64) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
EOT
        ,
<<<EOT
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time_start` (`time_start`),
  ADD KEY `chain` (`chain`),
  ADD KEY `emp_id` (`emp_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `creator_id` (`creator_id`);
EOT
        ,
<<<EOT
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);
EOT
,
<<<EOT
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);
EOT
    ,
<<<EOT
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
EOT
    ,
<<<EOT
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
EOT
    ,
<<<EOT
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
EOT
        ,
<<<EOT
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`creator_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
EOT
        ,
<<<EOT
INSERT INTO `rooms` (`id`, `room_name`) VALUES
(1, 'Boardroom 1'),
(2, 'Boardroom 2'),
(3, 'Boardroom 3');
EOT
    ,
<<<EOT
INSERT INTO `employees`(`login`, `is_admin`) VALUES ('admin',1)
EOT
    ,
    ];

    public function __construct($config = [])
    {
        $this->rewind();
        parent::__construct($config);
    }

    public function current()
    {
        return self::$statements[$this->key];
    }

    public function next()
    {
        $this->key++;
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return isset(self::$statements[$this->key]);
    }

    public function rewind()
    {
        $this->key = 0;
    }
    public function up()
    {
        foreach($this as $statement) {
            try {
                $this->execute($statement);
            } catch (\Exception $e) {
                echo 'Exception: ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")\n";
                echo 'on statement:' . "\n";
                echo $statement;
                echo $e->getTraceAsString() . "\n";
                return false;
            }
        }
        return true;
    }

    public function down()
    {
        $this->dropTable('appointments');
        $this->dropTable('rooms');
        $this->dropTable('employees');
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
