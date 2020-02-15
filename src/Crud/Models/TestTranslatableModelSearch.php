<?php

namespace Sichkarev\Translatable\Crud\Models;

use yii\data\ActiveDataProvider;

/**
 * Class TestTranslatableModelSearch represents the model behind the search form of `Sichkarev\Translatable\models\TestTranslatableModel`
 *
 * @package Sichkarev\Translatable\Crud\Models
 */
class TestTranslatableModelSearch extends TestTranslatableModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [
                [
                    'name', 'nameUa', 'nameEn',
                    'description', 'descriptionUa', 'descriptionEn',
                    'text',
                    'translations'
                ],
                'safe'
            ],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = TestTranslatableModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['likeasdasd', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'text', $this->text])

            ->andFilterWhereTranslate(['like', 'nameEn', '%' . $this->nameEn . '%'])
            ->andFilterWhereTranslate(['like', 'nameUa', '%' . $this->nameUa . '%']);

        return $dataProvider;
    }
}
