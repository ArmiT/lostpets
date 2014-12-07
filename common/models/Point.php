<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "point".
 *
 * @property integer $id
 * @property integer $animal_id
 * @property integer $type
 * @property integer $user_id
 * @property integer $status
 * @property string $created_at
 */
class Point extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point';
    }

    public $lat = 0;
    public $lng = 0;

    public function fields(){
        return[
            'id',
            'animal_id',
            'type',
            'user_id',
            'status',
            'lng',
            'lat'
        ];

    }


    public static function  getTypeList(){
        return [
            2 => 'Lost',
            4 => 'Found',
            8 => 'Search new home'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['animal_id', 'type', 'user_id', 'status'], 'integer'],
            [['created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'animal_id' => 'Animal ID',
            'type' => 'Type',
            'user_id' => 'User ID',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public static function findPolygon($topleft,$botright, $typeSet = [], $animalSet=[]){

        // plz understand and forgive
        $sqlType = "and type IN (2,4,8)";
        if (is_array($typeSet) && !empty($typeSet)){
            array_walk($typeSet,function(&$item){$item = (int)$item;});
            $sqlType = " and type IN (".implode(",",$typeSet ).")";
        }

        $sqlSet = "";

        if (is_array($animalSet)){
            array_walk($animalSet,function(&$item){$item = (int)$item;});
            $sqlSet = " and animal_id IN (".implode(",",$animalSet ).")";
        }

        $result =  Yii::$app->db->createCommand("SELECT `point`.id,animal_id,type,user_id,`status`,created_at,X(coordinate) AS lng, Y(coordinate) AS lat,
        description.title as d_title, description.description as d_text,description.phone as d_phone,description.email as d_email,description.photo as d_photo   FROM `point` INNER JOIN description ON description.point_id = `point`.id INNER JOIN animal ON `animal`.`id` = animal_id  WHERE MBRWithin(`coordinate`,
                                        GeomFromText('
                                        Polygon((
                                        {$topleft[0]} {$topleft[1]},
                                        {$botright[0]} {$topleft[1]},
                                        {$botright[0]} {$botright[1]},
                                        {$topleft[0]} {$botright[1]},
                                        {$topleft[0]} {$topleft[1]}))')) = 1
                                        $sqlType
                                        $sqlSet
                                        LIMIT 0,100")->queryAll();

        return $result;
    }

    /*
    public function afterFind() {

        Yii::$app->db->createCommand("select  X(coordinate) AS lng, Y(coordinate) AS lat from `point` where ");

        parent::afterFind(); // TODO: Change the autogenerated stub
    }
    */
    public function insert($runValidation = true, $attributes = null) {

        // sorry :(
        $this->animal_id = (int) $this->animal_id;
        $this->type = (int) $this->type;
        $this->user_id = (int) $this->user_id;
        $this->status = (int) $this->status;
        $this->lng = (float) $this->lng;
        $this->lat = (float) $this->lat;

        Yii::$app->db->createCommand("
        insert into `point`(animal_id,type,user_id,`status`,coordinate)
        VALUES ('{$this->animal_id}','{$this->type}','{$this->user_id}','{$this->status}',GeomFromText('POINT({$this->lat} {$this->lng})'))
        ")->execute();

        return Yii::$app->db->lastInsertID;
    }


    public function afterSave($insert, $changedAttributes) {

        $coordinate = "GeomFromText('POINT({$this->lat} {$this->lng})')";
        Yii::$app->db->createCommand("UPDATE SET `coordinate`=$coordinate WHERE id={$this->id}")->queryOne();

        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }


    public function select(){



    }


}

