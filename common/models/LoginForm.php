<?php
namespace common\models;

use Yii;
use yii\base\Model;

use yii\web\Session;
use yii\db\Query;
use app\models\Empleado;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    var $session;

    /**
     * @inheritdoc
     */
    
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
    
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    /*
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }
    */
    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    /*
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }
    */
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    /*
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
    */

    /*****************************************************/
    public function validatePassword($attribute, $params)
    {
        
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Usuario o Contraseña Incorrecto....!!');
            }
            else{
                $this->MenuOpciones();
            }
        }
    }
    
    public function login()
    {
        if ($this->validate()) {

            $this->session = Yii::$app->session;
            $_SESSION['rol'] = $this->username;

            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
    
    /************************************************************************/
    
    public function MenuOpciones()
    {
        //$id_rol = $this->getId_rol($this->username);
        $id_rol  = User::getId_rol_findByUsername($this->username);
        $persona = $this->getPersona($this->username); 
        $menus   = $this->getMenusOpciones($id_rol);
        $_SESSION['nombre_persona'] = $persona['Nombre'];
        $_SESSION['id_persona_menu'] = $persona['idPersonas'];

        $items = [];

        foreach ($menus as $d) {
            
            $_SESSION['nombre_rol'] = $d['nombre'];

            $opcion = explode("|",$d['opcion']);
            $label  = $opcion[0];
            $icono  = $opcion[1];
            $url    = $opcion[2];

            if($url=="#") //Tiene submenus
            {
                $subMenu = [];
                $sub = explode("*",$d['submenu']);
                foreach ($sub as $ds) {
                    $opciones = explode("|",$ds);
                    $label_sub = $opciones[0];
                    $icono_sub = $opciones[1];
                    $url_sub   = $opciones[2];
                    $subMenu[] = ['label' => ucfirst($label_sub),'icon'=>$icono_sub,'url' => [$url_sub]];
                }
                $items[] = ['label' => ucfirst($label), 'icon'=>$icono, 'url' => [$url],'items' => $subMenu];
            }
            else
            {
                if($icono=="header")
                    $items[] = ['label' => ucfirst($label), 'options'=> ['class'=>$icono]];
                else
                    $items[] = ['label' => ucfirst($label), 'icon'=>$icono, 'url' => [$url]];
            }
        }
        $_SESSION['menuOpciones'] = $items;
        //print_r($items);
        //exit;
    }

    public function getMenusOpciones($id_rol)
    {
        $query = new Query;
        $query->select('*')->from('v_menus')->where(['id_rol'=>$id_rol]);
        $command = $query->createCommand();
        $data = $command->queryAll(); 
        return $data;
    }

    public function getPersona($usuario)
    {
        $query = new Query;
        $query->select('*')->from('Empleado')->where(['usuario'=>$usuario]);
        $command = $query->createCommand();
        $data = $command->queryOne(); 
        return $data;
    }
}
