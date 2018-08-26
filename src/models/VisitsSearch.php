<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\visitors\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use johnsnook\visitors\models\Visits;
use johnsnook\parsel\ParselQuery;

/**
 * VisitsSearch represents the model behind the search form of `common\models\Visits`.
 *
 * @property-read ParselQuery $parselQuery The [[ParselQuery]] object used to parse the user query.
 * @property-read array $fields The table fields to search in query
 */
class VisitsSearch extends Visits {

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
    protected $parselQuery;

    /**
     * @var array The fields to search with ParselQuery
     */
    protected $fields = ['request', 'referer', 'user_agent'];

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['ip', 'request', 'referer', 'user_agent'], 'string'],
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
     * Create the ParselQuery from the params and return the query object for
     * use by [[search]] and [[Visits]] ajax methods for maps and graphs
     *
     * @param array $params
     * @return \yii\db\ActiveQuery
     */
    public function loadQuery($params) {
        $this->load($params);
        $query = Visits::find();
        if (!empty($this->ip)) {
            $query->where(['ip' => $this->ip]);
        }

        $this->parselQuery = new ParselQuery([
            'userQuery' => $this->userQuery,
            'searchFields' => $this->fields,
            'dbQuery' => $query
        ]);
        return $this->parselQuery->dbQuery;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->loadQuery($params),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
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

    /**
     * The search fields list
     *
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

}
