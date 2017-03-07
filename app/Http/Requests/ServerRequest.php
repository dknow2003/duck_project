<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ServerRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rule = [
            'name'   => ['required', 'unique:servers,name'],
            'start_from' => ['required', 'date_format:Y-m-d'],
            'host.*' => 'required',
            'port.*' => 'required',
            'database.*' => 'required',
            'username.*' => 'required',
            // 从 Password 改为 pwd， Laravel 过滤了 session 旧值中的 password。
            'pwd.*' => 'required',
        ];
        if ($server = $this->route('server')) {
            array_pop($rule['name']);
            array_push($rule['name'], 'unique:servers,name,' . $server->id);
        }

        return $rule;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name'   => '服务器名称',
            'start_from' => '开服时间',
            'host.*' => '主机',
            'port.*' => '端口',
            'database.*' => '数据库',
            'username.*' => '用户名',
            'pwd.*' => '密码',
        ];
    }
}
