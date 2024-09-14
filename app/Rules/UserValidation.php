<?php

namespace App\Rules;

use Illuminate\Validation\Rules\ValidationRule;
use Illuminate\Validation\ValidationRuleParser;

class UserValidation extends ValidationRuleParser
{
    protected $attribute;
    protected $value;
    protected $parameters;

    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        $this->value = $value;

        switch ($attribute) {
            case 'Usuario':
                return $this->validateUsuario();
            case 'Nombre_Usuario':
                return $this->validateNombreUsuario();
            case 'Estado_Usuario':
                return $this->validateEstadoUsuario();
            case 'Contrasena':
                return $this->validatePassword();
            case 'Confirmar_Contrasena':
                return $this->validateConfirmPassword();
            case 'Correo_Electronico':
                return $this->validateEmail();
            default:
                return false;
        }
    }

    public function message()
    {
        switch ($this->attribute) {
            case 'Usuario':
                return 'El usuario debe estar en mayúsculas y contener otros caracteres.';
            case 'Nombre_Usuario':
                return 'El nombre de usuario debe tener la primera letra en mayúscula y no contener números ni otros caracteres.';
            case 'Estado_Usuario':
                return 'El estado del usuario solo debe contener texto.';
            case 'Contrasena':
                return 'La contraseña debe cumplir con las reglas establecidas.';
            case 'Confirmar_Contrasena':
                return 'La confirmación de la contraseña debe coincidir con la nueva contraseña y cumplir con las reglas establecidas.';
            case 'Correo_Electronico':
                return 'El correo electrónico debe ser válido y estar en minúsculas.';
            default:
                return 'Error de validación.';
        }
    }

    protected function validateUsuario()
    {
        return preg_match('/^[A-Z\s\W]+$/', $this->value);
    }

    protected function validateNombreUsuario()
    {
        return preg_match('/^[A-Z][a-zA-Z]+$/', $this->value);
    }

    protected function validateEstadoUsuario()
    {
        return preg_match('/^[a-zA-Z]+$/', $this->value);
    }

    protected function validatePassword()
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $this->value) && !str_contains($this->value, ' ') && $this->value !== $this->parameters['nombre_usuario'];
    }

    protected function validateConfirmPassword()
    {
        return $this->parameters['password'] === $this->value;
    }

    protected function validateEmail()
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) && strtolower($this->value) === $this->value;
    }
}

