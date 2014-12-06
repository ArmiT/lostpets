<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class FilterForm extends Model
{
    public $animal;
    public $type;
    public $topleft;
    public $botright;

    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['animal','type'], 'required'],
            [['topleft'],function($attribute, $params){
                if(!is_array($this->topleft) || count($this->topleft)!=2){
                    $this->addError('topleft','is not array');
                }
            }],
            [['botright'],function($attribute, $params){
                if(!is_array($this->botright) || count($this->botright)!=2){
                    $this->addError('botright','is not array');
                }
            }],
        ];
    }

}