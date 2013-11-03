<?php

    class profiler {
        protected static $sample = null;
        protected $name = null;
        protected $start = null;
        protected static $record_ids = array();

        public function __construct($name) {
            $this->name = $name;
        }

        public function start() {
            $this->start = microtime(true);
            $db = Application::getDb();
            $url = addslashes($this->get_url());
            $sample = $this->get_sample_num();
            $table = $this->get_db_table();
            $name = addslashes($this->name);
            $parent_id = $this->get_parent_id();

            $sql = "
                INSERT INTO $table (
                    parent_id,
                    sample_num,
                    url,
                    part,
                    time
                ) VALUES (
                    $parent_id,
                    $sample,
                    '$url',
                    '$name',
                    0
                )
            ";

            $db->execute($sql);

            $record_id = $db->getLastAutoIncrementValue();
            array_push(self::$record_ids, $record_id);

        }

        protected function get_db_table() {
            return 'gen_time';
        }

        protected function get_parent_id() {
            if (!self::$record_ids) return 0;
            $keys = array_keys(self::$record_ids);
            return self::$record_ids[$keys[count($keys)-1]];
        }

        protected function get_sample_num() {
            if (!self::$sample) {
                $db = Application::getDb();
                $table = $this->get_db_table();
                $gen_time_sample_num = (int)$db->executeScalar("SELECT MAX(sample_num) FROM $table");
                self::$sample = $gen_time_sample_num+1;
                $db->execute("
                    DELETE FROM $table WHERE sample_num<($gen_time_sample_num-100)
                ");
            }
            return self::$sample;
        }

        protected function get_url() {
            return $_SERVER['REQUEST_URI'];
        }

        public function stop() {
            $db = Application::getDb();
            $time = (microtime(true) - $this->start) * 1000;
            $table = $this->get_db_table();

            $record_id = array_pop(self::$record_ids);

            $db->execute("
                UPDATE $table SET time=$time
                WHERE id=$record_id
            ");
        }

        public function load_sample($sample_num, $parent_id=0) {
            $db = Application::getDb();
            $table = $this->get_db_table();
            $sample_num = (int)$sample_num;
            $parent_id = (int)$parent_id;
            $sql = "
                SELECT * FROM $table
                WHERE sample_num=$sample_num
                AND parent_id=$parent_id
            ";
            $list = $db->executeSelectAllObjects($sql);
            foreach ($list as &$item) {
                $item->children = $this->load_sample($sample_num, $item->id);
            }

            return $list;

        }



    }








