<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * The plain text version of the token.
     *
     * @var string
     */
    public $token;

    /**
     * Instantiate a new controller instance.
     *
     * @param  $resource
     * @param  string  $token
     * @return void
     */
    public function __construct($resource, string $token)
    {
        parent::__construct($resource);

        $this->token = $token;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'accessToken' => $this->token,
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email
            ]
        ];
    }
}
