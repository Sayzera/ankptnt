<?php
namespace App\Validators;


use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
class MainValidators
{
    private $validator;
    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    public function getLangMessageValidator($input)
    {
        $min = 2;
        $max = 255;
        $constraint =  new Assert\Collection([
            'key' => [
                new Assert\NotBlank(['message' => 'İsim alanı boş bırakılamaz.']),
                new Assert\Length(['min' => $min, 'max' => $max, 'minMessage' => "Bu alan en az {$min} karakter olmalıdır."])
            ],
            'value' => [
                new Assert\NotBlank(['message' => 'Değer alanı boş bırakılamaz.']),
                new Assert\Length(['min' => $min, 'max' => $max, 'minMessage' => "Bu alan en az {$min} karakter olmalıdır."])
            ],
            'lang' => [
                new Assert\NotBlank(['message' => 'Bu alan boş bırakılamaz.']),
                new Assert\Length(['min' => $min, 'max' => $max, 'minMessage' => "Bu alan en az {$min} karakter olmalıdır."])
            ],
            'token' => [
                new Assert\NotBlank(['message' => 'Bu alan boş bırakılamaz.']),
                new Assert\Length(['min' => $min, 'max' => $max, 'minMessage' => "Bu alan en az {$min} karakter olmalıdır."])
            ],
            // idye izin ver
            'id' => [
                new Assert\Length(['min' => 1, 'max' => 255, 'minMessage' => "Bu alan en az {$min} karakter olmalıdır."])
            ]
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