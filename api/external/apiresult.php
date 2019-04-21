<?php

    namespace api;

    /** Generalized API result class
     *  
    */

    class APIResult {

        private $result;
        private $details;
        private $docURL;
        private $detailsJSON;

        public function __construct($result = "", $details = "", $detailsJSON = false) {
            $this->docURL = (($_SERVER['DOCUMENT_ROOT'] != '')  ? 'HTTP://' . $_SERVER['SERVER_NAME'] . '/api/external/index.php'
                                                                : './api/external/index.php');
            $this->result = $result;
            $this->detailsJSON = $detailsJSON;
            if ($result == "not found") {
                $this->details = "";
            } else {
                $this->details = $details;
            }
        }

        public function getJSON() {
            if ($this->result == null) {
                return null;
            }
            if ($this->result == "not found") {
                return json_encode(array("result" => $this->result, "documentation" => $this->docURL), JSON_UNESCAPED_SLASHES);
            } else {
                if ($this->detailsJSON) {
                    $tempArr = json_decode($this->details);
                    return json_encode(array("result" => $this->result, "details" => $tempArr,
                                                            "documentation" => $this->docURL), JSON_UNESCAPED_SLASHES);
                } else {
                    return json_encode(array("result" => $this->result, "details" => $this->details,
                                                                    "documentation" => $this->docURL), JSON_UNESCAPED_SLASHES);
                }
            }
        }
    }

?>