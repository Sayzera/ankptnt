<?php

namespace App\Validators;


use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class ObservationValidators
{
    private $validator;
    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    public function checkObservationValidator($input)
    {
        $min = 2;
        $max = 255;
        $constraint =  new Assert\Collection([
            'marka-adi' => [
                new Assert\NotBlank(['message' => 'Marka adı alanı boş bırakılamaz.']),
                new Assert\Length(['min' => $min, 'max' => $max, 'minMessage' => "Marka alanı en az {$min} karakter olmalıdır."])
            ],
            // 'bulten-no' => [
            //     new Assert\NotBlank(['message' => 'Bülten no alanı boş bırakılamaz.']),
            // ],
            'siniflar' => [
                new Assert\NotBlank(['message' => 'Sınıf alanı boş bırakılamaz.']),
            ],
            'token' => [
                new Assert\NotBlank(['message' => 'Bu alan boş bırakılamaz.']),
                new Assert\Length(['min' => $min, 'max' => $max, 'minMessage' => "Bu alan en az {$min} karakter olmalıdır."])
            ],
        ]);

        $errors = [];

        $validation =  $this->validator->validate($input, $constraint);

        if (count($validation) > 0) {
            foreach ($validation as $error) {
                $errors[preg_replace(
                    '/\[|\]/',
                    '',
                    $error->getPropertyPath()
                )] = $error->getMessage();
            }
        }

        return $errors;
    }
}
