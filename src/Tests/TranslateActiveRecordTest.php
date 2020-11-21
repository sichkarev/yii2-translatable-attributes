<?php

namespace Sichkarev\Translatable\Tests;

use Codeception\Test\Unit;
use Sichkarev\Translatable\Crud\Models\TestTranslatableModel;
use Sichkarev\Translatable\Languages\Russian;
use Sichkarev\Translatable\Languages\Ukrainian;
use Yii;

/**
 * Class TranslateActiveRecordTest
 *
 * @package Sichkarev\Translatable\Tests\Unit
 */
class TranslateActiveRecordTest extends Unit
{
    public function testTranslateTranslatableModelModel()
    {
        $faker = \Faker\Factory::create();
        $originalName = $faker->regexify('[A-Z0-9._%+-]{32,32}');

        //создаем экземпляр класса, устанавливаем имя по умолчанию
        $translatableModel = new TestTranslatableModel([
            'name' => $originalName
        ]);
        expect($translatableModel->save())->true();

        //проверяем что переводы для свойств не заполнены (не затронуты)
        expect($translatableModel->name)->equals($originalName);
        expect($translatableModel->nameUa)->null();

        //ищем созданный экземпляр по идентификатору и устанавливаем контекст, передав Язык по умолчанию (Русский)
        //! ничего не должно поменяться
        $translatableModel = TestTranslatableModel::findOne($translatableModel->id)->setContextLanguage(new Russian());
        expect($translatableModel->name)->equals($originalName);
        expect($translatableModel->nameUa)->null();

        //ищем созданный экземпляр по идентификатору и устанавливаем контекст, передав Язык по умолчанию (Русский)
        //! ничего не должно поменяться
        $translatableModel = TestTranslatableModel::find()->setContextLanguage(new Russian())
                                                  ->andWhere(['id' => $translatableModel->id])
                                                  ->one();
        expect($translatableModel->name)->equals($originalName);
        expect($translatableModel->nameUa)->null();

        //ищем созданный экземпляр по идентификатору и устанавливаем контекст, передав Украинский язык
        //! ничего не должно поменяться, т.к. нужных переводов нет
        $translatableModel = TestTranslatableModel::findOne($translatableModel->id)->setContextLanguage(new Ukrainian());
        expect($translatableModel->name)->equals($originalName);
        expect($translatableModel->nameUa)->null();

        //далее берём рандомный язык
        //устанавливаем контекст этого языка и записываем новое значение в БАЗОВОЕ свойство модели
        //после сохранения записанное значение должно быть записано в перевод, а основное значение не должно быть затронуто
        $randomLang = $this->getRandomLang();
        echo "!!!!" . $randomLang->getTitle() . PHP_EOL;
        $translatableModel->setContextLanguage($randomLang);
        $translatableModel->name = $randomLang->getTitle();
        expect($translatableModel->save())->true();

        $propertyName = 'name'. ucfirst($randomLang->getCode());

        expect($translatableModel->name)->equals($randomLang->getTitle()); //свойство с учетом контекста
        expect($translatableModel->{$propertyName})->notNull();
        expect($translatableModel->{$propertyName})->equals($translatableModel->name);

        //очищаем контекст и проверяем, что модель в полном порядке
        $translatableModel->clearLangContext();
        expect($translatableModel->name)->equals($originalName); //оригинальное свойство на месте
        expect($translatableModel->{$propertyName})->notNull();
        expect($translatableModel->{$propertyName})->equals($randomLang->getTitle());

        //далее ищем созданный экземпляр по идентификатору и НЕ устанавливаем контекст
        $translatableModel = TestTranslatableModel::findOne($translatableModel->id);
        expect($translatableModel->{$propertyName})->equals($randomLang->getTitle());
        expect($translatableModel->name)->equals($originalName);

        //далее ищем созданный экземпляр по идентификатору и устанавливаем контекст, передав Язык $randomLang
        //свойство по умолчанию и свойство перевод должны быть одинаковыми
        $translatableModel = TestTranslatableModel::find()
                                                  ->setContextLanguage($randomLang)
                                                  ->andWhere(['id' => $translatableModel->id])
                                                  ->one();
        expect($translatableModel->name)->equals($translatableModel->{$propertyName});
        expect($translatableModel->{$propertyName})->equals($translatableModel->{$propertyName});

        //далее ищем созданный экземпляр по идентификатору и вручную задаем свойства
        $translatableModel = TestTranslatableModel::findOne($translatableModel->id);
        $ru = $faker->name;
        $ua = $faker->name;

        //через массовое обновление
        $updateData = [
            'name' => $ru,
            'nameUa' => $ua,
            'description' => 'description_' . $ru,
            'descriptionUa' => 'description_' . $ua,
        ];
        $translatableModel->updateAttributes($updateData);
        expect($translatableModel->name)->equals($ru);
        expect($translatableModel->nameUa)->equals($ua);
        expect($translatableModel->description)->equals($updateData['description']);
        expect($translatableModel->descriptionUa)->equals($updateData['descriptionUa']);

        expect($translatableModel->save())->true();
        $translatableModel->refresh();

        //обнуляем всё
        $translatableModel->name = $translatableModel->nameUa = null;
        expect($translatableModel->save())->true();
        expect($translatableModel->name)->null();
        expect($translatableModel->nameUa)->null();

        //снова заполняем
        $translatableModel->updateAttributes([
            'name' => $ru,
            'nameUa' => $ua,
        ]);

        //обнуляем все переводы, основное значение не трогаем
        $translatableModel->clearTranslate();
        expect($translatableModel->name)->equals($ru);
        expect($translatableModel->nameUa)->null();
        expect($translatableModel->save())->true();

        //обновляем
        $translatableModel->name = $ru;
        $translatableModel->nameUa = $ua;
        expect($translatableModel->save())->true();

        //проверяем в текущей модели
        expect($translatableModel->name)->equals($ru);
        expect($translatableModel->nameUa)->equals($ua);

        //получаем данные из БД и проверяем что всё сохранилось правильно
        $translatableModel = TestTranslatableModel::findOne($translatableModel->id);
        expect($translatableModel->name)->equals($ru);
        expect($translatableModel->nameUa)->equals($ua);

        //устанавливаем контекст по-умолчанию
        //!ничего не должно поменяться, т.к. Русский язык не хранится в переводах
        $translatableModel->setContextLanguage(new Russian());
        expect($translatableModel->name)->equals($ru);
        expect($translatableModel->nameUa)->equals($ua);

        //устанавливаем контекст украинского языка
        $translatableModel->setContextLanguage(new Ukrainian());
        expect($translatableModel->name)->equals($ua);
        expect($translatableModel->nameUa)->equals($ua);

        //очищаем контекст, без сохранения, поля должны быть в изначальном виде
        $translatableModel->clearLangContext();
        expect($translatableModel->name)->equals($ru);
        expect($translatableModel->nameUa)->equals($ua);

        //очищаем все переводы
        $translatableModel->name = $ru;
        $translatableModel->clearTranslate();
        expect($translatableModel->save())->true();

        $translatableModel = TestTranslatableModel::findOne($translatableModel->id);
        expect($translatableModel->name)->equals($ru);
        expect($translatableModel->nameUa)->null();

        //создаем экземпляр класса с заполнением переводов
        $translatableModel = new TestTranslatableModel([
            'name' => $faker->name,
            'nameUa' => 'Ukrainian'
        ]);
        expect($translatableModel->save())->true();

        //проверяем что переводы для свойств не заполнены
        expect($translatableModel->nameUa)->equals('Ukrainian');
    }

    /**
     * @return \Sichkarev\Translatable\Interfaces\LanguageInterface
     */
    private function getRandomLang()
    {
        $languages = Yii::$app->translatable->getListLanguages();
        //shuffle($languages);
        return $languages[1];
    }
}
