<?php

namespace app\controllers;

use Yii;
use app\models\course\{Course, Module, Lesson};
use app\models\webinar\{Webinar};

class SiteController extends AppController
{
    // public $layout = false;

    /**
     * {@inheritdoc}
     */
    // public function actions()
    // {
    //     return [
    //         'error' => [
    //             'class' => 'yii\web\ErrorAction',
    //             'layout' => false,
    //         ],
    //         'captcha' => [
    //             'class' => 'yii\captcha\CaptchaAction',
    //             'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
    //         ],
    //     ];
    // }

    /**
     * Displays homepage
     * @return string
     */
    // public function actionIndex()
    // {
    //     return $this->render('index');
    // }

    public function actionCreateSitemap() {
        $xmldata = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL; 
        $xmldata .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
        
        $sub = Yii::$app->params['subInx'];
        $link = Yii::$app->params['listSubs'][$sub]['link'];
        $now = date('Y-m-d');

        $xmldata .= $this->url($link, [
            'lastmod' => $now,
            'changefreq' => 'monthly',
            'priority' => 1.0,
        ]);

        $courses = Course::find()->select(['id','subject_id','publish'])
            ->where(['subject_id'=>$sub, 'publish'=>1])
            ->with([
                'modules' => function ($query) {
                    $query->select(['id','course_id','free','publish'])->where(['publish'=>1,'free'=>1]);
                },
                'modules.lessons' => function ($query) {
                    $query->select(['id','module_id','free','publish'])->where(['publish'=>1,'free'=>1]);
                }
            ])
            ->asArray()->all();
        if (!empty($courses)) {
            $xmldata .= $this->url($link.'courses', [
                'lastmod' => $now,
                'changefreq' => 'weekly',
                'priority' => 1.0,
            ]);
            foreach ($courses as $course) {
                $xmldata .= $this->url($link."course/$course[id]", [
                    'lastmod' => $now,
                    'changefreq' => 'weekly',
                    'priority' => 0.9,
                ]);
                if (!empty($course['modules']))
                    foreach ($course['modules'] as $module) {
                        $xmldata .= $this->url($link."module/$module[id]", [
                            'lastmod' => $now,
                            'changefreq' => 'weekly',
                            'priority' => 0.8,
                        ]);
                        if (!empty($course['lessons']))
                            foreach ($course['lessons'] as $lesson) {
                                $xmldata .= url($link."module/$module[id]?lesson=$lesson[id]", [
                                    'lastmod' => $now,
                                    'changefreq' => 'weekly',
                                    'priority' => 0.7,
                                ]);
                            }
                    }
            }
        }

        $webinars = Webinar::find()->select(['id','subject_id','publish'])->where(['publish'=>1])
            ->andWhere(['in','subject_id',[1,$sub]])->asArray()->all();
        if (!empty($webinars)) {
            $xmldata .= $this->url($link.'webinars', [
                'lastmod' => $now,
                'changefreq' => 'daily',
                'priority' => 0.9,
            ]);
            foreach ($webinars as $web) {
                $xmldata .= $this->url($link."webinar/$web[id]", [
                    'lastmod' => $now,
                    'changefreq' => 'daily',
                    'priority' => 0.8,
                ]);
            }
        }

        $xmldata .= $this->url($link.'exams', [
            'lastmod' => $now,
            'changefreq' => 'daily',
            'priority' => 0.8,
        ]);
        	
		$xmldata .= '</urlset>'; 
		
		if(file_put_contents('sitemap.xml',$xmldata))
            return "sitemap.xml file created on project root folder..";
        else
            return "DEFEAT";
    }

    protected function url($url, $data) {
        $xmldata = '<url>';
        $xmldata .= '<loc>'.$url.'</loc>';
        if (!empty($data['lastmod']))
            $xmldata .= '<lastmod>'.$data['lastmod'].'</lastmod>';
        if (!empty($data['changefreq']))
            $xmldata .= '<changefreq>'.$data['changefreq'].'</changefreq>';
        if (!empty($data['priority']))
            $xmldata .= '<priority>'.$data['priority'].'</priority>';
        $xmldata .= '</url>'.PHP_EOL;
        return $xmldata;
    }
}
