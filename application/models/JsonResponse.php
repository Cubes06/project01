<?php

    class Application_Model_JsonResponse implements JsonSerializable {

        protected $status = 'ok';
        protected $statusMessage = '';
        protected $payload = array();

        public function getStatus() {
            return $this->status;
        }

        public function getStatusMessage() {
            return $this->statusMessage;
        }

        public function getPayload() {
            return $this->payload;
        }

        protected function setStatus($status) {
            $this->status = $status;
            return $this;
        }

        public function setStatusMessage($statusMessage) {
            $this->statusMessage = $statusMessage;
            return $this;
        }

        public function setPayload($payload) {
            $this->payload = $payload;
            return $this;
        }

        public function statusIsError() {
            $this->setStatus('error');
            return $this;
        }

        public function statusIsOk() {
            $this->setStatus('ok');
            return $this;
        }

        public function setPayloadKey($key, $value) {
            $this->payload[$key] = $value;
            return $this;
        }

        public function jsonSerialize() {
            return array(
                'status' => $this->getStatus(),
                'statusMessage' => $this->getStatusMessage(),
                'payload' => $this->getPayload(),
            );
        }

    }
