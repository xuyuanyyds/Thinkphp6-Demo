<?php
declare (strict_types = 1);
namespace app\middleware;
use app\model\Auth as AuthModel;

class Auth
{
    /**
     * 提示模板路径
     *
     * @var string
     */
    private $toast = 'public/toast';

    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {

        //得到管理员
        $auth = AuthModel::where('name', session('admin'))->find();

        //权限 模块/方法 列表
        $roles = [];


        //遍历角色列表
        foreach ($auth->role as $key=>$obj) {
            //拆分uri，装入roles
            foreach (explode(',', $obj->uri) as $value) {
                $roles[] = $value;
            }
        }

        //得到当前的uri
        $uri = $request->controller().'/'.$request->action();

        //超管判断
        if ($roles[0] != 'All') {
            //权限范围，提示
            if (!in_array($uri, $roles)) {
                return view($this->toast, [
                    'infos'     =>  ['你没有操作权限~'],
                    'url_text'  =>  '返回首页',
                    'url_path'  =>  url('/')
                ]);
            }
        }

        return $next($request);
    }
}
