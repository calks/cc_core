<?php

    class BreadcrumbsNode {
        public $link;
        public $text;
        // if set to true, no additional nodes will be added
        protected $finalized;

        public function __construct($link, $text) {
            $this->link = $link;
            $this->text = $text;
        }
    }


    class Breadcrumbs {
        protected static $path;

        public function __construct() {
            if (!self::$path) {
                self::$path = array();
            }
            $this->finalized = false;
        }

        public function addNode($link, $text) {
            if ($this->finalized) return;
            $link = Application::getSeoUrl($link);
            $this->path[] = new BreadcrumbsNode($link, $text);
        }

        public function getPath() {
            return $this->path;
        }

        public function getPathLength() {
            return count($this->path);
        }

        public function getLastNode() {
            $node_keys = array_keys($this->path);
            if(empty($node_keys)) return null;
            $last_key = $node_keys[count($node_keys)-1];
            return $this->path[$last_key];
        }

        public function getLastNodeLink() {
            $node = $this->getLastNode();
            return is_null($node) ? '' : $node->link;
        }

        public function getLastNodeText() {
            $node = $this->getLastNode();
            return is_null($node) ? '' : $node->text;
        }

        public function finalize() {
            $this->finalized = true;
        }



    }
