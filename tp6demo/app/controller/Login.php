<?php
namespace app\controller;
use think\facade\Validate;
use think\Request;

class Login
{
    /**
     * 提示模板路径
     *
     * @var string
     */
    private $toast = 'public/toast';

    public function index()
    {
        return view('index');
    }

    public function check(Request $request)
    {
        $data = $request->param();

        //错误集合
        $errors = [];

        //验证
        $validate = Validate::rule([
            'name'  =>  'unique:auth,name^password'
        ]);

        $result = $validate->check([
            'name'      =>  $data['name'],
            'password'  =>  sha1($data['password'])
        ]);

        //错误提示，反向操作
        //如果用户名和密码同时比对存在，那其实就是正确的
        if ($result) {
            $errors[] = '用户名或密码错误~';
        }

        //验证码
        if (!captcha_check($data['code'])) {
            $errors[] = '验证码不正确~';
        }

        //判断跳转
        if (!empty($errors)) {
            return view($this->toast, [
                'infos'     =>  $errors,
                'url_text'  =>  '返回登录',
                'url_path'  =>  url('/login')
            ]);
        } else {
            session('admin', $data['name']);
            return redirect('/');
        }

    }

    public function out()
    {
        session('admin', null);
        return redirect('/login');
    }
}