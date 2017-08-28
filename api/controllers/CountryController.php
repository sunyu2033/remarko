<?php

namespace app\controllers;

use yii\rest\ActiveController;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class CountryController extends ActiveController
{
    public $modelClass = 'api\models\Country';

    public function CreateAction(){
        print_r(11111);
    }


}


