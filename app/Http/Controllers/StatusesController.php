<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Status;
use Auth;

class StatusesController extends Controller
{
	/*
	设置中间件验证用户是否登录，只允许登录操作微博增删
	*/
    public function __construct()
    {
        $this->middleware('auth');
    }
    // 创建微博的动作
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        Auth::user()->statuses()->create([
            'content' => $request['content']
        ]);
        session()->flash('success', '发布成功！');
        return redirect()->back();
    }
    // 删除微博的动作
    public function destroy(Status $status)
    {
        $this->authorize('destroy', $status);
        $status->delete();
        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }
}
