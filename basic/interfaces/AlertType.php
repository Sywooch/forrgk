<?php


namespace app\interfaces;



interface AlertType {
    /**
     * @param $alert
     * @return mixed
     */
    public function handleAlert($alert);
}