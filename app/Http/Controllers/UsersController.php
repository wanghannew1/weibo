<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [            
            'except' => ['show', 'create', 'store', 'index']
        ]);
        /* 
        不需要验证是否登录的页面是：创建用户的页面（create）、显示用户个人信息的页面（show）
        不需要验证是否登录的操作是：创建用户时，点击创建按钮时的操作（store）
        */
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    //创建用户的页面，/users/create，不受中间件限制
    public function create()
    {
        return view('users.create');
    }
    //显示用户个人信息的页面，/users/{user}，不受中间件限制
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
    //创建用户，不受中间件限制，点击创建按钮时的操作
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        
        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success', '个人资料更新成功！');
        /*
        $user->update([
            'name' => $request->name,
            'password' => bcrypt($request->password),
        ]);
        */
        return redirect()->route('users.show', $user->id);
    }
    
    /*
    用户列表
    */
    public function index()
    {
        //$users = User::all();
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    /*
    删除用户
    */
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}
