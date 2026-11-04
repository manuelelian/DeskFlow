<?php

namespace app\controllers;

use \Controller;
use app\models\UserModel;



class UserController extends Controller
{
	/*  .....................
	Atributos Principales
	.....................
*/
	protected $token;
	protected $email;
	protected $password;
	protected $nombre;
	protected $apellido;
	protected $tipo;
	protected $activo;

	/*  .....................
	FIN - Atributos Principales
	.....................
*/

	/*obtiene todos los datos de un usuarios por id o por email según dato ingresado*/
	public static function getUser($emailOrId)
	{
		if (filter_var($emailOrId, FILTER_VALIDATE_EMAIL)) {
			# obtener datos de usuario por Email
			$userData = UserModel::findEmail($emailOrId);
			// var_dump($userData);
		}
		return $userData;
	}

	#----------------Valicaciones de campos de formularios

	/*Validad si el e-mail asociado a un Usuario se encuentra en estado Activo*/
	public static function checkActivo($userEmail)
	{
		$datosUsuario = UserModel::findEmail($userEmail);
		// var_dump($datosUsuario);
		if ($datosUsuario) {
			if ($datosUsuario->estado == '1') {
				$result =  true;
			} else {
				// $result = 'El Usuario no se encuentra activo!';
				$result = 'Problemas al acceder a la cuenta! <br>(UC-#42)';
			}
		} else {
			$result = false;
		}
		// var_dump ($result);
		return $result;
	}

