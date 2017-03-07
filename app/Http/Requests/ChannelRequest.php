<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ChannelRequest extends Request
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
        $rules = [
            'name' => ['required', 'unique:channels,name'],
            'channel_id' => ['required', 'unique:channels,channel_id']
        ];

        if ($channel = $this->route('channel')) {
            array_pop($rules['name']);
            array_push($rules['name'], 'unique:channels,name,' . $channel->id);
            array_pop($rules['channel_id']);
            array_push($rules['channel_id'], 'unique:channels,channel_id,' . $channel->id);
        }

        return $rules;
    }
}
