<?php

    Application::loadLibrary("seo/url");

    abstract class RewriteRule {

        protected function fetch_lookup_info($table, $id_field, $url_field, $where='') {
            $out = array();
            $db = Application::getDb();

            if ($where) $where = "WHERE $where";

            $data = $db->executeSelectAllObjects("
                SELECT `$id_field` AS id, `$url_field` AS url
                FROM `$table`
                $where
            ");

            foreach ($data as $item) {
                $out[$item->id] = $item->url;
            }

            return $out;
        }

        abstract public function seoToInternal(URL $seo_url);

        abstract public function internalToSeo(URL $internal_url);

    }
