<?php

    namespace api;

    /** At this stage all API errors will return two or three values
    *   result (which will be either "not found" or "failure")
    *   if result is "not found", the other value will be "documentation",
    *   and will contain the URL for the API documentation (in JSON format)
    *   if the result is "failure", the documentation value will be returned
    *   along with another value ("details"), which will contain exactly
    *   what it sounds like it will contain 
    */

    class APIError {

        private $result;
        private $details;
        private $docURL;

        public function __construct($result = "", $details = "") {
            $this->docURL = (($_SERVER['DOCUMENT_ROOT'] != '')  ? $_SERVER['SERVER_NAME'] . '/api/index.php'
                                                                : './api/index.php');
            if (in_array($result, array("not found", "failure"))) {
                $this->result = $result;
                if ($result == "not found") {
                    $this->details = "";
                } else {
                    $this->details = $details;
                }

            } else {
                $this->result = null;
                $this->details = null;
            }
        }

        public function getJSON() {
            if ($this->result == null) {
                return null;
            }
            if ($this->result == "not found") {
                return json_encode(array("result" => $this->result, "documentation" => $this->docURL));
            } else {
                return json_encode(array("result" => $this->result, "details" => $this->details,
                                                                    "documentation" => $this->docURL));
            }
        }
    }

?>