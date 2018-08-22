<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date Aug 4, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\visitor\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use johnsnook\visitor\models\Visitor;
use johnsnook\parsel\ParselQuery;

/**
 * VisitorSearch represents the model behind the search form of
 * [[johnsnook\visitor\models\Visitor]].
 *
 * This is the model class for table "visitor".
 *
 * @property string $ip
 * @property boolean $banned
 * @property string $created_at
 * @property string $updated_at
 * @property integer $user_id
 * @property string $city
 * @property string $region
 * @property string $country
 * @property string $postal
 * @property double $latitude
 * @property double $longitude
 * @property string $asn
 * @property string $organization
 * @property string $proxy
 * @property string $hat_color
 * @property string $hat_rule
 *
 * @property string $sql
 */
class VisitorSearch extends Visitor {

    /**
     * @var string Virtual field to pass user query for yii2-parsel
     */
    public $userQuery;

    /**
     * @var string Any parser errors that may have occurred
     */
    public $queryError;

    /**
     * @var ParselQuery The sql string generated by ParselQuery. For debugging purposes
     */
    private $parselQuery;

    /**
     * @var array The fields to search with ParselQuery
     */
    private $fields = ['ip' => 'v.ip', 'city', 'region', 'country', 'asn', 'organization', 'proxy', 'request', 'referer'];

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            //[['id'], 'integer'],
            [['ip', 'userQuery'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $this->load($params);

        $this->parselQuery = new ParselQuery([
            'userQuery' => $this->userQuery,
            'searchFields' => $this->fields,
            'dbQuery' => Visitor::find()
                    ->select(['v.ip'])
                    ->distinct()
                    ->addSelect(['city', 'region', 'country', 'visits', 'asn', 'organization', 'updated_at'])
                    ->from('visitor v')
                    ->leftJoin('visitor_log vl', 'v.ip = vl.ip')
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $this->parselQuery->dbQuery,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
            ],
        ]);

        return $dataProvider;
    }

    /**
     * The ParselQuery object
     *
     * @return ParselQuery
     */
    public function getParselQuery() {
        return $this->parselQuery;
    }

}