<?php
namespace app\index\controller;

use common\model\User;
use common\model\Score;

class Index extends Base {
    public function score() {
        $result = [];
        $result['code'] = -1;
        $result['msg']  = "";


        try {

        $data = input('post.');
        if (empty($data['password'])) {
            $data['password'] = "1";
        }
        


        $nickname = $data['nickname'];
        $password = $data['password'];

        $user = model('User')->get(['nickname' => $nickname]);

        if (!$user) {
            $user = [];
            $user['nickname'] = $nickname;

            $user['password'] = sha1($password);
            model('User')->save($user);
            $user = model('User')->get(['nickname' => $user['nickname']]);
        } else {
            if (sha1($password) != $user->password) {
                $result['msg'] = 'uncorrect_pass';
                return json($result);   
            }
        }

        $score = [];
        $score['score'] = $data['score'];
        $score['user_id'] = $user->id;
        model('Score')->save($score);

        $result['code'] = 1;
        $result['msg'] = "OK";


        } catch (\Exception $e) {
            $result['msg'] = $e->getMessage();
        }

        return json($result);
    }

    public function rank() {
        $result = [];
        $result['code'] = -1;
        $result['msg']  = "";
        $result['data'] = [];

        try {
            $data = input('get.');
            $size = $data['size'];
            $page = $data['page'];

            $scores = model('Score')
                ->alias("s")
                ->join("user u", "u.id = s.user_id")
                ->field("s.id, u.nickname, s.score")
                ->order("s.score", "desc")
                ->page($page, $size)->select();

            $result['data'] = $scores;

            $result['code'] = 1;
            $result['msg']  = "OK";
            
        } catch (\Exception $e) {
            $result['msg'] = $e->getMessage();
        }

        return json($result);
    }

}
