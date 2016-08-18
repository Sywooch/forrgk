<?php

//namespace app\plugins;

// set alert type
if (!class_exists('EmailAlertTypePlugin')) {
    class AlertTypeEmail implements \app\interfaces\AlertType
    {
        public function handleAlert($alert)
        {
            $vars = json_decode($alert->var_list, true);
            // Здесь мы делаем отправку на e-mail
            Yii::$app->mailer->compose()
                ->setTo($vars['newUseremail'])
                ->setFrom(['admin@test.com' => 'RGKNotifier'])
                ->setSubject($alert->title)
                ->setTextBody($alert->content)
                ->send();
        }
    }


    class EmailAlertTypePlugin
    {

    }
    Yii::$app->rgk->plugins[basename(__FILE__)] = new EmailAlertTypePlugin();
    $alert_types['email'] = new AlertTypeEmail();

}
