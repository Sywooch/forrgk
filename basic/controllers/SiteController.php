<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\RegisterForm;
use yii\web\ForbiddenHttpException;
use app\models\Alerts;
use app\modules\admin\models\Articles;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'checklogin' => ['post'],
                    'checkregister' => ['post']
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->user->can('adminAccess')) {
                return $this->redirect(array('/admin'));
            } else
                return $this->redirect(array('site/alerts'));
        }

        return $this->render('index');
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {

        (new \app\models\Alerts())->triggerAlert('logout', array('logoutUsername' => Yii::$app->user->identity->username));
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Вход на сайт
     */
    public function actionChecklogin() {
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->user->can('adminAccess')) {
                return $this->redirect(array('/admin/default'));
            } else
                return $this->redirect(array('site/alerts'));
        }

        $model = new LoginForm();
        $model->attributes = Yii::$app->request->post();

        $model->on('login', function ($event) {
            (new \app\models\Alerts())->triggerAlert('login', $event->vars);
        });

        if ($model->login()) {
            return $this->goHome();
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionRegister() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        return $this->render('register', ['errors' => array()]);
    }

    public function actionCheckregister() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();

        // Вешаем обработчик события на регистрацию
        $model->on('register', function ($event) {
            (new \app\models\Alerts())->triggerAlert('register', $event->vars);
        });

        $model->attributes = Yii::$app->request->post();


        if ($model->validate() && $model->register()) {
            return $this->goHome();

        } else {
            return $this->render('register', [
            'errors' => $model->errors,
            ]);
        }
    }


    public function actionAlerts() {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

        //(new Alerts)->triggerAlert('register', array('testVar'=>'Привет, Мир1!'));

        $page = Yii::$app->request->get('page');
        $user = Yii::$app->user->getId();

        return $this->render('view_alerts', [
            'alerts' => (new Alerts())->getListForUser('browser', $user, $page)
        ]);
    }

    public function actionAlertslist() {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

        $page = Yii::$app->request->get('page');
        $user = Yii::$app->user->getId();

        return json_encode((new Alerts())->getListForUser('browser', $user, $page));
    }

    public function actionSwitch() {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

        $id = Yii::$app->request->get('id');
        $page = Yii::$app->request->get('page');
        $user = Yii::$app->user->getId();

        if (!(new Alerts())->setReaded($user, $id)) {
            throw new BadRequestHttpException();
        } else {
            return $this->redirect(['site/alerts', ['page'=>$page]]);
        }
    }

    public function actionArticle() {
        $id = (int)Yii::$app->request->get('id');

        $article = Articles::findOne($id);

        if (!$article) {
            throw  new NotFoundHttpException();
        }

        return $this->render('article', [
            'article' => $article
        ]);
    }
}
