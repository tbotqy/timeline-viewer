<?php

class StatusesController extends AppController{
       
    public function import(){
        //ユーザーのツイートをAPI経由で取得して、データベースに記録する。
        //処理時代はajaxで行うので、このアクションではimport.ctpを表示するのみ。
    }

    public function acquire_statuses(){
        //APIを呼んでデータを取得
        //データベースに保存
    }
}