	/*Validar de campo E-mail, Email valído, comprobar email duplicado y comprobar existencia en db*/
	public static function checkEmail($email, bool $verifyDuplicate = null, bool $verifyExistDB = null)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$result = true; // email valido
			if (isset($verifyDuplicate)) { // Validacion con chequeo de campo duplicado en base de datos
				if ($verifyDuplicate && !isset($verifyExistDB)) {
					if (UserModel::CheckEmail($email)) {
						$result = 'El <b>E-MAIL</b> ya se encuentra en uso!'; // email no valido
					} else {
						$result = true; // email valido
					}
				}
			} elseif ($verifyExistDB && !isset($verifyDuplicate)) { // Validacion con chequeo de existencia de campo en base de datos
				if (UserModel::CheckEmail($email)) {
					$result = true; // existe en la base de datos
				} else {
					$result = 'El <b>E-MAIL</b> no se encuentra registrado!'; // no existe en la base de datos
				}
			}
		} else {
			$result = 'Revise el E-mail ingresado!'; // email no valido
		}

		return $result;
	}

	public static function checkEmailModificar($email, $userId, bool $verifyDuplicate = null, bool $verifyExistDB = null)
	{
	    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
	        $result = true; // email válido

	        if (isset($verifyDuplicate)) { // Validación con chequeo de campo duplicado en base de datos
	            if ($verifyDuplicate && !isset($verifyExistDB)) {
	                $existingUser = UserModel::getUserByEmail($email); // Obtener usuario con ese email

	                if ($existingUser && $existingUser[0]->id_usuario != $userId) {
	                    $result = 'El E-MAIL ya se encuentra en uso por otro usuario!'; // email no válido
	                } else {
	                    $result = true; // email válido
	                }
	            }
	        } elseif ($verifyExistDB && !isset($verifyDuplicate)) { // Validación con chequeo de existencia de campo en base de datos
	            if (UserModel::CheckEmail($email)) {
	                $result = true; // existe en la base de datos
	            } else {
	                $result = 'El E-MAIL no se encuentra registrado!'; // no existe en la base de datos
	            }
	        }
	    } else {
	        $result = 'Revise el E-mail ingresado!'; // email no válido
	    }

	    return $result;
	}


	/*Comprobar campo vacio, personalizacion de mensaje de salida, minimo de caracteres y maximo de caracteres opcional*/
	public static function checkEmpty($dato, string $campoName, int $minlength = null, int $maxlength = null)
	{

	    if (empty(trim($dato))) {
	        $result = "Por favor ingrese " . ($campoName); // campo vacío
	    } else {
	        if (isset($minlength) && isset($maxlength)) {
	            if (strlen($dato) >= $minlength && strlen($dato) <= $maxlength) {
	                $result = true; // campo no vacío con restricción de caracteres
	            } else {
	                $result = "Por favor ingrese " . ($campoName) . " entre $minlength y $maxlength caracteres!"; // longitud incorrecta
	            }
	        } elseif (isset($minlength)) {
	            if (strlen($dato) >= $minlength) {
	                $result = true; // campo no vacío con restricción mínima de caracteres
	            } else {
	                $result = "Por favor ingrese una " . ($campoName) . " mayor a $minlength caracteres!"; // longitud incorrecta
	            }
	        } else {
	            $result = true; // campo no vacío sin restricción de caracteres
	        }
	    }

	    return $result;
	}


	/*Comprobar campo vacio, personalizacion de mensaje de salida, minimo de caracteres y maximo de caracteres opcional, si opcional es TRUE puede quedar vacio*/
	public static function checkEmptyOpcional($dato, string $campoName, int $minlength = null, int $maxlength = null)
	{
		if (empty(trim($dato))) {
			$result = true; // campo vacio no obligatorio
		} else {
			if (isset($minlength) && isset($maxlength)) {
				if (strlen(trim($dato)) >= $minlength && strlen(trim($dato)) <= $maxlength) {
					$result = true; // campo no vacio con restriccion de caracteres.
				} else {
					$result = "Por favor ingrese <b>" . ucfirst($campoName) . "</b> entre $minlength y $maxlength caracteres!"; // campo vacio
				}
			} elseif (isset($minlength)) {
				if (strlen(trim($dato)) >= $minlength) {
					$result = true; // campo no vacio con restriccion de caracteres.
				} else {
					$result = "Por favor ingrese una <b>" . ucfirst($campoName) . "</b> mayor a $minlength caracteres!"; // campo vacio
				}
			} else {
				$result = true; // campo no vacio sin restriccion de caracteres
			}
		}
		return $result;
	}

	/*Comprobnar campo numerico,  personalizacion de mensaje de salida, chequeo de número minimo y maximo*/
	public static function checkNumber($number, string $campoName, int $min = null, float $max = null)
	{
		if (is_numeric($number)) {
			if (isset($min) && isset($max)) {
				if ($number >= $min && $number <= $max) {
					$result = true; // es número con rango a comprobar
				} else {
					$result = "Por favor ingrese un número entre $min y $max en: <b>" . strtoupper($campoName) . "!</b>"; // email no valido
				}
			} else {
				$result = true; // es número sin rango a comprobar
			}
		} else {
			$result = "Por favor ingrese un número en: " . $campoName . "!"; // no es un numero
		}
		return $result;
	}

	/*Comprobnar campo numerico,  personalizacion de mensaje de salida, chequeo de número minimo y maximo*/
	public static function checkNumberOpcional($number, string $campoName, int $min = null, float $max = null)
	{
		if (empty(trim($number))) {
			$result = true;  // campo vacio no obligatorio
		} else {
			if (is_numeric($number)) {
				if (isset($min) && isset($max)) {
					if ($number >= $min && $number <= $max) {
						$result = true; // es número con rango a comprobar
					} else {
						$result = "Por favor ingrese un número entre $min y $max en: <b>" . strtoupper($campoName) . "!</b>"; // email no valido
					}
				} else {
					$result = true; // es número sin rango a comprobar
				}
			} else {
				$result = "Por favor ingrese solo números en: <b>" . strtoupper($campoName) . "!</b>"; // no es un numero
			}
		}
		return $result;
	}

	/*Comprobnar campo numerico,  personalizacion de mensaje de salida, chequeo de número minimo y maximo, Chequeo de numero sin puntos ni guiones*/
	public static function checkOnlyNumero($dni, string $campoName, int $min = null, float $max = null)
	{
		if (is_numeric($dni)) {
			if (isset($min) && isset($max)) {
				if ($dni >= $min && $dni <= $max) {
					if (!preg_match('/^[0-9]*$/', $dni)) {
						$result = "Por favor ingrese un número sin puntos, comas, o guiones <b>" . strtoupper($campoName) . "!</b>"; // número no valido
					} else {
						$result = true; // es número con rango a comprobar
					}
				} else {
					$result = "Por favor ingrese un número entre $min y $max en: <b>" . strtoupper($campoName) . "!</b>"; // número no valido
				}
			} else {
				if (!preg_match('/^[0-9]*$/', $dni)) {
					$result = "Por favor ingrese un número sin puntos, comas, o guiones <b>" . strtoupper($campoName) . "!</b>"; // número no valido
				} else {
					$result = true; // es número sin rango a comprobar
				}
			}
		} else {
			$result = "Por favor ingrese solo números en: <b>" . strtoupper($campoName) . "!</b>"; // no es un numero
		}
		return $result;
	}

	public static function checkPasswword($userIdOremail, $passwordInput)
	{
		$userData = self::getUser($userIdOremail);

		$check = password_verify($passwordInput, trim($userData->password_usuario));

		if ($check === true) {
			$result = true;
		} else {
			$result = 'Usuario y/o contraseña Incorrectos';
		}

		return $result;
	}

	public static function getPasswordHash($password = null)
	{
		if (!isset($password)) {
			$password = rand(1000, 9999);
		}

		/*Se genera un HASH de contraseña*/
		$password = password_hash($password, PASSWORD_DEFAULT);
		return $password;
	}

	// En UserController.php
	public static function checkPasswordsMatch($password, $confirmation)
	{
		return $password === $confirmation;
	}


	#Fin----------------Valicaciones de campos de formularios

	/* Agregar usuario 
	EL parametro $alta_tipo puede tomar 2 valores
	- registro "por_sistema"
		- puede ser parcial ($parcial = true), datos minimos
		- completo ($parcial = null o false)
	- registro "de_usuario"
		- puede ser parcial ($parcial = true), datos minimos

	*/


	public static function deleteToken()
	{
		unset($_SESSION['acceso_valido']);
	}
}
