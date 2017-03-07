<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Auth;

class UserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->is_super;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'username' => ['required', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'pwd' => 'required',
            'full_name' => 'required',
        ];

        if ($user = $this->route('user')) {
            array_pop($rules['username']);
            array_push($rules['username'], 'unique:users,username,' . $user->id);
            array_pop($rules['email']);
            array_push($rules['email'], 'unique:users,username,' . $user->id);
        }

        // 只有一种情况下我们不需要验证密码的输入， 即修改帐号，且不改变旧有密码的情况下。
        if (\Route::current()->getName() === 'admin.users.update'
            && !\Request::has('change_password')) {
            unset($rules['pwd']);
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'username' => trans('user.username'),
            'email' => trans('user.email'),
            'pwd' => trans('user.password'),
            'full_name' => trans('user.full_name'),
        ];
    }
}
