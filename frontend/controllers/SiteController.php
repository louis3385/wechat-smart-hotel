<?php
namespace frontend\controllers;

use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * 前端日志搜集
     */
    public function actionLog()
    {
        $request = \Yii::$app->request;
        $type = $request->get('type');
        $user = \Yii::$app->user->isGuest ? 'Guest' : \Yii::$app->user->id;
        $msg = $request->get('msg');
        $url = $request->get('url');
        $line = $request->get('line');
        if ($type == 'error') {
            \Yii::error("Javascript runtime error:\nUser:" . $user . "\nMessagae:" . $msg . "\nUrl:" . $url . "\nLine:" . $line, 'javascript');
        } elseif ($type == 'warning') {
            \Yii::warning("Javascript runtime warning:\nUser:" . $user . "\nMessagae:" . $msg . "\nUrl:" . $url . "\nLine:" . $line, 'javascript');
        } elseif ($type == 'info') {
            \Yii::info("Javascript runtime info:\nUser:" . $user . "\nMessagae:" . $msg . "\nUrl:" . $url . "\nLine:" . $line, 'javascript');
        }
        header('Status: 204 No Content');
        \Yii::$app->end();
    }
}
