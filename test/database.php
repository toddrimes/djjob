<?php

require dirname(__FILE__) . "/../DJJob.php";

DJJob::configure("mysql:host=127.0.0.1;dbname=djjob_test", "root", "");

DJJob::runQuery("
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
`handler` VARCHAR(2000) NOT NULL,
`queue` VARCHAR(255) NOT NULL DEFAULT 'default',
`run_at` DATETIME NULL,
`locked_at` DATETIME NULL,
`locked_by` VARCHAR(255) NULL,
`failed_at` DATETIME NULL,
`error` VARCHAR(2000) NULL,
`created_at` DATETIME NOT NULL
) ENGINE = MEMORY;
");

class HelloWorldJob {
    public function __construct($name) {
        $this->name = $name;
    }
    public function perform() {
        echo "Hello {$this->name}!\n";
        sleep(1);
    }
}

var_dump(DJJob::status());

DJJob::enqueue(new HelloWorldJob("delayed_job"));
DJJob::bulkEnqueue(array(
    new HelloWorldJob("shopify"),
    new HelloWorldJob("github"),
));

$worker = new DJWorker(array("count" => 3));
$worker->start();

var_dump(DJJob::status());
