<?php


namespace app\interfaces;



interface EventType {

    /**
     * В этот метод передается заголовок и контент, для обработки.
     * Например, можно подменивать переменные и т.д.
     *
     * @param string $inputData Input data to handle
     * @return string Data output
     */
    public function handleData(&$inputData);


    /**
     * Этот метод должен возвращать массив из переменных, для отображения в админке,
     * какие переменные доступны для использования.
     *
     * @return array|string Массив из списков переменных
     */
    public function varList();
}