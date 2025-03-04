<?php
namespace App\Validation;

use Illuminate\Validation\Factory as Validator;

class LoginValidation
{
    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Valider les données d'entrée pour la connexion
     *
     * @param array $data
     * @return \Illuminate\Support\MessageBag
     */
    public function validate(array $data)
    {
        return $this->validator->make($data, [
            'username' => 'required',
            'password' => 'required',
        ]);
    }
}
 
